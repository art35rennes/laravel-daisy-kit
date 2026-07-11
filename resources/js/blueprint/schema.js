const FIELD_TYPES = new Set([
    'text',
    'textarea',
    'number',
    'select',
    'checkbox',
    'multiselect',
    'code-editor',
    'wysiwyg',
]);
const TRANSITION_SHAPES = new Set(['straight', 'curve', 's', 'orthogonal']);
const TRANSITION_COLORS = new Set([
    'primary',
    'secondary',
    'accent',
    'neutral',
    'info',
    'success',
    'warning',
    'error',
]);
const UNSAFE_PATH_SEGMENTS = new Set(['__proto__', 'prototype', 'constructor']);

/**
 * Clones a JSON-compatible value.
 *
 * @param {*} value - Value to clone.
 * @param {*} fallback - Value returned when the input is absent.
 * @returns {*} Cloned value.
 */
function cloneValue(value, fallback) {
    if (value === undefined || value === null) {
        return fallback;
    }

    return JSON.parse(JSON.stringify(value));
}

/**
 * Determines whether a value is a plain object.
 *
 * @param {*} value - Value to inspect.
 * @returns {boolean} Whether the value is a plain object.
 */
function isPlainObject(value) {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

/**
 * Normalizes human-readable text.
 *
 * @param {*} value - Candidate text.
 * @returns {string} Trimmed text.
 */
function normalizeText(value) {
    return typeof value === 'string' || typeof value === 'number'
        ? String(value).trim()
        : '';
}

/**
 * Determines whether a dotted data path is safe to traverse.
 *
 * @param {string} path - Data path.
 * @returns {boolean} Whether every segment is safe.
 */
function isSafePath(path) {
    const segments = path.split('.').filter(Boolean);

    return segments.length > 0 && segments.every(segment => !UNSAFE_PATH_SEGMENTS.has(segment));
}

/**
 * Normalizes a selectable option.
 *
 * @param {*} option - Raw option.
 * @returns {{value: string, label: string, disabled: boolean}|null} Normalized option.
 */
function normalizeOption(option) {
    if (typeof option === 'string' || typeof option === 'number') {
        const value = normalizeText(option);

        return value ? { value, label: value, disabled: false } : null;
    }

    if (!isPlainObject(option)) {
        return null;
    }

    const value = normalizeText(option.value ?? option.id);
    if (!value) {
        return null;
    }

    return {
        value,
        label: normalizeText(option.label ?? option.name) || value,
        disabled: option.disabled === true,
    };
}

/**
 * Copies an optional scalar field attribute when valid.
 *
 * @param {object} target - Normalized field.
 * @param {object} source - Raw field.
 * @param {string} key - Attribute name.
 * @param {'string'|'number'} type - Expected type.
 * @returns {void}
 */
function copyOptionalAttribute(target, source, key, type) {
    const value = source[key];

    if (type === 'string' && (typeof value === 'string' || typeof value === 'number')) {
        target[key] = String(value);
    }

    if (type === 'number' && Number.isFinite(Number(value))) {
        target[key] = Number(value);
    }
}

/**
 * Normalizes an integrator field descriptor.
 *
 * @param {*} field - Raw descriptor.
 * @returns {object|null} Normalized descriptor.
 */
function normalizeField(field) {
    if (!isPlainObject(field)) {
        return null;
    }

    const key = normalizeText(field.key);
    const type = normalizeText(field.type);
    if (!key || !isSafePath(key) || !FIELD_TYPES.has(type)) {
        return null;
    }

    const normalized = {
        key,
        type,
        label: normalizeText(field.label) || key,
        section: normalizeText(field.section),
        help: normalizeText(field.help),
        required: field.required === true,
    };

    if (type === 'select' || type === 'multiselect') {
        normalized.options = (Array.isArray(field.options) ? field.options : [])
            .map(normalizeOption)
            .filter(Boolean);
    }

    copyOptionalAttribute(normalized, field, 'placeholder', 'string');
    copyOptionalAttribute(normalized, field, 'language', 'string');
    copyOptionalAttribute(normalized, field, 'height', 'string');
    copyOptionalAttribute(normalized, field, 'maxLength', 'number');
    copyOptionalAttribute(normalized, field, 'min', 'number');
    copyOptionalAttribute(normalized, field, 'max', 'number');
    copyOptionalAttribute(normalized, field, 'step', 'number');

    return normalized;
}

/**
 * Normalizes an integrator category.
 *
 * @param {*} category - Raw category.
 * @param {boolean} withPresentation - Whether transition presentation is accepted.
 * @param {boolean} withColor - Whether category colors are accepted.
 * @returns {object|null} Normalized category.
 */
function normalizeCategory(category, withPresentation, withColor) {
    if (typeof category === 'string' || typeof category === 'number') {
        const value = normalizeText(category);

        return value ? { value, label: value, defaults: {}, fields: [] } : null;
    }

    if (!isPlainObject(category)) {
        return null;
    }

    const value = normalizeText(category.value);
    if (!value) {
        return null;
    }

    const normalized = {
        value,
        label: normalizeText(category.label) || value,
        defaults: isPlainObject(category.defaults) ? cloneValue(category.defaults, {}) : {},
        fields: (Array.isArray(category.fields) ? category.fields : [])
            .map(normalizeField)
            .filter(Boolean),
    };

    if (withPresentation && TRANSITION_SHAPES.has(category.shape)) {
        normalized.shape = category.shape;
    }

    if ((withPresentation || withColor) && TRANSITION_COLORS.has(category.color)) {
        normalized.color = category.color;
    }

    return normalized;
}

/**
 * Normalizes public node or transition categories.
 *
 * @param {*} categories - Raw category collection.
 * @param {{withPresentation?: boolean, withColor?: boolean}} options - Normalization options.
 * @returns {Array<object>} Normalized categories.
 */
export function normalizeCategories(categories, { withPresentation = false, withColor = false } = {}) {
    return (Array.isArray(categories) ? categories : [])
        .map(category => normalizeCategory(category, withPresentation, withColor))
        .filter(Boolean);
}

/**
 * Resolves a category without leaking mutable schema state.
 *
 * @param {Array<object>} categories - Normalized categories.
 * @param {string} value - Category value.
 * @returns {object|null} Cloned category or null.
 */
export function categoryFor(categories, value) {
    const category = categories.find(item => item.value === value);

    return category ? cloneValue(category, null) : null;
}

/**
 * Recursively fills missing object keys from defaults.
 *
 * Arrays and scalar values are treated atomically and existing values always win.
 *
 * @param {*} value - Integrator value.
 * @param {*} defaults - Category defaults.
 * @returns {*} Merged clone.
 */
export function mergeDefaults(value, defaults) {
    if (!isPlainObject(defaults)) {
        return cloneValue(value, {});
    }

    const result = isPlainObject(value) ? cloneValue(value, {}) : {};

    Object.entries(defaults).forEach(([key, defaultValue]) => {
        if (UNSAFE_PATH_SEGMENTS.has(key)) {
            return;
        }

        if (!(key in result)) {
            result[key] = cloneValue(defaultValue, null);

            return;
        }

        if (isPlainObject(result[key]) && isPlainObject(defaultValue)) {
            result[key] = mergeDefaults(result[key], defaultValue);
        }
    });

    return result;
}

/**
 * Deeply merges an integrator data patch into existing data.
 *
 * @param {*} value - Existing data.
 * @param {*} patch - Partial data patch.
 * @returns {object} Merged data.
 */
export function mergeData(value, patch) {
    const result = isPlainObject(value) ? cloneValue(value, {}) : {};

    if (!isPlainObject(patch)) {
        return result;
    }

    Object.entries(patch).forEach(([key, patchValue]) => {
        if (UNSAFE_PATH_SEGMENTS.has(key)) {
            return;
        }

        result[key] = isPlainObject(result[key]) && isPlainObject(patchValue)
            ? mergeData(result[key], patchValue)
            : cloneValue(patchValue, null);
    });

    return result;
}
