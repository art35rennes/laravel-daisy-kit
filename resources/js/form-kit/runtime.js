/**
 * Form viewer runtime: binds a Daisy JSON schema to server-rendered inputs, evaluates JSONata for
 * visibility and computed fields, runs validation rules, and coordinates submit modes.
 *
 * Dispatches `daisy-form:ready`, `daisy-form:change`, `daisy-form:invalid`, and `daisy-form:submit`.
 *
 * @module form-kit/runtime
 */

import { CONTAINER_FIELD_TYPES, getFieldValue } from './fields.js';
import { canonicalizeSchema, flattenFields, validateSchema } from './schema.js';
import { evaluateExpression } from './jsonata-engine.js';

/**
 * Bootstraps reactive behavior for a form root produced by the Blade viewer component.
 *
 * @param {HTMLElement} root - Usually the `<form data-module="form-viewer">` element.
 * @param {Object} [options={}] - Runtime options layered on schema-driven defaults.
 * @param {Object} options.schema - Canonical Daisy form schema.
 * @param {Record<string, unknown>} [options.value] - Initial field values keyed by submit name or id.
 * @param {Record<string, string|string[]>} [options.errors] - Server-side validation messages by field key.
 * @param {string} [options.action] - Overrides form action when using fetch submit mode.
 * @param {string} [options.method] - HTTP verb override for fetch submits.
 * @param {'event'|'html'|'fetch'|'none'} [options.submitMode] - How successful validation should finalize.
 * @returns {{
 *   refresh: () => Promise<void>,
 *   validate: () => Promise<boolean>,
 *   submit: (event?: Event|null) => Promise<boolean>,
 *   state: Object,
 *   serialize: () => Record<string, unknown>,
 * }}
 */
