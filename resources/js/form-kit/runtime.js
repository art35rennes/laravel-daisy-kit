/**
 * Form viewer runtime: binds a Daisy JSON schema to server-rendered inputs, evaluates JSONata for
 * visibility and computed fields, runs validation rules, and coordinates submit modes.
 *
 * Dispatches `daisy-form:ready`, `daisy-form:change`, `daisy-form:invalid`,
 * `daisy-form:step-change`, `daisy-form:submit`, and `daisy-form:destroy`.
 *
 * @module form-kit/runtime
 */

import { CONTAINER_FIELD_TYPES, NON_SUBMITTING_FIELD_TYPES, getFieldValue } from './fields.js';
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
 * @param {'input'|'change'|'submit'} [options.validateOn='submit'] - Interaction that triggers live validation.
 * @param {boolean} [options.readonly=false] - Whether the viewer is rendered for display-only usage.
 * @returns {{
 *   id: string,
 *   root: HTMLElement,
 *   refresh: () => Promise<void>,
 *   validate: (options?: {scope?: 'all'|'current'}) => Promise<boolean>,
 *   submit: (event?: Event|null) => Promise<boolean>,
 *   destroy: () => void,
 *   on: (name: string, listener: EventListener, options?: AddEventListenerOptions|boolean) => () => void,
 *   off: (name: string, listener: EventListener, options?: EventListenerOptions|boolean) => void,
 *   state: Object,
 *   serialize: () => Record<string, unknown>,
 *   getSchema: () => Object,
 *   getSubmitMode: () => 'event'|'html'|'fetch'|'none',
 *   getValidateOn: () => 'input'|'change'|'submit',
 *   isReadonly: () => boolean,
 *   getValues: (options?: {visible?: boolean}) => Record<string, unknown>,
 *   getValue: (key: string) => unknown,
 *   setValue: (key: string, value: unknown, options?: {refresh?: boolean}) => Promise<void>,
 *   setValues: (values: Record<string, unknown>, options?: {refresh?: boolean}) => Promise<void>,
 *   reset: (values?: Record<string, unknown>) => Promise<void>,
 *   getErrors: () => Record<string, string[]>,
 *   setErrors: (errors: Record<string, string|string[]>) => void,
 *   clearErrors: () => void,
 *   getField: (key: string) => Object|null,
 *   getInput: (key: string) => HTMLElement|null,
 *   getVisibleFields: () => Object[],
 *   isValid: () => boolean,
 *   getStep: () => number,
 *   setStep: (index: number) => Promise<number>,
 *   nextStep: () => Promise<number>,
 *   previousStep: () => Promise<number>,
 * }}
 */
