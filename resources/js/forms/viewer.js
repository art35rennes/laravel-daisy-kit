import jsonata from 'jsonata';

import '../../css/forms-viewer.css';
import { createInstanceIdentifier } from '../core/identifiers.js';
import { createMountable, installLivewireAdapter as createLivewireAdapter } from '../core/mountable.js';

const supportedTypes = new Set(['checkbox', 'date', 'email', 'number', 'select', 'textarea', 'text']);

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:forms-viewer:${name}`, { bubbles: true, detail }));
}

function fieldType(field) {
    return supportedTypes.has(field.type) ? field.type : 'text';
}

function fieldsFrom(configuration) {
    const fields = configuration.schema?.fields;

    return Array.isArray(fields) ? fields.filter((field) => field && typeof field === 'object') : [];
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
    if (fieldType(field) === 'checkbox') {
        input.checked = value === true;

        return;
    }

    input.value = value ?? '';
}

function createInput(field, value, fieldId) {
    const type = fieldType(field);
    const input = type === 'textarea' ? document.createElement('textarea') : document.createElement(type === 'select' ? 'select' : 'input');

    input.name = field.name;
    input.id = fieldId;
    input.required = field.required === true;

    if (type !== 'textarea' && type !== 'select') {
        input.type = type;
    }

    if (type === 'select') {
        const options = Array.isArray(field.options) ? field.options : [];

        options.forEach((option) => {
            const element = document.createElement('option');
            const optionValue = typeof option === 'object' && option !== null ? option.value : option;
            const optionLabel = typeof option === 'object' && option !== null ? option.label : option;
            element.value = String(optionValue ?? '');
            element.textContent = String(optionLabel ?? optionValue ?? '');
            input.append(element);
        });
    }

    setInputValue(input, field, value);

    return input;
}

function renderField(form, field, values, onChange, fieldId) {
    if (typeof field.name !== 'string' || field.name === '') {
        return;
    }

    const wrapper = document.createElement('div');
    const label = document.createElement('label');
    const input = createInput(field, values[field.name], fieldId);

    wrapper.dataset.daisyKitFormsField = field.name;
    label.htmlFor = input.id;
    label.textContent = typeof field.label === 'string' ? field.label : field.name;
    input.addEventListener('input', onChange);
    input.addEventListener('change', onChange);
    wrapper.append(label, input);
    form.append(wrapper);
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

function initialize(root, configuration) {
    const form = root.querySelector('[data-daisy-kit-forms-content]');

    if (!(form instanceof HTMLFormElement)) {
        setStatus(root, 'The form mount point is unavailable.', 'error');
        emit(root, 'error', { reason: 'missing-content' });

        return;
    }

    const fields = fieldsFrom(configuration);
    const values = { ...(configuration.value ?? {}) };
    const fieldWrappers = new Map();
    const instanceId = root.dataset.daisyKitFormsInstance ?? createInstanceIdentifier('daisy-kit-forms');
    let active = true;

    async function refreshVisibility() {
        for (const field of fields) {
            const wrapper = fieldWrappers.get(field.name);

            if (! active || ! wrapper) {
                continue;
            }

            wrapper.hidden = ! await isVisible(root, field, values);
        }

        if (active && fields.length > 0) {
            setStatus(root, '', 'ready');
        }
    }

    if (fields.length === 0) {
        queueMicrotask(() => {
            if (active) {
                setStatus(root, 'No form fields are available.', 'empty');
            }
        });
    } else {
        root.dataset.daisyKitFormsInstance = instanceId;

        fields.forEach((field, index) => {
            renderField(form, field, values, (event) => {
                const input = event.currentTarget;

                if (!(input instanceof HTMLInputElement || input instanceof HTMLSelectElement || input instanceof HTMLTextAreaElement)) {
                    return;
                }

                values[field.name] = valueForField(input, field);
                emit(root, 'changed', { name: field.name, value: values[field.name], values: { ...values } });
                void refreshVisibility();
            }, `${instanceId}-${index}`);

            const wrapper = form.lastElementChild;

            if (wrapper instanceof HTMLElement) {
                wrapper.hidden = typeof field.visibleWhen === 'string' && field.visibleWhen.trim() !== '';
                fieldWrappers.set(field.name, wrapper);
            }
        });

        const submit = document.createElement('button');
        submit.type = 'submit';
        submit.textContent = typeof configuration.schema?.submitLabel === 'string'
            ? configuration.schema.submitLabel
            : 'Submit';
        form.append(submit);
        void refreshVisibility();
    }

    const onSubmit = (event) => {
        event.preventDefault();
        emit(root, 'submitted', { values: { ...values } });
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
