/**
 * Client-side form schema builder: palette of field types, field outline with reorder/remove,
 * inspector for ids and JSONata hooks, live JSON mirror, and compile-time diagnostics.
 *
 * @module form-kit/builder
 */

import { DEFAULT_FIELD_DEFINITIONS, createField, normalizeOption } from './fields.js';
import { canonicalizeSchema, createDefaultSchema, validateSchema } from './schema.js';
import { compileExpression, registerCatalog } from './jsonata-engine.js';

/**
 * Creates a builder controller bound to a DOM root that exposes `data-builder-*` regions.
 *
 * @param {HTMLElement} root - Container element (typically `[data-module="form-builder"]`).
 * @param {Object} [options={}] - Initial configuration.
 * @param {Object} [options.schema] - Starting Daisy form schema; defaults via {@link createDefaultSchema}.
 * @param {Array<{type: string, label?: string}>} [options.fieldTypes] - Palette entries; defaults to built-in field types.
 * @param {Array<Object>} [options.functionCatalog] - JSONata function catalog entries for registration/display.
 * @returns {{addField: Function, deleteField: Function, moveField: Function, render: Function, state: Object}}
 */
export function createFormBuilder(root, options = {}) {
    // Single mutable snapshot shared across palette/outline/inspector; always re-canonicalized on render.
    const state = {
        schema: canonicalizeSchema(options.schema ?? createDefaultSchema()),
        selectedId: null,
        fieldTypes: Array.isArray(options.fieldTypes) ? options.fieldTypes : DEFAULT_FIELD_DEFINITIONS,
        functionCatalog: registerCatalog(options.functionCatalog ?? []),
        diagnostics: [],
    };

    // Regions are optional so hosts may omit preview/json/aside blocks without breaking init.
    const elements = {
        palette: root.querySelector('[data-builder-palette]'),
        outline: root.querySelector('[data-builder-outline]'),
        inspector: root.querySelector('[data-builder-inspector]'),
        preview: root.querySelector('[data-builder-preview]'),
        json: root.querySelector('[data-builder-json]'),
        hidden: root.querySelector('[data-builder-hidden]'),
        diagnostics: root.querySelector('[data-builder-diagnostics]'),
        functions: root.querySelector('[data-builder-functions]'),
    };

    /**
     * Recomputes diagnostics, redraws all panels, syncs hidden/json outputs, and emits change events.
     *
     * @returns {Promise<void>}
     */
    async function render() {
        state.schema = canonicalizeSchema(state.schema);
        state.diagnostics = await collectDiagnostics(state.schema);

        // Full repaint strategy keeps DOM and Blade conventions aligned without incremental diffing.
        renderPalette(elements.palette, state, addField);
        renderOutline(elements.outline, state, selectField, moveField, deleteField);
        renderInspector(elements.inspector, state, updateSelectedField);
        renderPreview(elements.preview, state);
        renderFunctions(elements.functions, state.functionCatalog);
        syncJson(elements, state);
        renderDiagnostics(elements.diagnostics, state.diagnostics);
        dispatch('daisy-form-builder:change', { schema: state.schema, diagnostics: state.diagnostics });

        // Separate invalid event lets hosts gate autosave pipelines without parsing diagnostics arrays.
        if (state.diagnostics.length > 0) {
            dispatch('daisy-form-builder:invalid', { diagnostics: state.diagnostics });
        }
    }

    function dispatch(name, detail) {
        root.dispatchEvent(new CustomEvent(name, { detail, bubbles: true }));
    }

    function addField(type) {
        const field = createField(type, state.schema.fields.length + 1);
        state.schema.fields.push(field);
        state.selectedId = field.id;
        void render();
    }

    function selectField(id) {
        state.selectedId = id;
        void render();
    }

    function updateSelectedField(updates) {
        state.schema.fields = state.schema.fields.map((field) => {
            if (field.id !== state.selectedId) {
                return field;
            }

            // Strip undefined so clearing optional JSONata blocks removes keys instead of serializing null.
            return canonicalizeFieldUpdate({ ...field, ...updates });
        });

        // Renaming id updates outline dataset hooks; keep selection consistent with the renamed row.
        if (updates.id) {
            state.selectedId = updates.id;
        }

        void render();
    }

    function deleteField(id) {
        state.schema.fields = state.schema.fields.filter((field) => field.id !== id);

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

        // Swap positions immutably so reactive watchers observing `fields` references still behave predictably.
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
                // Avoid wiping author edits: surface parse failure locally without rewriting the textarea via syncJson.
                state.diagnostics = [{ code: 'json_parse_error', message: error.message }];
                renderDiagnostics(elements.diagnostics, state.diagnostics);

                return;
            }

            void render();
        });
    }

    state.selectedId = state.schema.fields[0]?.id ?? null;
    void render();

    return {
        addField,
        deleteField,
        moveField,
        render,
        state,
    };
}