export function createFormRuntime(root, options = {}) {
    const schema = canonicalizeSchema(options.schema);
    const validation = validateSchema(schema);
    // Containers render recursively in Blade but runtime logic only binds leaf controls carrying submit names.
    const fields = flattenFields(schema.fields).filter((field) => !CONTAINER_FIELD_TYPES.includes(field.type));
    const state = {
        schema,
        fields,
        values: {
            ...(options.value ?? {}),
        },
        errors: normalizeErrors(options.errors ?? {}),
        visible: {},
        touched: {},
        valid: validation.valid,
    };

    fields.forEach((field) => {
        const key = field.name ?? field.id;

        if (!Object.prototype.hasOwnProperty.call(state.values, key)) {
            // Hydrate defaults without clobbering explicit null sent from the server for nullable fields.
            state.values[key] = getFieldValue(state.values, field);
        }

        // Visibility defaults true until JSONata runs; keeps SSR markup visible before first microtask tick.
        state.visible[field.id] = true;
    });

    /**
     * Builds the JSONata evaluation context for a single leaf field.
     *
     * @param {Object} field - Flattened field definition.
     * @returns {Object}
     */
    function getContext(field) {
        const key = field.name ?? field.id;

        return {
            values: state.values,
            field: {
                id: field.id,
                name: field.name,
                type: field.type,
                value: state.values[key],
            },
            visible: state.visible,
            meta: state.schema.meta ?? {},
            step: null, // Reserved for wizard-aware expressions later without breaking JSONata payloads today.
        };
    }

    /**
     * Reads values from the DOM, reapplies computed expressions and visibility, then refreshes markup/state flags.
     *
     * @returns {Promise<void>}
     */
    async function refresh() {
        syncValuesFromDom(root, fields, state.values);
        // Computed fields must run before visibility so expressions can reference freshly derived values.
        await applyComputedValues();
        await applyVisibility();
        applyDomState(root, fields, state);
        dispatch('daisy-form:change', { values: { ...state.values }, visible: { ...state.visible } });
    }

    /**
     * @returns {Promise<void>}
     */
    async function applyVisibility() {
        for (const field of fields) {
            if (!field.visibleWhen?.expression) {
                state.visible[field.id] = true;

                continue;
            }

            const result = await evaluateExpression(field.visibleWhen.expression, getContext(field));
            // Fail-closed: broken JSONata hides the field and surfaces the engine error alongside validation output.
            state.visible[field.id] = result.ok ? Boolean(result.value) : false;

            if (!result.ok) {
                addFieldError(state.errors, field, result.error.message, result.error.code);
            }
        }
    }

    /**
     * Writes computed results into state/DOM unless `suggested` mode preserves user input.
     *
     * @returns {Promise<void>}
     */
    async function applyComputedValues() {
        for (const field of fields) {
            if (!field.computed?.expression) {
                continue;
            }

            const result = await evaluateExpression(field.computed.expression, getContext(field));

            if (!result.ok) {
                addFieldError(state.errors, field, result.error.message, result.error.code);

                continue;
            }

            const key = field.name ?? field.id;

            // Suggested mode behaves like a default: once the user types, we stop overwriting their input on each refresh.
            if (field.computed.mode === 'suggested' && hasValue(state.values[key])) {
                continue;
            }

            state.values[key] = result.value;
            setFieldInputValue(root, field, result.value);
        }
    }

    /**
     * Validates visible fields using schema rules (simple strings + JSONata predicates).
     *
     * @returns {Promise<boolean>}
     */
    async function validate() {
        const errors = {};

        if (!validation.valid) {
            errors._schema = validation.errors.map((error) => error.message);
        }

        for (const field of fields) {
            if (state.visible[field.id] === false) {
                continue;
            }

            // Hidden fields must not block submit; server should mirror this policy when trusting client payloads.
            const fieldErrors = await validateField(field, state.values, getContext(field));

            if (fieldErrors.length > 0) {
                errors[field.name ?? field.id] = fieldErrors;
            }
        }

        state.errors = errors;
        state.valid = Object.keys(errors).length === 0;
        applyDomState(root, fields, state);

        if (!state.valid) {
            dispatch('daisy-form:invalid', { errors });
        }

        return state.valid;
    }

    /**
     * Runs refresh + validation, then optionally performs HTML submit, fetch POST, or emits a submit event.
     *
     * @param {Event|null} [event=null] - Native submit event when hooked from the form element.
     * @returns {Promise<boolean>}
     */
    async function submit(event = null) {
        if (event) {
            event.preventDefault();
        }

        await refresh();

        if (!await validate()) {
            return false;
        }

        const payload = serializeVisibleValues(fields, state.values, state.visible);
        const submitMode = options.submitMode ?? schema.submit?.mode ?? 'event';

        if (submitMode === 'none') {
            return true;
        }

        if (submitMode === 'fetch') {
            await submitWithFetch(root, payload, options);
            // Fetch executes without aborting the flow so hosts still observe `daisy-form:submit` after networking completes.
        }

        if (submitMode === 'html' && root instanceof HTMLFormElement) {
            root.submit();

            return true;
        }

        // Default `event` mode keeps Laravel CSR cookies untouched while letting hosts wire SPA routers.
        dispatch('daisy-form:submit', { values: payload, schema });

        return true;
    }

    function dispatch(name, detail) {
        root.dispatchEvent(new CustomEvent(name, { detail, bubbles: true }));
    }

    // `change` covers selects/checkboxes; `input` keeps ranges/text reactive without waiting for blur.
    root.addEventListener('input', () => {
        void refresh();
    });
    root.addEventListener('change', () => {
        void refresh();
    });
    root.addEventListener('submit', (event) => {
        void submit(event);
    });

    void refresh().then(() => {
        dispatch('daisy-form:ready', { schema, values: { ...state.values } });
    });

    return {
        refresh,
        validate,
        submit,
        state,
        serialize: () => serializeVisibleValues(fields, state.values, state.visible),
    };
}

/**
 * Evaluates all declarative rules attached to one field definition.
 *
 * @param {Object} field - Field definition containing optional `rules` array.
 * @param {Record<string, unknown>} values - Full form values map.
 * @param {Object} context - JSONata context object from the runtime.
 * @returns {Promise<string[]>} Human-readable validation messages (empty when valid).
 */
export async function validateField(field, values, context) {
    const errors = [];
    const value = values[field.name ?? field.id];

    for (const rule of field.rules ?? []) {
        if (typeof rule === 'string') {
            const message = validateSimpleRule(rule, value, values, field);

            if (message) {
                errors.push(message);
            }

            continue;
        }

        if (rule?.type === 'jsonata') {
            const result = await evaluateExpression(rule.expression, context);

            if (!result.ok) {
                errors.push(result.error.message);

                continue;
            }

            // JSONata validation rules must resolve strictly to boolean true to pass (parity with PHP evaluator expectations).
            if (result.value !== true) {
                errors.push(rule.message || 'The field value is invalid.');
            }
        }
    }

    return errors;
}

