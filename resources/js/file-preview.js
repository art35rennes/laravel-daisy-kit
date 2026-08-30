import '../css/file-preview.css';
import { readConfiguration } from './core/configuration.js';
import { createInstanceIdentifier } from './core/identifiers.js';
import { createMountable } from './core/mountable.js';
import { normalizeConfiguration } from './file-preview/configuration.js';
import { frameChannel, frameDocument, themeTokens } from './file-preview/frame-document.js';
import { createFramePayload, fetchPreview } from './file-preview/transport.js';

const frameReadyTimeout = 10_000;

function setVisible(element, visible) {
    if (element) element.hidden = !visible;
}

function elements(root) {
    return {
        closeButtons: [...root.querySelectorAll('[data-daisy-kit-file-preview-close-preview]')],
        downloads: [...root.querySelectorAll('[data-daisy-kit-file-preview-download]')],
        empty: root.querySelector('[data-daisy-kit-empty]'),
        frame: root.querySelector('[data-daisy-kit-file-preview-frame]'),
        inlineHost: root.querySelector('[data-daisy-kit-file-preview-inline-content]'),
        loading: root.querySelector('[data-daisy-kit-loading]'),
        modal: root.querySelector('[data-daisy-kit-file-preview-modal]'),
        modalBox: root.querySelector('[data-daisy-kit-file-preview-modal-box]'),
        modalContent: root.querySelector('[data-daisy-kit-file-preview-modal-content]'),
        open: root.querySelector('[data-daisy-kit-file-preview-open]'),
        openButtons: [
            ...root.querySelectorAll('[data-daisy-kit-file-preview-open-preview]'),
            ...root.querySelectorAll('[data-daisy-kit-file-preview-trigger-slot]'),
        ],
        retry: root.querySelector('[data-daisy-kit-file-preview-retry]'),
        status: root.querySelector('[data-daisy-kit-status]'),
        statusMessage: root.querySelector('[data-daisy-kit-status-message]'),
        zoomControls: [...root.querySelectorAll('[data-daisy-kit-file-preview-zoom]')],
        zoomOutput: root.querySelector('[data-daisy-kit-file-preview-zoom-output]'),
    };
}

