import '../css/file-preview-frame.css';
import { renderAsync } from 'docx-preview';

const channel = 'daisy-kit:file-preview:frame';
const output = document.querySelector('[data-daisy-kit-file-preview-output]');
const token = output?.dataset.daisyKitFilePreviewToken;
const allowedThemeTokens = new Set([
    '--color-base-100',
    '--color-base-200',
    '--color-base-content',
    '--radius-box',
]);
let activeRenderId = null;
let docxStyleContainer = null;
let objectUrl = null;

function post(type, renderId = null) {
    window.parent.postMessage({ channel, renderId, token, type }, '*');
}

function validMessage(event) {
    return event.source === window.parent
        && event.data
        && event.data.channel === channel
        && event.data.token === token;
}

function releaseObjectUrl() {
    if (!objectUrl) return;

    URL.revokeObjectURL(objectUrl);
    objectUrl = null;
}

function releaseDocxStyles() {
    docxStyleContainer?.remove();
    docxStyleContainer = null;
}

function applyTheme(theme) {
    if (!theme || typeof theme !== 'object') return;

    for (const [name, value] of Object.entries(theme)) {
        if (!allowedThemeTokens.has(name) || typeof value !== 'string' || value.length > 128) continue;
        if (/url\s*\(/i.test(value)) continue;

        document.documentElement.style.setProperty(name, value);
    }
}

function applyZoom(zoom) {
    const value = Math.max(25, Math.min(Math.round(Number(zoom) || 100), 200));
    const wrappers = [...output.querySelectorAll('.docx-wrapper')];

    [output, ...wrappers].forEach((element) => {
        [...element.classList]
            .filter((className) => className.startsWith('daisy-kit-file-preview-zoom-'))
            .forEach((className) => element.classList.remove(className));
    });
    wrappers.forEach((wrapper) => wrapper.classList.add(`daisy-kit-file-preview-zoom-${value}`));
}

function blobUrl(data, mimeType) {
    releaseObjectUrl();
    objectUrl = URL.createObjectURL(new Blob([data], { type: mimeType }));

    return objectUrl;
}

function renderText(payload) {
    const text = document.createElement('pre');

    text.textContent = payload.truncated ? `${payload.data}\n\n…` : payload.data;
    output.append(text);

    if (payload.truncated) {
        const notice = document.createElement('p');

        notice.className = 'daisy-kit-file-preview-frame__notice';
        notice.textContent = payload.truncatedLabel;
        output.append(notice);
    }
}

function renderImage(payload) {
    const image = document.createElement('img');

    image.alt = payload.name;
    image.src = blobUrl(payload.data, payload.mimeType);
    output.append(image);
}

function renderVideo(payload) {
    const video = document.createElement('video');

    video.controls = true;
    video.preload = 'metadata';
    video.src = blobUrl(payload.data, payload.mimeType);
    output.append(video);
}

function renderAudio(payload) {
    const audio = document.createElement('audio');

    audio.controls = true;
    audio.preload = 'metadata';
    audio.src = blobUrl(payload.data, payload.mimeType);
    output.append(audio);
}

function renderPdf(payload) {
    const pdf = document.createElement('iframe');

    pdf.setAttribute('sandbox', '');
    pdf.title = payload.name;
    pdf.src = blobUrl(payload.data, payload.mimeType);
    output.append(pdf);
}

async function renderDocx(payload) {
    output.dataset.daisyKitDocxView = payload.docxView === 'width' ? 'width' : 'page';
    docxStyleContainer = document.createElement('div');
    docxStyleContainer.hidden = true;
    docxStyleContainer.dataset.daisyKitFilePreviewDocxStyles = '';
    document.body.append(docxStyleContainer);
    await renderAsync(payload.data, output, docxStyleContainer, {
        renderAltChunks: false,
        renderComments: false,
        useBase64URL: true,
    });
    applyZoom(payload.zoom);
}

async function render(payload) {
    releaseObjectUrl();
    releaseDocxStyles();
    output.replaceChildren();
    output.dataset.daisyKitFilePreviewType = payload.type;
    applyTheme(payload.theme);
    applyZoom(payload.zoom);

    if (payload.type === 'text') return renderText(payload);
    if (payload.type === 'image') return renderImage(payload);
    if (payload.type === 'video') return renderVideo(payload);
    if (payload.type === 'audio') return renderAudio(payload);
    if (payload.type === 'pdf') return renderPdf(payload);
    if (payload.type === 'docx') return renderDocx(payload);

    throw new Error('Unsupported preview type.');
}

window.addEventListener('message', async (event) => {
    if (!validMessage(event)) return;

    if (event.data.type === 'zoom' && event.data.renderId === activeRenderId) {
        applyZoom(event.data.zoom);

        return;
    }

    if (event.data.type !== 'render' || !Number.isSafeInteger(event.data.renderId)) return;

    activeRenderId = event.data.renderId;

    try {
        await render(event.data.payload);
        post('rendered', activeRenderId);
    } catch {
        output.replaceChildren();
        post('error', activeRenderId);
    }
});

window.addEventListener('pagehide', () => {
    releaseObjectUrl();
    releaseDocxStyles();
});
queueMicrotask(() => {
    document.documentElement.dataset.daisyKitFilePreviewFrame = 'ready';
    post('ready');
});
