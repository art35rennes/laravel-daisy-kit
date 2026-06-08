/**
 * AJV-backed Daisy Form schema normalization, structural validation, and dependency analysis utilities.
 *
 * Mirrors server-side guarantees so builders and viewers stay aligned before submission.
 *
 * @module form-kit/schema
 */

import Ajv from 'ajv';
import addFormats from 'ajv-formats';
import {
    CONTAINER_FIELD_TYPES,
    FIELD_TYPES,
    NON_SUBMITTING_FIELD_TYPES,
    SIMPLE_RULES,
    createField,
    normalizeOption,
} from './fields.js';

/** Semantic version carried inside persisted schemas for compatibility gates on PHP + JS sides. */
export const FORM_SCHEMA_VERSION = '1.0';

const identifierPattern = /^[A-Za-z][A-Za-z0-9_-]*$/;
const namePattern = /^[A-Za-z_][A-Za-z0-9_.\-[\]]*$/;

const ajv = new Ajv({ allErrors: true, allowUnionTypes: true });
addFormats(ajv);

const schemaDefinition = {
    type: 'object',
    required: ['version', 'id', 'fields'],
    additionalProperties: true,
    properties: {
        version: { const: FORM_SCHEMA_VERSION },
        id: { type: 'string', minLength: 1 },
        meta: { type: 'object' },
        jsonata: {
            type: 'object',
            additionalProperties: true,
            properties: {
                engine: { const: 'jsonata' },
                minVersion: { type: 'string' },
                functions: {
                    type: 'array',
                    items: { type: 'string' },
                },
            },
        },
        layout: { type: 'object' },
        fields: {
            type: 'array',
            items: { type: 'object' },
        },
        submit: { type: 'object' },
    },
};

const validateBaseSchema = ajv.compile(schemaDefinition);

/**
 * @returns {Object} Starter schema aligned with {@link FORM_SCHEMA_VERSION} containing one text field.
 */
export function createDefaultSchema() {
    return canonicalizeSchema({
        version: FORM_SCHEMA_VERSION,
        id: 'form',
        meta: {
            title: 'Untitled form',
        },
        jsonata: {
            engine: 'jsonata',
            minVersion: '2.1.0',
            functions: [],
        },
        layout: {
            type: 'sections',
        },
        fields: [
            createField('text', 1),
        ],
        submit: {
            mode: 'event',
            label: 'Submit',
        },
    });
}

/**
 * Parses JSON embedded inside Blade `<script type="application/json">` regions.
 *
 * @param {unknown} value - Raw stringified JSON or already materialized objects.
 * @param {unknown} [fallback=null] - Returned when parsing fails or input is empty.
 * @returns {unknown}
 */
export function parseJsonPayload(value, fallback = null) {
    if (value === null || value === undefined || value === '') {
        return fallback;
    }

    if (typeof value !== 'string') {
        return value;
    }

    try {
        return JSON.parse(value);
    } catch (_) {
        return fallback;
    }
}

/**
 * Produces deterministic object shape for downstream validation, diffing, and persistence.
 *
 * @param {Object|null|undefined} schema - Authoritative schema object.
 * @returns {Object}
 */
export function canonicalizeSchema(schema) {
    const source = schema && typeof schema === 'object' ? schema : createDefaultSchema();

    const canonical = compactObject({
        version: String(source.version ?? FORM_SCHEMA_VERSION),
        id: String(source.id ?? 'form'),
        meta: source.meta && typeof source.meta === 'object' ? compactObject({ ...source.meta }) : {},
        jsonata: canonicalizeJsonata(source.jsonata),
        layout: canonicalizeLayout(source.layout),
        fields: Array.isArray(source.fields) ? source.fields.map(canonicalizeField).filter(Boolean) : [],
        submit: canonicalizeSubmit(source.submit),
    });

    canonical.fields = Array.isArray(canonical.fields) ? canonical.fields : [];

    return canonical;
}

