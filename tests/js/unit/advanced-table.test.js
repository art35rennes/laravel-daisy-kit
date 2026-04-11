import { describe, expect, it } from 'vitest';
import {
    applySingleSelection,
    buildSelectionDetail,
    collectSelectedIdsFromInputs,
    computeSelectAllState,
    getUrlStateFromSearch,
    parseClientOptions,
    resolveNextSelectAllState,
    setInputsChecked,
    syncSelectAllState,
    updateClientUrl,
} from '../../../resources/js/advanced-table.js';

function makeInput(rowId, checked = false, disabled = false) {
    return {
        checked,
        disabled,
        dataset: {
            rowId: String(rowId),
        },
    };
}

describe('advanced-table helpers', () => {
    it('collects selected ids for single selection state', () => {
        const inputs = [makeInput(1, false), makeInput(2, true), makeInput(3, false)];

        applySingleSelection(inputs, inputs[1]);

        expect(collectSelectedIdsFromInputs(inputs)).toEqual(['2']);
    });

    it('collects selected ids for multiple selection state', () => {
        const inputs = [makeInput(1, true), makeInput(2, false), makeInput(3, true)];

        expect(collectSelectedIdsFromInputs(inputs)).toEqual(['1', '3']);
    });

    it('computes select all and indeterminate states', () => {
        const partiallySelected = [makeInput(1, true), makeInput(2, false), makeInput(3, false)];
        const fullySelected = [makeInput(1, true), makeInput(2, true), makeInput(3, true)];

        expect(computeSelectAllState(partiallySelected)).toEqual({
            checked: false,
            indeterminate: true,
        });

        expect(computeSelectAllState(fullySelected)).toEqual({
            checked: true,
            indeterminate: false,
        });
    });

    it('toggles all rows from the select-all control', () => {
        const inputs = [makeInput(1, false), makeInput(2, false), makeInput(3, false, true)];

        setInputsChecked(inputs, true);

        expect(inputs.map((input) => input.checked)).toEqual([true, true, false]);
    });

    it('builds the advanced-table selection event payload', () => {
        const inputs = [makeInput('a', true), makeInput('b', false), makeInput('c', true)];

        expect(buildSelectionDetail(inputs)).toEqual({
            selected: ['a', 'c'],
        });
    });

    it('syncs mixed state on the header selector', () => {
        const selectAll = {
            checked: false,
            indeterminate: false,
            attrs: {},
            setAttribute(name, value) {
                this.attrs[name] = value;
            },
        };

        const state = syncSelectAllState(selectAll, [makeInput(1, true), makeInput(2, false)]);

        expect(state).toEqual({
            checked: false,
            indeterminate: true,
        });
        expect(selectAll.indeterminate).toBe(true);
        expect(selectAll.attrs['aria-checked']).toBe('mixed');
    });

    it('parses client options from data attributes', () => {
        const options = parseClientOptions({
            dataset: {
                clientOptions: JSON.stringify({ pageSize: 25, sortBy: 'name', sortDirection: 'desc' }),
            },
        });

        expect(options).toEqual({
            pageSize: 25,
            sortBy: 'name',
            sortDirection: 'desc',
        });
    });

    it('resolves the next select-all state from mixed and unchecked states', () => {
        expect(resolveNextSelectAllState({ checked: false, indeterminate: true })).toBe(true);
        expect(resolveNextSelectAllState({ checked: false, indeterminate: false })).toBe(true);
        expect(resolveNextSelectAllState({ checked: true, indeterminate: false })).toBe(false);
    });

    it('reads client table state from standard query params', () => {
        const state = getUrlStateFromSearch('?search=jane&status=ready&sort=-email&page=3&per_page=25', {
            searchParameter: 'search',
            sortParameter: 'sort',
            pageParameter: 'page',
            perPageParameter: 'per_page',
            pageSize: 10,
        });

        expect(state).toEqual({
            searchTerm: 'jane',
            columnFilters: { status: 'ready' },
            sortBy: 'email',
            sortDirection: 'desc',
            page: 3,
            pageSize: 25,
        });
    });

    it('reads client table state from query builder params', () => {
        const state = getUrlStateFromSearch('?filter%5Bsearch%5D=jane&filter%5Bstatus%5D=ready&sort=name&page=2&per_page=50', {
            queryBuilder: true,
            searchParameter: 'search',
            sortParameter: 'sort',
            pageParameter: 'page',
            perPageParameter: 'per_page',
            pageSize: 10,
        });

        expect(state).toEqual({
            searchTerm: 'jane',
            columnFilters: { status: 'ready' },
            sortBy: 'name',
            sortDirection: 'asc',
            page: 2,
            pageSize: 50,
        });
    });

    it('updates the URL for client-side state', () => {
        const previousWindow = global.window;
        const calls = [];

        global.window = {
            location: {
                href: 'https://example.com/users?foo=bar',
            },
            history: {
                replaceState: (...args) => calls.push(args),
            },
        };

        updateClientUrl({
            searchTerm: 'jane',
            columnFilters: { status: 'ready' },
            sortBy: 'email',
            sortDirection: 'desc',
            page: 2,
            pageSize: 25,
        }, {
            searchParameter: 'search',
            sortParameter: 'sort',
            pageParameter: 'page',
            perPageParameter: 'per_page',
        });

        expect(calls).toHaveLength(1);
        expect(calls[0][2]).toContain('foo=bar');
        expect(calls[0][2]).toContain('search=jane');
        expect(calls[0][2]).toContain('status=ready');
        expect(calls[0][2]).toContain('sort=-email');
        expect(calls[0][2]).toContain('page=2');
        expect(calls[0][2]).toContain('per_page=25');

        global.window = previousWindow;
    });
});
