import { renderAsync } from 'docx-preview';

import '../css/file-preview.css';
import { createMountable } from './core/mountable.js';

function previewType(configuration) {
    if (['docx', 'image', 'text'].includes(configuration.type)) {
        return configuration.type;
    }

    const path = typeof configuration.src === 'string' ? configuration.src.toLowerCase() : '';

    if (path.endsWith('.docx')) {
        return 'docx';
    }

    if (/\.(avif|gif|jpe?g|png|svg|webp)$/.test(path)) {
        return 'image';
    }

    return 'text';
}

function safeSource(source) {
    if (typeof source !== 'string' || source.length === 0) {
        return null;
    }

    try {
        const url = new URL(source, window.location.href);

        return ['http:', 'https:'].includes(url.protocol) ? url.toString() : null;
    } catch {
        return null;
    }
}

function setVisible(element, visible) {
    if (element) {
        element.hidden = !visible;
    }
}

function showError(root, message) {
    root.dataset.daisyKitState = 'error';
    const status = root.querySelector('[data-daisy-kit-status]');

    if (status) {
        status.hidden = false;
        status.textContent = message;
    }

    root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:error', { bubbles: true, detail: { message } }));
}

function initializeFilePreview(root, configuration) {
    const source = safeSource(configuration.src);
    const loading = root.querySelector('[data-daisy-kit-loading]');
    const empty = root.querySelector('[data-daisy-kit-empty]');
    const image = root.querySelector('[data-daisy-kit-file-preview-image]');
    const text = root.querySelector('[data-daisy-kit-file-preview-text]');
    const docx = root.querySelector('[data-daisy-kit-file-preview-docx]');

    if (!source) {
        setVisible(empty, true);
        root.dataset.daisyKitState = 'empty';
        root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:empty', { bubbles: true }));

        return () => {};
    }

    const abortController = new AbortController();
    let destroyed = false;
    setVisible(loading, true);
    root.dataset.daisyKitState = 'loading';
    root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:loading', { bubbles: true }));

    async function loadPreview() {
        try {
            const response = await fetch(source, { credentials: 'omit', signal: abortController.signal });

            if (!response.ok) {
                throw new Error('The file could not be loaded.');
            }

            if (destroyed) {
                return;
            }

            const type = previewType(configuration);

            if (type === 'image' && image) {
                image.alt = typeof configuration.name === 'string' ? configuration.name : 'Previewed image';
                image.src = source;
                setVisible(image, true);
            }

            if (type === 'text' && text) {
                const content = await response.text();

                if (destroyed) {
                    return;
                }

                text.textContent = content;
                setVisible(text, true);
            }

            if (type === 'docx' && docx) {
                const document = await response.blob();

                if (destroyed) {
                    return;
                }

                docx.replaceChildren();
                await renderAsync(document, docx, undefined, { renderComments: false });

                if (destroyed) {
                    docx.replaceChildren();

                    return;
                }

                setVisible(docx, true);
            }

            if (destroyed) {
                return;
            }

            setVisible(loading, false);
            root.dataset.daisyKitState = 'ready';
            root.dispatchEvent(new CustomEvent('daisy-kit:file-preview:ready', { bubbles: true, detail: { type } }));
        } catch (error) {
            if (destroyed || (error instanceof DOMException && error.name === 'AbortError')) {
                return;
            }

            showError(root, 'The file preview could not be loaded.');
        }
    }

    void loadPreview();

    return () => {
        destroyed = true;
        abortController.abort();
        image?.removeAttribute('src');
        text?.replaceChildren();
        docx?.replaceChildren();
    };
}

const module = createMountable('file-preview', initializeFilePreview);

export const { mount, mountAll, unmount } = module;
