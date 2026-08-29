import { afterEach, describe, expect, it, vi } from 'vitest';

import { getInstance, mount, unmount } from '../../../resources/js/file-preview.js';

const mountedRoots = [];

function root(configuration = {}) {
    const wrapper = document.createElement('div');

    wrapper.innerHTML = `
        <section data-daisy-kit-module="file-preview">
            <div data-daisy-kit-status hidden role="alert">
                <span data-daisy-kit-status-message></span>
                <button data-daisy-kit-file-preview-retry hidden type="button">Retry</button>
            </div>
            <p data-daisy-kit-loading hidden role="status"></p>
            <p data-daisy-kit-empty hidden role="status"></p>
            <button data-daisy-kit-file-preview-open-preview type="button">Preview</button>
            <button data-daisy-kit-file-preview-zoom="out" type="button">Zoom out</button>
            <button data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
            <output data-daisy-kit-file-preview-zoom-output></output>
            <div data-daisy-kit-file-preview-inline-content>
                <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
            </div>
            <dialog data-daisy-kit-file-preview-modal>
                <div data-daisy-kit-file-preview-modal-box>
                    <button data-daisy-kit-file-preview-close-preview type="button">Close</button>
                    <div data-daisy-kit-file-preview-modal-content></div>
                </div>
            </dialog>
            <div data-daisy-kit-file-preview-frame-staging></div>
            <a data-daisy-kit-file-preview-open hidden rel="noopener noreferrer" target="_blank">Open</a>
            <a data-daisy-kit-file-preview-download hidden>Download</a>
            <script data-daisy-kit-config type="application/json">${JSON.stringify({
                canPreview: true,
                labels: {
                    error: 'Preview failed.',
                    frameNotReady: 'Frame unavailable.',
                    invalidType: 'Invalid type.',
                    tooLarge: 'Too large.',
                },
                layout: 'card',
                maxPreviewBytes: 10_000,
                maxTextPreviewBytes: 1_000,
                name: 'Report.txt',
                previewMode: 'modal',
                type: 'text',
                url: '/report.txt',
                ...configuration,
            })}</script>
        </section>
    `;
    const element = wrapper.firstElementChild;

    document.body.append(element);
    mountedRoots.push(element);

    return element;
}

function frameToken(element) {
    return element.querySelector('[data-daisy-kit-file-preview-frame]').srcdoc
        .match(/data-daisy-kit-file-preview-token="([^"]+)"/)?.[1];
}

function frameMessage(element, message, overrides = {}) {
    const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');

    window.dispatchEvent(new MessageEvent('message', {
        data: {
            channel: 'daisy-kit:file-preview:frame',
            token: frameToken(element),
            ...message,
        },
        origin: 'null',
        source: frame.contentWindow,
        ...overrides,
    }));
}

afterEach(() => {
    mountedRoots.splice(0).forEach((element) => unmount(element));
    document.body.replaceChildren();
    vi.unstubAllGlobals();
    vi.useRealTimers();
});

