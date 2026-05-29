/**
 * Client-side form schema builder controller.
 *
 * Blade owns the builder markup through `<template data-builder-template="...">` fragments.
 * This module only mutates schema state, clones templates, wires events, and emits changes.
 *
 * @module form-kit/builder
 */

import { DEFAULT_FIELD_DEFINITIONS, createField, normalizeOption } from './fields.js';
import { canonicalizeSchema, createDefaultSchema, flattenFields, validateSchema } from './schema.js';
import { compileExpression, registerCatalog } from './jsonata-engine.js';

/**
 * Creates a builder controller bound to a DOM root that exposes `data-builder-*` regions.
 *
 * @param {HTMLElement} root - Container element (typically `[data-module="form-builder"]`).
 * @param {Object} [options={}] - Initial configuration.
 * @param {Object} [options.schema] - Starting Daisy form schema; defaults via {@link createDefaultSchema}.
 * @param {Array<{type: string, label?: string}>} [options.fieldTypes] - Palette entries; defaults to built-in field types.
 * @param {Array<Object>} [options.functionCatalog] - JSONata function catalog entries for registration/display.
 * @returns {{addField: Function, addSection: Function, addStep: Function, deleteField: Function, moveField: Function, render: Function, state: Object}}
 */
export function createFormBuilder(root, options = {}) {
    const state = {
        schema: canonicalizeSchema(options.schema ?? createDefaultSchema()),
        selectedId: null,
        fieldTypes: Array.isArray(options.fieldTypes) ? options.fieldTypes : DEFAULT_FIELD_DEFINITIONS,
        functionCatalog: registerCatalog(options.functionCatalog ?? []),
        diagnostics: [],
    };

    const elements = {
        palette: root.querySelector('[data-builder-palette]'),
        outline: root.querySelector('[data-builder-outline]'),
        inspector: root.querySelector('[data-builder-inspector]'),
        preview: root.querySelector('[data-builder-preview]'),
        json: root.querySelector('[data-builder-json]'),
        hidden: root.querySelector('[data-builder-hidden]'),
        diagnostics: root.querySelector('[data-builder-diagnostics]'),
        functions: root.querySelector('[data-builder-functions]'),
        templates: {
            paletteItem: root.querySelector('template[data-builder-template="palette-item"]'),
            outlineItem: root.querySelector('template[data-builder-template="outline-item"]'),
            inspectorEmpty: root.querySelector('template[data-builder-template="inspector-empty"]'),
            inspectorInput: root.querySelector('template[data-builder-template="inspector-input"]'),
            inspectorTextarea: root.querySelector('template[data-builder-template="inspector-textarea"]'),
            previewField: root.querySelector('template[data-builder-template="preview-field"]'),
            functionItem: root.querySelector('template[data-builder-template="function-item"]'),
            diagnosticItem: root.querySelector('template[data-builder-template="diagnostic-item"]'),
        },
    };

    async function render() {
        state.schema = canonicalizeSchema(state.schema);
        state.diagnostics = await collectDiagnostics(state.schema);

        renderPalette(elements.palette, state, addField, elements.templates.paletteItem);
        renderOutline(elements.outline, state, selectField, moveField, deleteField, elements.templates.outlineItem);
        renderInspector(elements.inspector, state, updateSelectedField, elements.templates);
        renderPreview(elements.preview, state, elements.templates.previewField);
        renderFunctions(elements.functions, state.functionCatalog, elements.templates.functionItem);
        syncJson(elements, state);
        renderDiagnostics(elements.diagnostics, state.diagnostics, elements.templates.diagnosticItem);
        dispatch('daisy-form-builder:change', { schema: state.schema, diagnostics: state.diagnostics });

        if (state.diagnostics.length > 0) {
            dispatch('daisy-form-builder:invalid', { diagnostics: state.diagnostics });
        }
    }

    function dispatch(name, detail) {
        root.dispatchEvent(new CustomEvent(name, { detail, bubbles: true }));
    }

    function addField(type) {
        const field = createField(type, nextFieldIndex(state.schema));
        addFieldToSelectedContainer(state, field);
        state.selectedId = field.id;
        void render();
    }

    function addSection(label = 'Section') {
        const section = createField('section', nextFieldIndex(state.schema));
        section.label = label;
        section.fields = [];

        if (state.schema.layout?.type === 'multi-step') {
            const step = selectedField(state.schema, state.selectedId)?.type === 'wizardStep'
                ? selectedField(state.schema, state.selectedId)
                : firstFieldOfType(state.schema, 'wizardStep');

            if (step) {
                state.schema.fields = appendFieldToTree(state.schema.fields, step.id, section);
            } else {
                state.schema.fields.push(section);
            }
        } else {
            state.schema.fields.push(section);
        }

        state.selectedId = section.id;
        void render();
    }

    function addStep(label = 'Step') {
        state.schema.layout = { ...(state.schema.layout ?? {}), type: 'multi-step' };

        const step = createField('wizardStep', nextFieldIndex(state.schema));
        step.label = label;
        step.fields = [];
        state.schema.fields.push(step);
        state.selectedId = step.id;
        void render();
    }

    function selectField(id) {
        state.selectedId = id;
        void render();
    }

    function updateSelectedField(updates) {
        state.schema.fields = mapFieldTree(state.schema.fields, (field) => {
            if (field.id !== state.selectedId) {
                return field;
            }

            return canonicalizeFieldUpdate({ ...field, ...updates });
        });

        if (updates.id) {
            state.selectedId = updates.id;
        }

        void render();
    }

    function deleteField(id) {
        state.schema.fields = removeFieldFromTree(state.schema.fields, id);

        if (state.selectedId === id) {
            state.selectedId = state.schema.fields[0]?.id ?? null;
        }

        void render();
    }

    function moveField(id, direction) {
        const index = state.schema.fields.findIndex((field) => field.id === id);
        const target = index + direction;

        if (index < 0 || target < 0 || target >= state.schema.fields.length) {
            return;
        }

        const fields = [...state.schema.fields];
        const [field] = fields.splice(index, 1);
        fields.splice(target, 0, field);
        state.schema.fields = fields;
        void render();
    }

    if (elements.json) {
        elements.json.addEventListener('change', () => {
            try {
                state.schema = canonicalizeSchema(JSON.parse(elements.json.value));
                state.selectedId = state.schema.fields[0]?.id ?? null;
            } catch (error) {
                state.diagnostics = [{ code: 'json_parse_error', message: error.message }];
                renderDiagnostics(elements.diagnostics, state.diagnostics, elements.templates.diagnosticItem);

                return;
            }

            void render();
        });
    }

    state.selectedId = state.schema.fields[0]?.id ?? null;
    void render();

    return {
        addField,
        addSection,
        addStep,
        deleteField,
        moveField,
        render,
        state,
    };
}

