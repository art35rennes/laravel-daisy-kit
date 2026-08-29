const frameAssets = [
    new URL('../../../.tmp/file-preview-frame/file-preview-frame.js', import.meta.url),
    new URL('../../../.tmp/file-preview-frame/file-preview-frame.css', import.meta.url),
];

export const frameChannel = 'daisy-kit:file-preview:frame';

export function frameDocument(token) {
    const [script, stylesheet] = frameAssets;
    const assetOrigins = [...new Set(frameAssets.map((asset) => asset.origin))].join(' ');

    return `<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="default-src 'none'; base-uri 'none'; connect-src 'none'; form-action 'none'; frame-src blob:; img-src data: blob:; font-src data: blob:; media-src blob:; object-src 'none'; script-src-attr 'none'; script-src-elem ${assetOrigins}; style-src 'unsafe-inline' ${assetOrigins}">
    <title>File preview</title>
    <link rel="stylesheet" href="${stylesheet.href}">
</head>
<body>
    <main data-daisy-kit-file-preview-output data-daisy-kit-file-preview-token="${token}"></main>
    <script src="${script.href}"></script>
</body>
</html>`;
}

export function themeTokens(root) {
    const computed = getComputedStyle(root);
    const tokens = {};

    for (const token of ['--color-base-100', '--color-base-200', '--color-base-content', '--radius-box']) {
        const value = computed.getPropertyValue(token).trim();

        if (value !== '' && value.length <= 128 && !/url\s*\(/i.test(value)) tokens[token] = value;
    }

    return tokens;
}
