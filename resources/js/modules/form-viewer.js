/**
 * Daisy Kit module bridge for `<form data-module="form-viewer">`.
 *
 * Hydrates serialized schema/value/errors payloads and attaches {@link createFormRuntime}.
 *
 * @module modules/form-viewer
 */

import { createFormRuntime } from '../form-kit/runtime.js';
import { parseJsonPayload } from '../form-kit/schema.js';

/**
 * @param {HTMLElement} root - `<form>` element rendered by `forms.viewer` Blade component.
 * @param {Object} [options={}] - Overrides resolved from dataset (`submitMode`, `validateOn`, ...).
 * @returns {ReturnType<typeof createFormRuntime>}
 */
export default function initFormViewer(root, options = {}) {
    const schema = readJson(root, '[data-form-schema]', {});
    const value = readJson(root, '[data-form-value]', {});
    const errors = readJson(root, '[data-form-errors-payload]', {});

    const runtime = createFormRuntime(root, {
        schema,
        value,
        errors,
        // Prefer explicit module options but fall back to native form attributes for progressive enhancement.
        action: options.action ?? root.getAttribute('action'),
        method: options.method ?? root.getAttribute('method') ?? 'POST',
        submitMode: options.submitMode ?? root.dataset.submitMode,
        validateOn: options.validateOn ?? root.dataset.validateOn ?? 'submit',
    });

    root.__daisyFormRuntime = runtime;

    return runtime;
}

/**
 * @param {HTMLElement} root - Form element scanned for viewer payload `<script>` tags.
 * @param {string} selector - Embedded JSON selector (`data-form-schema`, ...).
 * @param {unknown} fallback - Fallback when parsing fails.
 * @returns {unknown}
 */
function readJson(root, selector, fallback) {
    const node = root.querySelector(selector);

    return parseJsonPayload(node?.textContent?.trim(), fallback);
}
