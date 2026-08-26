import { renderAsync } from 'docx-preview';

const channel = 'daisy-kit:file-preview:frame';
const output = document.querySelector('[data-daisy-kit-file-preview-output]');
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
