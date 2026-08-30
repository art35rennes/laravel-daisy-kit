import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, unmount } from '../../../resources/js/copyable.js';

function root(configuration, text = 'Visible value') {
    document.body.innerHTML = `
        <section data-daisy-kit-module="copyable">
            <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>
            <button data-daisy-kit-copyable-button type="button">${text}</button>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="copyable"]');
}

afterEach(() => {
    vi.unstubAllGlobals();
    vi.useRealTimers();
});

describe('copyable entry', () => {
    it('copies the configured value, announces success, emits an event, and exposes a facade', async () => {
        const writeText = vi.fn(() => Promise.resolve());
        vi.stubGlobal('navigator', { clipboard: { writeText } });
        const element = root({ value: 'secret', copyLabel: 'Copy', successLabel: 'Done', errorLabel: 'Failed', feedbackDuration: 1000, disabled: false });
        const copied = [];
        element.addEventListener('daisy-kit:copyable:copied', (event) => copied.push(event.detail));

        const instance = mount(element);
        await expect(instance.copy()).resolves.toBe(true);

        expect(getInstance(element)).toBe(instance);
        expect(Object.keys(instance).sort()).toEqual(['copy', 'getValue']);
        expect(writeText).toHaveBeenCalledWith('secret');
        expect(element.querySelector('[data-daisy-kit-status]').textContent).toBe('Done');
        expect(copied).toEqual([{ value: 'secret' }]);
    });

    it('copies visible text when no explicit value is supplied and restores the original DOM on unmount', async () => {
        const writeText = vi.fn(() => Promise.resolve());
        vi.stubGlobal('navigator', { clipboard: { writeText } });
        const element = root({ value: null, copyLabel: 'Copy', successLabel: 'Done', errorLabel: 'Failed', feedbackDuration: 1000, disabled: false }, 'Visible text');
        const button = element.querySelector('[data-daisy-kit-copyable-button]');

        mount(element);
        button.click();
        await vi.waitFor(() => expect(writeText).toHaveBeenCalledWith('Visible text'));
        unmount(element);

        expect(button.getAttribute('aria-label')).toBeNull();
        expect(button.disabled).toBe(false);
        expect(getInstance(element)).toBeNull();
    });

    it('reports clipboard failures without throwing', async () => {
        vi.stubGlobal('navigator', { clipboard: { writeText: vi.fn(() => Promise.reject(new Error('denied'))) } });
        const element = root({ value: 'secret', copyLabel: 'Copy', successLabel: 'Done', errorLabel: 'Failed', feedbackDuration: 1000, disabled: false });
        const errors = [];
        element.addEventListener('daisy-kit:copyable:error', (event) => errors.push(event.detail));

        await expect(mount(element).copy()).resolves.toBe(false);

        expect(element.querySelector('[data-daisy-kit-status]').textContent).toBe('Failed');
        expect(errors).toEqual([{
            code: 'clipboard-rejected',
            message: 'The clipboard rejected the copy request.',
            value: 'secret',
        }]);
    });

    it.each([
        [{ disabled: true, value: 'secret' }, 'disabled', 'Copying is disabled.'],
        [{ disabled: false, value: '' }, 'empty-value', 'There is no text to copy.'],
        [{ disabled: false, value: 'secret' }, 'clipboard-unavailable', 'The Clipboard API is unavailable.'],
    ])('reports a structured %s operational error', async (overrides, code, message) => {
        vi.stubGlobal('navigator', {});
        const element = root({ copyLabel: 'Copy', successLabel: 'Done', errorLabel: 'Failed', feedbackDuration: 1000, ...overrides });
        const errors = [];
        element.addEventListener('daisy-kit:copyable:error', (event) => errors.push(event.detail));

        await expect(mount(element).copy()).resolves.toBe(false);

        expect(errors).toEqual([{ code, message, value: overrides.value }]);
    });
});