function canonicalizeLayout(layout) {
    const source = layout && typeof layout === 'object' ? layout : {};
    const type = ['one-page', 'multi-step', 'sections'].includes(source.type) ? source.type : 'one-page';

    return compactObject({
        ...source,
        type,
    });
}

function canonicalizeJsonata(jsonataConfig) {
    const source = jsonataConfig && typeof jsonataConfig === 'object' ? jsonataConfig : {};

    return compactObject({
        engine: source.engine ?? 'jsonata',
        minVersion: source.minVersion ?? '2.1.0',
        functions: Array.isArray(source.functions) ? Array.from(new Set(source.functions.map(String))) : [],
    });
}

function canonicalizeSubmit(submit) {
    const source = submit && typeof submit === 'object' ? submit : {};
    const mode = ['event', 'html', 'fetch', 'none'].includes(source.mode) ? source.mode : 'event';

    return compactObject({
        mode,
        label: source.label ?? 'Submit',
    });
}

/**
 * Normalizes nested field definitions while preserving optional layout containers.
 *
 * @param {Object|null|undefined} field - Raw field object.
 * @returns {Object|null}
 */
export function canonicalizeField(field) {
    if (!field || typeof field !== 'object') {
        return null;
    }

    const type = FIELD_TYPES.includes(field.type) ? field.type : field.type;
    const canonical = {
        id: String(field.id ?? ''),
        type,
        name: field.name === undefined || field.name === null ? undefined : String(field.name),
        label: field.label === undefined || field.label === null ? undefined : String(field.label),
        description: field.description === undefined || field.description === null ? undefined : String(field.description),
        text: field.text === undefined || field.text === null ? undefined : String(field.text),
        default: field.default,
        options: Array.isArray(field.options) ? field.options.map(normalizeOption).filter(Boolean) : undefined,
        rules: Array.isArray(field.rules) ? field.rules.map(canonicalizeRule).filter(Boolean) : undefined,
        visibleWhen: canonicalizeExpression(field.visibleWhen),
        computed: canonicalizeComputed(field.computed),
        attrs: field.attrs && typeof field.attrs === 'object' ? compactObject({ ...field.attrs }) : undefined,
        ui: field.ui && typeof field.ui === 'object' ? compactObject({ ...field.ui }) : undefined,
        fields: Array.isArray(field.fields) ? field.fields.map(canonicalizeField).filter(Boolean) : undefined,
    };

    return compactObject(canonical);
}

function canonicalizeRule(rule) {
    if (typeof rule === 'string') {
        return rule;
    }

    if (!rule || typeof rule !== 'object') {
        return null;
    }

    if (rule.type === 'jsonata') {
        return compactObject({
            type: 'jsonata',
            expression: String(rule.expression ?? ''),
            dependsOn: Array.isArray(rule.dependsOn) ? rule.dependsOn.map(String) : [],
            message: rule.message === undefined ? undefined : String(rule.message),
        });
    }

    return compactObject({ ...rule });
}

function canonicalizeComputed(computed) {
    const expression = canonicalizeExpression(computed);

    if (!expression) {
        return undefined;
    }

    return compactObject({
        ...expression,
        mode: ['readonly', 'hidden', 'suggested'].includes(computed.mode) ? computed.mode : 'readonly',
    });
}

function canonicalizeExpression(expression) {
    if (!expression || typeof expression !== 'object') {
        return undefined;
    }

    if (expression.type !== 'jsonata') {
        return compactObject({ ...expression });
    }

    return compactObject({
        type: 'jsonata',
        expression: String(expression.expression ?? ''),
        dependsOn: Array.isArray(expression.dependsOn) ? expression.dependsOn.map(String) : [],
    });
}

/**
 * Removes `null`, empty arrays, and empty nested objects recursively to keep payloads lean.
 *
 * @param {unknown} value - Arbitrary JSON-friendly structure.
 * @returns {unknown}
 */
