import { renderAsync } from 'docx-preview';

const channel = 'daisy-kit:file-preview:frame';
const output = document.querySelector('[data-daisy-kit-file-preview-output]');
const token = output?.dataset.daisyKitFilePreviewToken;
let ready = false;

function announceReady() {
    if (ready) return;

    ready = true;
    document.documentElement.dataset.daisyKitFilePreviewFrame = 'ready';
    window.parent.postMessage({ channel, token, type: 'ready' }, '*');
}

function validMessage(event) {
    return event.source === window.parent
        && event.data
        && event.data.channel === channel
        && event.data.token === token
        && event.data.type === 'render';
}
let objectUrl = null;

async function render(payload) {
    if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
    }
    output.replaceChildren();

    if (payload.type === 'text') {
        const text = document.createElement('pre');
        text.textContent = payload.data;
        output.append(text);

        return;
    }

    if (payload.type === 'image') {
        const image = document.createElement('img');
        image.alt = payload.name;
        objectUrl = URL.createObjectURL(new Blob([payload.data]));
        image.src = objectUrl;
        output.append(image);

        return;
    }

    if (payload.type === 'video') {
        const video = document.createElement('video');
        video.controls = true;
        video.preload = 'metadata';
        objectUrl = URL.createObjectURL(new Blob([payload.data]));
        video.src = objectUrl;
        output.append(video);

        return;
    }

    if (payload.type === 'pdf') {
        const pdf = document.createElement('iframe');
        pdf.setAttribute('sandbox', '');
        pdf.title = payload.name;
        objectUrl = URL.createObjectURL(new Blob([payload.data], { type: 'application/pdf' }));
        pdf.src = objectUrl;
        output.append(pdf);

        return;
    }

    await renderAsync(payload.data, output, document.head, {
        renderAltChunks: false,
        renderComments: false,
        useBase64URL: true,
    });
}

window.addEventListener('message', (event) => {
    if (!validMessage(event)) return;

    document.dispatchEvent(new CustomEvent('daisy-kit:file-preview:render', { detail: event.data }));
});

document.addEventListener('daisy-kit:file-preview:render', async (event) => {
    const message = event.detail;

    try {
        await render(message.payload);
        window.parent.postMessage({ channel, token: message.token, type: 'rendered' }, '*');
    } catch {
        output.replaceChildren();
        window.parent.postMessage({ channel, token: message.token, type: 'error' }, '*');
    }
});

queueMicrotask(announceReady);
