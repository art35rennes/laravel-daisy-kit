import { describe, expect, it } from 'vitest';
import { normalizeCategories } from '../../../resources/js/blueprint/schema.js';

describe('Blueprint presentation categories', () => {
    it('keeps only node category presentation', () => {
        const categories = normalizeCategories([{
            value: ' approved ',
            label: ' Approved ',
            color: 'success',
            defaults: { owner: 'Ada' },
            fields: [{ key: 'owner', type: 'text' }],
            sections: [{ value: 'identity' }],
            tabs: [{ value: 'identity' }],
            helpMode: 'icon',
        }], { withColor: true });

        expect(categories).toEqual([{
            value: 'approved',
            label: 'Approved',
            color: 'success',
        }]);
    });

    it('keeps only transition category presentation', () => {
        const categories = normalizeCategories([{
            value: 'return',
            label: 'Return',
            color: 'warning',
            shape: 's',
            fields: [{ key: 'guarded', type: 'checkbox' }],
        }], { withPresentation: true });

        expect(categories).toEqual([{
            value: 'return',
            label: 'Return',
            shape: 's',
            color: 'warning',
        }]);
    });

    it('rejects malformed categories and unsupported presentation tokens', () => {
        const categories = normalizeCategories([
            null,
            { label: 'Missing value' },
            { value: 'valid', color: 'purple', shape: 'diagonal' },
            'simple',
        ], { withPresentation: true });

        expect(categories).toEqual([
            { value: 'valid', label: 'valid' },
            { value: 'simple', label: 'simple' },
        ]);
    });
});