export function compactObject(value) {
    if (Array.isArray(value)) {
        return value.map(compactObject);
    }

    if (!value || typeof value !== 'object') {
        return value;
    }

    return Object.entries(value).reduce((carry, [key, item]) => {
        if (item === undefined || item === null) {
            return carry;
        }

        if (Array.isArray(item) && item.length === 0) {
            return carry;
        }

        if (typeof item === 'object' && !Array.isArray(item)) {
            const compacted = compactObject(item);

            if (Object.keys(compacted).length === 0) {
                return carry;
            }

            carry[key] = compacted;

            return carry;
        }

        carry[key] = item;

        return carry;
    }, {});
}

/**
 * Depth-first traversal helper used for validation cycles and viewer leaf selection.
 *
 * @param {Object[]} fields - Root `fields` array or nested container children.
 * @param {string|null} [parent=null] - Parent field id for contextual metadata.
 * @returns {Object[]}
 */
export function flattenFields(fields, parent = null) {
    return (Array.isArray(fields) ? fields : []).flatMap((field) => {
        const entry = { ...field, parent };
        const children = flattenFields(field.fields, field.id);

        return [entry, ...children];
    });
}

/**
 * Runs JSON Schema compile-time checks plus Daisy-specific semantic validation (types, deps, cycles).
 *
 * @param {Object} schema - Raw or canonical schema object.
 * @returns {{valid: boolean, errors: Array<{path: string, code: string, message: string}>, schema: Object}}
 */
export function validateSchema(schema) {
    const canonical = canonicalizeSchema(schema);
    const errors = [];

    if (!validateBaseSchema(canonical)) {
        validateBaseSchema.errors.forEach((error) => {
            errors.push({
                path: error.instancePath || '/',
                code: 'schema_invalid',
                message: error.message ?? 'The form schema is invalid.',
            });
        });
    }

    if (canonical.version !== FORM_SCHEMA_VERSION) {
        errors.push({
            path: '/version',
            code: 'unsupported_version',
            message: `DaisyFormSchema version ${canonical.version} is not supported.`,
        });
    }

    validateFields(canonical.fields, errors);
    validateDependencies(canonical.fields, errors);

    return {
        valid: errors.length === 0,
        errors,
        schema: canonical,
    };
}

function validateFields(fields, errors) {
    const ids = new Set();
    const names = new Set();

    flattenFields(fields).forEach((field) => {
        const path = `/fields/${field.id || '?'}`;

        if (!identifierPattern.test(String(field.id ?? ''))) {
            errors.push({ path, code: 'invalid_id', message: 'Field id must be a stable identifier.' });
        }

        if (ids.has(field.id)) {
            errors.push({ path, code: 'duplicate_id', message: `Field id "${field.id}" is duplicated.` });
        }

        ids.add(field.id);

        if (!FIELD_TYPES.includes(field.type)) {
            errors.push({ path, code: 'unknown_field_type', message: `Field type "${field.type}" is not supported.` });
        }

        if (!CONTAINER_FIELD_TYPES.includes(field.type) && !NON_SUBMITTING_FIELD_TYPES.includes(field.type)) {
            if (!namePattern.test(String(field.name ?? ''))) {
                errors.push({ path, code: 'invalid_name', message: `Field "${field.id}" needs a valid submit name.` });
            }

            if (names.has(field.name)) {
                errors.push({ path, code: 'duplicate_name', message: `Field name "${field.name}" is duplicated.` });
            }

            names.add(field.name);
        }

        validateRules(field, path, errors);
        validateExpression(field.visibleWhen, `${path}/visibleWhen`, errors);
        validateExpression(field.computed, `${path}/computed`, errors);
    });
}

