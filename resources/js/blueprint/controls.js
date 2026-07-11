const UNSAFE_PATH_SEGMENTS = new Set(['__proto__', 'prototype', 'constructor']);
let richControlId = 0;

/**
 * Clones JSON-compatible integrator data.
 *
 * @param {*} value - Value to clone.
 * @returns {*} Cloned value.
 */
function cloneValue(value) {
    return JSON.parse(JSON.stringify(value ?? {}));
}

/**
 * Returns safe segments for a dotted data path.
 *
 * @param {string} path - Dotted data path.
 * @returns {Array<string>} Safe path segments, or an empty array.
 */
function pathSegments(path) {
    const segments = String(path ?? '').split('.').filter(Boolean);

    return segments.length > 0 && segments.every(segment => !UNSAFE_PATH_SEGMENTS.has(segment))
        ? segments
        : [];
}

/**
 * Reads a nested integrator value.
 *
 * @param {*} data - Integrator data.
 * @param {string} path - Dotted data path.
 * @returns {*} Stored value.
 */
export function getDataPath(data, path) {
    return pathSegments(path).reduce(
        (value, segment) => value && typeof value === 'object' ? value[segment] : undefined,
        data,
    );
}

/**
 * Writes a nested integrator value without mutating the source object.
 *
 * @param {*} data - Integrator data.
 * @param {string} path - Dotted data path.
 * @param {*} value - New value.
 * @returns {object} Updated data.
 */
export function setDataPath(data, path, value) {
    const segments = pathSegments(path);
    const result = cloneValue(data);

    if (segments.length === 0) {
        return result;
    }

    let target = result;
    segments.slice(0, -1).forEach((segment) => {
        if (!target[segment] || typeof target[segment] !== 'object' || Array.isArray(target[segment])) {
            target[segment] = {};
        }

        target = target[segment];
    });
    target[segments.at(-1)] = cloneValue(value);

    return result;
}

/**
 * Creates a DOM element with optional classes and text.
 *
 * @param {string} tagName - Element tag.
 * @param {string} className - CSS classes.
 * @param {string|null} text - Text content.
 * @returns {HTMLElement} Created element.
 */
function createElement(tagName, className = '', text = null) {
    const element = document.createElement(tagName);
    element.className = className;

    if (text !== null) {
        element.textContent = text;
    }

    return element;
}

/**
 * Resolves a CSP-safe generated dimension utility.
 *
 * @param {*} value - CSS dimension.
 * @param {string} prefix - Utility prefix.
 * @param {number} remMultiplier - Token multiplier for rem values.
 * @returns {string} Utility class or an empty string.
 */
function dimensionClass(value, prefix, remMultiplier) {
    const normalized = String(value ?? '').trim();
    const pixels = normalized.match(/^(\d+(?:\.\d+)?)px$/);
    if (pixels) {
        const token = Math.round(Number(pixels[1]));

        return token >= 1 && token <= 1200 ? `${prefix}-px-${token}` : '';
    }

    const rems = normalized.match(/^(\d+(?:\.\d+)?)rem$/);
    if (rems) {
        const token = Math.round(Number(rems[1]) * remMultiplier);

        return token >= 1 && token <= 400 ? `${prefix}-rem-${token}` : '';
    }

    return '';
}

/**
 * Applies common native field constraints.
 *
 * @param {HTMLElement} control - Native control.
 * @param {object} field - Field descriptor.
 * @returns {void}
 */
function applyConstraints(control, field) {
    control.dataset.blueprintField = field.key;
    control.required = field.required === true;

    ['placeholder', 'maxLength', 'min', 'max', 'step'].forEach((attribute) => {
        if (field[attribute] !== undefined) {
            control.setAttribute(attribute.toLowerCase(), String(field[attribute]));
        }
    });
}

/**
 * Appends normalized options to a select control.
 *
 * @param {HTMLSelectElement} select - Select control.
 * @param {Array<object>} options - Normalized options.
 * @returns {void}
 */
function appendOptions(select, options) {
    options.forEach((option) => {
        const element = document.createElement('option');
        element.value = option.value;
        element.textContent = option.label;
        element.disabled = option.disabled === true;
        select.append(element);
    });
}

/**
 * Creates a native input control.
 *
 * @param {object} field - Field descriptor.
 * @param {*} value - Current value.
 * @returns {HTMLElement} Native control.
 */
function createNativeControl(field, value) {
    if (field.type === 'textarea') {
        const textarea = createElement('textarea', 'textarea textarea-bordered min-h-24 w-full');
        textarea.value = value ?? '';
        applyConstraints(textarea, field);

        return textarea;
    }

    if (field.type === 'select') {
        const select = createElement('select', 'select select-bordered w-full');
        appendOptions(select, field.options ?? []);
        select.value = value ?? '';
        applyConstraints(select, field);

        return select;
    }

    const input = createElement('input', field.type === 'checkbox'
        ? 'checkbox checkbox-primary'
        : 'input input-bordered w-full');
    input.type = field.type === 'number' ? 'number' : field.type === 'checkbox' ? 'checkbox' : 'text';

    if (field.type === 'checkbox') {
        input.checked = value === true;
    } else {
        input.value = value ?? '';
    }

    applyConstraints(input, field);

    return input;
}

