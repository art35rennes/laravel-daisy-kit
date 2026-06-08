/**
 * Shared field taxonomy, factory helpers, and normalization utilities consumed by schema validation,
 * the interactive builder palette, and the viewer runtime.
 *
 * @module form-kit/fields
 */

/** Supported Daisy Form Kit control identifiers aligned with Blade partial switches. */
export const FIELD_TYPES = [
    'text',
    'email',
    'tel',
    'url',
    'password',
    'number',
    'textarea',
    'select',
    'radio',
    'checkbox',
    'toggle',
    'range',
    'date',
    'time',
    'datetime-local',
    'month',
    'color',
    'file',
    'signature',
    'hidden',
    'staticText',
    'section',
    'tabs',
    'wizardStep',
];

/** Structural nodes that nest additional definitions instead of emitting standalone controls. */
export const CONTAINER_FIELD_TYPES = ['section', 'tabs', 'wizardStep'];

/** Leaf nodes rendered for content/layout but intentionally absent from submitted values. */
export const NON_SUBMITTING_FIELD_TYPES = ['staticText'];

/** Validation helpers mirrored loosely after Laravel rule strings (subset). */
export const SIMPLE_RULES = [
    'required',
    'nullable',
    'email',
    'min',
    'max',
    'between',
    'length',
    'pattern',
    'in',
    'accepted',
    'same',
];

/** Palette metadata pairing each supported field type with human-readable labels and grouping hints. */
export const DEFAULT_FIELD_DEFINITIONS = FIELD_TYPES.map((type) => ({
    type,
    label: labelForType(type),
    group: CONTAINER_FIELD_TYPES.includes(type) ? 'layout' : 'fields',
}));

/**
 * Turns camelCase identifiers into Title Case fragments suitable for palette defaults.
 *
 * @param {string} type - Raw field type token.
 * @returns {string}
 */
export function labelForType(type) {
    return String(type)
        .replace(/([A-Z])/g, ' $1')
        .replace(/^./, (letter) => letter.toUpperCase());
}

/**
 * Generates a starter field definition used when authors click palette buttons.
 *
 * @param {string} type - Requested {@link FIELD_TYPES} member (falls back to `text`).
 * @param {number} [index=1] - Numeric suffix guaranteeing uniqueness within drafts.
 * @returns {Object}
 */
export function createField(type, index = 1) {
    const safeType = FIELD_TYPES.includes(type) ? type : 'text';
    const id = `${safeType}-${index}`;
    const field = {
        id,
        type: safeType,
        // Submit names prefer underscores because bracket/array notation arrives later via inspector edits.
        name: id.replaceAll('-', '_'),
        label: labelForType(safeType),
    };

    if (safeType === 'select' || safeType === 'radio') {
        field.options = [
            { label: 'Option A', value: 'a' },
            { label: 'Option B', value: 'b' },
        ];
    }

    if (safeType === 'checkbox' || safeType === 'toggle') {
        field.default = false;
    }

    if (safeType === 'staticText') {
        delete field.name;
        field.text = 'Static text';
    }

    if (CONTAINER_FIELD_TYPES.includes(safeType)) {
        // Layout containers never post independently; nested leaves carry submit names instead.
        delete field.name;
        field.fields = [];
    }

    return field;
}

/**
 * Normalizes heterogeneous option payloads (`string`, primitives, `{label,value}` pairs).
 *
 * @param {unknown} option - Raw option entry sourced from schema JSON.
 * @returns {{label: string, value: string, disabled?: boolean}|null}
 */
export function normalizeOption(option) {
    if (typeof option === 'string' || typeof option === 'number' || typeof option === 'boolean') {
        return {
            label: String(option),
            value: String(option),
        };
    }

    if (!option || typeof option !== 'object') {
        return null;
    }

    return {
        label: String(option.label ?? option.value ?? ''),
        value: String(option.value ?? option.label ?? ''),
        disabled: option.disabled === true,
    };
}

/**
 * Resolves an effective value prioritizing explicit payloads, falling back to defaults.
 *
 * @param {Record<string, unknown>|null|undefined} values - Hydrated submission bag.
 * @param {Object} field - Canonical field definition (`name`, `id`, `default`).
 * @returns {unknown}
 */
export function getFieldValue(values, field) {
    if (!field || typeof field !== 'object') {
        return null;
    }

    const key = field.name ?? field.id;

    if (values && Object.prototype.hasOwnProperty.call(values, key)) {
        return values[key];
    }

    // Older payloads sometimes hydrate by stable field id rather than renamed submit column key.
    if (values && field.id && Object.prototype.hasOwnProperty.call(values, field.id)) {
        return values[field.id];
    }

    return field.default ?? null;
}
