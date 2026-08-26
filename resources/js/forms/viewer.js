import jsonata from 'jsonata';

import '../../css/forms-viewer.css';
import { createInstanceIdentifier } from '../core/identifiers.js';
import { createMountable, installLivewireAdapter as createLivewireAdapter } from '../core/mountable.js';

const controlTypes = new Set(['checkbox', 'date', 'email', 'file', 'number', 'radio', 'select', 'textarea', 'text']);
const containerTypes = new Set(['section', 'wizardStep']);
const submitModes = new Set(['event', 'fetch', 'html', 'none']);

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

    if (fieldType(field) === 'checkbox') {
        input.checked = value === true;

        return;
    }

    input.value = value ?? '';
}

function setReadonly(input, field, readonly) {
    if (! readonly && field.readonly !== true && typeof field.computed !== 'string') {
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
    input.required = field.required === true;

    if (type !== 'textarea' && type !== 'select') {
        input.type = type;
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

    setInputValue(input, field, value);
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

function renderField(root, parent, field, values, errors, onChange, fieldId, readonly, entries, parentEntry) {
    if (typeof field.name !== 'string' || field.name === '') {
        return;
    }

    const wrapper = document.createElement('div');
    const label = document.createElement('label');
    const messages = errorMessages(errors, field.name);
    const control = createControl(field, values[field.name], fieldId, readonly);
    const inputs = control instanceof HTMLInputElement || control instanceof HTMLSelectElement || control instanceof HTMLTextAreaElement
        ? [control]
        : [...control.querySelectorAll('input')];

    wrapper.dataset.daisyKitFormsField = field.name;
    label.textContent = typeof field.label === 'string' ? field.label : field.name;

    if (inputs.length === 1) {
        label.htmlFor = inputs[0].id;
    }

    if (hasUnsupportedType(field)) {
        const typeError = document.createElement('p');

        typeError.dataset.daisyKitFormsTypeError = field.name;
        typeError.setAttribute('role', 'alert');
        typeError.textContent = `Unsupported field type "${field.type}" was rendered as text.`;
        wrapper.append(typeError);
        emit(root, 'error', { field: field.name, reason: 'unsupported-type', type: field.type });
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
    if (typeof field.visibleWhen !== 'string' || field.visibleWhen.trim() === '') {
        return true;
    }

    try {
        return Boolean(await jsonata(field.visibleWhen).evaluate(values));
    } catch {
        emit(root, 'error', { field: field.name, reason: 'invalid-expression' });

        return false;
    }
}

function valueForField(input, field) {
    if (fieldType(field) === 'checkbox') {
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
    let active = true;
    let currentStep = 0;

    async function refreshVisibility() {
        const visibility = new Map();

        for (const { field } of context.entries) {
            if (typeof field.name !== 'string' || typeof field.computed !== 'string' || field.computed.trim() === '') {
                continue;
            }

            try {
                values[field.name] = await jsonata(field.computed).evaluate(values);
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
            const action = typeof configuration.schema?.submit?.action === 'string'
                ? configuration.schema.submit.action
                : form.action;
            const method = typeof configuration.schema?.submit?.method === 'string'
                ? configuration.schema.submit.method
                : 'POST';

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

    return () => {
        active = false;
        form.removeEventListener('submit', onSubmit);
        form.replaceChildren();
    };
}

const module = createMountable('forms-viewer', initialize);

export const { mount, mountAll, unmount } = module;

export function installLivewireAdapter() {
    return createLivewireAdapter('forms-viewer', mountAll, unmount);
}
