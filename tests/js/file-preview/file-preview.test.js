import { afterEach, describe, expect, it, vi } from 'vitest';

vi.mock('docx-preview', () => ({ renderAsync: vi.fn(() => Promise.resolve()) }));

import { mount, unmount } from '../../../resources/js/file-preview.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="file-preview">
            <p data-daisy-kit-status hidden role="alert"></p>
            <div data-daisy-kit-content>
                <p data-daisy-kit-loading hidden></p>
                <p data-daisy-kit-empty hidden></p>
                <img data-daisy-kit-file-preview-image hidden>
                <pre data-daisy-kit-file-preview-text hidden></pre>
                <div data-daisy-kit-file-preview-docx hidden></div>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="file-preview"]');
}

afterEach(() => vi.unstubAllGlobals());

describe('file preview entry', () => {
    it('renders text safely and destroys its instance', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', { status: 200 }))));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(element.querySelector('[data-daisy-kit-file-preview-text]').textContent).toBe('plain text'));

        unmount(element);

        expect(element.querySelector('[data-daisy-kit-file-preview-text]').textContent).toBe('');
    });

    it('shows an empty state when there is no safe source', () => {
        const element = root({ src: 'javascript:alert(1)' });

        mount(element);

        expect(element.dataset.daisyKitState).toBe('empty');
        expect(element.querySelector('[data-daisy-kit-empty]').hidden).toBe(false);
    });
});