/**
 * Lightweight Laravel-inspired validation helpers mirroring server-side ergonomics.
 *
 * @param {string} rule - Rule string such as `required`, `email`, `min:5`.
 * @param {unknown} value - Candidate field value.
 * @param {Record<string, unknown>} [values={}] - Cross-field map (`same` rule).
 * @param {Object} [field={}] - Metadata used for friendly messages (`label`, `name`).
 * @returns {string|null} Message when invalid; otherwise `null`.
 */
export function validateSimpleRule(rule, value, values = {}, field = {}) {
    const [name, parameter = ''] = String(rule).split(':');

    // Laravel evaluates `nullable` before other constraints so optional empty fields skip trailing checks.
    if (name === 'nullable' && !hasValue(value)) {
        return null;
    }

    if (name === 'required' && !hasValue(value)) {
        return `${field.label ?? field.name ?? 'This field'} is required.`;
    }

    // After nullable/required passes, remaining rules behave like Laravel's implicit optional pipeline.
    if (!hasValue(value)) {
        return null;
    }

    if (name === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value))) {
        return `${field.label ?? field.name ?? 'This field'} must be a valid email address.`;
    }

    if (name === 'min' && Number(value) < Number(parameter)) {
        return `${field.label ?? field.name ?? 'This field'} must be at least ${parameter}.`;
    }

    if (name === 'max' && Number(value) > Number(parameter)) {
        return `${field.label ?? field.name ?? 'This field'} must be at most ${parameter}.`;
    }

    if (name === 'between') {
        const [min, max] = parameter.split(',');
        const number = Number(value);

        if (number < Number(min) || number > Number(max)) {
            return `${field.label ?? field.name ?? 'This field'} must be between ${min} and ${max}.`;
        }
    }

    if (name === 'length' && String(value).length !== Number(parameter)) {
        return `${field.label ?? field.name ?? 'This field'} must be ${parameter} characters.`;
    }

    if (name === 'pattern' && !(new RegExp(parameter).test(String(value)))) {
        return `${field.label ?? field.name ?? 'This field'} has an invalid format.`;
    }

    if (name === 'in' && !parameter.split(',').includes(String(value))) {
        return `${field.label ?? field.name ?? 'This field'} has an invalid value.`;
    }

    if (name === 'accepted' && !['1', 'true', 'yes', 'on'].includes(String(value))) {
        return `${field.label ?? field.name ?? 'This field'} must be accepted.`;
    }

    if (name === 'same' && value !== values[parameter]) {
        return `${field.label ?? field.name ?? 'This field'} must match ${parameter}.`;
    }

    return null;
}

/**
 * Builds the payload honoring visibility flags and skipping non-submitting artifacts (`staticText`).
 *
 * @param {Object[]} fields - Flattened field definitions from {@link flattenFields}.
 * @param {Record<string, unknown>} values - Live value bag.
 * @param {Record<string, boolean>} visible - Visibility map keyed by field id.
 * @returns {Record<string, unknown>}
 */
export function serializeVisibleValues(fields, values, visible) {
    return fields.reduce((payload, field) => {
        if (field.type === 'staticText' || visible[field.id] === false) {
            return payload;
        }

        const key = field.name ?? field.id;
        payload[key] = values[key] ?? null;

        return payload;
    }, {});
}

/**
 * Pulls current control values using `[data-form-input="{field.id}"]` anchors rendered by Blade.
 *
 * @param {HTMLElement} root - Form subtree scanned for controls.
 * @param {Object[]} fields - Active leaf fields.
 * @param {Record<string, unknown>} values - Mutable values bag updated in place.
 * @returns {void}
 */
function syncValuesFromDom(root, fields, values) {
    fields.forEach((field) => {
        const key = field.name ?? field.id;
        const input = root.querySelector(`[data-form-input="${cssEscape(field.id)}"]`);

        if (!input) {
            return;
        }

        // Blade wraps some atoms (radio groups, signature widgets) where `data-form-input` targets the wrapper element.
        if (!(input instanceof HTMLInputElement) && !(input instanceof HTMLTextAreaElement) && !(input instanceof HTMLSelectElement)) {
            const nestedInput = input.querySelector('input, textarea, select');
            values[key] = nestedInput?.value ?? null;

            return;
        }

        if (input.type === 'checkbox') {
            values[key] = input.checked;

            return;
        }

        if (input.type === 'radio') {
            // Multiple radios share `name`; scan within the form subtree for whichever peer is checked.
            const checked = root.querySelector(`[name="${cssEscape(field.name)}"]:checked`);
            values[key] = checked ? checked.value : null;

            return;
        }

        if (input instanceof HTMLSelectElement && input.multiple) {
            values[key] = Array.from(input.selectedOptions).map((option) => option.value);

            return;
        }

        values[key] = input.value;
    });
}