/**
 * Creates the markup consumed by the existing multi-select module.
 *
 * @param {object} field - Field descriptor.
 * @param {*} value - Current selected values.
 * @returns {{wrapper: HTMLElement, control: HTMLSelectElement}} Multi-select elements.
 */
function createMultiSelectControl(field, value) {
    const wrapper = createElement('div', 'dropdown w-full');
    wrapper.dataset.module = 'multi-select';
    wrapper.dataset.submitName = '';
    wrapper.dataset.placeholder = field.placeholder ?? '';
    const shell = createElement('div', 'select daisy-multi-select relative flex h-auto min-h-10 w-full items-center gap-2 py-1.5 pr-10');
    shell.dataset.role = 'shell';
    const selected = createElement('div', 'flex min-w-0 grow flex-wrap items-center gap-1.5 overflow-hidden');
    selected.dataset.role = 'selected';
    const selectedValues = Array.isArray(value) ? value.map(String) : [];
    selectedValues.forEach((selectedValue) => {
        const option = (field.options ?? []).find(item => item.value === selectedValue);
        const badge = createElement('span', 'badge badge-soft badge-neutral');
        badge.dataset.multiSelectItem = '';
        badge.dataset.value = selectedValue;
        badge.dataset.label = option?.label ?? selectedValue;
        selected.append(badge);
    });
    const search = createElement('input', 'w-10 min-w-8 flex-1 basis-10 border-0 bg-transparent p-0 text-sm outline-none');
    search.type = 'text';
    search.dataset.role = 'input';
    selected.append(search);
    shell.append(selected);
    const select = document.createElement('select');
    select.multiple = true;
    select.hidden = true;
    select.tabIndex = -1;
    select.dataset.role = 'native';
    select.dataset.blueprintField = field.key;
    appendOptions(select, field.options ?? []);
    Array.from(select.options).forEach((option) => {
        option.selected = selectedValues.includes(option.value);
    });
    const hiddenInputs = createElement('div');
    hiddenInputs.dataset.role = 'hidden-inputs';
    const list = createElement('ul', 'dropdown-content menu z-10 mt-2 hidden max-h-72 w-full overflow-auto rounded-box bg-base-100 p-2 shadow');
    list.dataset.role = 'list';
    const message = createElement('p', 'validator-hint mt-1 hidden text-error');
    message.dataset.role = 'message';
    wrapper.append(shell, select, hiddenInputs, list, message);

    return { wrapper, control: select };
}

/**
 * Creates CodeMirror-compatible markup with a textarea fallback.
 *
 * @param {object} field - Field descriptor.
 * @param {*} value - Current value.
 * @returns {{wrapper: HTMLElement, control: HTMLTextAreaElement}} Code editor elements.
 */
function createCodeEditorControl(field, value) {
    const wrapper = createElement('div', 'code-editor card-border rounded-box overflow-hidden');
    wrapper.dataset.module = 'code-editor';
    wrapper.dataset.language = field.language ?? 'javascript';
    wrapper.dataset.tabSize = '2';
    const host = createElement('div', [
        'cm-host',
        dimensionClass(field.height, 'daisy-code-editor-height', 100),
    ].filter(Boolean).join(' '));
    const control = createElement('textarea', 'hidden');
    control.dataset.sync = '';
    control.dataset.blueprintField = field.key;
    control.dataset.language = wrapper.dataset.language;
    control.value = value ?? '';
    const initial = document.createElement('template');
    initial.dataset.initial = '';
    initial.textContent = JSON.stringify({ value: control.value });
    wrapper.append(host, control, initial);

    return { wrapper, control };
}

/**
 * Creates Trix-compatible markup with a hidden input fallback.
 *
 * @param {object} field - Field descriptor.
 * @param {*} value - Current value.
 * @returns {{wrapper: HTMLElement, control: HTMLInputElement}} WYSIWYG elements.
 */
function createWysiwygControl(field, value) {
    richControlId += 1;
    const wrapper = createElement('div', 'trix-wrapper');
    wrapper.dataset.module = 'lazy-editors';
    wrapper.dataset.trixAttachments = '0';
    const container = createElement('div');
    container.dataset.trixContainer = '';
    const control = document.createElement('input');
    control.type = 'hidden';
    control.id = `blueprint-trix-${richControlId}`;
    control.value = value ?? '';
    control.dataset.blueprintField = field.key;
    const editor = document.createElement('trix-editor');
    editor.setAttribute('input', control.id);
    editor.className = [
        'trix-content',
        dimensionClass(field.height, 'daisy-wysiwyg-min-height', 4),
    ].filter(Boolean).join(' ');
    if (field.placeholder) {
        editor.setAttribute('placeholder', field.placeholder);
    }
    container.append(control, editor);
    wrapper.append(container);

    return { wrapper, control };
}

