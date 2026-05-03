/**
 * Barrel export exposing primitives for hosts bundling Daisy Form Kit manually or assigning globals.
 *
 * When executed in a browser bundle, assigns {@link DaisyFormKit} to `window.DaisyFormKit`.
 *
 * @module form-kit/index
 */

export * from './builder.js';
export * from './fields.js';
export * from './jsonata-engine.js';
export * from './runtime.js';
export * from './schema.js';

import { jsonataRuntime } from './jsonata-engine.js';
import { createFormBuilder } from './builder.js';
import { createFormRuntime } from './runtime.js';
import { createDefaultSchema, validateSchema } from './schema.js';

/** Namespaced helpers mirroring the Blade-powered builder/viewer workflows. */
export const DaisyFormKit = {
    builder: {
        create: createFormBuilder,
    },
    jsonata: jsonataRuntime,
    runtime: {
        create: createFormRuntime,
    },
    schema: {
        createDefault: createDefaultSchema,
        validate: validateSchema,
    },
};

// CDN / legacy bundles without explicit imports rely on this global for debugging consoles.
if (typeof window !== 'undefined') {
    window.DaisyFormKit = DaisyFormKit;
}
