import { categoryFor, mergeData, mergeDefaults } from './schema.js';

const DEFAULT_VIEWPORT = Object.freeze({ x: 0, y: 0, zoom: 1 });

function cloneValue(value, fallback) {
    if (value === undefined || value === null) {
        return fallback;
    }

    return JSON.parse(JSON.stringify(value));
}

function normalizeText(value) {
    return typeof value === 'string' ? value.trim() : '';
}

function normalizePosition(position) {
    if (!position || !Number.isFinite(Number(position.x)) || !Number.isFinite(Number(position.y))) {
        return null;
    }

    return { x: Number(position.x), y: Number(position.y) };
}

function normalizeNode(node = {}) {
    return {
        id: normalizeText(node.id),
        label: normalizeText(node.label),
        description: normalizeText(node.description),
        category: normalizeText(node.category),
        position: normalizePosition(node.position),
        data: cloneValue(node.data, {}),
    };
}

function normalizeTransition(transition = {}) {
    return {
        id: normalizeText(transition.id),
        source: normalizeText(transition.source),
        target: normalizeText(transition.target),
        label: normalizeText(transition.label),
        description: normalizeText(transition.description),
        category: normalizeText(transition.category),
        data: cloneValue(transition.data, {}),
    };
}

function normalizeViewport(viewport = {}) {
    const zoom = Number(viewport.zoom);

    return {
        x: Number.isFinite(Number(viewport.x)) ? Number(viewport.x) : DEFAULT_VIEWPORT.x,
        y: Number.isFinite(Number(viewport.y)) ? Number(viewport.y) : DEFAULT_VIEWPORT.y,
        zoom: Number.isFinite(zoom) ? Math.min(2, Math.max(0.2, zoom)) : DEFAULT_VIEWPORT.zoom,
    };
}

/**
 * Applies an entity data patch and fills missing category defaults.
 *
 * @param {object} entity - Current normalized entity.
 * @param {object} changes - Entity changes.
 * @param {Array<object>} categories - Available entity categories.
 * @returns {object} Entity changes with normalized integrator data.
 */
function changesWithIntegratorData(entity, changes, categories) {
    const category = changes.category ?? entity.category;
    const patchedData = Object.hasOwn(changes, 'data')
        ? mergeData(entity.data, changes.data)
        : entity.data;
    const defaults = categoryFor(categories, category)?.defaults ?? {};

    return {
        ...changes,
        data: mergeDefaults(patchedData, defaults),
    };
}

/**
 * Applies category defaults to a new entity.
 *
 * @param {object} entity - New entity.
 * @param {Array<object>} categories - Available entity categories.
 * @returns {object} Entity with defaulted integrator data.
 */
function entityWithDefaults(entity, categories) {
    const defaults = categoryFor(categories, entity.category)?.defaults ?? {};

    return {
        ...entity,
        data: mergeDefaults(entity.data, defaults),
    };
}

export function normalizeWorkflow(value = {}) {
    const workflow = value && typeof value === 'object' && !Array.isArray(value) ? value : {};

    return {
        version: 1,
        nodes: Array.isArray(workflow.nodes) ? workflow.nodes.map(normalizeNode) : [],
        transitions: Array.isArray(workflow.transitions) ? workflow.transitions.map(normalizeTransition) : [],
        viewport: normalizeViewport(workflow.viewport),
    };
}

export function validateWorkflow(value = {}) {
    const workflow = normalizeWorkflow(value);
    const errors = [];
    const nodeIds = new Set();
    const transitionIds = new Set();

    workflow.nodes.forEach((node, index) => {
        if (!node.id) {
            errors.push({ code: 'missing_node_id', path: `nodes.${index}.id` });
        } else if (nodeIds.has(node.id)) {
            errors.push({ code: 'duplicate_node_id', path: `nodes.${index}.id`, id: node.id });
        }

        nodeIds.add(node.id);
    });

    workflow.transitions.forEach((transition, index) => {
        if (!transition.id) {
            errors.push({ code: 'missing_transition_id', path: `transitions.${index}.id` });
        } else if (transitionIds.has(transition.id)) {
            errors.push({ code: 'duplicate_transition_id', path: `transitions.${index}.id`, id: transition.id });
        }

        transitionIds.add(transition.id);

        if (!nodeIds.has(transition.source)) {
            errors.push({
                code: 'unknown_transition_source',
                path: `transitions.${index}.source`,
                id: transition.source,
            });
        }

        if (!nodeIds.has(transition.target)) {
            errors.push({
                code: 'unknown_transition_target',
                path: `transitions.${index}.target`,
                id: transition.target,
            });
        }
    });

    return errors;
}

function assertValid(workflow) {
    const [error] = validateWorkflow(workflow);

    if (error) {
        throw new Error(error.code);
    }

    return normalizeWorkflow(workflow);
}

export function addNode(workflow, node, { categories = [] } = {}) {
    return assertValid({
        ...normalizeWorkflow(workflow),
        nodes: [
            ...normalizeWorkflow(workflow).nodes,
            normalizeNode(entityWithDefaults(node, categories)),
        ],
    });
}

export function updateNode(workflow, id, changes, { categories = [] } = {}) {
    const normalized = normalizeWorkflow(workflow);
    let found = false;
    const nodes = normalized.nodes.map((node) => {
        if (node.id !== id) {
            return node;
        }

        found = true;

        return normalizeNode({
            ...node,
            ...changesWithIntegratorData(node, changes, categories),
            id: changes.id ?? node.id,
        });
    });

    if (!found) {
        throw new Error('unknown_node_id');
    }

    const transitions = normalized.transitions.map(transition => ({
        ...transition,
        source: transition.source === id ? changes.id ?? id : transition.source,
        target: transition.target === id ? changes.id ?? id : transition.target,
    }));

    return assertValid({ ...normalized, nodes, transitions });
}

export function removeNode(workflow, id) {
    const normalized = normalizeWorkflow(workflow);

    return normalizeWorkflow({
        ...normalized,
        nodes: normalized.nodes.filter(node => node.id !== id),
        transitions: normalized.transitions.filter(transition => (
            transition.source !== id && transition.target !== id
        )),
    });
}

export function addTransition(workflow, transition, { categories = [] } = {}) {
    const normalized = normalizeWorkflow(workflow);

    return assertValid({
        ...normalized,
        transitions: [
            ...normalized.transitions,
            normalizeTransition(entityWithDefaults(transition, categories)),
        ],
    });
}

export function updateTransition(workflow, id, changes, { categories = [] } = {}) {
    const normalized = normalizeWorkflow(workflow);
    let found = false;
    const transitions = normalized.transitions.map((transition) => {
        if (transition.id !== id) {
            return transition;
        }

        found = true;

        return normalizeTransition({
            ...transition,
            ...changesWithIntegratorData(transition, changes, categories),
            id: changes.id ?? transition.id,
        });
    });

    if (!found) {
        throw new Error('unknown_transition_id');
    }

    return assertValid({ ...normalized, transitions });
}

export function removeTransition(workflow, id) {
    const normalized = normalizeWorkflow(workflow);

    return normalizeWorkflow({
        ...normalized,
        transitions: normalized.transitions.filter(transition => transition.id !== id),
    });
}

export function createEntityId(prefix, existingIds = []) {
    const occupied = new Set(existingIds);
    let index = occupied.size + 1;
    let id = `${prefix}-${index}`;

    while (occupied.has(id)) {
        index += 1;
        id = `${prefix}-${index}`;
    }

    return id;
}
