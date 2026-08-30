import '../css/file-preview.css';
import { createInstanceIdentifier } from './core/identifiers.js';
import { createMountable } from './core/mountable.js';

const defaultMaximumBytes = 5 * 1024 * 1024;
const absoluteMaximumBytes = 10 * 1024 * 1024;
const frameReadyTimeout = 10_000;
const frameChannel = 'daisy-kit:file-preview:frame';
const supportedTypes = new Set(['docx', 'image', 'pdf', 'text', 'video']);
const publicFacades = new WeakMap();
const frameAssets = [
    new URL('../../.tmp/file-preview-frame/file-preview-frame.js', import.meta.url),
];

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:file-preview:${name}`, { bubbles: true, detail }));
}

function previewType(configuration) {
    if (typeof configuration.type === 'string' && configuration.type !== '') {
        return supportedTypes.has(configuration.type) ? configuration.type : null;
    }

    const path = typeof configuration.src === 'string' ? configuration.src.toLowerCase() : '';

    if (path.endsWith('.docx')) return 'docx';
    if (path.endsWith('.pdf')) return 'pdf';
    if (/\.(avif|gif|jpe?g|png|svg|webp)$/.test(path)) return 'image';
    if (/\.(m4v|mov|mp4|webm)$/.test(path)) return 'video';

    return 'text';
}

function safeSource(source) {
    if (typeof source !== 'string' || source.length === 0) return null;

    try {
        const url = new URL(source, window.location.href);

        return ['http:', 'https:'].includes(url.protocol) ? url.toString() : null;
    } catch {
        return null;
    }
}

function maximumBytes(configuration) {
    if (!Number.isSafeInteger(configuration.maxBytes) || configuration.maxBytes < 1) return defaultMaximumBytes;

    return Math.min(configuration.maxBytes, absoluteMaximumBytes);
}

function validContentType(type, contentType) {
    const mime = contentType.toLowerCase().split(';', 1)[0].trim();

    if (type === 'docx') return mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    if (type === 'image') return mime.startsWith('image/');
    if (type === 'pdf') return mime === 'application/pdf';
    if (type === 'video') return mime.startsWith('video/');

    return mime === 'text/plain';
}

function frameDocument(token) {
    const scriptSources = [...new Set(frameAssets.map((asset) => asset.origin))].join(' ');
    const [renderer] = frameAssets.map((asset) => asset.href);

    return `<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="default-src 'none'; base-uri 'none'; connect-src 'none'; form-action 'none'; frame-src blob:; img-src data: blob:; font-src data: blob:; media-src blob:; object-src 'none'; script-src-attr 'none'; script-src-elem ${scriptSources}; style-src 'unsafe-inline'">
    <title>File preview</title>
</head>
<body>
    <main data-daisy-kit-file-preview-output data-daisy-kit-file-preview-token="${token}"></main>
    <script src="${renderer}"></script>
