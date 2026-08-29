import { validContentType } from './configuration.js';

export async function fetchPreview(configuration, signal) {
    const response = await fetch(configuration.source, {
        credentials: 'omit',
        redirect: 'error',
        signal,
    });

    if (!response.ok) throw new Error(configuration.labels.error);

    const responseMimeType = (response.headers.get('content-type') ?? '').toLowerCase().split(';', 1)[0].trim();
    const mimeType = responseMimeType || configuration.mimeType || '';

    if (!validContentType(configuration.type, mimeType)) {
        throw new Error(configuration.labels.invalidType);
    }

    const contentLength = Number(response.headers.get('content-length') ?? '0');

    if (Number.isSafeInteger(contentLength) && contentLength > configuration.maxPreviewBytes) {
        throw new Error(configuration.labels.tooLarge);
    }

    const blob = await response.blob();

    if (blob.size > configuration.maxPreviewBytes) throw new Error(configuration.labels.tooLarge);

    return { blob, mimeType };
}

export async function createFramePayload(configuration, preview) {
    if (configuration.type === 'text') {
        const truncated = preview.blob.size > configuration.maxTextPreviewBytes;
        const textBlob = truncated ? preview.blob.slice(0, configuration.maxTextPreviewBytes) : preview.blob;

        return {
            data: await textBlob.text(),
            mimeType: preview.mimeType,
            name: configuration.name,
            truncated,
            type: configuration.type,
        };
    }

    return {
        data: await preview.blob.arrayBuffer(),
        mimeType: preview.mimeType,
        name: configuration.name,
        type: configuration.type,
    };
}