export function createFormRuntime(root, options = {}) {
    const schema = canonicalizeSchema(options.schema);
    const validation = validateSchema(schema);
    const validateOn = ['input', 'change', 'submit'].includes(options.validateOn) ? options.validateOn : 'submit';
    const submitModes = ['event', 'html', 'fetch', 'none'];
    const submitMode = submitModes.includes(options.submitMode)
        ? options.submitMode
        : (submitModes.includes(schema.submit?.mode) ? schema.submit.mode : 'event');
    const readonly = options.readonly === true;
    // Containers render recursively in Blade. Every schema node can drive visibility,
    // while only submitting leaves bind values, validation rules, and submit payloads.
    const allFields = flattenFields(schema.fields);
    const visibilityFields = allFields;
    const fields = allFields.filter((field) => !CONTAINER_FIELD_TYPES.includes(field.type));
    const submittingFields = fields.filter((field) => !NON_SUBMITTING_FIELD_TYPES.includes(field.type));
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
        currentStep: 0,
    };
    const isMultiStep = schema.layout?.type === 'multi-step';
    const stepFields = schema.fields.filter((field) => field.type === 'wizardStep');
    const runtimeId = root.dataset.formId || root.id || schema.id || `daisy-form-${Date.now()}`;
    let destroyed = false;
    let expressionErrors = {};

    if (!root.id) {
        root.id = runtimeId;
    }

    root.dataset.formId = runtimeId;
    root.dataset.formRuntimeState = 'initializing';
    root.dataset.formSubmitMode = submitMode;
    root.dataset.formValidateOn = validateOn;
    root.dataset.formReadonly = readonly ? 'true' : 'false';

    submittingFields.forEach((field) => {
        const key = field.name ?? field.id;

        if (!Object.prototype.hasOwnProperty.call(state.values, key)) {
            // Hydrate defaults without clobbering explicit null sent from the server for nullable fields.
            state.values[key] = getFieldValue(state.values, field);
        }
    });

    visibilityFields.forEach((field) => {
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
        syncValuesFromDom(root, submittingFields, state.values);
        clearExpressionErrors();
        // Computed fields must run before visibility so expressions can reference freshly derived values.
        await applyComputedValues();
        await applyVisibility();
        applyDomState(root, visibilityFields, state);
        applyStepState(root, state);
        dispatch('daisy-form:change', { values: { ...state.values }, visible: { ...state.visible } });
    }

    /**
     * @returns {Promise<void>}
     */
    async function applyVisibility() {
        for (const field of visibilityFields) {
            if (!field.visibleWhen?.expression) {
                state.visible[field.id] = true;

                continue;
            }

            const result = await evaluateExpression(field.visibleWhen.expression, getContext(field));
            // Fail-closed: broken JSONata hides the field and surfaces the engine error alongside validation output.
            state.visible[field.id] = result.ok ? Boolean(result.value) : false;

            if (!result.ok) {
                addExpressionError(field, result.error.message);
            }
        }
    }

    /**
     * Writes computed results into state/DOM unless `suggested` mode preserves user input.
     *
     * @returns {Promise<void>}
     */
    async function applyComputedValues() {
        for (const field of submittingFields) {
            if (!field.computed?.expression) {
                continue;
            }

            const result = await evaluateExpression(field.computed.expression, getContext(field));

            if (!result.ok) {
                addExpressionError(field, result.error.message);

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
    async function validate(options = {}) {
        const scope = options.scope ?? 'all';
        const errors = {};

        if (!validation.valid) {
            errors._schema = validation.errors.map((error) => error.message);
        }

        for (const field of submittingFields) {
            if (state.visible[field.id] === false) {
                continue;
            }

            if (isMultiStep && scope === 'current' && !isFieldOnCurrentStep(field, state, stepFields, allFields)) {
                continue;
            }

            // Hidden fields must not block submit; server should mirror this policy when trusting client payloads.
            const fieldErrors = await validateField(field, state.values, getContext(field));

            if (fieldErrors.length > 0) {
                errors[field.name ?? field.id] = fieldErrors;
            }
        }

        state.errors = mergeErrors(errors, expressionErrors);
        state.valid = Object.keys(state.errors).length === 0;
        applyDomState(root, visibilityFields, state);
        applyStepState(root, state);

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

        const payload = serializeVisibleValues(submittingFields, state.values, state.visible);
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
        root.dispatchEvent(new CustomEvent(name, {
            detail: {
                id: runtimeId,
                runtime: publicApi,
                ...detail,
            },
            bubbles: true,
        }));
    }

    async function handleInteraction(kind) {
        await refresh();

        if (validateOn === kind || (validateOn === 'input' && kind === 'change')) {
            await validate();
        }
    }

    // `change` covers selects/checkboxes; `input` keeps ranges/text reactive without waiting for blur.
    const onInput = () => {
        void handleInteraction('input');
    };
    const onChange = () => {
        void handleInteraction('change');
    };
    const onColorPickerChange = () => {
        void handleInteraction('change');
    };
    const onSubmit = (event) => {
        void submit(event);
    };
    const onNextStep = async () => {
        await nextStep();
    };
    const onPreviousStep = async () => {
        await previousStep();
    };

    root.addEventListener('input', onInput);
    root.addEventListener('change', onChange);
    root.addEventListener('colorpicker:change', onColorPickerChange);
    root.addEventListener('submit', onSubmit);
    root.querySelectorAll('[data-form-next]').forEach((button) => {
        button.addEventListener('click', onNextStep);
    });
    root.querySelectorAll('[data-form-previous]').forEach((button) => {
        button.addEventListener('click', onPreviousStep);
    });

    async function setValue(key, value, options = {}) {
        const field = findRuntimeField(fields, key);
        const stateKey = field?.name ?? key;

        state.values[stateKey] = value;

        if (field) {
            setFieldInputValue(root, field, value);
        }

        if (options.refresh !== false) {
            await refresh();
        }
    }

    async function setValues(values, options = {}) {
        for (const [key, value] of Object.entries(values ?? {})) {
            const field = findRuntimeField(fields, key);
            const stateKey = field?.name ?? key;

            state.values[stateKey] = value;

            if (field) {
                setFieldInputValue(root, field, value);
            }
        }

        if (options.refresh !== false) {
            await refresh();
        }
    }

    async function reset(values = options.value ?? {}) {
        state.values = { ...(values ?? {}) };
        state.errors = {};
        state.touched = {};

        submittingFields.forEach((field) => {
            const key = field.name ?? field.id;
            const value = Object.prototype.hasOwnProperty.call(state.values, key)
                ? state.values[key]
                : getFieldValue(state.values, field);

            state.values[key] = value;
            setFieldInputValue(root, field, value);
        });

        await refresh();
    }

    function setErrors(errors) {
        expressionErrors = {};
        state.errors = normalizeErrors(errors);
        state.valid = Object.keys(state.errors).length === 0;
        applyDomState(root, visibilityFields, state);
        applyStepState(root, state);
    }

    function clearErrors() {
        expressionErrors = {};
        setErrors({});
    }

    function clearExpressionErrors() {
        state.errors = removeErrors(state.errors, expressionErrors);
        expressionErrors = {};
    }

    function addExpressionError(field, message) {
        addFieldError(expressionErrors, field, message);
        addFieldError(state.errors, field, message);
    }

    /**
     * Attaches a host listener to the form root and returns an unsubscribe callback.
     *
     * @param {string} name - Event name, for example `daisy-form:submit`.
     * @param {EventListener} listener - Listener invoked with a `CustomEvent` detail payload.
     * @param {AddEventListenerOptions|boolean} [options] - Native listener options.
     * @returns {() => void}
     */
    function on(name, listener, options = undefined) {
        root.addEventListener(name, listener, options);

        return () => off(name, listener, options);
    }

    /**
     * Removes a listener previously registered with {@link on}.
     *
     * @param {string} name - Event name.
     * @param {EventListener} listener - Listener reference.
     * @param {EventListenerOptions|boolean} [options] - Native listener options.
     * @returns {void}
     */
    function off(name, listener, options = undefined) {
        root.removeEventListener(name, listener, options);
    }

    /**
     * Detaches runtime listeners while leaving server-rendered HTML in place.
     *
     * @returns {void}
     */
    function destroy() {
        if (destroyed) {
            return;
        }

        destroyed = true;
        root.removeEventListener('input', onInput);
        root.removeEventListener('change', onChange);
        root.removeEventListener('colorpicker:change', onColorPickerChange);
        root.removeEventListener('submit', onSubmit);
        root.querySelectorAll('[data-form-next]').forEach((button) => {
            button.removeEventListener('click', onNextStep);
        });
        root.querySelectorAll('[data-form-previous]').forEach((button) => {
            button.removeEventListener('click', onPreviousStep);
        });
        root.dataset.formRuntimeState = 'destroyed';
        delete root.__daisyFormRuntime;
        dispatch('daisy-form:destroy', { schema, values: { ...state.values } });
    }

    async function setStep(index) {
        const next = Math.max(0, Math.min(Number(index) || 0, Math.max(0, stepFields.length - 1)));
        state.currentStep = next;
        applyStepState(root, state);
        dispatch('daisy-form:step-change', { currentStep: state.currentStep });

        return state.currentStep;
    }

    async function nextStep() {
        await refresh();

        if (!await validate({ scope: 'current' })) {
            return state.currentStep;
        }

        return setStep(state.currentStep + 1);
    }

    async function previousStep() {
        return setStep(state.currentStep - 1);
    }

    const publicApi = {
        id: runtimeId,
        root,
        refresh,
        validate,
        submit,
        destroy,
        on,
        off,
        state,
        serialize: () => serializeVisibleValues(submittingFields, state.values, state.visible),
        getSchema: () => state.schema,
        getSubmitMode: () => submitMode,
        getValidateOn: () => validateOn,
        isReadonly: () => readonly,
        getValues: (options = {}) => options.visible ? serializeVisibleValues(submittingFields, state.values, state.visible) : { ...state.values },
        getValue: (key) => state.values[findRuntimeField(fields, key)?.name ?? key],
        setValue,
        setValues,
        reset,
        getErrors: () => ({ ...state.errors }),
        setErrors,
        clearErrors,
        getField: (key) => findRuntimeField(fields, key) ?? null,
        getInput: (key) => {
            const field = findRuntimeField(fields, key);

            return field ? root.querySelector(`[data-form-input="${cssEscape(field.id)}"]`) : null;
        },
        getVisibleFields: () => fields.filter((field) => state.visible[field.id] !== false),
        isValid: () => state.valid,
        getStep: () => state.currentStep,
        setStep,
        nextStep,
        previousStep,
    };

    void refresh().then(() => {
        root.dataset.formRuntimeState = 'ready';
        dispatch('daisy-form:ready', { schema, values: { ...state.values } });
    });

    return publicApi;
}

function findRuntimeField(fields, key) {
    return fields.find((field) => field.id === key || field.name === key);
}

function isFieldOnCurrentStep(field, state, stepFields, fields) {
    if (stepFields.length === 0) {
        return true;
    }

    const currentStep = stepFields[state.currentStep];

    if (!currentStep) {
        return true;
    }

    const fieldMap = new Map(fields.map((item) => [item.id, item]));
    let parent = field.parent;

    while (parent) {
        if (parent === currentStep.id) {
            return true;
        }

        parent = fieldMap.get(parent)?.parent;
    }

    return field.id === currentStep.id;
}

function applyStepState(root, state) {
    const steps = Array.from(root.querySelectorAll('[data-form-step]'));

    if (steps.length === 0) {
        return;
    }

    steps.forEach((step, index) => {
        const stepId = step.dataset.formStep;
        const stepVisible = !stepId || state.visible[stepId] !== false;
        const hidden = index !== state.currentStep || !stepVisible;

        step.classList.toggle('hidden', hidden);
        step.toggleAttribute('aria-hidden', hidden);
    });

    root.querySelectorAll('[data-form-step-indicator]').forEach((indicator) => {
        const index = Number(indicator.dataset.formStepIndicator);
        indicator.classList.toggle('step-primary', index <= state.currentStep);
    });

    root.querySelectorAll('[data-form-previous]').forEach((button) => {
        button.toggleAttribute('disabled', state.currentStep === 0);
    });

    root.querySelectorAll('[data-form-next]').forEach((button) => {
        button.classList.toggle('hidden', state.currentStep >= steps.length - 1);
    });

    root.querySelectorAll('[data-form-submit]').forEach((button) => {
        button.classList.toggle('hidden', state.currentStep < steps.length - 1);
    });
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
 * Builds the payload honoring visibility flags and skipping non-submitting schema artifacts.
 *
 * @param {Object[]} fields - Flattened field definitions from {@link flattenFields}.
 * @param {Record<string, unknown>} values - Live value bag.
 * @param {Record<string, boolean>} visible - Visibility map keyed by field id.
 * @returns {Record<string, unknown>}
 */
export function serializeVisibleValues(fields, values, visible) {
    return fields.reduce((payload, field) => {
        if (NON_SUBMITTING_FIELD_TYPES.includes(field.type) || visible[field.id] === false) {
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
            const checked = root.querySelector(`[name="${cssEscape(field.name ?? field.id)}"]:checked`);
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

    if (input.type === 'radio') {
        root.querySelectorAll(`[name="${cssEscape(field.name ?? field.id)}"]`).forEach((radio) => {
            radio.checked = radio.value === String(value);
        });

        return;
    }

    if (input instanceof HTMLSelectElement && input.multiple) {
        const values = Array.isArray(value) ? value.map(String) : [String(value)];
        Array.from(input.options).forEach((option) => {
            option.selected = values.includes(option.value);
        });

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
        const wrapper = root.querySelector(`[data-form-field="${cssEscape(field.id)}"]`)
            ?? (field.type === 'wizardStep' ? root.querySelector(`[data-form-step="${cssEscape(field.id)}"]`) : null);

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
 * Combines multiple normalized error bags without mutating the source maps.
 *
 * @param {...Record<string, string[]>} bags - Normalized error bags.
 * @returns {Record<string, string[]>}
 */
function mergeErrors(...bags) {
    return bags.reduce((carry, bag) => {
        Object.entries(bag ?? {}).forEach(([key, messages]) => {
            carry[key] = [...(carry[key] ?? []), ...messages];
        });

        return carry;
    }, {});
}

/**
 * Removes a transient error bag from a larger bag while preserving unrelated host/validation errors.
 *
 * @param {Record<string, string[]>} source - Current error bag.
 * @param {Record<string, string[]>} transient - Messages previously generated by runtime expressions.
 * @returns {Record<string, string[]>}
 */
function removeErrors(source, transient) {
    return Object.entries(source ?? {}).reduce((carry, [key, messages]) => {
        const transientMessages = transient?.[key] ?? [];
        const nextMessages = messages.filter((message) => !transientMessages.includes(message));

        if (nextMessages.length > 0) {
            carry[key] = nextMessages;
        }

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