/**
 * @param {HTMLElement|null} container - `[data-builder-palette]` region.
 * @param {{ fieldTypes: Array<{type: string, label?: string}> }} state - Builder state slice.
 * @param {(type: string) => void} addField - Adds a field of the given type.
 * @returns {void}
 */
function renderPalette(container, state, addField) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    state.fieldTypes.forEach((fieldType) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm justify-start';
        button.textContent = fieldType.label ?? fieldType.type;
        button.dataset.builderAdd = fieldType.type;
        button.addEventListener('click', () => addField(fieldType.type));
        container.appendChild(button);
    });
}

/**
 * @param {HTMLElement|null} container - `[data-builder-outline]` region.
 * @returns {void}
 */
function renderOutline(container, state, selectField, moveField, deleteField) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    state.schema.fields.forEach((field) => {
        const item = document.createElement('div');
        item.className = `flex items-center gap-2 rounded-box border border-base-300 bg-base-100 p-2 ${state.selectedId === field.id ? 'ring-2 ring-primary' : ''}`;
        item.dataset.builderField = field.id;

        const select = document.createElement('button');
        select.type = 'button';
        select.className = 'btn btn-ghost btn-sm flex-1 justify-start';
        select.textContent = `${field.label ?? field.id} (${field.type})`;
        select.addEventListener('click', () => selectField(field.id));
        item.appendChild(select);

        [
            ['up', -1, '↑'],
            ['down', 1, '↓'],
        ].forEach(([action, direction, label]) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-ghost btn-xs';
            button.dataset.builderMove = action;
            button.textContent = label;
            button.addEventListener('click', () => moveField(field.id, direction));
            item.appendChild(button);
        });

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-ghost btn-xs text-error';
        remove.dataset.builderDelete = field.id;
        remove.textContent = '×';
        remove.addEventListener('click', () => deleteField(field.id));
        item.appendChild(remove);

        container.appendChild(item);
    });
}

/**
 * @param {HTMLElement|null} container - `[data-builder-inspector]` region.
 * @returns {void}
 */
function renderInspector(container, state, updateSelectedField) {
    if (!container) {
        return;
    }

    const field = state.schema.fields.find((item) => item.id === state.selectedId);
    container.innerHTML = '';

    if (!field) {
        container.textContent = 'Select a field.';

        return;
    }

    [
        ['id', 'Id', field.id],
        ['name', 'Name', field.name ?? ''],
        ['label', 'Label', field.label ?? ''],
    ].forEach(([key, label, value]) => {
        container.appendChild(createTextInput(label, value, (nextValue) => updateSelectedField({ [key]: nextValue })));
    });

    container.appendChild(createTextarea('Options JSON', JSON.stringify(field.options ?? [], null, 2), (nextValue) => {
        try {
            const options = JSON.parse(nextValue).map(normalizeOption).filter(Boolean);
            updateSelectedField({ options });
        } catch (_) {
            // Invalid JSON while typing is ignored until the author fixes the textarea payload.
        }
    }));

    container.appendChild(createTextarea('Visible when JSONata', field.visibleWhen?.expression ?? '', (expression) => {
        updateSelectedField({
            visibleWhen: expression.trim()
                ? { type: 'jsonata', expression, dependsOn: field.visibleWhen?.dependsOn ?? [] }
                : undefined,
        });
    }));

    container.appendChild(createTextInput('Visible dependsOn CSV', (field.visibleWhen?.dependsOn ?? []).join(','), (value) => {
        updateSelectedField({
            visibleWhen: {
                type: 'jsonata',
                expression: field.visibleWhen?.expression ?? '',
                dependsOn: splitCsv(value),
            },
        });
    }));

    container.appendChild(createTextarea('Computed JSONata', field.computed?.expression ?? '', (expression) => {
        updateSelectedField({
            computed: expression.trim()
                ? { type: 'jsonata', expression, dependsOn: field.computed?.dependsOn ?? [], mode: field.computed?.mode ?? 'readonly' }
                : undefined,
        });
    }));
}

/**
 * @param {HTMLElement|null} container - `[data-builder-preview]` region.
 * @returns {void}
 */
function renderPreview(container, state) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    state.schema.fields.forEach((field) => {
        const wrapper = document.createElement('label');
        wrapper.className = 'form-control w-full';

        const label = document.createElement('span');
        label.className = 'label-text mb-1';
        label.textContent = field.label ?? field.id;
        wrapper.appendChild(label);

        const input = document.createElement(field.type === 'textarea' ? 'textarea' : 'input');
        input.className = field.type === 'textarea' ? 'textarea textarea-bordered w-full' : 'input input-bordered w-full';
        input.disabled = true;
        input.placeholder = field.type;
        wrapper.appendChild(input);
        container.appendChild(wrapper);
    });
}

