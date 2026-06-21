/**
 * Custom icon normalization and sanitization for drawing tools and business markers.
 *
 * Integrators may provide SVG/HTML snippets for toolbar icons, but executable
 * markup is stripped before it reaches innerHTML.
 */

/**
 * @param {string|null} markup
 * @returns {string|null}
 */
function sanitizeIconMarkup(markup) {
    if (!markup || typeof markup !== 'string') {
        return null;
    }

    const value = markup.trim();

    if (value === '') {
        return null;
    }

    if (!globalThis.DOMParser) {
        return value
            .replace(/<script[\s\S]*?<\/script>/gi, '')
            .replace(/\son[a-z]+\s*=\s*(['"]).*?\1/gi, '')
            .replace(/\s(?:href|src|xlink:href)\s*=\s*(['"])\s*javascript:[\s\S]*?\1/gi, '');
    }

    const document = new DOMParser().parseFromString(`<template>${value}</template>`, 'text/html');
    const template = document.querySelector('template');

    if (!template) {
        return null;
    }

    template.content.querySelectorAll('script, iframe, object, embed, foreignObject').forEach(node => node.remove());
    template.content.querySelectorAll('*').forEach(node => {
        [...node.attributes].forEach(attribute => {
            const name = attribute.name.toLowerCase();
            const attrValue = attribute.value.trim();

            if (name.startsWith('on') || ((name === 'href' || name === 'src' || name === 'xlink:href') && /^javascript:/i.test(attrValue))) {
                node.removeAttribute(attribute.name);
            }
        });
    });

    return template.innerHTML || null;
}

/**
 * @param {string|null} svg
 * @returns {string|null}
 */
function svgToDataUrl(svg) {
    if (!svg || typeof svg !== 'string') {
        return null;
    }

    const sanitized = sanitizeIconMarkup(svg);

    return sanitized ? `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(sanitized)}` : null;
}

/**
 * @param {string|null} url
 * @returns {string|null}
 */
function normalizeIconUrl(url) {
    if (!url || typeof url !== 'string') {
        return null;
    }

    const value = url.trim();

    if (value === '') {
        return null;
    }

    if (value.startsWith('/') || /^https?:\/\//i.test(value) || /^data:image\/(?:svg\+xml|png|jpeg|jpg|webp);/i.test(value)) {
        return value;
    }

    return null;
}

export {
    normalizeIconUrl,
    sanitizeIconMarkup,
    svgToDataUrl,
};