function addFieldToSelectedContainer(state, field) {
    const selected = selectedField(state.schema, state.selectedId);

    if (selected && ['section', 'wizardStep', 'tabs'].includes(selected.type)) {
        state.schema.fields = appendFieldToTree(state.schema.fields, selected.id, field);

        return;
    }

    const firstSection = firstFieldOfType(state.schema, 'section');

    if (firstSection) {
        state.schema.fields = appendFieldToTree(state.schema.fields, firstSection.id, field);

        return;
    }

    const firstStep = firstFieldOfType(state.schema, 'wizardStep');

    if (state.schema.layout?.type === 'multi-step' && firstStep) {
        state.schema.fields = appendFieldToTree(state.schema.fields, firstStep.id, field);

        return;
    }

    state.schema.fields.push(field);
}

function selectedField(schema, selectedId) {
    return flattenFields(schema.fields).find((field) => field.id === selectedId) ?? null;
}

function firstFieldOfType(schema, type) {
    return flattenFields(schema.fields).find((field) => field.type === type) ?? null;
}

function nextFieldIndex(schema) {
    return flattenFields(schema.fields).length + 1;
}

function mapFieldTree(fields, callback) {
    return fields.map((field) => {
        const mapped = callback(field);

        if (Array.isArray(mapped.fields)) {
            return {
                ...mapped,
                fields: mapFieldTree(mapped.fields, callback),
            };
        }

        return mapped;
    });
}

function removeFieldFromTree(fields, id) {
    return fields
        .filter((field) => field.id !== id)
        .map((field) => ({
            ...field,
            fields: Array.isArray(field.fields) ? removeFieldFromTree(field.fields, id) : field.fields,
        }));
}

function appendFieldToTree(fields, containerId, child) {
    return fields.map((field) => {
        if (field.id === containerId) {
            return {
                ...field,
                fields: [...(field.fields ?? []), child],
            };
        }

        if (Array.isArray(field.fields)) {
            return {
                ...field,
                fields: appendFieldToTree(field.fields, containerId, child),
            };
        }

        return field;
    });
}