function initialize(root, input) {
    const configuration = normalizeConfiguration(input);
    const dom = elements(root);
    const token = createInstanceIdentifier('daisy-kit-file-preview');
    let abortController = null;
    let activeRenderId = null;
    let destroyed = false;
    let frameReady = false;
    let frameReadyTimer = null;
    let isOpen = false;
    let objectUrl = null;
    let preview = null;
    let renderSequence = 0;
    let returnFocus = null;
    let status = 'idle';

    function snapshot() {
        return Object.freeze({
            canDownload: configuration.canDownload,
            canPreview: configuration.canPreview,
            expanded: isOpen,
            isOpen,
            layout: configuration.layout,
            mimeType: preview?.mimeType ?? configuration.mimeType,
            name: configuration.name,
            open: isOpen,
            previewMode: configuration.previewMode,
            status,
            type: configuration.type,
            zoom: configuration.zoom,
        });
    }

    function emit(name, detail = {}) {
        root.dispatchEvent(new CustomEvent(`daisy-kit:file-preview:${name}`, {
            bubbles: true,
            detail: { ...snapshot(), ...detail },
        }));
    }

    function updateStatus(nextStatus) {
        status = nextStatus;
        root.dataset.daisyKitState = nextStatus;
        root.setAttribute('aria-busy', String(nextStatus === 'loading'));
    }

    function showError(message) {
        updateStatus('error');
        setVisible(dom.loading, false);
        setVisible(dom.frame, false);
        setVisible(dom.status, true);
        setVisible(dom.retry, true);

        if (dom.statusMessage) dom.statusMessage.textContent = message;
        else if (dom.status) dom.status.textContent = message;

        emit('error', { code: 'preview-failed', message });
    }

    function clearError() {
        setVisible(dom.status, false);
        setVisible(dom.retry, false);
    }

    function showReady(detail = {}) {
        const wasReady = status === 'ready';

        updateStatus('ready');
        setVisible(dom.loading, false);
        setVisible(dom.frame, isOpen || configuration.previewMode === 'inline');

        if (!wasReady) emit('ready', { mimeType: preview?.mimeType, ...detail });
    }

    function restoreFrame() {
        if (!(dom.frame instanceof HTMLIFrameElement)) return;

        const target = configuration.previewMode === 'inline' ? dom.inlineHost : dom.modalContent;

        if (target instanceof HTMLElement && !target.contains(dom.frame)) target.append(dom.frame);
        setVisible(dom.frame, configuration.previewMode === 'inline' && status === 'ready');
    }

    function close() {
        if (destroyed || (!isOpen && !(dom.modal instanceof HTMLDialogElement && dom.modal.open))) return false;

        isOpen = false;
        root.dataset.daisyKitPreviewOpen = 'false';

        if (dom.modal instanceof HTMLDialogElement && dom.modal.open) {
            if (typeof dom.modal.close === 'function') dom.modal.close();
            else dom.modal.open = false;
        }

        restoreFrame();

        if (returnFocus instanceof HTMLElement && returnFocus.isConnected) {
            returnFocus.focus({ preventScroll: true });
        }

        emit('close');
        emit('preview', { open: false });

        return true;
    }

    function open(trigger = null) {
        if (destroyed || !configuration.canPreview || !(dom.frame instanceof HTMLIFrameElement)) return false;
        if (!(dom.modal instanceof HTMLDialogElement) || !(dom.modalContent instanceof HTMLElement)) return false;
        if (isOpen) return false;

        returnFocus = trigger instanceof HTMLElement ? trigger : document.activeElement;
        isOpen = true;
        root.dataset.daisyKitPreviewOpen = 'true';
        if (!dom.modalContent.contains(dom.frame)) dom.modalContent.append(dom.frame);
        setVisible(dom.frame, status !== 'error');

        if (!dom.modal.open) {
            if (typeof dom.modal.showModal === 'function') dom.modal.showModal();
            else dom.modal.open = true;
        }

        const focusTarget = dom.closeButtons.find((button) => button instanceof HTMLElement);
        focusTarget?.focus({ preventScroll: true });

        if (preview && frameReady && activeRenderId === null) void sendPayload();

        emit('open');
        emit('preview', { open: true });

        return true;
    }

    function setExpanded(expanded) {
        if (typeof expanded !== 'boolean') return false;

        const changed = expanded ? open() : close();

        if (changed) emit('layout', { expanded: isOpen, layout: configuration.layout });

        return changed;
    }

    function setZoom(value) {
        if (destroyed || !Number.isFinite(value)) return false;

        configuration.zoom = Math.max(25, Math.min(Math.round(value), 200));
        root.dataset.daisyKitZoom = String(configuration.zoom);
        if (dom.zoomOutput) dom.zoomOutput.textContent = `${configuration.zoom}%`;

        if (frameReady && dom.frame instanceof HTMLIFrameElement && dom.frame.contentWindow) {
            dom.frame.contentWindow.postMessage({
                channel: frameChannel,
                renderId: activeRenderId,
                token,
                type: 'zoom',
                zoom: configuration.zoom,
            }, '*');
        }

        emit('zoom', { zoom: configuration.zoom });

        return true;
    }

    function fit() {
        if (destroyed || !frameReady || activeRenderId === null || !(dom.frame instanceof HTMLIFrameElement) || !dom.frame.contentWindow) {
            return false;
        }

        dom.frame.contentWindow.postMessage({
            channel: frameChannel,
            renderId: activeRenderId,
            token,
            type: 'fit',
        }, '*');

        return true;
    }

    async function sendPayload() {
        if (destroyed || !frameReady || !preview || !(dom.frame instanceof HTMLIFrameElement)) return;
        if (!dom.frame.contentWindow) return;

        const renderId = ++renderSequence;
        const payload = await createFramePayload(configuration, preview);

        if (destroyed || !frameReady || renderId !== renderSequence || !dom.frame.contentWindow) return;

        activeRenderId = renderId;
        dom.frame.contentWindow.postMessage({
            channel: frameChannel,
            payload: {
                ...payload,
                docxView: configuration.docxView,
                theme: themeTokens(root),
                zoom: configuration.zoom,
            },
            renderId,
            token,
            type: 'render',
        }, '*', payload.data instanceof ArrayBuffer ? [payload.data] : []);
    }

    function exposeValidatedActions(blob) {
        if (objectUrl) URL.revokeObjectURL(objectUrl);

        objectUrl = URL.createObjectURL(blob);

        if (dom.open instanceof HTMLAnchorElement) {
            dom.open.href = objectUrl;
            dom.open.hidden = false;
        }

        dom.downloads.forEach((download) => {
            if (!(download instanceof HTMLAnchorElement) || configuration.downloadUrl) return;

            download.href = objectUrl;
            download.download = configuration.name;
            download.hidden = false;
        });
    }

    async function load() {
        abortController?.abort();
        const request = new AbortController();

        abortController = request;
        activeRenderId = null;
        renderSequence += 1;
        preview = null;
        clearError();
        updateStatus('loading');
        setVisible(dom.empty, false);
        setVisible(dom.loading, true);
        setVisible(dom.frame, false);
        emit('loading');

        try {
            const result = await fetchPreview(configuration, request.signal);

            if (destroyed || request.signal.aborted || abortController !== request) return;

            preview = result;
            exposeValidatedActions(result.blob);

            if (configuration.previewMode === 'inline' || isOpen) await sendPayload();
            else if (frameReady) showReady({ deferred: true });
        } catch (error) {
            if (destroyed || (error instanceof DOMException && error.name === 'AbortError')) return;

            showError(error instanceof Error ? error.message : configuration.labels.error);
        }
    }

    function retry() {
        if (destroyed || !configuration.canPreview || !configuration.source) return false;

        emit('retry');
        void load();

        return true;
    }

    function onMessage(event) {
        if (destroyed || !(dom.frame instanceof HTMLIFrameElement)) return;
        if (event.source !== dom.frame.contentWindow || !event.data) return;
        if (event.data.channel !== frameChannel || event.data.token !== token) return;

        if (event.data.type === 'ready') {
            frameReady = true;
            window.clearTimeout(frameReadyTimer);

            if (preview) {
                if (configuration.previewMode === 'inline' || isOpen) void sendPayload();
                else showReady({ deferred: true });
            }

            return;
        }

        if (event.data.renderId !== activeRenderId) return;

        if (event.data.type === 'zoom') {
            const zoom = Number(event.data.zoom);

            if (!Number.isFinite(zoom)) return;

            configuration.zoom = Math.max(25, Math.min(Math.round(zoom), 200));
            root.dataset.daisyKitZoom = String(configuration.zoom);
            if (dom.zoomOutput) dom.zoomOutput.textContent = `${configuration.zoom}%`;
            emit('zoom', { mode: event.data.mode === 'fit' ? 'fit' : 'manual', zoom: configuration.zoom });

            return;
        }

        if (event.data.type === 'rendered') {
            showReady();
        }

        if (event.data.type === 'error') showError(configuration.labels.error);
    }

    function onNativeClose() {
        if (!isOpen) return;

        isOpen = false;
        root.dataset.daisyKitPreviewOpen = 'false';
        restoreFrame();
        if (returnFocus instanceof HTMLElement && returnFocus.isConnected) returnFocus.focus({ preventScroll: true });
        emit('close');
        emit('preview', { open: false });
    }

    function destroy() {
        destroyed = true;
        abortController?.abort();
        window.clearTimeout(frameReadyTimer);
        window.removeEventListener('message', onMessage);
        dom.modal?.removeEventListener('close', onNativeClose);
        dom.openButtons.forEach((button) => button.removeEventListener('click', onOpenClick));
        dom.closeButtons.forEach((button) => button.removeEventListener('click', close));
        dom.zoomControls.forEach((button) => button.removeEventListener('click', onZoomClick));
        dom.retry?.removeEventListener('click', retry);

        if (dom.modal instanceof HTMLDialogElement && dom.modal.open) {
            if (typeof dom.modal.close === 'function') dom.modal.close();
            else dom.modal.open = false;
        }
        isOpen = false;
        root.dataset.daisyKitPreviewOpen = 'false';
        restoreFrame();
        setVisible(dom.frame, false);
        if (dom.frame instanceof HTMLIFrameElement) dom.frame.removeAttribute('srcdoc');
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        preview = null;
    }

    function onOpenClick(event) {
        open(event.target instanceof HTMLElement ? event.target : event.currentTarget);
    }

    function onZoomClick(event) {
        const action = event.currentTarget.dataset.daisyKitFilePreviewZoom;

        if (action === 'fit') {
            fit();

            return;
        }

        const amount = action === 'in' ? 10 : -10;

        setZoom(configuration.zoom + amount);
    }

    const facade = Object.freeze({
        close,
        destroy,
        fit,
        getState: snapshot,
        open,
        retry,
        setExpanded,
        setZoom,
        zoomIn: () => setZoom(configuration.zoom + 10),
        zoomOut: () => setZoom(configuration.zoom - 10),
    });

    root.dataset.daisyKitPreviewOpen = 'false';
    root.dataset.daisyKitZoom = String(configuration.zoom);
    dom.openButtons.forEach((button) => button.addEventListener('click', onOpenClick));
    dom.closeButtons.forEach((button) => button.addEventListener('click', close));
    dom.zoomControls.forEach((button) => button.addEventListener('click', onZoomClick));
    dom.retry?.addEventListener('click', retry);
    dom.modal?.addEventListener('close', onNativeClose);
    window.addEventListener('message', onMessage);

    if (!configuration.source || !configuration.canPreview || !(dom.frame instanceof HTMLIFrameElement)) {
        updateStatus(configuration.source ? 'unsupported' : 'empty');
        setVisible(dom.empty, !configuration.source);
        emit(configuration.source ? 'ready' : 'empty');

        return facade;
    }

    dom.frame.srcdoc = frameDocument(token);
    frameReadyTimer = window.setTimeout(() => {
        if (!destroyed && !frameReady) showError(configuration.labels.frameNotReady);
    }, frameReadyTimeout);
    void load();

    return facade;
}

function initializeFacade(root, input) {
    let runtime = initialize(root, input);
    let destroyed = false;

    return {
        close: () => runtime.close(),
        destroy() {
            destroyed = true;
            runtime.destroy();
        },
        fit: () => runtime.fit(),
        getState: () => runtime.getState(),
        open: (trigger) => runtime.open(trigger),
        async reload() {
            if (destroyed) return false;

            const { error, value } = readConfiguration(root);

            if (error) {
                root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:error', {
                    bubbles: true,
                    detail: {
                        ...runtime.getState(),
                        code: error,
                        message: 'This module configuration is invalid.',
                    },
                }));

                return false;
            }

            runtime.destroy();
            runtime = initialize(root, value);

            return true;
        },
        retry: () => runtime.retry(),
        setExpanded: (expanded) => runtime.setExpanded(expanded),
        setZoom: (zoom) => runtime.setZoom(zoom),
        zoomIn: () => runtime.zoomIn(),
        zoomOut: () => runtime.zoomOut(),
    };
}

const module = createMountable('file-preview', initializeFacade);

export const { getInstance, mount, mountAll, unmount } = module;
