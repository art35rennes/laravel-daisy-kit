/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';
import { createFormBuilder } from '../../../resources/js/form-kit/builder.js';
import { evaluateExpression, registerFunction } from '../../../resources/js/form-kit/jsonata-engine.js';
import { createFormRuntime } from '../../../resources/js/form-kit/runtime.js';
import { createDefaultSchema, validateSchema } from '../../../resources/js/form-kit/schema.js';

async function tick() {
    await new Promise((resolve) => setTimeout(resolve, 0));
    await new Promise((resolve) => setTimeout(resolve, 0));
}

describe('form-kit schema', () => {
    it('canonicalizes and validates a v1 schema', () => {
        const result = validateSchema(createDefaultSchema());

        expect(result.valid).toBe(true);
        expect(result.schema.version).toBe('1.0');
    });

    it('rejects unsupported versions and dependency cycles', () => {
        const result = validateSchema({
            version: '2.0',
            id: 'bad',
            fields: [
                {
                    id: 'a',
                    type: 'text',
                    name: 'a',
                    computed: { type: 'jsonata', expression: 'values.b', dependsOn: ['b'] },
                },
                {
                    id: 'b',
                    type: 'text',
                    name: 'b',
                    computed: { type: 'jsonata', expression: 'values.a', dependsOn: ['a'] },
                },
            ],
        });

        expect(result.valid).toBe(false);
        expect(result.errors.map((error) => error.code)).toContain('unsupported_version');
        expect(result.errors.map((error) => error.code)).toContain('dependency_cycle');
    });
});

describe('form-kit JSONata runtime', () => {
    it('registers custom functions and evaluates expressions', async () => {
        registerFunction('$double', (value) => value * 2, {
            name: '$double',
            signature: '<n:n>',
        });

        const result = await evaluateExpression('$double(values.quantity)', {
            values: { quantity: 4 },
        });

        expect(result.ok).toBe(true);
        expect(result.value).toBe(8);
    });

    it('normalizes unknown function errors', async () => {
        const result = await evaluateExpression('$missingFunction()', {});

        expect(result.ok).toBe(false);
        expect(result.error.code).toBe('unknown_function');
    });
});

describe('form-kit viewer runtime', () => {
    it('handles visibility, validation and computed values', async () => {
        document.body.innerHTML = `
            <form>
                <div data-form-field="customer_type">
                    <input data-form-input="customer_type" name="customer_type" value="personal" />
                    <p data-form-errors="customer_type" class="hidden"></p>
                </div>
                <div data-form-field="company_vat">
                    <input data-form-input="company_vat" name="company_vat" value="" />
                    <p data-form-errors="company_vat" class="hidden"></p>
                </div>
                <div data-form-field="quantity">
                    <input data-form-input="quantity" name="quantity" value="2" />
                    <p data-form-errors="quantity" class="hidden"></p>
                </div>
                <div data-form-field="unit_price">
                    <input data-form-input="unit_price" name="unit_price" value="10" />
                    <p data-form-errors="unit_price" class="hidden"></p>
                </div>
                <div data-form-field="total">
                    <input data-form-input="total" name="total" value="" />
                    <p data-form-errors="total" class="hidden"></p>
                </div>
            </form>
        `;

        const root = document.querySelector('form');
        const runtime = createFormRuntime(root, {
            schema: {
                version: '1.0',
                id: 'quote',
                fields: [
                    { id: 'customer_type', type: 'text', name: 'customer_type' },
                    {
                        id: 'company_vat',
                        type: 'text',
                        name: 'company_vat',
                        visibleWhen: { type: 'jsonata', expression: "values.customer_type = 'company'", dependsOn: ['customer_type'] },
                    },
                    {
                        id: 'quantity',
                        type: 'number',
                        name: 'quantity',
                        rules: [{ type: 'jsonata', expression: '$number(field.value) > 0', dependsOn: ['quantity'], message: 'Quantity must be positive.' }],
                    },
                    { id: 'unit_price', type: 'number', name: 'unit_price' },
                    {
                        id: 'total',
                        type: 'number',
                        name: 'total',
                        computed: { type: 'jsonata', expression: '$number(values.quantity) * $number(values.unit_price)', dependsOn: ['quantity', 'unit_price'], mode: 'readonly' },
                    },
                ],
            },
        });

        await tick();

        expect(runtime.state.visible.company_vat).toBe(false);
        expect(root.querySelector('[name="total"]').value).toBe('20');
        expect(await runtime.validate()).toBe(true);

        root.querySelector('[name="customer_type"]').value = 'company';
        root.dispatchEvent(new Event('input', { bubbles: true }));
        await tick();

        expect(runtime.state.visible.company_vat).toBe(true);
    });
});

describe('form-kit builder', () => {
    it('adds, reorders and exports fields', async () => {
        document.body.innerHTML = `
            <div data-module="form-builder">
                <div data-builder-palette></div>
                <div data-builder-outline></div>
                <div data-builder-inspector></div>
                <div data-builder-preview></div>
                <textarea data-builder-json></textarea>
                <textarea data-builder-hidden></textarea>
                <ul data-builder-diagnostics></ul>
                <ul data-builder-functions></ul>
            </div>
        `;

        const root = document.querySelector('[data-module="form-builder"]');
        const builder = createFormBuilder(root, {
            schema: {
                version: '1.0',
                id: 'builder',
                fields: [],
            },
            fieldTypes: [{ type: 'text', label: 'Text' }, { type: 'email', label: 'Email' }],
        });

        await tick();

        root.querySelector('[data-builder-add="text"]').click();
        await tick();
        root.querySelector('[data-builder-add="email"]').click();
        await tick();

        expect(builder.state.schema.fields.map((field) => field.type)).toEqual(['text', 'email']);

        root.querySelector('[data-builder-field="email-2"] [data-builder-move="up"]').click();
        await tick();

        expect(builder.state.schema.fields.map((field) => field.type)).toEqual(['email', 'text']);
        expect(JSON.parse(root.querySelector('[data-builder-hidden]').value).fields).toHaveLength(2);
    });
});