/**
 * Creates a field control and its lifecycle hooks.
 *
 * @param {object} field - Field descriptor.
 * @param {*} value - Current value.
 * @param {boolean} enhanceRichControls - Whether rich modules should load.
 * @returns {{element: HTMLElement, control: HTMLElement, destroy: Function}} Control lifecycle.
 */
function createControl(field, value, enhanceRichControls) {
    if (field.type === 'multiselect') {
        const result = createMultiSelectControl(field, value);

        if (enhanceRichControls) {
            import('../modules/multi-select.js')
                .then(module => module.default(result.wrapper))
                .catch(() => {});
        }

        return { element: result.wrapper, control: result.control, destroy() {} };
    }

    if (field.type === 'code-editor') {
        const result = createCodeEditorControl(field, value);

        if (enhanceRichControls) {
            import('../code-editor.js')
                .then(module => module.default(result.wrapper))
                .catch(() => {});
        }

        return {
            element: result.wrapper,
            control: result.control,
            destroy() {
                result.wrapper.__cmView?.destroy();
            },
        };
    }

    if (field.type === 'wysiwyg') {
        const result = createWysiwygControl(field, value);

        if (enhanceRichControls) {
            import('trix').catch(() => {});
        }

        return { element: result.wrapper, control: result.control, destroy() {} };
    }

    const control = createNativeControl(field, value);

    return { element: control, control, destroy() {} };
}

/**
 * Reads a typed value from a field lifecycle.
 *
 * @param {object} field - Field descriptor.
 * @param {{element: HTMLElement, control: HTMLElement}} lifecycle - Control lifecycle.
 * @returns {*} Typed field value.
 */
function readControl(field, lifecycle) {
    const { control, element } = lifecycle;

    if (field.type === 'checkbox') {
        return control.checked;
    }

    if (field.type === 'number') {
        return control.value === '' ? null : Number(control.value);
    }

    if (field.type === 'multiselect') {
        return Array.from(control.selectedOptions).map(option => option.value);
    }

    if (field.type === 'code-editor') {
        return window.DaisyCodeEditor?.getValue(element) ?? control.value;
    }

    return control.value;
}

/**
 * Wraps a control with its label and help text.
 *
 * @param {object} field - Field descriptor.
 * @param {HTMLElement} control - Rendered control.
 * @returns {HTMLElement} Field wrapper.
 */
function createFieldWrapper(field, control) {
    const wrapper = createElement('label', 'form-control grid gap-1');
    const label = createElement('span', 'label-text text-sm font-medium', field.label);
    wrapper.append(label, control);

    if (field.help) {
        wrapper.append(createElement('span', 'text-xs text-base-content/60', field.help));
    }

    return wrapper;
}

/**
 * Creates all integrator controls in an inspector container.
 *
 * @param {HTMLElement} container - Dynamic fields container.
 * @param {Array<object>} fields - Normalized field descriptors.
 * @param {object} data - Current integrator data.
 * @param {{onInput?: Function, enhanceRichControls?: boolean}} options - Control options.
 * @returns {{read: Function, invalidField: Function, destroy: Function}} Controls manager.
 */
export function createControls(
    container,
    fields,
    data,
    { onInput = () => {}, enhanceRichControls = true } = {},
) {
    const lifecycles = [];
    const sections = new Map();

    container.replaceChildren();

    fields.forEach((field) => {
        const lifecycle = createControl(field, getDataPath(data, field.key), enhanceRichControls);
        lifecycles.push({ field, ...lifecycle });
        const wrapper = createFieldWrapper(field, lifecycle.element);

        if (!field.section) {
            container.append(wrapper);

            return;
        }

        if (!sections.has(field.section)) {
            const fieldset = createElement('fieldset', 'grid gap-3 rounded-box border border-base-300 p-3');
            fieldset.append(createElement('legend', 'px-1 text-sm font-medium', field.section));
            sections.set(field.section, fieldset);
            container.append(fieldset);
        }

        sections.get(field.section).append(wrapper);
    });

    container.addEventListener('input', onInput);
    container.addEventListener('change', onInput);
    container.addEventListener('code:change', onInput);
    container.addEventListener('trix-change', onInput);

    return {
        read() {
            return lifecycles.reduce(
                (result, lifecycle) => setDataPath(
                    result,
                    lifecycle.field.key,
                    readControl(lifecycle.field, lifecycle),
                ),
                data,
            );
        },
        invalidField() {
            const lifecycle = lifecycles.find((item) => {
                const value = readControl(item.field, item);
                const missingRequiredValue = item.field.required && (
                    value === null
                    || value === ''
                    || value === false
                    || (Array.isArray(value) && value.length === 0)
                );
                const violatesNativeConstraint = typeof item.control.checkValidity === 'function'
                    && !item.control.checkValidity();

                return missingRequiredValue || violatesNativeConstraint;
            });

            return lifecycle?.field.key ?? null;
        },
        destroy() {
            container.removeEventListener('input', onInput);
            container.removeEventListener('change', onInput);
            container.removeEventListener('code:change', onInput);
            container.removeEventListener('trix-change', onInput);
            lifecycles.forEach(lifecycle => lifecycle.destroy());
            container.replaceChildren();
        },
    };
}
