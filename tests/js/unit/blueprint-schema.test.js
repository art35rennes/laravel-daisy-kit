import { describe, expect, it } from 'vitest';
import {
    categoryFor,
    mergeDefaults,
    normalizeCategories,
} from '../../../resources/js/blueprint/schema.js';

describe('Blueprint integrator schema', () => {
    it('normalizes category defaults and supported fields', () => {
        const categories = normalizeCategories([
            {
                value: ' workflow-step ',
                label: ' Workflow step ',
                defaults: {
                    forwardable: true,
                    nested: { enabled: true },
                },
                fields: [
                    {
                        key: ' status_uuid ',
                        type: 'select',
                        label: ' Status ',
                        required: true,
                        options: [
                            { value: 'draft', label: 'Draft' },
                            'published',
                        ],
                    },
                    { key: 'ignored', type: 'unsupported' },
                ],
            },
        ]);

        expect(categories).toEqual([{
            value: 'workflow-step',
            label: 'Workflow step',
            defaults: {
                forwardable: true,
                nested: { enabled: true },
            },
            fields: [{
                key: 'status_uuid',
                type: 'select',
                label: 'Status',
                section: '',
                help: '',
                required: true,
                options: [
                    { value: 'draft', label: 'Draft', disabled: false },
                    { value: 'published', label: 'published', disabled: false },
                ],
            }],
        }]);
    });

    it('rejects unsafe data paths and malformed categories', () => {
        const categories = normalizeCategories([
            null,
            { label: 'Missing value' },
            {
                value: 'safe',
                fields: [
                    { key: '__proto__.polluted', type: 'text' },
                    { key: 'constructor.value', type: 'text' },
                    { key: 'valid.path', type: 'text' },
                ],
            },
        ]);

        expect(categories).toHaveLength(1);
        expect(categories[0].fields.map(field => field.key)).toEqual(['valid.path']);
        expect({}.polluted).toBeUndefined();
    });

    it('keeps semantic colors for node categories when presentation is enabled', () => {
        const categories = normalizeCategories([{
            value: 'approved',
            label: 'Approved',
            color: 'success',
        }], { withColor: true });

        expect(categories).toEqual([{
            value: 'approved',
            label: 'Approved',
            defaults: {},
            fields: [],
            color: 'success',
        }]);
    });

    it('merges defaults recursively without replacing integrator values', () => {
        const result = mergeDefaults(
            {
                forwardable: false,
                nested: { custom: 'kept' },
                list: ['custom'],
            },
            {
                forwardable: true,
                nested: { enabled: true, custom: 'default' },
                list: ['default'],
                added: 42,
            },
        );

        expect(result).toEqual({
            forwardable: false,
            nested: { custom: 'kept', enabled: true },
            list: ['custom'],
            added: 42,
        });
    });

    it('resolves categories without exposing mutable schema state', () => {
        const categories = normalizeCategories([{
            value: 'work',
            defaults: { enabled: true },
            fields: [{ key: 'owner', type: 'text' }],
        }]);
        const resolved = categoryFor(categories, 'work');

        resolved.defaults.enabled = false;
        resolved.fields[0].key = 'changed';

        expect(categoryFor(categories, 'work')).toMatchObject({
            defaults: { enabled: true },
            fields: [{ key: 'owner' }],
        });
    });
});