/**
 * @param {HTMLElement} root - Form subtree.
 * @param {Object} field - Target field definition.
 * @param {unknown} value - Next serialized control value.
 * @returns {void}
 */
function setFieldInputValue(root, field, value) {
    const input = root.querySelector(`[data-form-input="${cssEscape(field.id)}"]`);

    if (!input) {
        return;
    }

    if (!(input instanceof HTMLInputElement) && !(input instanceof HTMLTextAreaElement) && !(input instanceof HTMLSelectElement)) {
        const nestedInput = input.querySelector('input, textarea, select');

        if (nestedInput) {
            nestedInput.value = value ?? '';
        }

        return;
    }

    if (input.type === 'checkbox') {
        input.checked = Boolean(value);

        return;
    }

    input.value = value ?? '';
}

/**
 * Toggles wrappers via `[data-form-field]`, applies readonly hints for computed fields, injects errors.
 *
 * @returns {void}
 */
function applyDomState(root, fields, state) {
    fields.forEach((field) => {
        const wrapper = root.querySelector(`[data-form-field="${cssEscape(field.id)}"]`);

        if (wrapper) {
            wrapper.classList.toggle('hidden', state.visible[field.id] === false);
        }

        const input = root.querySelector(`[data-form-input="${cssEscape(field.id)}"]`);

        // `readOnly` stays false for suggested computed fields so authors can override automated values manually.
        if (input && field.computed?.mode && field.computed.mode !== 'suggested') {
            input.readOnly = field.computed.mode === 'readonly';
        }

        renderFieldErrors(root, field, state.errors[field.name ?? field.id] ?? []);
    });
}

/**
 * @param {HTMLElement} root - Form subtree containing `[data-form-errors="{field.id}"]`.
 * @returns {void}
 */
function renderFieldErrors(root, field, errors) {
    const errorNode = root.querySelector(`[data-form-errors="${cssEscape(field.id)}"]`);

    if (!errorNode) {
        return;
    }

    errorNode.textContent = errors.join(' ');
    errorNode.classList.toggle('hidden', errors.length === 0);
}

/**
 * Coerces Laravel-style bags / associative arrays into `string[][]` buckets per field key.
 *
 * @param {unknown} errors - Arbitrary error payload from Blade options.
 * @returns {Record<string, string[]>}
 */
function normalizeErrors(errors) {
    if (!errors || typeof errors !== 'object') {
        return {};
    }

    return Object.entries(errors).reduce((carry, [key, value]) => {
        carry[key] = Array.isArray(value) ? value.map(String) : [String(value)];

        return carry;
    }, {});
}

/**
 * @param {Record<string, string[]>} errors - Mutable runtime errors map.
 * @param {Object} field - Field receiving the diagnostic.
 * @param {string} message - Human-readable description.
 * @returns {void}
 */
function addFieldError(errors, field, message) {
    const key = field.name ?? field.id;
    errors[key] = errors[key] ?? [];
    errors[key].push(message);
}

/**
 * @param {unknown} value - Candidate primitive/array submission value.
 * @returns {boolean}
 */
function hasValue(value) {
    if (Array.isArray(value)) {
        return value.length > 0;
    }

    return value !== null && value !== undefined && value !== '';
}

/**
 * Performs a JSON `fetch` against `options.action` or the host form `action` attribute.
 *
 * @returns {Promise<Response|null>}
 */
async function submitWithFetch(root, payload, options) {
    const endpoint = options.action ?? root.getAttribute('action');

    if (!endpoint) {
        return null;
    }

    return fetch(endpoint, {
        method: options.method ?? root.getAttribute('method') ?? 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify(payload),
    });
}

/**
 * Escapes identifiers for safe use inside `querySelector` selectors.
 *
 * @param {unknown} value - Field id fragment.
 * @returns {string}
 */
function cssEscape(value) {
    if (typeof CSS !== 'undefined' && CSS.escape) {
        return CSS.escape(String(value));
    }

    // Minimal fallback for legacy environments lacking `CSS.escape`; ids normally avoid needing full selector grammar escapes.
    return String(value).replace(/["\\]/g, '\\$&');
}