function renderPalette(container, state, addField, template) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    state.fieldTypes.forEach((fieldType) => {
        const button = cloneTemplateElement(template, 'button');
        button.dataset.builderAdd = fieldType.type;
        setTemplateText(button, fieldType.label ?? fieldType.type);
        button.addEventListener('click', () => addField(fieldType.type));
        container.appendChild(button);
    });
}

function renderOutline(container, state, selectField, moveField, deleteField, template) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    renderOutlineFields(container, state.schema.fields, state, selectField, moveField, deleteField, template);
}

function renderOutlineFields(container, fields, state, selectField, moveField, deleteField, template, depth = 0) {
    fields.forEach((field) => {
        const item = cloneTemplateElement(template, 'div');
        item.dataset.builderField = field.id;
        item.classList.toggle('ring-2', state.selectedId === field.id);
        item.classList.toggle('ring-primary', state.selectedId === field.id);
        item.style.marginInlineStart = depth > 0 ? `${depth * 0.75}rem` : '';

        const select = item.querySelector('[data-builder-select]') ?? item;
        setTemplateText(select, `${field.label ?? field.id} (${field.type})`);
        select.addEventListener('click', () => selectField(field.id));

        [
            ['up', -1],
            ['down', 1],
        ].forEach(([action, direction]) => {
            const button = item.querySelector(`[data-builder-move="${action}"]`);

            if (button) {
                button.addEventListener('click', () => moveField(field.id, direction));
            }
        });

        const remove = item.querySelector('[data-builder-delete]');

        if (remove) {
            remove.dataset.builderDelete = field.id;
            remove.addEventListener('click', () => deleteField(field.id));
        }

        container.appendChild(item);

        if (Array.isArray(field.fields)) {
            renderOutlineFields(container, field.fields, state, selectField, moveField, deleteField, template, depth + 1);
        }
    });
}

function renderInspector(container, state, updateSelectedField, templates) {
    if (!container) {
        return;
    }

    const field = selectedField(state.schema, state.selectedId);
    container.innerHTML = '';

    if (!field) {
        container.appendChild(cloneTemplateElement(templates.inspectorEmpty, 'p'));

        return;
    }

    [
        ['id', 'Id', field.id],
        ['name', 'Name', field.name ?? ''],
        ['label', 'Label', field.label ?? ''],
    ].forEach(([key, label, value]) => {
        container.appendChild(createTextInput(templates.inspectorInput, label, value, (nextValue) => updateSelectedField({ [key]: nextValue })));
    });

    container.appendChild(createTextarea(templates.inspectorTextarea, 'Options JSON', JSON.stringify(field.options ?? [], null, 2), (nextValue) => {
        try {
            const options = JSON.parse(nextValue).map(normalizeOption).filter(Boolean);
            updateSelectedField({ options });
        } catch (_) {
            // Invalid JSON while typing is ignored until the author fixes the textarea payload.
        }
    }));

    container.appendChild(createTextarea(templates.inspectorTextarea, 'Visible when JSONata', field.visibleWhen?.expression ?? '', (expression) => {
        updateSelectedField({
            visibleWhen: expression.trim()
                ? { type: 'jsonata', expression, dependsOn: field.visibleWhen?.dependsOn ?? [] }
                : undefined,
        });
    }));

    container.appendChild(createTextInput(templates.inspectorInput, 'Visible dependsOn CSV', (field.visibleWhen?.dependsOn ?? []).join(','), (value) => {
        updateSelectedField({
            visibleWhen: {
                type: 'jsonata',
                expression: field.visibleWhen?.expression ?? '',
                dependsOn: splitCsv(value),
            },
        });
    }));

    container.appendChild(createTextarea(templates.inspectorTextarea, 'Computed JSONata', field.computed?.expression ?? '', (expression) => {
        updateSelectedField({
            computed: expression.trim()
                ? { type: 'jsonata', expression, dependsOn: field.computed?.dependsOn ?? [], mode: field.computed?.mode ?? 'readonly' }
                : undefined,
        });
    }));
}