/**
 * @param {HTMLElement|null} container - `[data-builder-functions]` list element.
 * @param {Array<{name: string, signature?: string}>} catalog - Registered JSONata definitions.
 * @returns {void}
 */
function renderFunctions(container, catalog) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    catalog.forEach((definition) => {
        const item = document.createElement('li');
        item.className = 'rounded-box bg-base-200 px-2 py-1';
        item.textContent = `${definition.name} ${definition.signature ?? ''}`.trim();
        container.appendChild(item);
    });
}

/**
 * @param {HTMLElement|null} container - `[data-builder-diagnostics]` region.
 * @param {Array<{message: string}>} diagnostics - Messages to surface to the author.
 * @returns {void}
 */
function renderDiagnostics(container, diagnostics) {
    if (!container) {
        return;
    }

    container.innerHTML = '';
    container.classList.toggle('hidden', diagnostics.length === 0);

    diagnostics.forEach((diagnostic) => {
        const item = document.createElement('li');
        item.textContent = diagnostic.message;
        container.appendChild(item);
    });
}

/**
 * Keeps the JSON textarea and optional hidden submission field aligned with `state.schema`.
 *
 * @param {Object} elements - Cached DOM handles from the builder root.
 * @param {{ schema: Object }} state - Current schema snapshot.
 * @returns {void}
 */
function syncJson(elements, state) {
    const value = JSON.stringify(state.schema, null, 2);

    // Skip reassignment when unchanged so caret position / undo stacks survive programmatic sync after inspector edits.
    if (elements.json && elements.json.value !== value) {
        elements.json.value = value;
    }

    if (elements.hidden) {
        elements.hidden.value = value;
    }
}

/**
 * Merges structural schema validation errors with JSONata compile failures per field.
 *
 * @param {Object} schema - Canonical Daisy form schema.
 * @returns {Promise<Array<{code?: string, message: string}>>}
 */
async function collectDiagnostics(schema) {
    const validation = validateSchema(schema);
    const diagnostics = [...validation.errors];

    // Structural errors come from AJV + Daisy semantic passes; expressions need separate compile probes per field.
    for (const field of schema.fields) {
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

/**
 * @param {string} label - Visible label (HTML-escaped).
 * @param {string} value - Controlled value.
 * @param {(next: string) => void} onChange - Invoked on `change`.
 * @returns {HTMLLabelElement}
 */
function createTextInput(label, value, onChange) {
    const wrapper = document.createElement('label');
    wrapper.className = 'form-control w-full';
    wrapper.innerHTML = `<span class="label-text mb-1">${escapeHtml(label)}</span>`;

    const input = document.createElement('input');
    input.className = 'input input-bordered input-sm w-full';
    input.value = value;
    input.addEventListener('change', () => onChange(input.value));
    wrapper.appendChild(input);

    return wrapper;
}

/**
 * @param {string} label - Visible label (HTML-escaped).
 * @param {string} value - Controlled value.
 * @param {(next: string) => void} onChange - Invoked on `change`.
 * @returns {HTMLLabelElement}
 */
function createTextarea(label, value, onChange) {
    const wrapper = document.createElement('label');
    wrapper.className = 'form-control w-full';
    wrapper.innerHTML = `<span class="label-text mb-1">${escapeHtml(label)}</span>`;

    const input = document.createElement('textarea');
    input.className = 'textarea textarea-bordered textarea-sm min-h-24 w-full font-mono';
    input.value = value;
    input.addEventListener('change', () => onChange(input.value));
    wrapper.appendChild(input);

    return wrapper;
}

/**
 * Drops undefined entries so compact merges do not clutter serialized schema.
 *
 * @param {Object} field - Field draft from the inspector.
 * @returns {Object}
 */
function canonicalizeFieldUpdate(field) {
    return Object.fromEntries(Object.entries(field).filter(([, value]) => value !== undefined));
}

/**
 * @param {string} value - Comma-separated dependency ids.
 * @returns {string[]}
 */
function splitCsv(value) {
    return String(value ?? '')
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
}

/**
 * @param {unknown} value - Plain text to embed in inspector markup.
 * @returns {string}
 */
function escapeHtml(value) {
    // Text-node round-trip avoids coupling inspector labels to a full HTML sanitizer dependency.
    const div = document.createElement('div');
    div.textContent = String(value ?? '');

    return div.innerHTML;
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
