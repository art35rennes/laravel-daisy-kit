const channel = 'daisy-kit:file-preview:frame';
const token = new URLSearchParams(location.hash.slice(1)).get('token');

function validMessage(event) {
    return event.source === window.parent
        && event.data
        && event.data.channel === channel
        && event.data.token === token
        && event.data.type === 'render';
}

window.addEventListener('message', (event) => {
    if (!validMessage(event)) return;

    document.dispatchEvent(new CustomEvent('daisy-kit:file-preview:render', { detail: event.data }));
});

window.addEventListener('load', () => {
    document.documentElement.dataset.daisyKitFilePreviewFrame = 'ready';
    window.parent.postMessage({ channel, token, type: 'ready' }, '*');
}, { once: true });
