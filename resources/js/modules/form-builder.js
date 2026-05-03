/**
 * Daisy Kit module bridge for `<div data-module="form-builder">`.
 *
 * Reads embedded JSON payloads (`data-builder-schema`, etc.) and mounts {@link mountFormBuilder}.
 *
 * @module modules/form-builder
 */

import { mountFormBuilder } from '../form-kit/builder.js';
import { parseJsonPayload } from '../form-kit/schema.js';

/**
 * @param {HTMLElement} root - Builder container rendered by `forms.builder` Blade component.
 * @param {Object} [options={}] - Extra options forwarded to {@link mountFormBuilder}.
 * @returns {ReturnType<typeof mountFormBuilder>}
 */
export default function initFormBuilder(root, options = {}) {
    const schema = readJson(root, '[data-builder-schema]', null);
    const fieldTypes = readJson(root, '[data-builder-field-types]', null);
    const functionCatalog = readJson(root, '[data-builder-function-catalog]', null);

    return mountFormBuilder(root, {
        schema,
        fieldTypes,
        functionCatalog,
        // Programmatic callers win over serialized payloads when keys collide (tests + Storybook harnesses).
        ...options,
    });
}

/**
 * @param {HTMLElement} root - Host element queried for `<script type="application/json">` blobs.
 * @param {string} selector - Attribute selector targeting embedded JSON script tags.
 * @param {unknown} fallback - Value returned when the node or payload is missing/invalid.
 * @returns {unknown}
 */
function readJson(root, selector, fallback) {
    const node = root.querySelector(selector);

    return parseJsonPayload(node?.textContent?.trim(), fallback);
}
