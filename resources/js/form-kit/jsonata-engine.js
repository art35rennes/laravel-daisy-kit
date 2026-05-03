/**
 * Lazy-loaded JSONata integration with registration hooks for catalogued/custom functions,
 * compilation caching, and normalized error surfaces shared by builder diagnostics and viewer runtime.
 *
 * @module form-kit/jsonata-engine
 */

const registeredFunctions = new Map();
const registeredCatalog = new Map();
const expressionCache = new Map();
let jsonataFactory = null;

/**
 * Registers catalog metadata entries keyed by normalized `$function` names for introspection/UI.
 *
 * @param {Array<Object>|Record<string, Object>} [catalog=[]] - Definitions describing callable metadata.
 * @returns {Object[]} Snapshot of merged catalog definitions.
 */
export function registerCatalog(catalog = []) {
    const items = Array.isArray(catalog) ? catalog : Object.values(catalog ?? {});

    items
        .filter((definition) => definition && typeof definition === 'object' && definition.name)
        .forEach((definition) => {
            // Latest catalog description wins so hosts can hot-patch metadata without restarting tabs.
            registeredCatalog.set(normalizeFunctionName(definition.name), {
                ...definition,
                name: normalizeFunctionName(definition.name),
            });
        });

    return Array.from(registeredCatalog.values());
}

/**
 * Binds a JavaScript implementation into JSONata under `$name`, clears compiled caches, and mirrors catalog metadata.
 *
 * @param {string} name - Function token (`total` or `$total`).
 * @param {Function} implementation - JSONata-invoked JS bridge matching JSONata arity expectations.
 * @param {Object} [definition={}] - Optional `{ signature }` payload surfaced to tooling.
 * @returns {{implementation: Function, definition: Object}}
 * @throws {Error} When name or implementation is missing.
 */
export function registerFunction(name, implementation, definition = {}) {
    const normalizedName = normalizeFunctionName(name);

    if (!normalizedName || typeof implementation !== 'function') {
        throw new Error('A JSONata custom function needs a name and an implementation.');
    }

    registeredFunctions.set(normalizedName, {
        implementation,
        definition: {
            ...definition,
            name: normalizedName,
        },
    });

    if (definition && typeof definition === 'object') {
        registerCatalog([{ ...definition, name: normalizedName }]);
    }

    // Compiled snippets cache arity signatures; registering functions invalidates stale AST bindings.
    expressionCache.clear();

    return registeredFunctions.get(normalizedName);
}

/**
 * @returns {Object[]} Registered catalog definitions deduped by normalized name.
 */
export function getFunctionCatalog() {
    return Array.from(registeredCatalog.values());
}

/**
 * @param {string} name - Candidate JSONata function token.
 * @returns {boolean}
 */
export function hasRegisteredFunction(name) {
    return registeredFunctions.has(normalizeFunctionName(name));
}

/**
 * Ensures JSONata identifiers include the leading `$` delimiter expected by registrations.
 *
 * @param {unknown} name - Raw identifier from schema/catalog inputs.
 * @returns {string}
 */
export function normalizeFunctionName(name) {
    const normalized = String(name ?? '').trim();

    if (!normalized) {
        return '';
    }

    return normalized.startsWith('$') ? normalized : `$${normalized}`;
}

/**
 * Compiles (cached) and evaluates JSONata against the structured viewer/builder context object.
 *
 * @param {string} expression - JSONata source snippet.
 * @param {Object} [context={}] - Evaluation bindings (`values`, `field`, ...).
 * @param {Object} [options={}] - Passed to {@link normalizeJsonataError} for richer codes.
 * @returns {Promise<{ok: boolean, value: *, error: null}|{ok: boolean, value: null, error: {code: string, message: string}}>}
 */
export async function evaluateExpression(expression, context = {}, options = {}) {
    try {
        const compiled = await compileExpression(expression);
        const result = await compiled.evaluate(context);

        return {
            ok: true,
            value: result,
            error: null,
        };
    } catch (error) {
        return {
            ok: false,
            value: null,
            error: normalizeJsonataError(error, options),
        };
    }
}

/**
 * Runs sequential evaluations keyed by arbitrary ids (batch tooling / diagnostics scaffolding).
 *
 * @param {Array<{id: string, expression: string, context?: Object}>} evaluations - Work units.
 * @param {Object} [context={}] - Shared fallback context merged per evaluation when omitted.
 * @param {Object} [options={}] - Error normalization options forwarded to {@link evaluateExpression}.
 * @returns {Promise<Record<string, {ok: boolean, value: *, error: *}>>}
 */
export async function evaluateBatch(evaluations, context = {}, options = {}) {
    const results = {};

    for (const evaluation of evaluations) {
        results[evaluation.id] = await evaluateExpression(evaluation.expression, evaluation.context ?? context, options);
    }

    return results;
}

/**
 * Returns a memoized JSONata expression applying every registered JS bridge function.
 *
 * @param {string} expression - Source snippet compiled via dynamic `jsonata` import.
 * @returns {Promise<Object>} Compiled JSONata expression with `.evaluate`.
 */
export async function compileExpression(expression) {
    const source = String(expression ?? '');

    if (expressionCache.has(source)) {
        return expressionCache.get(source);
    }

    const jsonata = await loadJsonata();
    const compiled = jsonata(source);

    registeredFunctions.forEach(({ implementation, definition }, name) => {
        // JSONata expects bare identifiers while our registry stores `$prefixed` names for readability.
        compiled.registerFunction(name.replace(/^\$/, ''), implementation, definition.signature);
    });

    expressionCache.set(source, compiled);

    return compiled;
}

/**
 * Dynamically imports JSONata once per tab/window to avoid bundling costs until expressions execute.
 *
 * @returns {Promise<Function>}
 */
async function loadJsonata() {
    if (jsonataFactory) {
        return jsonataFactory;
    }

    const module = await import('jsonata');
    jsonataFactory = module.default ?? module;

    return jsonataFactory;
}

/**
 * Maps vendor-specific failures into compact Daisy Form Kit diagnostic codes for UI/reporting parity.
 *
 * @param {unknown} error - Raw JSONata rejection reason.
 * @param {{timedOut?: boolean}} [options={}] - Allows callers to force timeout semantics.
 * @returns {{code: string, message: string}}
 */
export function normalizeJsonataError(error, options = {}) {
    const message = String(error?.message ?? error ?? 'JSONata evaluation failed.');
    const code = String(error?.code ?? '');

    if (options.timedOut || code === 'ETIMEDOUT') {
        return {
            code: 'timeout',
            message,
        };
    }

    // JSONata syntax failures historically report codes prefixed with `S`.
    if (code.startsWith('S')) {
        return {
            code: 'syntax_error',
            message,
        };
    }

    if (message.toLowerCase().includes('attempted to invoke') || message.toLowerCase().includes('unknown function')) {
        return {
            code: 'unknown_function',
            message,
        };
    }

    if (message.toLowerCase().includes('version')) {
        return {
            code: 'version_mismatch',
            message,
        };
    }

    return {
        code: 'evaluation_error',
        message,
    };
}

/** Imperative façade re-exported on `window.DaisyFormKit.jsonata` for host debugging. */
export const jsonataRuntime = {
    evaluateExpression,
    evaluateBatch,
    getFunctionCatalog,
    hasRegisteredFunction,
    registerCatalog,
    registerFunction,
};