describe('file preview runtime', () => {
    it('loads a file in an opaque, network-denying frame', async () => {
        vi.stubGlobal('crypto', {});
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root();

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());

        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');

        expect(frame.getAttribute('sandbox')).toBe('allow-scripts');
        expect(frame.srcdoc).toContain("connect-src 'none'");
        expect(frame.srcdoc).toContain("script-src-attr 'none'");
        expect(frame.srcdoc).toContain('<link rel="stylesheet"');
        expect(frame.srcdoc).not.toContain('allow-same-origin');
        expect(frame.srcdoc).not.toContain('onload=');
    });

    it('authenticates frame messages with source, token, and render id', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root();
        const controller = mount(element);
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');

        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        frameMessage(element, { token: 'wrong-token', type: 'ready' });
        frameMessage(element, { type: 'ready' }, { source: window });
        expect(postMessage).not.toHaveBeenCalled();

        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledOnce());
        const renderId = postMessage.mock.calls[0][0].renderId;
        frameMessage(element, { renderId: renderId + 1, type: 'rendered' });
        expect(controller.getState().status).toBe('loading');

        frameMessage(element, { renderId, type: 'rendered' });
        expect(controller.getState().status).toBe('ready');
    });

    it('truncates text independently from the transport limit', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Long report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ maxTextPreviewBytes: 4 });

        mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');
        frameMessage(element, { type: 'ready' });

        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            payload: expect.objectContaining({
                data: 'Long',
                truncated: true,
                truncatedLabel: 'Only the beginning of this file is shown.',
            }),
        }), '*', []));
    });

    it('rejects MIME mismatches with retry available', async () => {
        const fetch = vi.fn()
            .mockResolvedValueOnce(new Response('wrong', {
                headers: { 'content-type': 'text/html' },
                status: 200,
            }))
            .mockResolvedValueOnce(new Response('recovered', {
                headers: { 'content-type': 'text/plain' },
                status: 200,
            }));
        vi.stubGlobal('fetch', fetch);
        const element = root({ type: 'pdf' });
        const controller = mount(element);

        await vi.waitFor(() => expect(controller.getState().status).toBe('error'));
        expect(element.querySelector('[data-daisy-kit-status-message]').textContent).toBe('Invalid type.');
        expect(element.querySelector('[data-daisy-kit-file-preview-retry]').hidden).toBe(false);

        element.querySelector('script').textContent = '';
        controller.retry();
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(2));
    });

    it('stops reading a response as soon as the transport limit is exceeded', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Oversized report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ maxPreviewBytes: 4 });
        const controller = mount(element);

        await vi.waitFor(() => expect(controller.getState().status).toBe('error'));
        expect(element.querySelector('[data-daisy-kit-status-message]').textContent).toBe('Too large.');
    });

    it('does not let an aborted request overwrite a newer retry', async () => {
        let resolveFirstRequest;
        const firstRequest = new Promise((resolve) => {
            resolveFirstRequest = resolve;
        });
        const fetch = vi.fn()
            .mockReturnValueOnce(firstRequest)
            .mockResolvedValueOnce(new Response('Current response', {
                headers: { 'content-type': 'text/plain' },
                status: 200,
            }));
        vi.stubGlobal('fetch', fetch);
        const createObjectURL = vi.spyOn(URL, 'createObjectURL');
        const element = root();
        const controller = mount(element);

        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        controller.retry();
        await vi.waitFor(() => expect(createObjectURL).toHaveBeenCalledOnce());

        resolveFirstRequest(new Response('Stale response', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }));
        await Promise.resolve();
        await Promise.resolve();

        expect(createObjectURL).toHaveBeenCalledOnce();
    });

    it('keeps modal state and focus isolated between instances', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const first = root({ name: 'First' });
        const second = root({ name: 'Second' });
        const firstController = mount(first);
        const secondController = mount(second);
        const secondTrigger = second.querySelector('[data-daisy-kit-file-preview-open-preview]');

        secondTrigger.focus();
        secondController.open();

        expect(firstController.getState().isOpen).toBe(false);
        expect(secondController.getState().isOpen).toBe(true);
        expect(second.querySelector('[data-daisy-kit-file-preview-modal]').open).toBe(true);

        secondController.close();
        expect(document.activeElement).toBe(secondTrigger);
    });

    it('restores an inline frame after the modal closes', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ previewMode: 'inline' });
        const controller = mount(element);
        const inlineHost = element.querySelector('[data-daisy-kit-file-preview-inline-content]');
        const modalContent = element.querySelector('[data-daisy-kit-file-preview-modal-content]');
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');

        controller.open();
        expect(modalContent.contains(frame)).toBe(true);

        controller.close();
        expect(inlineHost.contains(frame)).toBe(true);
    });

    it('exposes bounded DOCX zoom controls and events', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ docxZoom: 195, type: 'docx' });
        const events = [];
        element.addEventListener('daisy-kit:file-preview:zoom', (event) => events.push(event.detail.zoom));
        const controller = mount(element);

        expect(controller.zoomIn()).toBe(200);
        expect(controller.setZoom(10)).toBe(25);
        expect(controller.zoomOut()).toBe(25);
        expect(events).toEqual([200, 25, 25]);
    });

    it('aborts transport, revokes object URLs, and removes its facade on unmount', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const revokeObjectURL = vi.spyOn(URL, 'revokeObjectURL');
        const element = root();

        mount(element);
        await vi.waitFor(() => expect(element.querySelector('[data-daisy-kit-file-preview-open]').hidden).toBe(false));
        unmount(element);

        expect(revokeObjectURL).toHaveBeenCalledOnce();
        expect(getInstance(element)).toBeNull();
        expect(element.querySelector('[data-daisy-kit-file-preview-frame]').getAttribute('srcdoc')).toBeNull();
    });
});
