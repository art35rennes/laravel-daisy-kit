const previewTypes = new Set(['audio', 'docx', 'image', 'pdf', 'text', 'video']);
const defaultMaximumBytes = 10 * 1024 * 1024;
const absoluteMaximumBytes = 50 * 1024 * 1024;
const defaultMaximumTextBytes = 64 * 1024;
const absoluteMaximumTextBytes = 1024 * 1024;

function boundedInteger(value, fallback, maximum) {
    if (!Number.isSafeInteger(value) || value < 1) return fallback;

    return Math.min(value, maximum);
}

export function safeSource(source) {
    if (typeof source !== 'string' || source.trim() === '') return null;

    try {
        const url = new URL(source, window.location.href);

        return ['http:', 'https:'].includes(url.protocol) ? url.toString() : null;
    } catch {
        return null;
    }
}

export function normalizeConfiguration(configuration) {
    const previewMode = ['inline', 'modal', 'download'].includes(configuration.previewMode)
        ? configuration.previewMode
        : 'modal';
    const type = previewTypes.has(configuration.type) ? configuration.type : null;

    return {
        canDownload: configuration.canDownload === true,
        canPreview: configuration.canPreview !== false && type !== null && previewMode !== 'download',
        docxView: configuration.docxView === 'width' ? 'width' : 'page',
        downloadUrl: safeSource(configuration.downloadUrl),
        labels: {
            error: configuration.labels?.error || 'The file could not be previewed.',
            frameNotReady: configuration.labels?.frameNotReady || 'The preview frame is unavailable.',
            invalidType: configuration.labels?.invalidType || 'The server returned an unexpected file type.',
            tooLarge: configuration.labels?.tooLarge || 'This file is too large to preview.',
        },
        layout: ['card', 'compact-list', 'action-only'].includes(configuration.layout)
            ? configuration.layout
            : 'card',
        maxPreviewBytes: boundedInteger(configuration.maxPreviewBytes, defaultMaximumBytes, absoluteMaximumBytes),
        maxTextPreviewBytes: boundedInteger(
            configuration.maxTextPreviewBytes,
            defaultMaximumTextBytes,
            absoluteMaximumTextBytes,
        ),
        mimeType: typeof configuration.mimeType === 'string' ? configuration.mimeType.toLowerCase().trim() : null,
        name: typeof configuration.name === 'string' && configuration.name.trim() !== ''
            ? configuration.name
            : 'File preview',
        previewMode,
        source: safeSource(configuration.previewUrl ?? configuration.url),
        type,
        url: safeSource(configuration.url),
        zoom: Math.max(25, Math.min(Math.round(Number(configuration.docxZoom) || 100), 200)),
    };
}

export function validContentType(type, contentType) {
    const mimeType = contentType.toLowerCase().split(';', 1)[0].trim();

    if (type === 'audio') return mimeType.startsWith('audio/');
    if (type === 'docx') {
        return mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }
    if (type === 'image') return mimeType.startsWith('image/');
    if (type === 'pdf') return mimeType === 'application/pdf';
    if (type === 'text') {
        return mimeType.startsWith('text/') || ['application/json', 'application/xml'].includes(mimeType);
    }
    if (type === 'video') return mimeType.startsWith('video/');

    return false;
}
