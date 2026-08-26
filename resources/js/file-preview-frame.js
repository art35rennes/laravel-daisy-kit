import { renderAsync } from 'docx-preview';

const channel = 'daisy-kit:file-preview:frame';
const output = document.querySelector('[data-daisy-kit-file-preview-output]');

async function render(payload) {
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
        image.src = URL.createObjectURL(new Blob([payload.data]));
        output.append(image);

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