function renderPreview(container, state, template) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    flattenFields(state.schema.fields)
        .filter((field) => !['section', 'tabs', 'wizardStep', 'hidden', 'staticText'].includes(field.type))
        .forEach((field) => {
            const wrapper = cloneTemplateElement(template, 'label');
            setTemplateText(wrapper, field.label ?? field.id);

            const input = wrapper.querySelector('[data-builder-preview-input]');
            const textarea = wrapper.querySelector('[data-builder-preview-textarea]');

            if (textarea && input && field.type === 'textarea') {
                input.classList.add('hidden');
                textarea.classList.remove('hidden');
                textarea.placeholder = field.type;
            } else if (input) {
                input.placeholder = field.type;
                input.type = ['email', 'tel', 'url', 'password', 'number', 'date', 'time', 'datetime-local', 'month', 'color'].includes(field.type) ? field.type : 'text';
            }

            container.appendChild(wrapper);
        });
}

function renderFunctions(container, catalog, template) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    catalog.forEach((definition) => {
        const item = cloneTemplateElement(template, 'li');
        setTemplateText(item, `${definition.name} ${definition.signature ?? ''}`.trim());
        container.appendChild(item);
    });
}

function renderDiagnostics(container, diagnostics, template) {
    if (!container) {
        return;
    }

    container.innerHTML = '';
    container.classList.toggle('hidden', diagnostics.length === 0);

    diagnostics.forEach((diagnostic) => {
        const item = cloneTemplateElement(template, 'li');
        setTemplateText(item, diagnostic.message);
        container.appendChild(item);
    });
}

function syncJson(elements, state) {
    const value = JSON.stringify(state.schema, null, 2);

    if (elements.json && elements.json.value !== value) {
        elements.json.value = value;
    }

    if (elements.hidden) {
        elements.hidden.value = value;
    }
}

async function collectDiagnostics(schema) {
    const validation = validateSchema(schema);
    const diagnostics = [...validation.errors];

    for (const field of flattenFields(schema.fields)) {
        for (const expression of [field.visibleWhen, field.computed, ...(field.rules ?? []).filter((rule) => rule?.type === 'jsonata')]) {
            if (!expression?.expression) {
                continue;
            }

            try {
                await compileExpression(expression.expression);
            } catch (error) {
                diagnostics.push({
                    code: 'syntax_error',
                    message: `Expression error on "${field.id}": ${error.message}`,
                });
            }
        }
    }

    return diagnostics;
}

function createTextInput(template, label, value, onChange) {
    const wrapper = cloneTemplateElement(template, 'label');
    const input = ensureControl(wrapper, 'input');
    setTemplateText(wrapper, label);
    input.value = value;
    input.addEventListener('change', () => onChange(input.value));

    return wrapper;
}

function createTextarea(template, label, value, onChange) {
    const wrapper = cloneTemplateElement(template, 'label');
    const input = ensureControl(wrapper, 'textarea');
    setTemplateText(wrapper, label);
    input.value = value;
    input.addEventListener('change', () => onChange(input.value));

    return wrapper;
}

function canonicalizeFieldUpdate(field) {
    return Object.fromEntries(Object.entries(field).filter(([, value]) => value !== undefined));
}

function splitCsv(value) {
    return String(value ?? '')
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
}

function cloneTemplateElement(template, fallbackTag) {
    if (template instanceof HTMLTemplateElement) {
        const element = template.content.firstElementChild?.cloneNode(true);

        if (element instanceof HTMLElement) {
            return element;
        }
    }

    return document.createElement(fallbackTag);
}

function setTemplateText(element, value) {
    const target = element.querySelector('[data-builder-label]') ?? element;
    target.textContent = String(value ?? '');
}

function ensureControl(wrapper, tagName) {
    const existing = wrapper.querySelector('[data-builder-control]');

    if (existing instanceof HTMLInputElement || existing instanceof HTMLTextAreaElement) {
        return existing;
    }

    const control = document.createElement(tagName);
    control.dataset.builderControl = '';
    wrapper.appendChild(control);

    return control;
}

/**
 * Same as {@link createFormBuilder}, but stores the instance on `root.__daisyFormBuilder`.
 *
 * @param {HTMLElement} root - Builder container element.
 * @param {Object} [options={}] - Passed through to {@link createFormBuilder}.
 * @returns {ReturnType<typeof createFormBuilder>}
 */
export function mountFormBuilder(root, options = {}) {
    const builder = createFormBuilder(root, options);
    root.__daisyFormBuilder = builder;

    return builder;
}