</body>
</html>`;
}

function setVisible(element, visible) {
    if (element) element.hidden = !visible;
}

function showError(root, message) {
    root.dataset.daisyKitState = 'error';
    const status = root.querySelector('[data-daisy-kit-status]');

    if (status) {
        status.hidden = false;
        status.textContent = message;
    }

    emit(root, 'error', { code: 'preview-failed', message });
}

function showMetadata(root, blob, configuration, type) {
    const metadata = root.querySelector('[data-daisy-kit-file-preview-metadata]');

    if (!metadata) return;

    const name = root.querySelector('[data-daisy-kit-file-preview-name]');
    const size = root.querySelector('[data-daisy-kit-file-preview-size]');
    const previewType = root.querySelector('[data-daisy-kit-file-preview-type]');
    const filename = typeof configuration.name === 'string' && configuration.name.length > 0 ? configuration.name : 'File preview';

    if (name) name.textContent = filename;
    if (size) size.textContent = `${blob.size.toLocaleString()} bytes`;
    if (previewType) previewType.textContent = type;
    metadata.hidden = false;
}

function exposeActions(root, blob, configuration) {
    const actions = root.querySelector('[data-daisy-kit-file-preview-actions]');
    const download = root.querySelector('[data-daisy-kit-file-preview-download]');
    const open = root.querySelector('[data-daisy-kit-file-preview-open]');

    if (!actions || !download || !open) return null;

    const url = URL.createObjectURL(blob);
    const filename = typeof configuration.name === 'string' && configuration.name.length > 0 ? configuration.name : 'file-preview';

    download.download = filename;
    download.href = url;
    download.hidden = false;
    open.href = url;
    open.hidden = false;
    actions.hidden = false;

    return url;
}

async function fetchBlob(source, type, limit, abortController) {
    const response = await fetch(source, { credentials: 'omit', redirect: 'error', signal: abortController.signal });

    if (!response.ok || !validContentType(type, response.headers.get('content-type') ?? '')) {
        throw new Error('The selected file is not an allowed preview type.');
    }

    const contentLength = Number(response.headers.get('content-length') ?? '0');

    if (Number.isSafeInteger(contentLength) && contentLength > limit) throw new Error('The selected file is too large to preview.');

    const blob = await response.blob();

    if (blob.size > limit) throw new Error('The selected file is too large to preview.');

    return blob;
}

async function framePayload(type, blob, name) {
    if (type === 'text') return { data: await blob.text(), name, type };

    return { data: await blob.arrayBuffer(), name, type };
}

function initializeFilePreview(root, configuration) {
    const source = safeSource(configuration.src);
    const type = previewType(configuration);
    const loading = root.querySelector('[data-daisy-kit-loading]');
    const empty = root.querySelector('[data-daisy-kit-empty]');
    const frame = root.querySelector('[data-daisy-kit-file-preview-frame]');
    const layout = root.querySelector('[data-daisy-kit-file-preview-layout]');
    const modal = root.querySelector('[data-daisy-kit-file-preview-modal]');
    const content = root.querySelector('[data-daisy-kit-content]');
    const notice = root.querySelector('[data-daisy-kit-file-preview-notice]');
    const openPreview = root.querySelector('[data-daisy-kit-file-preview-open-preview]');
    const closePreview = root.querySelector('[data-daisy-kit-file-preview-close-preview]');
    const zoomControls = [...root.querySelectorAll('[data-daisy-kit-file-preview-zoom]')];

    if (!source) {
        setVisible(empty, true);
        root.dataset.daisyKitState = 'empty';
        emit(root, 'empty');

        return () => {};
    }

    if (!type || !(frame instanceof HTMLIFrameElement)) {
        showError(root, 'The selected file type is unsupported.');

        return () => {};
    }

    const abortController = new AbortController();
    const requestedLayout = typeof configuration.layout === 'string' ? configuration.layout : 'standard';
    const previewLayout = ['action-only', 'card', 'modal'].includes(requestedLayout) ? requestedLayout : 'standard';
    const frameToken = createInstanceIdentifier('daisy-kit-file-preview');
    let destroyed = false;
    let frameReady = false;
    let payloadSent = false;
    let rendered = false;
    let payloadBlob = null;
    let actionUrl = null;
    let renderTimeout = null;
    let handshakeId = 0;
    let renderId = 0;
    let activeRenderId = null;
    let modalCloseHandled = false;
    const rendersInline = previewLayout === 'standard' || previewLayout === 'card';
    let modalOpen = false;
    let previewOpen = rendersInline;
    const readyTimeout = window.setTimeout(() => {
        if (destroyed || frameReady) return;

        setVisible(loading, false);
        setVisible(frame, false);
        showError(root, 'The file preview frame did not become ready.');
    }, frameReadyTimeout);
    setVisible(loading, true);
    setVisible(frame, rendersInline);
    root.dataset.daisyKitState = 'loading';
    emit(root, 'loading');

    if (typeof configuration.notice === 'string' && configuration.notice.trim() !== '') {
        notice.textContent = configuration.notice;
        setVisible(notice, true);
    }
    const restoreInlinePreview = (restoreFocus = true) => {
        modalOpen = false;
        previewOpen = rendersInline;
        root.dataset.daisyKitPreviewOpen = String(previewOpen);

        if (rendersInline && modal instanceof HTMLDialogElement && modal.contains(frame) && content) {
            content.append(frame);
        }

        setVisible(frame, rendersInline);

        if (restoreFocus && openPreview instanceof HTMLElement) {
            openPreview.focus({ preventScroll: true });
        }
    };
    const closePreviewModal = () => {
        if (!(modal instanceof HTMLDialogElement)) {
            restoreInlinePreview();
            emit(root, 'preview', { open: false });

            return;
        }

        if (modal.open && typeof modal.close === 'function') {
            modalCloseHandled = true;
            restoreInlinePreview(false);
            emit(root, 'preview', { open: false });
            modal.close();
            if (openPreview instanceof HTMLElement) openPreview.focus({ preventScroll: true });

            return;
        }

        modal.open = false;
        restoreInlinePreview();
        emit(root, 'preview', { open: false });
    };
    const openPreviewModal = () => {
        modalCloseHandled = false;
        modalOpen = true;
        previewOpen = true;
        root.dataset.daisyKitPreviewOpen = 'true';
        setVisible(frame, true);

        if (!(modal instanceof HTMLDialogElement)) return;

        if (!modal.contains(frame)) modal.append(frame);

        if (!modal.open) {
            if (typeof modal.showModal === 'function') modal.showModal();
            else modal.open = true;
        }

        (closePreview instanceof HTMLElement ? closePreview : modal).focus({ preventScroll: true });
    };
    const onModalClose = () => {
        if (modalCloseHandled) return;

        restoreInlinePreview();
        emit(root, 'preview', { open: false });
    };
    const onPreviewClick = () => {
        openPreviewModal();
        emit(root, 'preview', { open: true });
    };
    const onClosePreview = () => {
        closePreviewModal();
    };
    const onLayoutClick = () => {
        const expanded = root.dataset.daisyKitLayout !== 'expanded';

        root.dataset.daisyKitLayout = expanded ? 'expanded' : 'standard';
        layout?.setAttribute('aria-pressed', String(expanded));
        emit(root, 'layout', { expanded, layout: root.dataset.daisyKitLayout });
    };
    const onZoomClick = (event) => {
        const direction = event.currentTarget.dataset.daisyKitFilePreviewZoom;
        const current = Number(root.dataset.daisyKitZoom ?? '100');
        const zoom = Math.max(50, Math.min(200, current + (direction === 'in' ? 25 : -25)));

        root.dataset.daisyKitZoom = String(zoom);
        emit(root, 'zoom', { zoom });
    };
    root.dataset.daisyKitLayout = requestedLayout === 'expanded' ? 'expanded' : previewLayout;
    root.dataset.daisyKitZoom = '100';
    root.dataset.daisyKitPreviewOpen = String(previewOpen);
    layout?.setAttribute('aria-pressed', String(root.dataset.daisyKitLayout === 'expanded'));
    layout?.addEventListener('click', onLayoutClick);
    openPreview?.addEventListener('click', onPreviewClick);
    closePreview?.addEventListener('click', onClosePreview);
    modal?.addEventListener('close', onModalClose);
    zoomControls.forEach((control) => control.addEventListener('click', onZoomClick));

    function sendPayload() {
        if (destroyed || !frameReady || !payloadBlob || !frame.contentWindow) return;

        const currentHandshakeId = handshakeId;
        const currentRenderId = ++renderId;

        void framePayload(type, payloadBlob, typeof configuration.name === 'string' ? configuration.name : 'File preview')
            .then((payload) => {
                if (destroyed || !frameReady || currentHandshakeId !== handshakeId || !frame.contentWindow) return;

                payloadSent = true;
                rendered = false;
                activeRenderId = currentRenderId;
                window.clearTimeout(renderTimeout);
                renderTimeout = window.setTimeout(() => {
                    if (destroyed || rendered || activeRenderId !== currentRenderId) return;

                    setVisible(loading, false);
                    setVisible(frame, false);
                    showError(root, 'The file preview frame did not render the file.');
                }, frameReadyTimeout);
                frame.contentWindow.postMessage({
                    channel: frameChannel,
                    payload,
                    renderId: currentRenderId,
                    token: frameToken,
                    type: 'render',
                }, '*', payload.data instanceof ArrayBuffer ? [payload.data] : []);
            })
            .catch(() => {
                if (!destroyed && currentHandshakeId === handshakeId) {
                    setVisible(loading, false);
                    setVisible(frame, false);
                    showError(root, 'The file preview could not be prepared.');
                }
            });
    }

    function onMessage(event) {
        if (destroyed || event.source !== frame.contentWindow || !event.data || event.data.channel !== frameChannel || event.data.token !== frameToken) return;

        if (event.data.type === 'ready') {
            frameReady = true;
            handshakeId += 1;
            payloadSent = false;
            rendered = false;
            activeRenderId = null;
            window.clearTimeout(readyTimeout);
            window.clearTimeout(renderTimeout);
            sendPayload();
        }

        if (event.data.type === 'rendered' && payloadSent && !rendered && event.data.renderId === activeRenderId) {
            rendered = true;
            window.clearTimeout(renderTimeout);
            setVisible(loading, false);
            if (!modalOpen) setVisible(frame, rendersInline);
            root.dataset.daisyKitState = 'ready';
            emit(root, 'ready', { type });
        }

        if (event.data.type === 'error' && event.data.renderId === activeRenderId) {
            window.clearTimeout(readyTimeout);
            window.clearTimeout(renderTimeout);
            setVisible(loading, false);
            setVisible(frame, false);
            showError(root, 'The file preview could not be rendered.');
        }
    }

    window.addEventListener('message', onMessage);
    frame.srcdoc = frameDocument(frameToken);

    void (async () => {
        try {
            const blob = await fetchBlob(source, type, maximumBytes(configuration), abortController);

            if (destroyed) return;

            showMetadata(root, blob, configuration, type);
            actionUrl = exposeActions(root, blob, configuration);
            payloadBlob = blob;
            sendPayload();
        } catch (error) {
            if (!destroyed && !(error instanceof DOMException && error.name === 'AbortError')) {
                window.clearTimeout(readyTimeout);
                setVisible(loading, false);
                setVisible(frame, false);
                showError(root, error instanceof Error ? error.message : 'The file preview could not be loaded.');
            }
        }
    })();

    return () => {
        destroyed = true;
        abortController.abort();
        window.clearTimeout(readyTimeout);
        window.clearTimeout(renderTimeout);
        window.removeEventListener('message', onMessage);
        layout?.removeEventListener('click', onLayoutClick);
        openPreview?.removeEventListener('click', onPreviewClick);
        closePreview?.removeEventListener('click', onClosePreview);
        modal?.removeEventListener('close', onModalClose);
        zoomControls.forEach((control) => control.removeEventListener('click', onZoomClick));
        if (modal instanceof HTMLDialogElement && modal.contains(frame)) {
            if (typeof modal.close === 'function') modal.close();
            else modal.open = false;
            content?.append(frame);
        }
        frame.removeAttribute('srcdoc');
        setVisible(frame, false);
        if (actionUrl) URL.revokeObjectURL(actionUrl);
        payloadBlob = null;
    };
}

const module = createMountable('file-preview', initializeFilePreview);

function configuration(root) {
    const node = root.querySelector('[data-daisy-kit-config]');

    if (!(node instanceof HTMLScriptElement)) {
        return {};
    }

    try {
        const value = JSON.parse(node.textContent ?? '');

        return value && typeof value === 'object' && !Array.isArray(value) ? value : {};
    } catch {
        return {};
    }
}

function createFacade(root) {
    return {
        close() {
            const modal = root.querySelector('[data-daisy-kit-file-preview-modal]');
            const control = root.querySelector('[data-daisy-kit-file-preview-close-preview]');

            if (!(modal instanceof HTMLDialogElement) || !modal.open || !(control instanceof HTMLButtonElement)) {
                return false;
            }

            control.click();

            return true;
        },
        getState() {
            const currentConfiguration = configuration(root);

            return {
                expanded: root.dataset.daisyKitLayout === 'expanded',
                layout: root.dataset.daisyKitLayout ?? null,
                open: root.dataset.daisyKitPreviewOpen === 'true',
                status: root.dataset.daisyKitState ?? null,
                type: previewType(currentConfiguration),
                zoom: Number(root.dataset.daisyKitZoom ?? '100'),
            };
        },
        open() {
            const control = root.querySelector('[data-daisy-kit-file-preview-open-preview]');

            if (!(control instanceof HTMLButtonElement)) {
                return false;
            }

            control.click();

            return true;
        },
        async reload() {
            module.unmount(root);

            return Boolean(module.mount(root));
        },
        setExpanded(expanded) {
            if (typeof expanded !== 'boolean' || (root.dataset.daisyKitLayout === 'expanded') === expanded) {
                return false;
            }

            const control = root.querySelector('[data-daisy-kit-file-preview-layout]');

            if (!(control instanceof HTMLButtonElement)) {
                return false;
            }

            control.click();

            return (root.dataset.daisyKitLayout === 'expanded') === expanded;
        },
        setZoom(zoom) {
            if (!Number.isFinite(zoom) || zoom < 50 || zoom > 200) {
                return false;
            }

            root.dataset.daisyKitZoom = String(zoom);
            emit(root, 'zoom', { zoom });

            return true;
        },
    };
}

export function mount(root) {
    if (!module.mount(root)) {
        return null;
    }

    if (!publicFacades.has(root)) {
        publicFacades.set(root, createFacade(root));
    }

    return publicFacades.get(root);
}

export function mountAll(scope = document) {
    return [...scope.querySelectorAll('[data-daisy-kit-module="file-preview"]')].map(mount);
}

export function unmount(root) {
    const unmounted = module.unmount(root);
    publicFacades.delete(root);

    return unmounted;
}

export function getInstance(root) {
    return module.getInstance(root) ? (publicFacades.get(root) ?? null) : null;
}
