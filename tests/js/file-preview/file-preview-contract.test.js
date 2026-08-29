import { afterEach, describe, expect, it, vi } from 'vitest';

import { getInstance, mount, unmount } from '../../../resources/js/file-preview.js';

function previewRoot(configuration = {}) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="file-preview">
            <p data-daisy-kit-status hidden role="alert"></p>
            <p data-daisy-kit-loading hidden role="status"></p>
            <p data-daisy-kit-empty hidden role="status"></p>
            <button data-daisy-kit-file-preview-open-preview type="button">Preview</button>
            <button data-daisy-kit-file-preview-retry hidden type="button">Retry</button>
            <button data-daisy-kit-file-preview-zoom="out" type="button">Zoom out</button>
            <button data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
            <div data-daisy-kit-file-preview-inline-content></div>
            <dialog data-daisy-kit-file-preview-modal>
                <div data-daisy-kit-file-preview-modal-box>
                    <button data-daisy-kit-file-preview-close-preview type="button">Close</button>
                    <div data-daisy-kit-file-preview-modal-content></div>
                </div>
            </dialog>
            <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
            <div data-daisy-kit-file-preview-actions hidden>
                <a data-daisy-kit-file-preview-open hidden rel="noopener noreferrer" target="_blank">Open</a>
                <a data-daisy-kit-file-preview-download hidden>Download</a>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify({
                labels: {
                    error: 'Preview failed.',
                    frameNotReady: 'Frame unavailable.',
                    invalidType: 'Invalid type.',
                    tooLarge: 'Too large.',
                },
                layout: 'card',
                maxPreviewBytes: 10_000,
                previewMode: 'modal',
                ...configuration,
            })}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="file-preview"]');
}

function tokenFromFrame(frame) {
    return frame.srcdoc.match(/data-daisy-kit-file-preview-token="([^"]+)"/)?.[1];
}

function dispatchFrameMessage(root, message) {
    const frame = root.querySelector('[data-daisy-kit-file-preview-frame]');

    window.dispatchEvent(new MessageEvent('message', {
        data: {
            channel: 'daisy-kit:file-preview:frame',
            token: tokenFromFrame(frame),
            ...message,
        },
        origin: 'null',
        source: frame.contentWindow,
    }));
}

afterEach(() => {
    vi.unstubAllGlobals();
    vi.useRealTimers();
});

describe('file preview public contract', () => {
    it('returns an instance-local facade with state and controls', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const root = previewRoot({ name: 'Report.txt', type: 'text', url: '/report.txt' });

        const controller = mount(root);

        expect(controller).toBe(getInstance(root));
        expect(controller.getState()).toMatchObject({
            isOpen: false,
            name: 'Report.txt',
            status: 'loading',
            type: 'text',
        });

        controller.open();

        expect(root.querySelector('[data-daisy-kit-file-preview-modal]').open).toBe(true);
        expect(root.querySelector('[data-daisy-kit-file-preview-modal-box]')
            .contains(root.querySelector('[data-daisy-kit-file-preview-frame]'))).toBe(true);

        controller.close();

        expect(root.querySelector('[data-daisy-kit-file-preview-modal]').open).toBe(false);
        unmount(root);
        expect(getInstance(root)).toBeNull();
    });

    it('forwards the validated MIME type to media renderers', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response(new Blob(['svg'], { type: 'image/svg+xml' }), {
            headers: { 'content-type': 'image/svg+xml' },
            status: 200,
        }))));
        const root = previewRoot({ mimeType: 'image/svg+xml', type: 'image', url: '/plan.svg' });

        mount(root);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = root.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        dispatchFrameMessage(root, { type: 'ready' });

        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            payload: expect.objectContaining({ mimeType: 'image/svg+xml', type: 'image' }),
            type: 'render',
        }), '*', [expect.any(ArrayBuffer)]));
    });

    it('retries a failed request through the facade', async () => {
        const fetch = vi.fn()
            .mockResolvedValueOnce(new Response('Unavailable', { status: 503 }))
            .mockResolvedValueOnce(new Response('Recovered', {
                headers: { 'content-type': 'text/plain' },
                status: 200,
            }));
        vi.stubGlobal('fetch', fetch);
        const root = previewRoot({ type: 'text', url: '/report.txt' });
        const controller = mount(root);

        await vi.waitFor(() => expect(controller.getState().status).toBe('error'));
        controller.retry();

        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(2));
        expect(controller.getState().status).toBe('loading');
    });
});
