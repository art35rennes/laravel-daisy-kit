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
    return `<section data-daisy-kit-module="transfer-list"><p data-daisy-kit-status hidden role="alert"></p><div data-daisy-kit-transfer-panel="source"><input data-daisy-kit-transfer-select-all="source" type="checkbox"><span data-daisy-kit-transfer-count="source"></span><input data-daisy-kit-transfer-search="source"><ul data-daisy-kit-transfer-source role="listbox"></ul><p data-daisy-kit-transfer-empty="source"></p><button data-daisy-kit-transfer-page="source:previous" type="button">previous</button><span data-daisy-kit-transfer-page-status="source"></span><button data-daisy-kit-transfer-page="source:next" type="button">next</button></div><button data-daisy-kit-transfer-move="to-target" type="button">add</button><button data-daisy-kit-transfer-move="to-source" type="button">remove</button><div data-daisy-kit-transfer-panel="target"><input data-daisy-kit-transfer-select-all="target" type="checkbox"><span data-daisy-kit-transfer-count="target"></span><input data-daisy-kit-transfer-search="target"><ul data-daisy-kit-transfer-target role="listbox"></ul><p data-daisy-kit-transfer-empty="target"></p><button data-daisy-kit-transfer-page="target:previous" type="button">previous</button><span data-daisy-kit-transfer-page-status="target"></span><button data-daisy-kit-transfer-page="target:next" type="button">next</button></div><div data-daisy-kit-transfer-values></div>${configuration.required ? '<input data-daisy-kit-transfer-required required>' : ''}<script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script></section>`;
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

        expect(Object.keys(instance).sort()).toEqual([
            'clearSelection',
            'getSelection',
            'getTargetValues',
            'move',
            'reorder',
            'selectAll',
            'setPage',
            'setSelection',
            'setTargetValues',
        ]);
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
        expect(changes.mock.calls[0][0].detail).toEqual({
            direction: 'to-target',
            movedValues: ['read'],
            values: ['read'],
        });
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

    it('renders safe rich rows, visible counts and action availability', () => {
        document.body.innerHTML = markup({
            sortable: false,
            items: [
                {
                    value: 'ada',
                    label: 'Ada Lovelace',
                    description: 'ada@example.test',
                    meta: 'Platform',
                    avatar: '/avatars/ada.webp',
                },
                { value: 'grace', label: 'Grace Hopper', initials: 'GH', disabled: true },
            ],
            value: [],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        mount(root);

        const ada = root.querySelector('[data-value="ada"]');
        expect(ada.querySelector('.daisy-kit-transfer-list__item-check').getAttribute('aria-hidden')).toBe('true');
        expect(ada.querySelector('.daisy-kit-transfer-list__item-check').tagName).toBe('SPAN');
        expect(ada.querySelector('img').getAttribute('src')).toBe('/avatars/ada.webp');
        expect(ada.textContent).toContain('ada@example.test');
        expect(ada.textContent).toContain('Platform');
        expect(root.querySelector('[data-daisy-kit-transfer-count="source"]').textContent).toBe('0 selected · 2 total');
        expect(root.querySelector('[data-daisy-kit-transfer-move="to-target"]').disabled).toBe(true);

        ada.click();

        expect(root.querySelector('[data-daisy-kit-transfer-count="source"]').textContent).toBe('1 selected · 2 total');
        expect(root.querySelector('[data-daisy-kit-transfer-move="to-target"]').disabled).toBe(false);
    });

    it('selects the current page without selecting disabled rows and paginates each panel independently', () => {
        document.body.innerHTML = markup({
            pagination: true,
            pageSize: 2,
            selectAllScope: 'page',
            sortable: false,
            items: [
                { value: 'one', label: 'One' },
                { value: 'locked', label: 'Locked', disabled: true },
                { value: 'three', label: 'Three' },
                { value: 'four', label: 'Four' },
                { value: 'five', label: 'Five' },
            ],
            value: [],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const pageChanges = vi.fn();
        const selections = vi.fn();
        root.addEventListener('daisy-kit:transfer-list:page-change', pageChanges);
        root.addEventListener('daisy-kit:transfer-list:selection-change', selections);
        const instance = mount(root);

        expect(instance.selectAll('source', 'page')).toBe(true);
        expect(instance.getSelection()).toEqual({ source: ['one'], target: [] });
        expect(root.querySelector('[data-daisy-kit-transfer-select-all="source"]').checked).toBe(true);
        expect(instance.setPage('source', 2)).toBe(true);
        expect([...root.querySelectorAll('[data-daisy-kit-transfer-source] [data-value]')].map((item) => item.dataset.value)).toEqual(['three', 'four']);
        expect(root.querySelector('[data-daisy-kit-transfer-page-status="source"]').textContent).toBe('Page 2 of 3');
        expect(instance.selectAll('source', 'page')).toBe(true);
        expect(instance.getSelection()).toEqual({ source: ['one', 'three', 'four'], target: [] });
        expect(pageChanges.mock.calls[0][0].detail).toEqual({ page: 2, pageSize: 2, side: 'source', totalPages: 3 });
        expect(selections).toHaveBeenCalled();

        instance.clearSelection('source');
        expect(instance.selectAll('source', 'filtered')).toBe(true);
        expect(instance.getSelection()).toEqual({ source: ['one', 'three', 'four', 'five'], target: [] });
    });

    it('sets detached selections through the stable facade and moves them without DOM inspection', () => {
        document.body.innerHTML = markup({
            sortable: false,
            items: [{ value: 'read', label: 'Read' }, { value: 'write', label: 'Write' }],
            value: [],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const selections = vi.fn();
        root.addEventListener('daisy-kit:transfer-list:selection-change', selections);
        const instance = mount(root);

        expect(instance.setSelection('source', ['write'])).toBe(true);
        const snapshot = instance.getSelection();
        snapshot.source.push('read');

        expect(instance.getSelection()).toEqual({ source: ['write'], target: [] });
        expect(instance.move('to-target')).toBe(true);
        expect(instance.getTargetValues()).toEqual(['write']);
        expect(selections.mock.calls[0][0].detail).toEqual({ side: 'source', values: ['write'] });
    });

    it('reports searches, distinguishes no results from empty lists and resets pagination', () => {
        document.body.innerHTML = markup({
            pagination: true,
            pageSize: 1,
            sortable: false,
            items: [{ value: 'ada', label: 'Ada' }, { value: 'grace', label: 'Grace' }],
            value: [],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const searches = vi.fn();
        root.addEventListener('daisy-kit:transfer-list:search', searches);
        const instance = mount(root);
        instance.setPage('source', 2);
        const search = root.querySelector('[data-daisy-kit-transfer-search="source"]');

        search.value = 'nobody';
        search.dispatchEvent(new Event('input', { bubbles: true }));

        expect(searches.mock.calls[0][0].detail).toEqual({ query: 'nobody', side: 'source' });
        expect(root.querySelector('[data-daisy-kit-transfer-empty="source"]').textContent).toBe('No matching items');
        expect(root.querySelector('[data-daisy-kit-transfer-empty="source"]').hidden).toBe(false);
        expect(root.querySelector('[data-daisy-kit-transfer-page-status="source"]').textContent).toBe('Page 1 of 1');
    });

    it('ranks understandable multi-field results and reports filtered membership', () => {
        document.body.innerHTML = markup({
            sortable: false,
            items: [
                { value: 'margaret', label: 'Margaret Hamilton', description: 'margaret@nasa.gov', meta: 'Flight software' },
                { value: 'grace', label: 'Grace Hopper', description: 'grace@navy.mil', meta: 'Infrastructure' },
                { value: 'ada', label: 'Ada Lovelace', description: 'ada@analytical-engine.org', meta: 'Platform' },
            ],
            value: ['margaret', 'grace'],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const search = root.querySelector('[data-daisy-kit-transfer-search="target"]');
        mount(root);

        search.value = 'arg';
        search.dispatchEvent(new Event('input', { bubbles: true }));

        expect([...root.querySelectorAll('[data-daisy-kit-transfer-target] [data-value]')].map(item => item.dataset.value)).toEqual(['margaret']);
        expect(root.querySelector('[data-daisy-kit-transfer-count="target"]').textContent).toBe('0 selected · 1 matching · 2 total');
    });

    it('enforces one-way mode in the visible actions and facade', () => {
        document.body.innerHTML = markup({
            oneWay: true,
            sortable: false,
            items: [{ value: 'ada', label: 'Ada' }],
            value: ['ada'],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const errors = vi.fn();
        root.addEventListener('daisy-kit:transfer-list:error', errors);
        const instance = mount(root);

        expect(root.querySelector('[data-daisy-kit-transfer-move="to-source"]').hidden).toBe(true);
        expect(instance.move('to-source', ['ada'])).toBe(false);
        expect(errors.mock.calls[0][0].detail).toEqual({
            code: 'one-way',
            direction: 'to-source',
            message: 'This transfer list only allows transfers to the target list.',
            values: ['ada'],
        });
    });

    it('does not submit values while the complete control is disabled', () => {
        document.body.innerHTML = markup({
            disabled: true,
            name: 'assignees',
            sortable: false,
            items: [{ value: 'ada', label: 'Ada' }],
            value: ['ada'],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const instance = mount(root);

        expect(root.querySelector('[name="assignees[]"]').disabled).toBe(true);
        expect(instance.move('to-source', ['ada'])).toBe(false);
        expect(instance.getTargetValues()).toEqual(['ada']);
    });

    it('restores generated panel state when unmounted', () => {
        document.body.innerHTML = markup({
            pagination: true,
            pageSize: 1,
            sortable: false,
            items: [{ value: 'ada', label: 'Ada' }, { value: 'grace', label: 'Grace' }],
            value: [],
        });
        const root = document.querySelector('[data-daisy-kit-module]');
        const count = root.querySelector('[data-daisy-kit-transfer-count="source"]');
        const empty = root.querySelector('[data-daisy-kit-transfer-empty="source"]');
        const selectAll = root.querySelector('[data-daisy-kit-transfer-select-all="source"]');
        const add = root.querySelector('[data-daisy-kit-transfer-move="to-target"]');

        mount(root);
        root.querySelector('[data-value="ada"]').click();
        expect(count.textContent).not.toBe('');
        expect(add.disabled).toBe(false);

        unmount(root);

        expect(count.textContent).toBe('');
        expect(empty.textContent).toBe('');
        expect(selectAll.checked).toBe(false);
        expect(selectAll.indeterminate).toBe(false);
        expect(selectAll.disabled).toBe(false);
        expect(add.disabled).toBe(false);
    });
});
