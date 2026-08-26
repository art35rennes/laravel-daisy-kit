import { afterEach, describe, expect, it, vi } from 'vitest';

import { mount, unmount } from '../../../resources/js/file-preview.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="file-preview">
            <p data-daisy-kit-status hidden role="alert"></p>
            <div data-daisy-kit-content>
                <p data-daisy-kit-loading hidden></p>
                <p data-daisy-kit-empty hidden></p>
                <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="file-preview"]');
}

function frameMessage(element, message, source = null, origin = 'null') {
    const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
    const token = frame.srcdoc.match(/data-daisy-kit-file-preview-token="([a-f0-9]+)"/)?.[1];

    window.dispatchEvent(new MessageEvent('message', {
        data: { channel: 'daisy-kit:file-preview:frame', token, ...message },
        origin,
        source: source ?? frame.contentWindow,
    }));

    return { frame, token };
}

afterEach(() => {
    vi.unstubAllGlobals();
    vi.useRealTimers();
});

describe('file preview entry', () => {
    it('sends validated text to the sandboxed frame and destroys its instance', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        const { token } = frameMessage(element, { type: 'ready' });

        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            payload: expect.objectContaining({ data: 'plain text', type: 'text' }),
            token,
            type: 'render',
        }), '*', []));

        unmount(element);

        expect(frame.getAttribute('srcdoc')).toBeNull();
    });

    it('accepts an opaque-origin ready message only from its frame with its token', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        const token = frame.srcdoc.match(/data-daisy-kit-file-preview-token="([a-f0-9]+)"/)?.[1];

        frameMessage(element, { token: 'a'.repeat(32), type: 'ready' });
        frameMessage(element, { type: 'ready' }, window);
        expect(postMessage).not.toHaveBeenCalled();

        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({ token, type: 'render' }), '*', []));
    });

    it('fails closed when the sandboxed frame does not become ready', () => {
        vi.useFakeTimers();
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        vi.advanceTimersByTime(10_001);

        expect(element.dataset.daisyKitState).toBe('error');
        expect(element.querySelector('[data-daisy-kit-status]').textContent).toContain('did not become ready');
    });

    it('ignores ready messages after destruction', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        const token = frame.srcdoc.match(/data-daisy-kit-file-preview-token="([a-f0-9]+)"/)?.[1];
        unmount(element);
        window.dispatchEvent(new MessageEvent('message', {
            data: { channel: 'daisy-kit:file-preview:frame', token, type: 'ready' },
            origin: 'null',
            source: frame.contentWindow,
        }));

        expect(postMessage).not.toHaveBeenCalled();
        expect(frame.getAttribute('srcdoc')).toBeNull();
    });

    it('keeps opaque-origin handshakes isolated between multiple instances', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        document.body.innerHTML = `
            <section data-daisy-kit-module="file-preview">
                <p data-daisy-kit-status hidden role="alert"></p><p data-daisy-kit-loading hidden></p><p data-daisy-kit-empty hidden></p>
                <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
                <script data-daisy-kit-config type="application/json">{"src":"/one.txt","type":"text"}</script>
            </section>
            <section data-daisy-kit-module="file-preview">
                <p data-daisy-kit-status hidden role="alert"></p><p data-daisy-kit-loading hidden></p><p data-daisy-kit-empty hidden></p>
                <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
                <script data-daisy-kit-config type="application/json">{"src":"/two.txt","type":"text"}</script>
            </section>`;
        const [first, second] = document.querySelectorAll('[data-daisy-kit-module="file-preview"]');

        mount(first);
        mount(second);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(2));
        const firstFrame = first.querySelector('[data-daisy-kit-file-preview-frame]');
        const secondFrame = second.querySelector('[data-daisy-kit-file-preview-frame]');
        const firstPostMessage = vi.spyOn(firstFrame.contentWindow, 'postMessage');
        const secondPostMessage = vi.spyOn(secondFrame.contentWindow, 'postMessage');

        frameMessage(first, { type: 'ready' });
        await vi.waitFor(() => expect(firstPostMessage).toHaveBeenCalledOnce());
        expect(secondPostMessage).not.toHaveBeenCalled();

        frameMessage(second, { type: 'ready' });
        await vi.waitFor(() => expect(secondPostMessage).toHaveBeenCalledOnce());
    });

    it('rejects unsafe sources and oversized responses', async () => {
        const unsafe = root({ src: 'javascript:alert(1)' });
        mount(unsafe);
        expect(unsafe.dataset.daisyKitState).toBe('empty');

        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('large', {
            headers: { 'content-length': '5242881', 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ maxBytes: 1, src: '/notes.txt', type: 'text' });
        mount(element);

        await vi.waitFor(() => expect(element.dataset.daisyKitState).toBe('error'));
        expect(element.querySelector('[data-daisy-kit-status]').textContent).toContain('too large');
    });

    it('does not write a pending response after unmount', async () => {
        let resolveBlob;
        const blob = new Promise((resolve) => { resolveBlob = resolve; });
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve({
            blob: () => blob,
            headers: new Headers({ 'content-type': 'text/plain' }),
            ok: true,
        })));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        unmount(element);
        resolveBlob(new Blob(['late content'], { type: 'text/plain' }));
        await Promise.resolve();
        await Promise.resolve();

        expect(element.dataset.daisyKitState).toBeUndefined();
        expect(frame.getAttribute('srcdoc')).toBeNull();
    });
});