function validateRules(field, path, errors) {
    (field.rules ?? []).forEach((rule, index) => {
        if (typeof rule === 'string') {
            const name = rule.split(':')[0];

            if (!SIMPLE_RULES.includes(name)) {
                errors.push({ path: `${path}/rules/${index}`, code: 'unknown_rule', message: `Rule "${name}" is not supported.` });
            }

            return;
        }

        if (rule?.type !== 'jsonata') {
            errors.push({ path: `${path}/rules/${index}`, code: 'unknown_rule', message: 'Only simple rules and JSONata rules are supported.' });

            return;
        }

        validateExpression(rule, `${path}/rules/${index}`, errors, true);
    });
}

function validateExpression(expression, path, errors, needsMessage = false) {
    if (!expression) {
        return;
    }

    if (expression.type !== 'jsonata') {
        errors.push({ path, code: 'invalid_expression_type', message: 'Only JSONata expressions are supported.' });

        return;
    }

    if (!String(expression.expression ?? '').trim()) {
        errors.push({ path, code: 'missing_expression', message: 'JSONata expression is required.' });
    }

    if (!Array.isArray(expression.dependsOn)) {
        errors.push({ path, code: 'missing_depends_on', message: 'JSONata expressions require dependsOn.' });
    }

    if (needsMessage && !String(expression.message ?? '').trim()) {
        errors.push({ path, code: 'missing_message', message: 'JSONata validation rules require a message.' });
    }
}

function validateDependencies(fields, errors) {
    const flatFields = flattenFields(fields);
    const ids = new Set(flatFields.map((field) => field.id));
    const graph = new Map(flatFields.map((field) => [field.id, []]));

    flatFields.forEach((field) => {
        // Declared dependency ids must exist even though JSONata bodies may read arbitrary paths.
        collectExpressionDependencies(field).forEach((dependency) => {
            if (!ids.has(dependency)) {
                errors.push({
                    path: `/fields/${field.id}`,
                    code: 'unknown_dependency',
                    message: `Field "${field.id}" depends on unknown field "${dependency}".`,
                });

                return;
            }
        });

        collectComputedDependencies(field).forEach((dependency) => {
            if (ids.has(dependency)) {
                // Edge `field -> dependency` means computed output relies on evaluating dependency first.
                graph.get(field.id).push(dependency);
            }
        });
    });

    findCycles(graph).forEach((cycle) => {
        errors.push({
            path: '/fields',
            code: 'dependency_cycle',
            message: `Field dependency cycle detected: ${cycle.join(' -> ')}.`,
        });
    });
}

function collectExpressionDependencies(field) {
    const dependencies = [];

    if (Array.isArray(field.visibleWhen?.dependsOn)) {
        dependencies.push(...field.visibleWhen.dependsOn);
    }

    if (Array.isArray(field.computed?.dependsOn)) {
        dependencies.push(...field.computed.dependsOn);
    }

    (field.rules ?? []).forEach((rule) => {
        if (rule?.type === 'jsonata' && Array.isArray(rule.dependsOn)) {
            dependencies.push(...rule.dependsOn);
        }
    });

    return Array.from(new Set(dependencies.map(String)));
}

function collectComputedDependencies(field) {
    if (!Array.isArray(field.computed?.dependsOn)) {
        return [];
    }

    return Array.from(new Set(field.computed.dependsOn.map(String)));
}

function findCycles(graph) {
    const cycles = [];
    const visited = new Set();
    const visiting = new Set();
    const stack = [];

    function visit(node) {
        if (visiting.has(node)) {
            // Capture the closed loop fragment for actionable author feedback instead of failing silently.
            cycles.push([...stack.slice(stack.indexOf(node)), node]);

            return;
        }

        if (visited.has(node)) {
            return;
        }

        visiting.add(node);
        stack.push(node);

        (graph.get(node) ?? []).forEach(visit);

        stack.pop();
        visiting.delete(node);
        visited.add(node);
    }

    // Disconnected nodes still participate because computed graphs may have multiple roots.
    Array.from(graph.keys()).forEach(visit);

    return cycles;
}
