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
            <button data-daisy-kit-file-preview-zoom="fit" type="button">Fit</button>
            <button data-daisy-kit-file-preview-zoom="in" type="button">Zoom in</button>
            <output data-daisy-kit-file-preview-zoom-output></output>
            <div data-daisy-kit-file-preview-inline-content></div>
            <dialog data-daisy-kit-file-preview-modal>
                <div data-daisy-kit-file-preview-modal-box>
                    <button data-daisy-kit-file-preview-close-preview type="button">Close</button>
                    <div data-daisy-kit-file-preview-modal-content>
                        <iframe data-daisy-kit-file-preview-frame hidden sandbox="allow-scripts"></iframe>
                    </div>
                    <a data-daisy-kit-file-preview-download data-daisy-kit-file-preview-modal-download hidden>Download</a>
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
    it('exposes a stable facade for preview state and reloads', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('plain text', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ previewMode: 'modal', url: '/notes.txt', type: 'text' });

        const preview = mount(element);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());

        expect(preview).toBe(getInstance(element));
        expect(Object.keys(preview).sort()).toEqual([
            'close', 'fit', 'getState', 'open', 'reload', 'retry', 'setExpanded', 'setZoom', 'zoomIn', 'zoomOut',
        ]);
        expect(preview.getState()).toMatchObject({
            expanded: false, isOpen: false, layout: 'card', open: false, status: 'loading', type: 'text', zoom: 100,
        });
        expect(preview.open()).toBe(true);
        expect(preview.getState().open).toBe(true);
        expect(preview.setZoom(175)).toBe(true);
        expect(preview.getState().zoom).toBe(175);
        expect(preview.close()).toBe(true);
        expect(preview.setExpanded(true)).toBe(true);
        expect(preview.getState().expanded).toBe(true);
        expect(element.querySelector('[data-daisy-kit-file-preview-modal]').open).toBe(true);
        expect(preview.setExpanded(true)).toBe(false);
        expect(preview.setExpanded('false')).toBe(false);
        expect(preview.close()).toBe(true);
        expect(await preview.reload()).toBe(true);
        expect(getInstance(element)).toBe(preview);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(2));

        unmount(element);

        expect(getInstance(element)).toBeNull();
    });

    it('loads a file in an opaque, network-denying frame', async () => {
        vi.stubGlobal('crypto', {});
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ previewMode: 'inline' });

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
        const element = root({ previewMode: 'inline' });
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
        const element = root({ maxTextPreviewBytes: 4, previewMode: 'inline' });

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
        expect(controller.retry()).toBe(true);
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

    it('updates every download action after validating the file', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root({ canDownload: true, downloadUrl: null });

        mount(element);

        await vi.waitFor(() => {
            const downloads = [...element.querySelectorAll('[data-daisy-kit-file-preview-download]')];

            expect(downloads).toHaveLength(2);
            expect(downloads.every((download) => download.hidden === false && download.href.startsWith('blob:'))).toBe(true);
        });
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

    it('reloads the current configuration without replacing the facade or accepting old frame messages', async () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ previewMode: 'inline' });
        const controller = mount(element);
        const initialState = controller.getState();
        const previousToken = frameToken(element);
        const previousSignal = fetch.mock.calls[0][1].signal;
        const configuration = element.querySelector('[data-daisy-kit-config]');
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');

        controller.open();
        expect(initialState.isOpen).toBe(false);
        configuration.textContent = JSON.stringify({
            ...JSON.parse(configuration.textContent),
            name: 'Updated.txt',
            url: '/updated.txt',
        });

        expect(await controller.reload()).toBe(true);
        expect(getInstance(element)).toBe(controller);
        expect(mount(element)).toBe(controller);
        expect(previousSignal.aborted).toBe(true);
        expect(fetch).toHaveBeenCalledTimes(2);
        expect(fetch.mock.calls[1][0]).toMatch(/\/updated\.txt$/);
        expect(controller.getState()).toMatchObject({ expanded: false, isOpen: false, name: 'Updated.txt', open: false });
        expect(element.querySelector('[data-daisy-kit-file-preview-inline-content]').contains(frame)).toBe(true);
        expect(element.querySelector('[data-daisy-kit-file-preview-modal]').open).toBe(false);
        expect(frameToken(element)).not.toBe(previousToken);

        frameMessage(element, { renderId: null, token: previousToken, type: 'rendered' });
        expect(controller.getState().status).toBe('loading');
        expect(controller.open()).toBe(true);
        expect(controller.close()).toBe(true);
    });

    it.each([
        ['invalid-configuration', '{'],
        ['invalid-configuration', '[]'],
        ['missing-configuration', null],
    ])('reports %s on reload without destroying the current runtime (%s)', async (code, configuration) => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root();
        const controller = mount(element);
        const token = frameToken(element);
        const state = controller.getState();
        const signal = fetch.mock.calls[0][1].signal;
        const errors = [];
        const onError = (event) => errors.push(event.detail);
        element.parentElement.addEventListener('daisy-kit:file-preview:error', onError);
        const configurationNode = element.querySelector('[data-daisy-kit-config]');

        if (configuration === null) configurationNode.remove();
        else configurationNode.textContent = configuration;

        expect(await controller.reload()).toBe(false);
        element.parentElement.removeEventListener('daisy-kit:file-preview:error', onError);
        expect(errors).toEqual([{
            ...state,
            code,
            message: 'This module configuration is invalid.',
        }]);
        expect(controller.getState()).toEqual(state);
        expect(signal.aborted).toBe(false);
        expect(getInstance(element)).toBe(controller);
        expect(frameToken(element)).toBe(token);
        expect(fetch).toHaveBeenCalledOnce();
        expect(controller.open()).toBe(true);
    });

    it('returns false for unavailable commands and keeps disposed facades inert', async () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root();
        const controller = mount(element);

        expect(controller.close()).toBe(false);
        expect(controller.fit()).toBe(false);
        expect(unmount(element)).toBe(true);
        expect(unmount(element)).toBe(false);
        expect(controller.open()).toBe(false);
        expect(controller.close()).toBe(false);
        expect(controller.setExpanded(true)).toBe(false);
        expect(controller.setZoom(150)).toBe(false);
        expect(controller.zoomIn()).toBe(false);
        expect(controller.zoomOut()).toBe(false);
        expect(controller.fit()).toBe(false);
        expect(controller.retry()).toBe(false);
        expect(await controller.reload()).toBe(false);
        expect(fetch).toHaveBeenCalledOnce();

        const unsupported = mount(root({ canPreview: false }));

        expect(unsupported.open()).toBe(false);
        expect(unsupported.retry()).toBe(false);
    });

    it('keeps facade state and legacy events in sync with modal and native close commands', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root();
        const controller = mount(element);
        const previews = [];
        const layouts = [];
        element.addEventListener('daisy-kit:file-preview:preview', (event) => previews.push(event.detail.open));
        element.addEventListener('daisy-kit:file-preview:layout', (event) => layouts.push(event.detail.expanded));

        expect(controller.setExpanded(true)).toBe(true);
        expect(controller.setExpanded(false)).toBe(true);
        expect(controller.open()).toBe(true);
        const modal = element.querySelector('[data-daisy-kit-file-preview-modal]');
        modal.open = false;
        modal.dispatchEvent(new Event('close'));

        expect(controller.getState()).toMatchObject({ expanded: false, isOpen: false, open: false });
        expect(previews).toEqual([true, false, true, false]);
        expect(layouts).toEqual([true, false]);
    });

    it('exposes bounded DOCX zoom controls and events', () => {
        vi.stubGlobal('fetch', vi.fn(() => new Promise(() => {})));
        const element = root({ docxZoom: 195, type: 'docx' });
        const events = [];
        element.addEventListener('daisy-kit:file-preview:zoom', (event) => events.push(event.detail.zoom));
        const controller = mount(element);

        expect(controller.zoomIn()).toBe(true);
        expect(controller.getState().zoom).toBe(200);
        expect(controller.setZoom(10)).toBe(true);
        expect(controller.getState().zoom).toBe(25);
        expect(controller.zoomOut()).toBe(true);
        expect(controller.getState().zoom).toBe(25);
        expect(controller.setZoom(NaN)).toBe(false);
        expect(controller.setZoom('175')).toBe(false);
        expect(events).toEqual([200, 25, 25]);
    });

    it('fits a DOCX through the authenticated frame and reflects its measured zoom', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response(new ArrayBuffer(2), {
            headers: { 'content-type': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' },
            status: 200,
        }))));
        const element = root({
            docxZoom: 125,
            mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            type: 'docx',
        });
        const events = [];
        element.addEventListener('daisy-kit:file-preview:zoom', (event) => events.push(event.detail));
        const controller = mount(element);
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');

        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        frameMessage(element, { type: 'ready' });
        controller.open();
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            type: 'render',
        }), '*', expect.any(Array)));
        expect(controller.fit()).toBe(true);

        expect(postMessage).toHaveBeenLastCalledWith(expect.objectContaining({
            renderId: expect.any(Number),
            type: 'fit',
        }), '*');

        const renderId = postMessage.mock.calls.at(-1)[0].renderId;
        frameMessage(element, { mode: 'fit', renderId, type: 'zoom', zoom: 82 });

        expect(controller.getState().zoom).toBe(82);
        expect(element.dataset.daisyKitZoom).toBe('82');
        expect(element.querySelector('[data-daisy-kit-file-preview-zoom-output]').textContent).toBe('82%');
        expect(events.at(-1)).toEqual(expect.objectContaining({ mode: 'fit', zoom: 82 }));
    });

    it('defers hidden modal rendering until the preview is opened', async () => {
        vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(new Response('Report', {
            headers: { 'content-type': 'text/plain' },
            status: 200,
        }))));
        const element = root();
        const controller = mount(element);
        const frame = element.querySelector('[data-daisy-kit-file-preview-frame]');
        const postMessage = vi.spyOn(frame.contentWindow, 'postMessage');

        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        frameMessage(element, { type: 'ready' });
        await vi.waitFor(() => expect(controller.getState().status).toBe('ready'));

        expect(postMessage).not.toHaveBeenCalled();

        controller.open();
        await vi.waitFor(() => expect(postMessage).toHaveBeenCalledWith(expect.objectContaining({
            type: 'render',
        }), '*', []));
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
