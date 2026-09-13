import { validContentType } from './configuration.js';

async function readBoundedBlob(response, mimeType, maximumBytes, tooLargeMessage) {
    const reader = response.body?.getReader();

    if (!reader) {
        const blob = await response.blob();

        if (blob.size > maximumBytes) throw new Error(tooLargeMessage);

        return blob;
    }

    const chunks = [];
    let receivedBytes = 0;

    while (true) {
        const { done, value } = await reader.read();

        if (done) break;

        receivedBytes += value.byteLength;

        if (receivedBytes > maximumBytes) {
            await reader.cancel();

            throw new Error(tooLargeMessage);
        }

        chunks.push(value);
    }

    return new Blob(chunks, { type: mimeType });
}

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

    const blob = await readBoundedBlob(
        response,
        mimeType,
        configuration.maxPreviewBytes,
        configuration.labels.tooLarge,
    );

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
            truncatedLabel: configuration.labels.truncated,
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
