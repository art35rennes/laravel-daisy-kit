/**
 * Daisy Kit module bridge for `<form data-module="form-viewer">`.
 *
 * Hydrates serialized schema/value/errors payloads and attaches {@link createFormRuntime}.
 *
 * @module modules/form-viewer
 */

import { createFormRuntime } from '../form-kit/runtime.js';
import { parseJsonPayload } from '../form-kit/schema.js';
import { initColorPicker } from '../color-picker.js';

/**
 * @param {HTMLElement} root - `<form>` element rendered by `forms.viewer` Blade component.
 * @param {Object} [options={}] - Overrides resolved from dataset (`submitMode`, `validateOn`, ...).
 * @returns {ReturnType<typeof createFormRuntime>}
 */
export default function initFormViewer(root, options = {}) {
    initNestedControls(root);

    const schema = readJson(root, '[data-form-schema]', {});
    const value = readJson(root, '[data-form-value]', {});
    const errors = readJson(root, '[data-form-errors-payload]', {});

    const runtime = createFormRuntime(root, {
        schema,
        value,
        errors,
        // Prefer explicit module options but fall back to native form attributes for progressive enhancement.
        action: options.action ?? root.getAttribute('action'),
        method: options.method ?? root.dataset.formMethod ?? root.getAttribute('method') ?? 'POST',
        submitMode: options.submitMode ?? root.dataset.submitMode,
        validateOn: options.validateOn ?? root.dataset.validateOn ?? 'submit',
        readonly: options.readonly ?? root.dataset.readonly === 'true',
    });

    root.__daisyFormRuntime = runtime;
    registerViewerRuntime(runtime);
    decorateDestroy(runtime);

    return runtime;
}

function initNestedControls(root) {
    root.querySelectorAll('[data-colorpicker="1"]').forEach((element) => {
        initColorPicker(element);
    });
}

/**
 * Keeps the global viewer registry aligned when a host manually destroys a runtime.
 *
 * @param {ReturnType<typeof createFormRuntime>} runtime - Public viewer runtime.
 * @returns {void}
 */
function decorateDestroy(runtime) {
    if (typeof window === 'undefined') {
        return;
    }

    if (runtime.__daisyDestroyDecorated) {
        return;
    }

    const destroy = runtime.destroy;

    runtime.destroy = () => {
        destroy();
        window.DaisyFormViewer?.unregister(runtime.id);
    };
    runtime.__daisyDestroyDecorated = true;
}

function registerViewerRuntime(runtime) {
    if (typeof window === 'undefined') {
        return;
    }

    window.DaisyFormViewer = window.DaisyFormViewer ?? createRegistry();
    window.DaisyFormViewer.register(runtime);
}

function createRegistry() {
    const runtimes = new Map();

    const pruneDisconnected = () => {
        for (const [id, runtime] of runtimes.entries()) {
            if (!runtime.root?.isConnected) {
                runtimes.delete(id);
            }
        }
    };

    return {
        register(runtime) {
            pruneDisconnected();
            runtimes.set(runtime.id, runtime);

            return runtime;
        },
        get(id) {
            pruneDisconnected();

            return runtimes.get(id) ?? null;
        },
        getByElement(element) {
            if (!element?.isConnected) {
                return null;
            }

            return element.__daisyFormRuntime ?? null;
        },
        all() {
            pruneDisconnected();

            return Array.from(runtimes.values());
        },
        unregister(id) {
            runtimes.delete(id);
        },
    };
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
