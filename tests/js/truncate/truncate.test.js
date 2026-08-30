import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, unmount } from '../../../resources/js/truncate.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="truncate">
            <p data-daisy-kit-status hidden role="status"></p>
            <p data-daisy-kit-truncate-text></p>
            <button data-daisy-kit-truncate-reveal hidden type="button"></button>
            <div data-daisy-kit-truncate-popover popover="manual"><p data-daisy-kit-truncate-full-text></p><button data-daisy-kit-truncate-close type="button">Close</button></div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="truncate"]');
}

afterEach(() => vi.restoreAllMocks());

describe('truncate entry', () => {
    it('only shows its reveal control when the rendered text overflows and restores it on destroy', () => {
        const element = root({ text: 'Long text', lines: 2, revealLabel: 'Read more', title: 'Summary' });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 40 }, clientWidth: { value: 100 }, scrollWidth: { value: 100 } });
        const button = element.querySelector('[data-daisy-kit-truncate-reveal]');

        mount(element);

        expect(button.hidden).toBe(false);
        expect(text.textContent).toBe('Long text');
        expect(getInstance(element).isTruncated()).toBe(true);
        unmount(element);
        expect(element.querySelector('[data-daisy-kit-truncate-reveal]').hidden).toBe(true);
        expect(element.querySelector('[data-daisy-kit-truncate-text]').textContent).toBe('');
    });

    it('opens and closes a native popover through its facade and events', () => {
        const element = root({ text: 'Long text', lines: 1, revealLabel: 'Read more', title: null });
        const text = element.querySelector('[data-daisy-kit-truncate-text]');
        Object.defineProperties(text, { clientHeight: { value: 20 }, scrollHeight: { value: 40 }, clientWidth: { value: 100 }, scrollWidth: { value: 100 } });
        const popover = element.querySelector('[data-daisy-kit-truncate-popover]');
        popover.showPopover = vi.fn();
        popover.hidePopover = vi.fn();
        const opened = [];
        element.addEventListener('daisy-kit:truncate:opened', (event) => opened.push(event.detail));

        const instance = mount(element);
        expect(Object.keys(instance).sort()).toEqual(['close', 'isTruncated', 'open', 'refresh']);
        expect(instance.refresh()).toBe(true);
        expect(instance.open()).toBe(true);
        expect(instance.open()).toBe(false);
        expect(instance.close()).toBe(true);
        expect(instance.close()).toBe(false);

        expect(popover.showPopover).toHaveBeenCalledOnce();
        expect(popover.hidePopover).toHaveBeenCalledOnce();
        expect(opened).toEqual([{ text: 'Long text' }]);
    });
});
