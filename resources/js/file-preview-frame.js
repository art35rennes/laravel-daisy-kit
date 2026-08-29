import '../css/file-preview-frame.css';
import { renderAsync } from 'docx-preview';
import { getDocument, GlobalWorkerOptions } from 'pdfjs-dist';
import PdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?worker&inline';

const channel = 'daisy-kit:file-preview:frame';
const output = document.querySelector('[data-daisy-kit-file-preview-output]');
const token = output?.dataset.daisyKitFilePreviewToken;
const allowedThemeTokens = new Set([
    '--color-base-100',
    '--color-base-200',
    '--color-base-content',
    '--radius-box',
]);
const maximumCanvasDimension = 8_192;
const maximumCanvasPixels = 16_777_216;
const maximumPdfPages = 250;
const maximumTotalCanvasPixels = 33_554_432;
let activeRenderId = null;
let docxStyleContainer = null;
let objectUrl = null;
let pdfLoadingTask = null;
let pdfWorker = null;

function post(type, renderId = null, detail = {}) {
    window.parent.postMessage({ channel, renderId, token, type, ...detail }, '*');
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

function releasePdf() {
    if (pdfLoadingTask) void pdfLoadingTask.destroy();

    pdfLoadingTask = null;
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

    return value;
}

function fitDocx() {
    const wrapper = output.querySelector('.docx-wrapper');
    const page = wrapper?.querySelector(':scope > section.docx');

    if (!(wrapper instanceof HTMLElement) || !(page instanceof HTMLElement)) return null;

    applyZoom(100);

    const wrapperStyle = window.getComputedStyle(wrapper);
    const horizontalPadding = (Number.parseFloat(wrapperStyle.paddingInlineStart) || 0)
        + (Number.parseFloat(wrapperStyle.paddingInlineEnd) || 0);
    const availableWidth = Math.max(1, output.clientWidth - horizontalPadding - 2);
    const pageWidth = page.getBoundingClientRect().width;

    if (!Number.isFinite(pageWidth) || pageWidth <= 0) return null;

    return applyZoom((availableWidth / pageWidth) * 100);
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

async function renderPdf(payload) {
    if (!pdfWorker) {
        pdfWorker = new PdfWorker();
        GlobalWorkerOptions.workerPort = pdfWorker;
    }

    const pages = document.createElement('div');

    pages.className = 'daisy-kit-file-preview-frame__pdf-pages';
    pages.setAttribute('aria-label', payload.name);
    output.append(pages);

    pdfLoadingTask = getDocument({
        data: new Uint8Array(payload.data),
        isEvalSupported: false,
        useWorkerFetch: false,
    });
    const pdf = await pdfLoadingTask.promise;
    const pageRendering = [];

    if (pdf.numPages > maximumPdfPages) throw new Error('PDF page limit exceeded.');

    pages.dataset.daisyKitPdfPages = String(pdf.numPages);

    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
        const page = await pdf.getPage(pageNumber);
        const naturalViewport = page.getViewport({ scale: 1 });
        const availableWidth = Math.max(1, output.clientWidth);
        let viewportScale = Math.min(
            2,
            availableWidth / naturalViewport.width,
            maximumCanvasDimension / naturalViewport.height,
        );
        let viewport = page.getViewport({ scale: viewportScale });
        let outputScale = Math.min(window.devicePixelRatio || 1, 2);
        const canvasPixels = viewport.width * outputScale * viewport.height * outputScale;

        const pageCanvasPixels = Math.min(maximumCanvasPixels, maximumTotalCanvasPixels / pdf.numPages);

        if (canvasPixels > pageCanvasPixels) {
            outputScale *= Math.sqrt(pageCanvasPixels / canvasPixels);
        }

        if (viewport.width * outputScale > maximumCanvasDimension) {
            viewportScale *= maximumCanvasDimension / (viewport.width * outputScale);
            viewport = page.getViewport({ scale: viewportScale });
        }

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { alpha: false });

        if (!context) throw new Error('Canvas is unavailable.');

        canvas.dataset.daisyKitPdfPage = String(pageNumber);
        canvas.setAttribute('aria-label', `${payload.name} — ${pageNumber} / ${pdf.numPages}`);
        canvas.width = Math.floor(viewport.width * outputScale);
        canvas.height = Math.floor(viewport.height * outputScale);
        pages.append(canvas);

        pageRendering.push(() => page.render({
            canvas,
            canvasContext: context,
            transform: outputScale === 1 ? null : [outputScale, 0, 0, outputScale, 0, 0],
            viewport,
        }).promise);
    }

    return {
        completion: pageRendering.reduce(
            (previousRendering, renderPage) => previousRendering.then(renderPage),
            Promise.resolve(),
        ),
    };
}

async function renderDocx(payload, renderId) {
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
    const zoom = payload.docxView === 'width' ? fitDocx() : applyZoom(payload.zoom);

    if (zoom !== null && zoom !== payload.zoom) post('zoom', renderId, { mode: 'fit', zoom });
}

async function render(payload, renderId) {
    releaseObjectUrl();
    releaseDocxStyles();
    releasePdf();
    output.replaceChildren();
    output.dataset.daisyKitFilePreviewType = payload.type;
    applyTheme(payload.theme);
    applyZoom(payload.zoom);

    if (payload.type === 'text') return renderText(payload);
    if (payload.type === 'image') return renderImage(payload);
    if (payload.type === 'video') return renderVideo(payload);
    if (payload.type === 'audio') return renderAudio(payload);
    if (payload.type === 'pdf') return renderPdf(payload);
    if (payload.type === 'docx') return renderDocx(payload, renderId);

    throw new Error('Unsupported preview type.');
}

window.addEventListener('message', async (event) => {
    if (!validMessage(event)) return;

    if (event.data.type === 'zoom' && event.data.renderId === activeRenderId) {
        applyZoom(event.data.zoom);

        return;
    }

    if (event.data.type === 'fit' && event.data.renderId === activeRenderId) {
        const zoom = fitDocx();

        if (zoom !== null) post('zoom', activeRenderId, { mode: 'fit', zoom });

        return;
    }

    if (event.data.type !== 'render' || !Number.isSafeInteger(event.data.renderId)) return;

    const renderId = event.data.renderId;

    activeRenderId = renderId;

    try {
        const result = await render(event.data.payload, renderId);

        if (activeRenderId !== renderId) return;

        post('rendered', renderId);

        void result?.completion?.catch(() => {
            if (activeRenderId !== renderId) return;

            output.replaceChildren();
            post('error', renderId);
        });
    } catch {
        if (activeRenderId !== renderId) return;

        output.replaceChildren();
        post('error', renderId);
    }
});

window.addEventListener('pagehide', () => {
    releaseObjectUrl();
    releaseDocxStyles();
    releasePdf();
    pdfWorker?.terminate();
    pdfWorker = null;
});
queueMicrotask(() => {
    document.documentElement.dataset.daisyKitFilePreviewFrame = 'ready';
    post('ready');
});
