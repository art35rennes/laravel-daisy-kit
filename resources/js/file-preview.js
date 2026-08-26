import '../css/file-preview.css';
import { createMountable } from './core/mountable.js';

const defaultMaximumBytes = 5 * 1024 * 1024;
const absoluteMaximumBytes = 10 * 1024 * 1024;
const frameReadyTimeout = 10_000;
const frameChannel = 'daisy-kit:file-preview:frame';
const supportedTypes = new Set(['docx', 'image', 'text']);
const frameAssets = [
    new URL('./file-preview-frame-bootstrap.js', import.meta.url),
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
    if (/\.(avif|gif|jpe?g|png|svg|webp)$/.test(path)) return 'image';

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

    return mime === 'text/plain';
}

function frameDocument(token) {
    const scriptSources = [...new Set(frameAssets.map((asset) => asset.origin))].join(' ');
    const [bootstrap, renderer] = frameAssets.map((asset) => asset.href);

    return `<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="default-src 'none'; base-uri 'none'; connect-src 'none'; form-action 'none'; frame-ancestors 'self'; img-src data: blob:; font-src data: blob:; media-src 'none'; object-src 'none'; script-src-attr 'none'; script-src-elem ${scriptSources}; style-src 'unsafe-inline'">
    <title>File preview</title>
</head>
<body>
    <main data-daisy-kit-file-preview-output data-daisy-kit-file-preview-token="${token}"></main>
    <script src="${bootstrap}"></script>
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

    emit(root, 'error', { message });
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
    const frameToken = crypto.randomUUID().replaceAll('-', '');
    let destroyed = false;
    let frameReady = false;
    let payloadSent = false;
    let rendered = false;
    let payload = null;
    let renderTimeout = null;
    const readyTimeout = window.setTimeout(() => {
        if (destroyed || frameReady) return;

        setVisible(loading, false);
        setVisible(frame, false);
        showError(root, 'The file preview frame did not become ready.');
    }, frameReadyTimeout);
    setVisible(loading, true);
    setVisible(frame, true);
    root.dataset.daisyKitState = 'loading';
    emit(root, 'loading');

    function sendPayload() {
        if (destroyed || !frameReady || payloadSent || !payload || !frame.contentWindow) return;

        payloadSent = true;
        renderTimeout = window.setTimeout(() => {
            if (destroyed || rendered) return;

            setVisible(loading, false);
            setVisible(frame, false);
            showError(root, 'The file preview frame did not render the file.');
        }, frameReadyTimeout);
        frame.contentWindow.postMessage({ channel: frameChannel, payload, token: frameToken, type: 'render' }, '*', payload.data instanceof ArrayBuffer ? [payload.data] : []);
    }

    function onMessage(event) {
        if (destroyed || event.source !== frame.contentWindow || !event.data || event.data.channel !== frameChannel || event.data.token !== frameToken) return;

        if (event.data.type === 'ready' && !frameReady) {
            frameReady = true;
            window.clearTimeout(readyTimeout);
            sendPayload();
        }

        if (event.data.type === 'rendered' && payloadSent && !rendered) {
            rendered = true;
            window.clearTimeout(renderTimeout);
            setVisible(loading, false);
            root.dataset.daisyKitState = 'ready';
            emit(root, 'ready', { type });
        }

        if (event.data.type === 'error') {
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

            payload = await framePayload(type, blob, typeof configuration.name === 'string' ? configuration.name : 'File preview');
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
        frame.removeAttribute('srcdoc');
        setVisible(frame, false);
        payload = null;
    };
}

const module = createMountable('file-preview', initializeFilePreview);

export const { mount, mountAll, unmount } = module;
