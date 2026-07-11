// @vitest-environment jsdom

import { describe, expect, it, vi } from 'vitest';
import {
    createControls,
    getDataPath,
    setDataPath,
} from '../../../resources/js/blueprint/controls.js';

describe('Blueprint inspector controls', () => {
    it('reads and writes safe nested data paths immutably', () => {
        const initial = {
            permissions: { web: true },
            opaque: { preserved: true },
        };
        const updated = setDataPath(initial, 'permissions.api', false);

        expect(getDataPath(updated, 'permissions.api')).toBe(false);
        expect(updated.opaque).toEqual({ preserved: true });
        expect(initial.permissions).toEqual({ web: true });
        expect(setDataPath(initial, '__proto__.polluted', true)).toEqual(initial);
        expect({}.polluted).toBeUndefined();
    });

    it('renders supported fields and coerces their values', () => {
        document.body.innerHTML = '<div id="fields"></div>';
        const onInput = vi.fn();
        const controls = createControls(
            document.querySelector('#fields'),
            [
                { key: 'name', type: 'text', label: 'Name', section: 'Identity', required: true },
                { key: 'description', type: 'textarea', label: 'Description', section: 'Identity' },
                { key: 'attempts', type: 'number', label: 'Attempts', min: 0, step: 1 },
                {
                    key: 'status',
                    type: 'select',
                    label: 'Status',
                    options: [{ value: 'draft', label: 'Draft', disabled: false }],
                },
                { key: 'enabled', type: 'checkbox', label: 'Enabled' },
                {
                    key: 'roles',
                    type: 'multiselect',
                    label: 'Roles',
                    options: [
                        { value: 'admin', label: 'Admin', disabled: false },
                        { value: 'reader', label: 'Reader', disabled: false },
                    ],
                },
                { key: 'expression', type: 'code-editor', label: 'Expression', language: 'jsonata' },
                { key: 'recommendation', type: 'wysiwyg', label: 'Recommendation' },
            ],
            {
                name: 'Review',
                attempts: 2,
                status: 'draft',
                enabled: true,
                roles: ['reader'],
                expression: '$exists(data)',
                recommendation: '<p>Check</p>',
                opaque: 'preserved',
            },
            { onInput, enhanceRichControls: false },
        );

        const root = document.querySelector('#fields');
        expect(root.querySelector('fieldset legend').textContent).toBe('Identity');
        expect(root.querySelector('[data-blueprint-field="name"]').required).toBe(true);
        expect(root.querySelector('[data-blueprint-field="expression"]').dataset.language).toBe('jsonata');

        root.querySelector('[data-blueprint-field="attempts"]').value = '4';
        root.querySelector('[data-blueprint-field="enabled"]').checked = false;
        root.querySelector('[data-blueprint-field="roles"] option[value="admin"]').selected = true;
        root.querySelector('[data-blueprint-field="roles"] option[value="reader"]').selected = false;
        root.querySelector('[data-blueprint-field="name"]').dispatchEvent(new Event('input', { bubbles: true }));

        expect(controls.read()).toEqual({
            name: 'Review',
            description: '',
            attempts: 4,
            status: 'draft',
            enabled: false,
            roles: ['admin'],
            expression: '$exists(data)',
            recommendation: '<p>Check</p>',
            opaque: 'preserved',
        });
        expect(onInput).toHaveBeenCalledOnce();

        controls.destroy();
        expect(root.children).toHaveLength(0);
    });
});
