import { afterEach, describe, expect, it, vi } from 'vitest';

import { getInstance, mount, unmount } from '../../../resources/js/file-preview.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="file-preview">
            <p data-daisy-kit-status hidden role="alert"></p>
            <div data-daisy-kit-content>
                <p data-daisy-kit-loading hidden></p>
                <p data-daisy-kit-empty hidden></p>
                <dialog data-daisy-kit-file-preview-modal><button data-daisy-kit-file-preview-close-preview type="button">Close preview</button></dialog>
                <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
                <dl data-daisy-kit-file-preview-metadata hidden><dd data-daisy-kit-file-preview-name></dd><dd data-daisy-kit-file-preview-type></dd><dd data-daisy-kit-file-preview-size></dd></dl>
                <p data-daisy-kit-file-preview-notice hidden></p>
                <button data-daisy-kit-file-preview-open-preview type="button">Preview file</button>
                <button data-daisy-kit-file-preview-layout type="button">Toggle expanded layout</button>
                <button data-daisy-kit-file-preview-zoom="out" type="button">Zoom out</button><button data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
                <p data-daisy-kit-file-preview-actions hidden><a data-daisy-kit-file-preview-open hidden rel="noopener" target="_blank">Open file</a><a data-daisy-kit-file-preview-download hidden>Download file</a></p>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="file-preview"]');
}

function tokenFromFrame(frame) {
    return frame.srcdoc.match(/data-daisy-kit-file-preview-token="([^"]+)"/)?.[1];
}

