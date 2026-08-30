import { beforeEach, describe, expect, it, vi } from 'vitest';

const { sortableOptions } = vi.hoisted(() => ({ sortableOptions: [] }));

vi.mock('sortablejs', () => ({
    default: {
        create: vi.fn((element, options) => {
            sortableOptions.push({ element, options });

            return { destroy: vi.fn() };
        }),
    },
}));

import { getInstance, mount, mountAll, unmount } from '../../../resources/js/transfer-list.js';

function markup(configuration) {
    return `<section data-daisy-kit-module="transfer-list"><p data-daisy-kit-status hidden role="alert"></p><div><input data-daisy-kit-transfer-search="source"><ul data-daisy-kit-transfer-source role="listbox"></ul><button data-daisy-kit-transfer-move="to-target" type="button">add</button><button data-daisy-kit-transfer-move="to-source" type="button">remove</button><input data-daisy-kit-transfer-search="target"><ul data-daisy-kit-transfer-target role="listbox"></ul></div><div data-daisy-kit-transfer-values></div>${configuration.required ? '<input data-daisy-kit-transfer-required required>' : ''}<script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script></section>`;
}

describe('transfer list', () => {
    beforeEach(() => { sortableOptions.length = 0; });

    it('reorders through buttons and Alt arrows without dragging and keeps keyboard focus', () => {
        document.body.innerHTML = markup({ name: 'permissions', sortable: false, items: [{ value: 'read', label: 'Read' }, { value: 'write', label: 'Write' }], value: ['read', 'write'] });
        const root = document.querySelector('[data-daisy-kit-module]');
        root.insertAdjacentHTML('beforeend', '<button data-daisy-kit-transfer-reorder="up">Move up</button><button data-daisy-kit-transfer-reorder="down">Move down</button>');
        const instance = mount(root);
        root.querySelector('[data-daisy-kit-transfer-target] [data-value="write"]').click();
        root.querySelector('[data-daisy-kit-transfer-reorder="up"]').click();
        expect(instance.getTargetValues()).toEqual(['write', 'read']);
        root.querySelector('[data-daisy-kit-transfer-target] [data-value="write"]').dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', altKey: true, bubbles: true }));
        expect(instance.getTargetValues()).toEqual(['read', 'write']);
        expect(document.activeElement.dataset.value).toBe('write');
        expect([...root.querySelectorAll('[name="permissions[]"]')].map((input) => input.value)).toEqual(['read', 'write']);
        unmount(root);
        root.querySelector('[data-daisy-kit-transfer-reorder="up"]').click();
        expect(root.querySelector('[data-daisy-kit-transfer-target]').children).toHaveLength(0);
    });

    it('keeps hidden target positions while reordering filtered values and refuses disabled crossings', () => {
        document.body.innerHTML = markup({ sortable: false, items: [{ value: 'a', label: 'Match A' }, { value: 'locked', label: 'Hidden', disabled: true }, { value: 'b', label: 'Match B' }], value: ['a', 'locked', 'b'] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);
        const search = root.querySelector('[data-daisy-kit-transfer-search="target"]');
        search.value = 'Match';
        search.dispatchEvent(new Event('input', { bubbles: true }));
        root.querySelector('[data-daisy-kit-transfer-target] [data-value="b"]').dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowUp', altKey: true, bubbles: true }));
        expect(instance.getTargetValues()).toEqual(['b', 'locked', 'a']);
        search.value = '';
        search.dispatchEvent(new Event('input', { bubbles: true }));
        root.querySelector('[data-daisy-kit-transfer-target] [data-value="a"]').dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowUp', altKey: true, bubbles: true }));
        expect(instance.getTargetValues()).toEqual(['b', 'locked', 'a']);
        unmount(root);
    });

    it('moves keyboard-selected values and preserves submitted order', () => {
        document.body.innerHTML = markup({ name: 'permissions', sortable: false, items: [{ value: 'read', label: 'Read' }, { value: 'write', label: 'Write' }] });
        const root = document.querySelector('[data-daisy-kit-module]'); const instance = mount(root);
        root.querySelector('[data-daisy-kit-transfer-source] [data-value="read"]').click();
        root.querySelector('[data-daisy-kit-transfer-move="to-target"]').click();
        expect(instance.setTargetValues(['write', 'read'])).toBe(true);

        expect(instance.getTargetValues()).toEqual(['write', 'read']);
        expect([...root.querySelectorAll('[name="permissions[]"]')].map((input) => input.value)).toEqual(['write', 'read']);
        unmount(root); expect(getInstance(root)).toBeNull();
    });

    it('uses a constraint-validation proxy for an empty required target', () => {
        document.body.innerHTML = markup({ name: 'permissions', required: true, sortable: false, items: [{ value: 'read', label: 'Read' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);
        const required = root.querySelector('[data-daisy-kit-transfer-required]');

        expect(Object.keys(instance).sort()).toEqual(['clearSelection', 'getTargetValues', 'move', 'reorder', 'setTargetValues']);
        expect(required.checkValidity()).toBe(false);
        instance.setTargetValues(['read']);
        expect(required.checkValidity()).toBe(true);
    });

    it('returns booleans from mutations and exposes structured change payloads', () => {
        document.body.innerHTML = markup({ name: 'permissions', sortable: false, items: [{ value: 'read', label: 'Read' }, { value: 'write', label: 'Write' }] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const changes = vi.fn();
        root.addEventListener('daisy-kit:transfer-list:change', changes);

        const instance = mount(root);

        expect(mount(root)).toBe(instance);
        expect(mountAll(document)).toEqual([instance]);
        expect(instance.move('to-target', ['read'])).toBe(true);
        expect(instance.reorder(['read'])).toBe(true);
        expect(instance.clearSelection()).toBe(true);
        expect(changes.mock.calls[0][0].detail).toEqual({ values: ['read'] });
    });

    it('rejects a transfer beyond the limit with a structured error', () => {
        document.body.innerHTML = markup({ maxItems: 1, sortable: false, items: [{ value: 'read', label: 'Read' }, { value: 'write', label: 'Write' }], value: ['read'] });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:transfer-list:error', (event) => errors.push(event.detail));
        const instance = mount(root);

        expect(instance.move('to-target', ['write'])).toBe(false);
        expect(instance.getTargetValues()).toEqual(['read']);
        expect(errors).toEqual([{
            code: 'max-items',
            maxItems: 1,
            message: 'The transfer list cannot contain more than 1 item.',
            values: ['read'],
        }]);
    });

    it('never transfers disabled items through the facade, keyboard, or action buttons', () => {
        document.body.innerHTML = markup({
            name: 'permissions',
            sortable: false,
            items: [
                { value: 'locked-source', label: 'Locked source', disabled: true },
                { value: 'locked-target', label: 'Locked target', disabled: true },
            ],
            value: ['locked-target'],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:transfer-list:error', (event) => errors.push(event.detail));
        const instance = mount(root);
        const lockedSource = root.querySelector('[data-daisy-kit-transfer-source] [data-value="locked-source"]');
        const lockedTarget = root.querySelector('[data-daisy-kit-transfer-target] [data-value="locked-target"]');

        lockedSource.click();
        root.querySelector('[data-daisy-kit-transfer-move="to-target"]').click();
        lockedSource.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        lockedTarget.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowLeft' }));
        expect(instance.move('to-target', ['locked-source'])).toBe(false);
        expect(instance.move('to-source', ['locked-target'])).toBe(false);

        expect(instance.getTargetValues()).toEqual(['locked-target']);
        expect([...root.querySelectorAll('[name="permissions[]"]')].map((input) => input.value)).toEqual(['locked-target']);
        expect(errors).toEqual([
            {
                code: 'disabled-item',
                disabledValues: ['locked-source'],
                message: 'Disabled transfer list items cannot be moved.',
                values: ['locked-target'],
            },
            {
                code: 'disabled-item',
                disabledValues: ['locked-target'],
                message: 'Disabled transfer list items cannot be moved.',
                values: ['locked-target'],
            },
        ]);
    });

    it('accepts only complete target permutations and keeps disabled items in place', () => {
        document.body.innerHTML = markup({
            name: 'permissions',
            sortable: false,
            items: [
                { value: 'read', label: 'Read' },
                { value: 'locked', label: 'Locked', disabled: true },
                { value: 'write', label: 'Write' },
            ],
            value: ['read', 'locked', 'write'],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:transfer-list:error', (event) => errors.push(event.detail));
        const instance = mount(root);

        expect(instance.reorder(['write', 'read'])).toBe(false);
        expect(instance.reorder(['locked', 'read', 'write'])).toBe(false);
        expect(instance.reorder(['write', 'locked', 'read'])).toBe(true);

        expect(instance.getTargetValues()).toEqual(['write', 'locked', 'read']);
        expect([...root.querySelectorAll('[name="permissions[]"]')].map((input) => input.value)).toEqual(['write', 'locked', 'read']);
        expect(errors).toEqual([
            {
                code: 'invalid-reorder',
                message: 'Reordering requires a complete permutation of the target values.',
                values: ['read', 'locked', 'write'],
            },
            {
                code: 'disabled-item',
                disabledValues: ['locked'],
                message: 'Disabled transfer list items cannot be moved.',
                values: ['read', 'locked', 'write'],
            },
        ]);
    });

    it.each([
        {
            expectedDisabled: ['locked-source'],
            initial: ['read'],
            next: ['read', 'locked-source'],
            scenario: 'addition',
        },
        {
            expectedDisabled: ['locked-target'],
            initial: ['read', 'locked-target', 'write'],
            next: ['read', 'write'],
            scenario: 'removal',
        },
        {
            expectedDisabled: ['locked-target'],
            initial: ['read', 'locked-target', 'write'],
            next: ['locked-target', 'read', 'write'],
            scenario: 'repositioning',
        },
    ])('rejects disabled item $scenario through setTargetValues without mutating submitted state', ({ expectedDisabled, initial, next }) => {
        document.body.innerHTML = markup({
            name: 'permissions',
            sortable: false,
            items: [
                { value: 'read', label: 'Read' },
                { value: 'locked-source', label: 'Locked source', disabled: true },
                { value: 'locked-target', label: 'Locked target', disabled: true },
                { value: 'write', label: 'Write' },
            ],
            value: initial,
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = [];
        root.addEventListener('daisy-kit:transfer-list:error', (event) => errors.push(event.detail));
        const instance = mount(root);
        const initialMarkup = root.querySelector('[data-daisy-kit-transfer-target]').innerHTML;

        expect(instance.setTargetValues(next)).toBe(false);

        expect(instance.getTargetValues()).toEqual(initial);
        expect(root.querySelector('[data-daisy-kit-transfer-target]').innerHTML).toBe(initialMarkup);
        expect([...root.querySelectorAll('[name="permissions[]"]')].map((input) => input.value)).toEqual(initial);
        expect(errors).toEqual([{
            code: 'disabled-item',
            disabledValues: expectedDisabled,
            message: 'Disabled transfer list items cannot be moved.',
            values: initial,
        }]);
    });

    it('configures Sortable to reject disabled target items', () => {
        document.body.innerHTML = markup({
            sortable: true,
            items: [{ value: 'locked', label: 'Locked', disabled: true }],
            value: ['locked'],
        });

        mount(document.querySelector('[data-daisy-kit-module]'));

        expect(sortableOptions).toHaveLength(1);
        expect(sortableOptions[0].options).toMatchObject({
            filter: '[aria-disabled="true"]',
            preventOnFilter: true,
        });
    });

    it('restores target order when a drag callback attempts to move a disabled item', () => {
        document.body.innerHTML = markup({
            name: 'permissions',
            sortable: true,
            items: [
                { value: 'read', label: 'Read' },
                { value: 'locked', label: 'Locked', disabled: true },
                { value: 'write', label: 'Write' },
            ],
            value: ['read', 'locked', 'write'],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);
        const target = root.querySelector('[data-daisy-kit-transfer-target]');
        target.prepend(target.querySelector('[data-value="locked"]'));

        sortableOptions[0].options.onEnd();

        expect(instance.getTargetValues()).toEqual(['read', 'locked', 'write']);
        expect([...target.querySelectorAll('[data-value]')].map((item) => item.dataset.value)).toEqual(['read', 'locked', 'write']);
        expect([...root.querySelectorAll('[name="permissions[]"]')].map((input) => input.value)).toEqual(['read', 'locked', 'write']);
    });
});
