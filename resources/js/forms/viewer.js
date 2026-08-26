import jsonata from 'jsonata';

import '../../css/forms-viewer.css';
import { createInstanceIdentifier } from '../core/identifiers.js';
import { createMountable, installLivewireAdapter as createLivewireAdapter } from '../core/mountable.js';

const controlTypes = new Set([
    'checkbox', 'color', 'date', 'datetime-local', 'email', 'file', 'hidden', 'month',
    'number', 'password', 'radio', 'range', 'select', 'tel', 'textarea', 'text', 'time',
    'toggle', 'url',
]);
const containerTypes = new Set(['section', 'wizardStep']);
const submitModes = new Set(['event', 'fetch', 'html', 'none']);
const runtimeApis = new WeakMap();

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:forms-viewer:${name}`, { bubbles: true, detail }));
}

function fieldType(field) {
    return controlTypes.has(field.type) ? field.type : 'text';
}

function hasUnsupportedType(field) {
    return typeof field.type === 'string' && field.type !== '' && !controlTypes.has(field.type) && !containerTypes.has(field.type);
}

function fieldsFrom(configuration) {
    const fields = configuration.schema?.fields;

    return Array.isArray(fields) ? fields.filter((field) => field && typeof field === 'object') : [];
}

function errorsFrom(configuration) {
    return configuration.errors && typeof configuration.errors === 'object' && !Array.isArray(configuration.errors)
        ? configuration.errors
        : {};
}

function jsonataExpression(value) {
    if (value && typeof value === 'object' && value.type === 'jsonata'
        && typeof value.expression === 'string' && value.expression.trim() !== '') {
        return value.expression;
    }

    return null;
}

function setStatus(root, message, state) {
    const status = root.querySelector('[data-daisy-kit-status]');
    root.dataset.daisyKitState = state;
    root.setAttribute('aria-busy', 'false');

    if (status) {
        status.textContent = message;
        status.hidden = message === '';
    }
}

function setInputValue(input, field, value) {
    if (fieldType(field) === 'file') {
        return;
    }

    if (fieldType(field) === 'checkbox' || fieldType(field) === 'toggle') {
        input.checked = value === true || value === 1 || value === '1' || value === 'on';

        return;
    }

    input.value = value ?? '';
}

function setReadonly(input, field, readonly) {
    if (! readonly && field.readonly !== true && jsonataExpression(field.computed) === null) {
        return;
    }

    if (input instanceof HTMLSelectElement || input.type === 'checkbox' || input.type === 'radio') {
        input.disabled = true;

        return;
    }

    input.readOnly = true;
}

function optionsFor(field) {
    return Array.isArray(field.options) ? field.options : [];
}

function rulesFor(field) {
    if (typeof field.rules === 'string') {
        return field.rules.split('|').filter((rule) => rule !== '');
    }

    return Array.isArray(field.rules) ? field.rules.filter((rule) => typeof rule === 'string') : [];
}

function ruleArgument(rules, name) {
    const rule = rules.find((candidate) => candidate.startsWith(`${name}:`));

    return rule ? rule.slice(name.length + 1) : null;
}

function applySafeAttributes(input, field) {
    const attributes = field.attrs && typeof field.attrs === 'object' && !Array.isArray(field.attrs)
        ? field.attrs
        : {};
    const permitted = ['accept', 'autocomplete', 'inputmode', 'max', 'maxlength', 'min', 'minlength', 'multiple', 'pattern', 'placeholder', 'step'];

    permitted.forEach((name) => {
        const value = attributes[name];

        if (typeof value === 'string' || typeof value === 'number') {
            input.setAttribute(name, String(value));
        }

        if (name === 'multiple' && value === true) {
            input.setAttribute('multiple', '');
        }
    });
}

function applyRules(input, field) {
    const rules = rulesFor(field);
    const type = fieldType(field);
    const minimum = ruleArgument(rules, 'min');
    const maximum = ruleArgument(rules, 'max');
    const between = ruleArgument(rules, 'between')?.split(',') ?? [];

    input.required = field.required === true || rules.includes('required') || rules.includes('accepted');

    if (rules.includes('email') && input instanceof HTMLInputElement && type === 'text') {
        input.type = 'email';
    }

    const setMinimum = (value) => {
        if (value === null || value === undefined || value === '') {
            return;
        }

        if (type === 'number' || type === 'range') {
            input.setAttribute('min', value);

            return;
        }

        input.setAttribute('minlength', value);
    };
    const setMaximum = (value) => {
        if (value === null || value === undefined || value === '') {
            return;
        }

        if (type === 'number' || type === 'range') {
            input.setAttribute('max', value);

            return;
        }

        input.setAttribute('maxlength', value);
    };

    setMinimum(between[0] ?? minimum);
    setMaximum(between[1] ?? maximum);
}

function optionValue(option) {
    return typeof option === 'object' && option !== null ? option.value : option;
}

function optionLabel(option) {
    return typeof option === 'object' && option !== null ? option.label ?? option.value : option;
}

function createControl(field, value, fieldId, readonly) {
    const type = fieldType(field);

    if (type === 'radio') {
        const controls = document.createElement('div');
        controls.setAttribute('role', 'radiogroup');

        optionsFor(field).forEach((option, optionIndex) => {
            const label = document.createElement('label');
            const input = document.createElement('input');
            const optionValueAsString = String(optionValue(option) ?? '');

            input.type = 'radio';
            input.name = field.name;
            input.id = `${fieldId}-${optionIndex}`;
            input.value = optionValueAsString;
            input.checked = value === optionValueAsString;
            applySafeAttributes(input, field);
            applyRules(input, field);
            setReadonly(input, field, readonly);
            label.htmlFor = input.id;
            label.append(input, document.createTextNode(String(optionLabel(option) ?? optionValueAsString)));
            controls.append(label);
        });

        return controls;
    }

    const input = type === 'textarea'
        ? document.createElement('textarea')
        : document.createElement(type === 'select' ? 'select' : 'input');

    input.name = field.name;
    input.id = fieldId;
    applySafeAttributes(input, field);
    applyRules(input, field);

    if (type !== 'textarea' && type !== 'select') {
        input.type = type === 'toggle' ? 'checkbox' : type;
    }

    if (type === 'select') {
        optionsFor(field).forEach((option) => {
            const element = document.createElement('option');
            const value = optionValue(option);

            element.value = String(value ?? '');
            element.textContent = String(optionLabel(option) ?? value ?? '');
            input.append(element);
        });
    }

    setInputValue(input, field, value ?? field.default);
    setReadonly(input, field, readonly);

    return input;
}

function errorMessages(errors, fieldName) {
    const messages = errors[fieldName];

    if (Array.isArray(messages)) {
        return messages.filter((message) => typeof message === 'string');
    }

    return typeof messages === 'string' ? [messages] : [];
}

function applyLayout(wrapper, field) {
    const width = field.ui && typeof field.ui === 'object' && !Array.isArray(field.ui)
        ? field.ui.width
        : null;
    const widths = {
        '1/4': 'quarter',
        '1/3': 'third',
        '1/2': 'half',
        '2/3': 'two-thirds',
        '3/4': 'three-quarters',
    };

    wrapper.classList.add(`daisy-kit-forms-field--span-${widths[width] ?? 'full'}`);
}

function renderField(root, parent, field, values, errors, onChange, fieldId, readonly, entries, parentEntry) {
    if (field.type === 'staticText') {
        const content = document.createElement('p');

        content.dataset.daisyKitFormsStaticText = '';
        content.textContent = typeof field.text === 'string'
            ? field.text
            : (typeof field.label === 'string' ? field.label : '');
        parent.append(content);
        entries.push({ field, parent: parentEntry, wrapper: content });

        return;
    }

    if (typeof field.name !== 'string' || field.name === '') {
        return;
    }

    const wrapper = document.createElement('div');
    const label = document.createElement('label');
    const messages = errorMessages(errors, field.name);
    wrapper.dataset.daisyKitFormsField = field.name;
    applyLayout(wrapper, field);
    label.textContent = typeof field.label === 'string' ? field.label : field.name;

    if (hasUnsupportedType(field)) {
        const typeError = document.createElement('p');

        typeError.dataset.daisyKitFormsTypeError = field.name;
        typeError.setAttribute('role', 'alert');
        typeError.textContent = `Unsupported field type "${field.type}" is unavailable.`;
        wrapper.append(label, typeError);
        emit(root, 'error', { field: field.name, reason: 'unsupported-type', type: field.type });

        parent.append(wrapper);
        entries.push({ field, parent: parentEntry, wrapper });

        return;
    }

    const control = createControl(field, values[field.name], fieldId, readonly);
    const inputs = control instanceof HTMLInputElement || control instanceof HTMLSelectElement || control instanceof HTMLTextAreaElement
        ? [control]
        : [...control.querySelectorAll('input')];

    if (inputs.length === 1) {
        label.htmlFor = inputs[0].id;
    }

    if (messages.length > 0) {
        const error = document.createElement('p');

        error.id = `${fieldId}-error`;
        error.dataset.daisyKitFormsError = field.name;
        error.setAttribute('role', 'alert');
        error.textContent = messages.join(' ');
        inputs.forEach((input) => {
            input.setAttribute('aria-describedby', error.id);
            input.setAttribute('aria-invalid', 'true');
        });
        wrapper.append(label, control, error);
    } else {
        wrapper.append(label, control);
    }

    inputs.forEach((input) => {
        input.addEventListener('input', onChange);
        input.addEventListener('change', onChange);
    });
    parent.append(wrapper);
    entries.push({ field, parent: parentEntry, wrapper });
}

function renderNodes(parent, fields, context, parentEntry = null) {
    fields.forEach((field) => {
        if (containerTypes.has(field.type)) {
            const container = document.createElement('fieldset');
            const legend = document.createElement('legend');
            const entry = { field, parent: parentEntry, wrapper: container };

            legend.textContent = typeof field.label === 'string' ? field.label : 'Section';
            container.append(legend);
            container.dataset.daisyKitFormsContainer = field.type;
            applyLayout(container, field);

            if (field.type === 'section') {
                container.dataset.daisyKitFormsSection = typeof field.id === 'string' ? field.id : '';
            }

            if (field.type === 'wizardStep') {
                container.dataset.daisyKitFormsStep = typeof field.id === 'string' ? field.id : '';
                context.steps.push(entry);
            }

            context.entries.push(entry);
            parent.append(container);
            renderNodes(container, fieldsFrom({ schema: field }), context, entry);

            return;
        }

        renderField(
            context.root,
            parent,
            field,
            context.values,
            context.errors,
            context.onChange,
            `${context.instanceId}-${context.fieldIndex++}`,
            context.readonly,
            context.entries,
            parentEntry,
        );
    });
}

async function isVisible(root, field, values) {
    const expression = jsonataExpression(field.visibleWhen);

    if (expression === null) {
        return true;
    }

    try {
        return Boolean(await jsonata(expression).evaluate(values));
    } catch {
        emit(root, 'error', { field: field.name, reason: 'invalid-expression' });

        return false;
    }
}

function valueForField(input, field) {
    if (fieldType(field) === 'checkbox' || fieldType(field) === 'toggle') {
        return input.checked;
    }

    if (fieldType(field) === 'number') {
        return input.value === '' ? null : input.valueAsNumber;
    }

    return input.value;
}

function syncValues(root, entries, values) {
    entries.forEach(({ field }) => {
        if (typeof field.name !== 'string' || field.name === '') {
            return;
        }

        const inputs = [...root.querySelectorAll('[name]')].filter((input) => input.getAttribute('name') === field.name);
        const input = fieldType(field) === 'radio'
            ? [...inputs].find((candidate) => candidate.checked)
            : inputs[0];

        if (input instanceof HTMLInputElement || input instanceof HTMLSelectElement || input instanceof HTMLTextAreaElement) {
            values[field.name] = valueForField(input, field);
        }
    });
}

function containsFileField(fields) {
    return fields.some((field) => fieldType(field) === 'file' || containsFileField(fieldsFrom({ schema: field })));
}

function validateStep(step) {
    const controls = [...step.wrapper.querySelectorAll('input, select, textarea')]
        .filter((control) => control instanceof HTMLInputElement || control instanceof HTMLSelectElement || control instanceof HTMLTextAreaElement);
    const invalid = controls.find((control) => control.willValidate && !control.checkValidity());

    if (!invalid) {
        return true;
    }

    invalid.reportValidity();

    return false;
}

function initialize(root, configuration) {
    const form = root.querySelector('[data-daisy-kit-forms-content]');

    if (!(form instanceof HTMLFormElement)) {
        setStatus(root, 'The form mount point is unavailable.', 'error');
        emit(root, 'error', { reason: 'missing-content' });

        return;
    }

    const fields = fieldsFrom(configuration);
    const values = { ...(configuration.value ?? {}) };
    const readonly = configuration.readonly === true || configuration.schema?.readonly === true;
    const mode = submitModes.has(configuration.submitMode)
        ? configuration.submitMode
        : (submitModes.has(configuration.schema?.submit?.mode) ? configuration.schema.submit.mode : 'event');
    const action = typeof configuration.action === 'string'
        ? configuration.action
        : (typeof configuration.schema?.submit?.action === 'string' ? configuration.schema.submit.action : '');
    const method = typeof configuration.method === 'string'
        ? configuration.method.toUpperCase()
        : (typeof configuration.schema?.submit?.method === 'string' ? configuration.schema.submit.method.toUpperCase() : 'POST');
    const validateOn = ['change', 'input', 'submit'].includes(configuration.validateOn)
        ? configuration.validateOn
        : (['change', 'input', 'submit'].includes(configuration.schema?.validateOn) ? configuration.schema.validateOn : 'submit');
    const instanceId = root.dataset.daisyKitFormsInstance ?? createInstanceIdentifier('daisy-kit-forms');
    const context = {
        entries: [],
        errors: errorsFrom(configuration),
        fieldIndex: 0,
        instanceId,
        onChange: null,
        readonly,
        root,
        steps: [],
        values,
    };
    const controlStates = new WeakMap();
    let active = true;
    let currentStep = 0;

    function setEntryInteractivity(entry, interactive) {
        entry.wrapper.querySelectorAll('input, select, textarea').forEach((control) => {
            const state = controlStates.get(control) ?? {
                disabled: control.disabled,
                required: control.required,
            };

            controlStates.set(control, state);
            control.disabled = state.disabled || !interactive;
            control.required = state.required && interactive;
        });
    }

    if (action !== '') {
        form.action = action;
    }

    form.method = method === 'GET' ? 'GET' : 'POST';
    if (!['GET', 'POST'].includes(method)) {
        const methodOverride = document.createElement('input');

        methodOverride.name = '_method';
        methodOverride.type = 'hidden';
        methodOverride.value = method;
        form.append(methodOverride);
    }

    if (containsFileField(fields)) {
        form.enctype = 'multipart/form-data';
    }

    async function refreshVisibility() {
        const visibility = new Map();
        const interactivity = new Map();
        const steps = new Map(context.steps.map((entry, index) => [entry, index]));

        for (const { field } of context.entries) {
            const expression = jsonataExpression(field.computed);

            if (typeof field.name !== 'string' || expression === null) {
                continue;
            }

            try {
                values[field.name] = await jsonata(expression).evaluate(values);
                const input = [...root.querySelectorAll('[name]')].find((candidate) => candidate.getAttribute('name') === field.name);

                if (input instanceof HTMLInputElement || input instanceof HTMLSelectElement || input instanceof HTMLTextAreaElement) {
                    setInputValue(input, field, values[field.name]);
                }
            } catch {
                emit(root, 'error', { field: field.name, reason: 'invalid-computed-expression' });
            }
        }

        for (const entry of context.entries) {
            const parentVisible = entry.parent ? visibility.get(entry.parent) !== false : true;
            const visible = parentVisible && await isVisible(root, entry.field, values);

            visibility.set(entry, visible);
            entry.wrapper.hidden = ! visible;
        }

        context.steps.forEach((entry, index) => {
            entry.wrapper.hidden = visibility.get(entry) === false || index !== currentStep;
        });

        context.entries.forEach((entry) => {
            const parentInteractive = entry.parent ? interactivity.get(entry.parent) === true : true;
            const step = steps.get(entry);
            const interactive = parentInteractive
                && visibility.get(entry) !== false
                && (step === undefined || step === currentStep);

            interactivity.set(entry, interactive);
            setEntryInteractivity(entry, interactive);
        });

        if (active && fields.length > 0) {
            setStatus(root, '', 'ready');
        }
    }

    context.onChange = (event) => {
        const input = event.currentTarget;
        const field = context.entries.find((entry) => entry.field.name === input.name)?.field;

        if (! active || ! field || !(input instanceof HTMLInputElement || input instanceof HTMLSelectElement || input instanceof HTMLTextAreaElement)) {
            return;
        }

        values[field.name] = valueForField(input, field);
        emit(root, 'changed', { name: field.name, value: values[field.name], values: { ...values } });
        if (validateOn === event.type && !input.checkValidity()) {
            input.reportValidity();
            emit(root, 'error', { field: field.name, reason: 'validation-failed' });
        }
        void refreshVisibility();
    };

    if (fields.length === 0) {
        queueMicrotask(() => {
            if (active) {
                setStatus(root, 'No form fields are available.', 'empty');
            }
        });
    } else {
        root.dataset.daisyKitFormsInstance = instanceId;
        root.dataset.daisyKitFormsSubmitMode = mode;
        root.dataset.daisyKitFormsReadonly = readonly ? 'true' : 'false';
        renderNodes(form, fields, context);

        if (! readonly && mode !== 'none') {
            const actions = document.createElement('div');

            actions.dataset.daisyKitFormsActions = '';

            if (context.steps.length > 1) {
                const previous = document.createElement('button');
                const next = document.createElement('button');

                previous.type = 'button';
                previous.textContent = 'Previous';
                previous.dataset.daisyKitFormsPrevious = '';
                previous.addEventListener('click', () => {
                    currentStep = Math.max(0, currentStep - 1);
                    void refreshVisibility();
                    emit(root, 'step-changed', { step: currentStep });
                });
                next.type = 'button';
                next.textContent = 'Next';
                next.dataset.daisyKitFormsNext = '';
                next.addEventListener('click', () => {
                    if (!validateStep(context.steps[currentStep])) {
                        emit(root, 'error', { reason: 'validation-failed', step: currentStep });

                        return;
                    }

                    currentStep = Math.min(context.steps.length - 1, currentStep + 1);
                    void refreshVisibility();
                    emit(root, 'step-changed', { step: currentStep });
                });
                actions.append(previous, next);
            }

            const submit = document.createElement('button');

            submit.type = 'submit';
            submit.textContent = typeof configuration.schema?.submit?.label === 'string'
                ? configuration.schema.submit.label
                : 'Submit';
            actions.append(submit);
            form.append(actions);
        }

        void refreshVisibility();
    }

    const onSubmit = async (event) => {
        if (readonly || mode === 'none') {
            event.preventDefault();

            return;
        }

        if (mode === 'html') {
            return;
        }

        event.preventDefault();
        syncValues(root, context.entries, values);

        if (! form.checkValidity()) {
            form.reportValidity();
            emit(root, 'error', { reason: 'validation-failed' });

            return;
        }

        if (mode === 'fetch') {
            try {
                const response = await fetch(action, {
                    body: new FormData(form),
                    method,
                });

                emit(root, 'submitted', { mode, status: response.status, values: { ...values } });
            } catch {
                emit(root, 'error', { reason: 'submission-failed' });
            }

            return;
        }

        emit(root, 'submitted', { mode, values: { ...values } });
    };

    form.addEventListener('submit', onSubmit);

    runtimeApis.set(root, {
        destroy: () => unmount(root),
        getValue: () => {
            syncValues(root, context.entries, values);

            return { ...values };
        },
        validate: () => {
            syncValues(root, context.entries, values);

            if (form.checkValidity()) {
                return true;
            }

            form.reportValidity();
            emit(root, 'error', { reason: 'validation-failed' });

            return false;
        },
    });

    return () => {
        active = false;
        runtimeApis.delete(root);
        form.removeEventListener('submit', onSubmit);
        form.replaceChildren();
    };
}

const module = createMountable('forms-viewer', initialize);

export function mount(root) {
    const instance = module.mount(root);

    return runtimeApis.get(root) ?? instance;
}

export function mountAll(scope = document) {
    return [...scope.querySelectorAll('[data-daisy-kit-module="forms-viewer"]')].map(mount);
}

export function unmount(root) {
    module.unmount(root);
}

export function installLivewireAdapter() {
    return createLivewireAdapter('forms-viewer', mountAll, unmount);
}