function frameMessage(element, message, source = null, origin = 'null') {
    const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
    const token = tokenFromFrame(frame);

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
    it('exposes a stable facade for preview state and reloads', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ layout: 'modal', src: '/notes.txt', type: 'text' });

        const preview = mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());

        expect(preview).toBe(getInstance(element));
        expect(Object.keys(preview).sort()).toEqual(['close', 'getState', 'open', 'reload', 'setExpanded', 'setZoom']);
        expect(preview.getState()).toMatchObject({ layout: 'modal', open: false, status: 'loading', type: 'text', zoom: 100 });
        expect(preview.open()).toBe(true);
        expect(preview.getState().open).toBe(true);
        expect(preview.setZoom(175)).toBe(true);
        expect(preview.getState().zoom).toBe(175);
        expect(preview.setExpanded(true)).toBe(true);
        expect(preview.getState().expanded).toBe(true);
        expect(preview.close()).toBe(true);
        expect(await preview.reload()).toBe(true);
        expect(getInstance(element)).toBe(preview);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(2));

        unmount(element);

        expect(getInstance(element)).toBeNull();
    });

    it('starts a sandboxed preview on an HTTP host without crypto.randomUUID', async () => {
        vi.stubGlobal('crypto', {});
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());

        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        expect(element.dataset.daisyKitState).toBe('loading');
        expect(frame.getAttribute('srcdoc')).toContain('data-daisy-kit-file-preview-token="daisy-kit-file-preview-');
    });

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

    it('hands media to an isolated frame with a network-denying child CSP', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response(new Blob(['image'], { type: 'image/png' }), {
            headers: { 'content-type': 'image/png' },
            status: 200,
        }))));
        const element = root({ src: '/preview.png', type: 'image' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        frameMessage(element, { type: 'ready' });

        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            payload: expect.objectContaining({ data: expect.any(ArrayBuffer), type: 'image' }),
            type: 'render',
        }), '*', [expect.any(ArrayBuffer)]));
        expect(frame.getAttribute('sandbox')).not.toContain('allow-same-origin');
        expect(frame.srcdoc).toContain("connect-src 'none'");
        expect(frame.srcdoc).toContain("script-src-attr 'none'");
        expect(frame.srcdoc).not.toContain('frame-ancestors');
        expect(frame.srcdoc).toContain('<script src=');
        expect(frame.srcdoc).not.toContain('onload=');
    });

    it('regenerates a transferable payload after the sandboxed frame navigates and handshakes again', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response(new Blob(['pdf'], { type: 'application/pdf' }), {
            headers: { 'content-type': 'application/pdf' },
            status: 200,
        }))));
        const element = root({ src: '/preview.pdf', type: 'pdf' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');

        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledOnce());
        const firstRender = postMessage.mock.calls[0][0];

        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledTimes(2));
        const secondRender = postMessage.mock.calls[1][0];

        expect(secondRender.renderId).not.toBe(firstRender.renderId);
        expect(secondRender.payload.data).toBeInstanceOf(ArrayBuffer);
        expect(secondRender.payload.data).not.toBe(firstRender.payload.data);

        frameMessage(element, { renderId: secondRender.renderId, type: 'rendered' });
        await vi.waitFor(() => expect(element.dataset.daisyKitState).toBe('ready'));
    });

    it('previews validated video with metadata and an explicit layout action', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response(new Blob(['video'], { type: 'video/mp4' }), {
            headers: { 'content-type': 'video/mp4' },
            status: 200,
        }))));
        const element = root({ layout: 'standard', name: 'Clip', src: '/clip.mp4', type: 'video' });
        const layouts = [];
        element.addEventListener('daisy-kit:file-preview:layout', (event) => layouts.push(event.detail.layout));

        mount(element);
        await vi.waitFor(() => expect(element.querySelector('[data-daisy-kit-file-preview-metadata]').hidden).toBe(false));
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            payload: expect.objectContaining({ type: 'video' }),
        }), '*', [expect.any(ArrayBuffer)]));

        element.querySelector('[data-daisy-kit-file-preview-layout]').click();
        expect(element.dataset.daisyKitLayout).toBe('expanded');
        expect(layouts).toEqual(['expanded']);
        expect(element.querySelector('[data-daisy-kit-file-preview-name]').textContent).toBe('Clip');
        expect(frame.srcdoc).toContain('media-src blob:');
    });

    it('supports modal and action-only preview controls with zoom and notices', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({
            layout: 'action-only',
            name: 'Notes',
            notice: 'Sensitive document',
            src: '/notes.txt',
            type: 'text',
        });
        const events = [];
        element.addEventListener('daisy-kit:file-preview:preview', (event) => events.push(event.detail));
        element.addEventListener('daisy-kit:file-preview:zoom', (event) => events.push(event.detail));

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        frameMessage(element, { type: 'ready' });
        frameMessage(element, { type: 'rendered' });

        expect(element.querySelector('[data-daisy-kit-file-preview-frame]').hidden).toBe(true);
        expect(element.querySelector('[data-daisy-kit-file-preview-notice]').textContent).toBe('Sensitive document');
        element.querySelector('[data-daisy-kit-file-preview-open-preview]').click();
        element.querySelector('[data-daisy-kit-file-preview-zoom="in"]').click();

        expect(element.querySelector('[data-daisy-kit-file-preview-frame]').hidden).toBe(false);
        expect(element.dataset.daisyKitZoom).toBe('125');
        expect(events).toEqual([{ open: true }, { zoom: 125 }]);
    });

    it('opens a modal layout only when the explicit preview action is requested', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ layout: 'modal', src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const modal = element.querySelector('[data-daisy-kit-file-preview-modal]');

        expect(modal.open).toBe(false);
        element.querySelector('[data-daisy-kit-file-preview-open-preview]').click();

        expect(modal.open).toBe(true);
        expect(modal.querySelector('[data-daisy-kit-file-preview-frame]')).not.toBeNull();
        modal.querySelector('[data-daisy-kit-file-preview-close-preview]').click();
        expect(modal.open).toBe(false);
        expect(element.dataset.daisyKitPreviewOpen).toBe('false');
    });

    it('commits modal close state before a native close event is delivered', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ layout: 'modal', src: '/notes.txt', type: 'text' });
        const events = [];
        element.addEventListener('daisy-kit:file-preview:preview', (event) => events.push(event.detail));

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const modal = element.querySelector('[data-daisy-kit-file-preview-modal]');
        const trigger = element.querySelector('[data-daisy-kit-file-preview-open-preview]');
        modal.close = vi.fn(() => {
            modal.open = false;
        });

        trigger.click();
        modal.querySelector('[data-daisy-kit-file-preview-close-preview]').click();

        expect(modal.open).toBe(false);
        expect(element.dataset.daisyKitPreviewOpen).toBe('false');
        expect(document.activeElement).toBe(trigger);
        expect(events).toEqual([{ open: true }, { open: false }]);

        modal.dispatchEvent(new Event('close'));
        expect(events).toEqual([{ open: true }, { open: false }]);
    });

    it('opens the preview dialog from a standard layout and restores its inline frame after close', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ layout: 'standard', src: '/notes.txt', type: 'text' });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const modal = element.querySelector('[data-daisy-kit-file-preview-modal]');
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const trigger = element.querySelector('[data-daisy-kit-file-preview-open-preview]');

        trigger.click();

        expect(modal.open).toBe(true);
        expect(element.dataset.daisyKitPreviewOpen).toBe('true');
        expect(modal.contains(frame)).toBe(true);

        modal.querySelector('[data-daisy-kit-file-preview-close-preview]').click();

        expect(modal.open).toBe(false);
        expect(element.dataset.daisyKitPreviewOpen).toBe('true');
        expect(element.querySelector('[data-daisy-kit-content]').contains(frame)).toBe(true);
        expect(frame.hidden).toBe(false);
    });

    it('keeps modal state and focus isolated between multiple standard previews', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        document.body.innerHTML = `
            <section data-daisy-kit-module="file-preview">
                <div data-daisy-kit-content><dialog data-daisy-kit-file-preview-modal><button data-daisy-kit-file-preview-close-preview type="button">Close</button></dialog><iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe></div>
                <button data-daisy-kit-file-preview-open-preview type="button">Preview first</button>
                <script data-daisy-kit-config type="application/json">{"layout":"standard","src":"/first.txt","type":"text"}</script>
            </section>
            <section data-daisy-kit-module="file-preview">
                <div data-daisy-kit-content><dialog data-daisy-kit-file-preview-modal><button data-daisy-kit-file-preview-close-preview type="button">Close</button></dialog><iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe></div>
                <button data-daisy-kit-file-preview-open-preview type="button">Preview second</button>
                <script data-daisy-kit-config type="application/json">{"layout":"standard","src":"/second.txt","type":"text"}</script>
            </section>`;
        const [first, second] = document.querySelectorAll('[data-daisy-kit-module="file-preview"]');

        mount(first);
        mount(second);
        const firstDialog = first.querySelector('[data-daisy-kit-file-preview-modal]');
        const secondDialog = second.querySelector('[data-daisy-kit-file-preview-modal]');
        const firstTrigger = first.querySelector('[data-daisy-kit-file-preview-open-preview]');

        firstTrigger.click();

        expect(firstDialog.open).toBe(true);
        expect(secondDialog.open).toBe(false);
        expect(second.dataset.daisyKitPreviewOpen).toBe('true');

        firstDialog.querySelector('[data-daisy-kit-file-preview-close-preview]').click();

        expect(firstDialog.open).toBe(false);
        expect(document.activeElement).toBe(firstTrigger);
        expect(first.dataset.daisyKitPreviewOpen).toBe('true');
        expect(second.dataset.daisyKitPreviewOpen).toBe('true');
    });

    it('retains card as an explicit non-modal layout', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ layout: 'card', src: '/notes.txt', type: 'text' });

        mount(element);

        expect(element.dataset.daisyKitLayout).toBe('card');
        expect(element.dataset.daisyKitPreviewOpen).toBe('true');
    });

    it('hands a PDF to a nested sandbox and exposes revocable user actions only after validation', async () => {
        const revokeObjectURL = vi.fn();
        const NativeURL = URL;
        vi.stubGlobal('URL', Object.assign(class extends NativeURL {}, {
            createObjectURL: vi.fn(() => 'blob:preview'),
            revokeObjectURL,
        }));
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response(new Blob(['pdf'], { type: 'application/pdf' }), {
            headers: { 'content-type': 'application/pdf' },
            status: 200,
        }))));
        const element = root({ name: 'Report.pdf', src: '/report.pdf', type: 'pdf' });

        mount(element);
        await vi.waitFor(() => expect(element.querySelector('[data-daisy-kit-file-preview-actions]').hidden).toBe(false));
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            payload: expect.objectContaining({ type: 'pdf' }),
        }), '*', [expect.any(ArrayBuffer)]));

        expect(element.querySelector('[data-daisy-kit-file-preview-open]').getAttribute('rel')).toBe('noopener');
        expect(element.querySelector('[data-daisy-kit-file-preview-download]').download).toBe('Report.pdf');
        expect(frame.srcdoc).toContain('frame-src blob:');
        unmount(element);
        expect(revokeObjectURL).toHaveBeenCalledWith('blob:preview');
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
        const token = tokenFromFrame(frame);

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
        const token = tokenFromFrame(frame);
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
