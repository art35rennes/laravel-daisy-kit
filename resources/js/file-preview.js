import '../css/file-preview.css';
import { readConfiguration, showConfigurationError } from './core/configuration.js';
import { createInstanceIdentifier } from './core/identifiers.js';
import { normalizeConfiguration } from './file-preview/configuration.js';
import { frameChannel, frameDocument, themeTokens } from './file-preview/frame-document.js';
import { createFramePayload, fetchPreview } from './file-preview/transport.js';

const instances = new WeakMap();
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
        staging: root.querySelector('[data-daisy-kit-file-preview-frame-staging]'),
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
            isOpen,
            layout: configuration.layout,
            mimeType: preview?.mimeType ?? configuration.mimeType,
            name: configuration.name,
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

        emit('error', { message });
    }

    function clearError() {
        setVisible(dom.status, false);
        setVisible(dom.retry, false);
    }

    function restoreFrame() {
        if (!(dom.frame instanceof HTMLIFrameElement)) return;

        const target = configuration.previewMode === 'inline' ? dom.inlineHost : dom.staging;

        if (target instanceof HTMLElement && !target.contains(dom.frame)) target.append(dom.frame);
        setVisible(dom.frame, configuration.previewMode === 'inline' && status === 'ready');
    }

    function close() {
        if (!isOpen && !(dom.modal instanceof HTMLDialogElement && dom.modal.open)) return;

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
    }

    function open(trigger = null) {
        if (!configuration.canPreview || !(dom.frame instanceof HTMLIFrameElement)) return;
        if (!(dom.modal instanceof HTMLDialogElement) || !(dom.modalContent instanceof HTMLElement)) return;
        if (isOpen) return;

        returnFocus = trigger instanceof HTMLElement ? trigger : document.activeElement;
        isOpen = true;
        root.dataset.daisyKitPreviewOpen = 'true';
        dom.modalContent.append(dom.frame);
        setVisible(dom.frame, status !== 'error');

        if (!dom.modal.open) {
            if (typeof dom.modal.showModal === 'function') dom.modal.showModal();
            else dom.modal.open = true;
        }

        const focusTarget = dom.closeButtons.find((button) => button instanceof HTMLElement);
        focusTarget?.focus({ preventScroll: true });
        emit('open');
    }

    function setZoom(value) {
        const parsed = Number(value);

        if (!Number.isFinite(parsed)) return configuration.zoom;

        configuration.zoom = Math.max(25, Math.min(Math.round(parsed), 200));
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

        return configuration.zoom;
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
            await sendPayload();
        } catch (error) {
            if (destroyed || (error instanceof DOMException && error.name === 'AbortError')) return;

            showError(error instanceof Error ? error.message : configuration.labels.error);
        }
    }

    function retry() {
        if (!configuration.canPreview || !configuration.source) return;

        emit('retry');
        void load();
    }

    function onMessage(event) {
        if (destroyed || !(dom.frame instanceof HTMLIFrameElement)) return;
        if (event.source !== dom.frame.contentWindow || !event.data) return;
        if (event.data.channel !== frameChannel || event.data.token !== token) return;

        if (event.data.type === 'ready') {
            frameReady = true;
            window.clearTimeout(frameReadyTimer);
            void sendPayload();

            return;
        }

        if (event.data.renderId !== activeRenderId) return;

        if (event.data.type === 'rendered') {
            updateStatus('ready');
            setVisible(dom.loading, false);
            setVisible(dom.frame, isOpen || configuration.previewMode === 'inline');
            emit('ready', { mimeType: preview?.mimeType });
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

        if (dom.modal instanceof HTMLDialogElement && dom.modal.open) dom.modal.close();
        if (dom.frame instanceof HTMLIFrameElement) dom.frame.removeAttribute('srcdoc');
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        preview = null;
    }

    function onOpenClick(event) {
        open(event.target instanceof HTMLElement ? event.target : event.currentTarget);
    }

    function onZoomClick(event) {
        const amount = event.currentTarget.dataset.daisyKitFilePreviewZoom === 'in' ? 10 : -10;

        setZoom(configuration.zoom + amount);
    }

    const facade = Object.freeze({
        close,
        destroy,
        getState: snapshot,
        open,
        retry,
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

export function mount(root) {
    if (!(root instanceof Element)) throw new TypeError('Daisy Kit modules mount Element roots only.');
    if (instances.has(root)) return instances.get(root);

    const { error, value } = readConfiguration(root);

    if (error) {
        showConfigurationError(root);

        return null;
    }

    const instance = initialize(root, value);

    instances.set(root, instance);
    root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:mounted', { bubbles: true }));

    return instance;
}

export function mountAll(scope = document) {
    return [...scope.querySelectorAll('[data-daisy-kit-module="file-preview"]')].map(mount);
}

export function getInstance(root) {
    return instances.get(root) ?? null;
}

export function unmount(root) {
    const instance = instances.get(root);

    if (!instance) return;

    instance.destroy();
    instances.delete(root);
    delete root.dataset.daisyKitState;
    root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:unmounted', { bubbles: true }));
}
