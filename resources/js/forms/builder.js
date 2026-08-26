import '../../css/forms-builder.css';
import { createMountable, installLivewireAdapter as createLivewireAdapter } from '../core/mountable.js';

const fieldTypes = ['text', 'email', 'number', 'date', 'textarea', 'checkbox', 'select'];

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:forms-builder:${name}`, { bubbles: true, detail }));
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

function normalizedSchema(configuration) {
    const fields = configuration.schema?.fields;

    return {
        fields: Array.isArray(fields)
            ? fields.filter((field) => field && typeof field === 'object').map((field) => ({
                name: typeof field.name === 'string' ? field.name : '',
                label: typeof field.label === 'string' ? field.label : '',
                type: fieldTypes.includes(field.type) ? field.type : 'text',
            }))
            : [],
    };
}

function input(labelText, value, attribute) {
    const label = document.createElement('label');
    const control = document.createElement('input');
    label.textContent = labelText;
    control.type = 'text';
    control.value = value;
    control.dataset.daisyKitBuilderField = attribute;
    label.append(control);

    return { control, label };
}

function fieldset(field, index, onChange) {
    const group = document.createElement('fieldset');
    const legend = document.createElement('legend');
    const name = input('Name', field.name, 'name');
    const label = input('Label', field.label, 'label');
    const typeLabel = document.createElement('label');
    const type = document.createElement('select');

    group.dataset.daisyKitBuilderIndex = String(index);
    legend.textContent = `Field ${index + 1}`;
    typeLabel.textContent = 'Type';
    type.dataset.daisyKitBuilderField = 'type';
    fieldTypes.forEach((fieldType) => {
        const option = document.createElement('option');
        option.value = fieldType;
        option.textContent = fieldType;
        option.selected = field.type === fieldType;
        type.append(option);
    });

    [name.control, label.control, type].forEach((control) => {
        control.addEventListener('input', onChange);
        control.addEventListener('change', onChange);
    });
    label.control.dataset.daisyKitBuilderLabel = '';
    typeLabel.append(type);
    group.append(legend, name.label, label.label, typeLabel);

    return group;
}

function initialize(root, configuration) {
    const content = root.querySelector('[data-daisy-kit-forms-builder-content]');

    if (!(content instanceof HTMLElement)) {
        setStatus(root, 'The form builder mount point is unavailable.', 'error');
        emit(root, 'error', { reason: 'missing-content' });

        return;
    }

    const schema = normalizedSchema(configuration);
    let active = true;

    function notify() {
        if (! active) {
            return;
        }

        emit(root, 'changed', { schema: structuredClone(schema) });
    }

    function render() {
        if (! active) {
            return;
        }

        content.replaceChildren();

        if (schema.fields.length === 0) {
            setStatus(root, 'No form fields are available.', 'empty');
        } else {
            setStatus(root, '', 'ready');
            schema.fields.forEach((field, index) => {
                content.append(fieldset(field, index, (event) => {
                    if (! active) {
                        return;
                    }

                    const control = event.currentTarget;
                    const targetField = schema.fields[index];

                    if (!(control instanceof HTMLInputElement || control instanceof HTMLSelectElement) || ! targetField) {
                        return;
                    }

                    targetField[control.dataset.daisyKitBuilderField] = control.value;
                    notify();
                }));
            });
        }

        const add = document.createElement('button');
        add.type = 'button';
        add.textContent = 'Add field';
        add.addEventListener('click', () => {
            if (! active) {
                return;
            }

            schema.fields.push({ name: '', label: '', type: 'text' });
            render();
            notify();
        });
        content.append(add);
    }

    queueMicrotask(render);

    return () => {
        active = false;
        content.replaceChildren();
    };
}

const module = createMountable('forms-builder', initialize);

export const { mount, mountAll, unmount } = module;

export function installLivewireAdapter() {
    return createLivewireAdapter('forms-builder', mountAll, unmount);
}
