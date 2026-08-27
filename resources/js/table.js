import {
    columnFilteringFeature,
    columnPinningFeature,
    columnVisibilityFeature,
    constructTable,
    createFilteredRowModel,
    createPaginatedRowModel,
    createSortedRowModel,
    functionalUpdate,
    globalFilteringFeature,
    rowPaginationFeature,
    rowSelectionFeature,
    rowSortingFeature,
    tableFeatures,
} from '@tanstack/table-core';
import { storeReactivityBindings } from '@tanstack/table-core/store-reactivity-bindings';

import '../css/table.css';
import { createMountable } from './core/mountable.js';

const daisyKitTableFeatures = tableFeatures({
    coreReactivityFeature: storeReactivityBindings(),
    columnFilteringFeature,
    globalFilteringFeature,
    rowSortingFeature,
    rowPaginationFeature,
    rowSelectionFeature,
    columnVisibilityFeature,
    columnPinningFeature,
    filteredRowModel: createFilteredRowModel(),
    sortedRowModel: createSortedRowModel(),
    paginatedRowModel: createPaginatedRowModel(),
});

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:table:${name}`, { bubbles: true, detail }));
}

function normalizeColumns(columns, filters = []) {
    if (!Array.isArray(columns)) {
        return [];
    }

    const standaloneFilters = new Map((Array.isArray(filters) ? filters : []).flatMap((filter) => {
        if (!filter || Array.isArray(filter) || typeof filter !== 'object' || typeof filter.id !== 'string') {
            return [];
        }

        return [[filter.id, filter]];
    }));

    return columns.flatMap((column, index) => {
        if (!column || Array.isArray(column) || typeof column !== 'object') {
            return [];
        }

        const id = typeof column.key === 'string' && column.key !== ''
            ? column.key
            : (typeof column.id === 'string' && column.id !== '' ? column.id : `column-${index}`);
        const accessorKey = typeof column.accessor === 'string' && column.accessor !== '' ? column.accessor : id;
        const label = typeof column.label === 'string' && column.label !== '' ? column.label : id;
        const configuredFilter = standaloneFilters.get(id);
        const filter = column.filter && typeof column.filter === 'object' && !Array.isArray(column.filter)
            ? column.filter
            : (configuredFilter ?? null);
        const filterType = ['boolean', 'date', 'number', 'select', 'text'].includes(filter?.type) ? filter.type : null;
        const filterOptions = Array.isArray(filter?.options)
            ? filter.options.flatMap((option) => {
                if (typeof option === 'string' || typeof option === 'number') {
                    return [{ label: String(option), value: String(option) }];
                }

                if (!option || Array.isArray(option) || typeof option !== 'object') {
                    return [];
                }

                const value = option.value;
                if (typeof value !== 'string' && typeof value !== 'number') {
                    return [];
                }

                return [{
                    label: typeof option.label === 'string' ? option.label : String(value),
                    value: String(value),
                }];
            })
            : [];

        return [{
            accessorKey,
            enableSorting: column.sortable !== false,
            filterFn: filterType === 'number'
                ? (row, columnId, value) => value === '' || Number(row.getValue(columnId)) === Number(value)
                : ['boolean', 'date', 'select'].includes(filterType)
                    ? (row, columnId, value) => value === '' || String(row.getValue(columnId)) === value
                    : filterType === 'text'
                        ? (row, columnId, value) => String(row.getValue(columnId)).toLocaleLowerCase().includes(String(value).toLocaleLowerCase())
                        : undefined,
            meta: {
                filterOptions,
                filterPlacement: column.filter ? 'column' : (configuredFilter ? 'toolbar' : null),
                filterType,
            },
            header: label,
            id,
            initialVisible: column.visible !== false,
        }];
    });
}

function normalizeRows(rows) {
    if (!Array.isArray(rows)) {
        return [];
    }

    return rows.filter((row) => row && !Array.isArray(row) && typeof row === 'object');
}

function normalizePageSize(value) {
    if (!Number.isInteger(value) || value < 1) {
        return 10;
    }

    return Math.min(value, 100);
}

function matchesGlobalFilter(value, query, mode) {
    const haystack = String(value ?? '').toLocaleLowerCase();
    const needle = String(query ?? '').trim().toLocaleLowerCase();

    if (needle === '' || haystack.includes(needle)) {
        return true;
    }

    if (mode !== 'fuzzy') {
        return false;
    }

    let position = 0;

    for (const character of needle) {
        position = haystack.indexOf(character, position);

        if (position === -1) {
            return false;
        }

        position += 1;
    }

    return true;
}

function normalizeRowActions(actions) {
    if (!Array.isArray(actions)) {
        return [];
    }

    return actions.flatMap((action) => {
        if (!action || Array.isArray(action) || typeof action !== 'object') {
            return [];
        }

        if (typeof action.id !== 'string' || action.id === '' || typeof action.label !== 'string' || action.label === '') {
            return [];
        }

        return [{ disabled: action.disabled === true, id: action.id, label: action.label }];
    });
}

function normalizeRowDetails(details) {
    if (details === true) {
        return { accessor: null, label: 'Details', mode: 'inline' };
    }

    if (!details || Array.isArray(details) || typeof details !== 'object') {
        return null;
    }

    return {
        accessor: typeof details.accessor === 'string' && details.accessor !== '' ? details.accessor : null,
        label: typeof details.label === 'string' && details.label !== '' ? details.label : 'Details',
        mode: details.mode === 'modal' ? 'modal' : 'inline',
    };
}

function normalizeEditable(editable) {
    if (editable === true) {
        return { columns: [], endpoint: null, method: 'PATCH' };
    }

    if (!editable || Array.isArray(editable) || typeof editable !== 'object') {
        return null;
    }

    const endpoint = normalizeSource(editable.endpoint);
    const method = typeof editable.method === 'string' ? editable.method.toUpperCase() : 'PATCH';

    return {
        columns: Array.isArray(editable.columns) ? editable.columns.filter((column) => typeof column === 'string' && column !== '') : [],
        endpoint,
        method: ['PATCH', 'POST', 'PUT'].includes(method) ? method : 'PATCH',
    };
}

function normalizePersistence(persistence) {
    if (!persistence || Array.isArray(persistence) || typeof persistence !== 'object') {
        return null;
    }

    const key = typeof persistence.key === 'string' && persistence.key !== '' ? persistence.key : null;

    if (!key || !['local', 'url'].includes(persistence.mode)) {
        return null;
    }

    return {
        fields: Array.isArray(persistence.fields)
            ? persistence.fields.filter((field) => ['columnFilters', 'columnPinning', 'columnVisibility', 'globalFilter', 'pagination', 'sorting'].includes(field))
            : ['columnFilters', 'columnPinning', 'columnVisibility', 'globalFilter', 'pagination', 'sorting'],
        key,
        mode: persistence.mode,
    };
}

function readPersistedState(persistence) {
    if (!persistence) {
        return {};
    }

    const storageKey = `daisy-kit-table[${persistence.key}]`;
    let serialized = null;

    if (persistence.mode === 'url') {
        serialized = new URLSearchParams(window.location.search).get(storageKey);
    } else {
        try {
            serialized = window.localStorage.getItem(storageKey);
        } catch {
            return {};
        }
    }

    if (!serialized || serialized.length > 4096) {
        return {};
    }

    try {
        const state = JSON.parse(serialized);

        return state && !Array.isArray(state) && typeof state === 'object' ? state : {};
    } catch {
        return {};
    }
}

function persistState(persistence, state) {
    if (!persistence) {
        return;
    }

    const storageKey = `daisy-kit-table[${persistence.key}]`;
    const selectedState = Object.fromEntries(persistence.fields.map((field) => [field, state[field]]));
    const serialized = JSON.stringify(selectedState);

    if (serialized.length > 4096) {
        return;
    }

    if (persistence.mode === 'url') {
        const url = new URL(window.location.href);
        url.searchParams.set(storageKey, serialized);
        window.history.replaceState({}, '', url);
        return;
    }

    try {
        window.localStorage.setItem(storageKey, serialized);
    } catch {
        // Storage can be disabled, full, or unavailable in a privacy-restricted host.
    }
}

function normalizeSource(value) {
    if (typeof value !== 'string' || value === '') {
        return null;
    }

    try {
        const source = new URL(value, window.location.href);

        return ['http:', 'https:'].includes(source.protocol) ? source : null;
    } catch {
        return null;
    }
}

function formatCell(value) {
    if (value === null || value === undefined) {
        return '';
    }

    if (typeof value === 'object') {
        return JSON.stringify(value);
    }

    return String(value);
}

function updateStatus(root, message = null) {
    const status = root.querySelector('[data-daisy-kit-status]');

    if (!status) {
        return;
    }

    status.hidden = message === null;
    status.textContent = message ?? '';
}

function initialize(root, configuration) {
    const content = root.querySelector('[data-daisy-kit-content]');
    const tableElement = root.querySelector('[data-daisy-kit-table]');
    const filter = root.querySelector('[data-daisy-kit-table-filter]');
    const previousButton = root.querySelector('[data-daisy-kit-table-previous]');
    const nextButton = root.querySelector('[data-daisy-kit-table-next]');
    const page = root.querySelector('[data-daisy-kit-table-page]');
    const pageSizeControl = root.querySelector('[data-daisy-kit-table-page-size]');
    const results = root.querySelector('[data-daisy-kit-table-results]');
    const selectionSummary = root.querySelector('[data-daisy-kit-table-selection]');
    const selectionCount = root.querySelector('[data-daisy-kit-table-selection-count]');
    const selectionPageCount = root.querySelector('[data-daisy-kit-table-selection-page-count]');
    const selectionOffPageCount = root.querySelector('[data-daisy-kit-table-selection-off-page-count]');
    const selectionBreakdown = root.querySelector('[data-daisy-kit-table-selection-breakdown]');
    const selectPageButton = root.querySelector('[data-daisy-kit-table-select-page]');
    const selectFilteredButton = root.querySelector('[data-daisy-kit-table-select-filtered]');
    const clearSelectionButton = root.querySelector('[data-daisy-kit-table-clear-selection]');

    if (!content || !tableElement || !filter || !previousButton || !nextButton || !page) {
        updateStatus(root, 'This table is missing its required markup.');
        root.dataset.daisyKitState = 'error';
        emit(root, 'error', { reason: 'missing-content' });

        return;
    }

    root.classList.add('card', 'border', 'border-base-300', 'bg-base-100', 'p-4', 'shadow-sm');
    tableElement.classList.add('table', 'table-zebra');
    filter.classList.add('input', 'input-bordered', 'w-full');
    previousButton.classList.add('btn', 'btn-sm');
    nextButton.classList.add('btn', 'btn-sm');

    const initialContent = content.innerHTML;
    const source = normalizeSource(configuration.mode === 'server' ? configuration.endpoint : null);
    const selection = configuration.selection && !Array.isArray(configuration.selection) && typeof configuration.selection === 'object'
        ? configuration.selection
        : {};
    const selectionMode = ['single', 'multiple'].includes(selection.mode)
        ? selection.mode
        : 'none';
    const selectable = selectionMode !== 'none';
    const rowKey = typeof selection.rowKey === 'string' && selection.rowKey !== '' ? selection.rowKey : 'id';
    const bulkActions = Array.isArray(configuration.bulkActions) ? configuration.bulkActions.filter((action) => action && typeof action.id === 'string' && typeof action.label === 'string') : [];
    const rowActions = normalizeRowActions(configuration.rowActions);
    const rowDetails = normalizeRowDetails(configuration.rowDetails);
    const editable = normalizeEditable(configuration.editable);
    const persistence = normalizePersistence(configuration.persistState);
    const columns = normalizeColumns(configuration.columns, configuration.filters);
    const toolbarFilters = [...root.querySelectorAll('[data-daisy-kit-table-filter]')]
        .filter((control) => control.dataset.daisyKitTableFilter !== '');
    const searchDebounce = configuration.search && !Array.isArray(configuration.search) && typeof configuration.search === 'object'
        && Number.isInteger(configuration.search.debounce)
        ? Math.max(0, Math.min(configuration.search.debounce, 5000))
        : 0;
    const searchMode = configuration.search?.mode === 'fuzzy' ? 'fuzzy' : 'includes';
    const configuredState = configuration.initialState && !Array.isArray(configuration.initialState) && typeof configuration.initialState === 'object'
        ? configuration.initialState
        : {};
    const configuredSelection = configuredState.selection && !Array.isArray(configuredState.selection) && typeof configuredState.selection === 'object'
        ? configuredState.selection
        : {};
    let selectionState = {
        allFilteredSelected: configuredSelection.allFilteredSelected === true,
        excludedIds: new Set(Array.isArray(configuredSelection.excludedIds) ? configuredSelection.excludedIds.map(String) : []),
        selectedIds: new Set(Array.isArray(configuredSelection.selectedIds) ? configuredSelection.selectedIds.map(String) : []),
    };
    const persistedState = readPersistedState(persistence);
    const expandedRowIds = new Set();
    const detailDialogs = new Set();
    let abortController = null;
    const editAbortControllers = new Set();
    let editing = null;
    let requestSerial = 0;
    let active = true;
    let searchTimer = null;
    let rows = normalizeRows(configuration.rows);
    let total = rows.length;
    const state = {
        columnPinning: {
            start: Array.isArray((persistedState.columnPinning ?? configuredState.columnPinning)?.start)
                ? (persistedState.columnPinning ?? configuredState.columnPinning).start.filter((column) => typeof column === 'string')
                : [],
            end: Array.isArray((persistedState.columnPinning ?? configuredState.columnPinning)?.end)
                ? (persistedState.columnPinning ?? configuredState.columnPinning).end.filter((column) => typeof column === 'string')
                : [],
        },
        columnFilters: Array.isArray(persistedState.columnFilters ?? configuredState.columnFilters)
            ? (persistedState.columnFilters ?? configuredState.columnFilters).filter((filter) => filter && typeof filter.id === 'string')
            : [],
        columnVisibility: {
            ...Object.fromEntries(columns.map((column) => [column.id, column.initialVisible])),
            ...(configuredState.columnVisibility ?? {}),
            ...(persistedState.columnVisibility ?? {}),
        },
        globalFilter: typeof persistedState.globalFilter === 'string'
            ? persistedState.globalFilter
            : (typeof configuredState.globalFilter === 'string' ? configuredState.globalFilter : ''),
        pagination: {
            pageIndex: Number.isInteger((persistedState.pagination ?? configuredState.pagination)?.pageIndex) && (persistedState.pagination ?? configuredState.pagination).pageIndex >= 0
                ? (persistedState.pagination ?? configuredState.pagination).pageIndex
                : 0,
            pageSize: normalizePageSize((persistedState.pagination ?? configuredState.pagination)?.pageSize ?? configuration.pageSize),
        },
        sorting: Array.isArray(persistedState.sorting ?? configuredState.sorting)
            ? (persistedState.sorting ?? configuredState.sorting).filter((sorting) => sorting && typeof sorting.id === 'string' && typeof sorting.desc === 'boolean')
            : [],
        rowSelection: Object.fromEntries(Object.entries(configuredState.rowSelection ?? {})
            .filter(([id, selected]) => typeof id === 'string' && selected === true)),
    };
    const table = constructTable({
        columns,
        data: rows,
        enableMultiRowSelection: selectionMode === 'multiple',
        features: daisyKitTableFeatures,
        getRowId: (row, index) => typeof row[rowKey] === 'string' || typeof row[rowKey] === 'number' ? String(row[rowKey]) : String(index),
        globalFilterFn: (row, columnId, value) => matchesGlobalFilter(row.getValue(columnId), value, searchMode),
        manualFiltering: source !== null,
        manualPagination: source !== null,
        manualSorting: source !== null,
        pageCount: source ? Math.max(Math.ceil(total / state.pagination.pageSize), 1) : undefined,
        onGlobalFilterChange: (updater) => {
            state.globalFilter = functionalUpdate(updater, state.globalFilter);
            state.pagination.pageIndex = 0;
            synchronizeTableState();
            persistState(persistence, state);
            render();
            emit(root, 'filtered', { query: state.globalFilter });
            requestRows();
        },
        onColumnFiltersChange: (updater) => {
            state.columnFilters = functionalUpdate(updater, state.columnFilters);
            state.pagination.pageIndex = 0;
            synchronizeTableState();
            persistState(persistence, state);
            render();
            emit(root, 'filtered', { filters: state.columnFilters });
            requestRows();
        },
        onColumnPinningChange: (updater) => {
            state.columnPinning = functionalUpdate(updater, state.columnPinning);
            synchronizeTableState();
            persistState(persistence, state);
            render();
            requestRows();
        },
        onColumnVisibilityChange: (updater) => {
            state.columnVisibility = functionalUpdate(updater, state.columnVisibility);
            synchronizeTableState();
            persistState(persistence, state);
            render();
            requestRows();
        },
        onPaginationChange: (updater) => {
            state.pagination = functionalUpdate(updater, state.pagination);
            synchronizeTableState();
            persistState(persistence, state);
            render();
            emit(root, 'page-changed', { page: state.pagination.pageIndex + 1 });
            requestRows();
        },
        onSortingChange: (updater) => {
            state.sorting = functionalUpdate(updater, state.sorting);
            synchronizeTableState();
            persistState(persistence, state);
            render();

            const [sorting] = state.sorting;

            if (sorting) {
                emit(root, 'sorted', {
                    column: sorting.id,
                    direction: sorting.desc ? 'desc' : 'asc',
                });
            }

            requestRows();
        },
        state,
    });

    function synchronizeTableState() {
        table.setOptions((current) => ({ ...current, state: { ...state } }));
    }

    function updateRow(rowId, nextRow) {
        rows = rows.map((row, index) => {
            const id = typeof row[rowKey] === 'string' || typeof row[rowKey] === 'number' ? String(row[rowKey]) : String(index);

            return id === rowId ? nextRow : row;
        });
        table.setOptions((current) => ({ ...current, data: rows }));
    }

    function isSelected(rowId) {
        return selectionState.allFilteredSelected
            ? !selectionState.excludedIds.has(rowId)
            : selectionState.selectedIds.has(rowId);
    }

    function selectedIds() {
        return [...selectionState.selectedIds];
    }

    function selectionActionPayload() {
        if (!selectionState.allFilteredSelected) {
            return { ids: selectedIds(), mode: 'ids' };
        }

        return {
            columnFilters: state.columnFilters.map((filter) => ({ ...filter })),
            excludedIds: [...selectionState.excludedIds],
            globalFilter: state.globalFilter,
            mode: 'filtered',
            sorting: state.sorting.map((sorting) => ({ ...sorting })),
        };
    }

    function selectionDetails(visibleRows = table.getRowModel().rows) {
        const visibleSelectedCount = visibleRows.filter((row) => isSelected(row.id)).length;
        const resultTotal = source ? total : table.getFilteredRowModel().rows.length;
        const selectedTotal = selectionState.allFilteredSelected
            ? Math.max(0, resultTotal - selectionState.excludedIds.size)
            : selectionState.selectedIds.size;

        return {
            offPageCount: Math.max(0, selectedTotal - visibleSelectedCount),
            selectedTotal,
            visibleSelectedCount,
        };
    }

    function emitSelectionChanged() {
        if (selectionState.allFilteredSelected) {
            emit(root, 'selection-changed', {
                allFilteredSelected: true,
                excludedIds: [...selectionState.excludedIds],
                ...selectionDetails(),
            });
            return;
        }

        emit(root, 'selection-changed', { ids: selectedIds() });
    }

    function toggleRowSelection(rowId, selected) {
        if (selectionMode === 'single') {
            selectionState = {
                allFilteredSelected: false,
                excludedIds: new Set(),
                selectedIds: new Set(selected ? [rowId] : []),
            };
        } else if (selectionState.allFilteredSelected) {
            if (selected) selectionState.excludedIds.delete(rowId);
            else selectionState.excludedIds.add(rowId);
        } else if (selected) {
            selectionState.selectedIds.add(rowId);
        } else {
            selectionState.selectedIds.delete(rowId);
        }

        emitSelectionChanged();
        render();
    }

    function selectPage() {
        if (selectionMode !== 'multiple') return;

        table.getRowModel().rows.forEach((row) => {
            if (selectionState.allFilteredSelected) selectionState.excludedIds.delete(row.id);
            else selectionState.selectedIds.add(row.id);
        });
        emitSelectionChanged();
        render();
    }

    function clearPage() {
        if (selectionMode !== 'multiple') return;

        table.getRowModel().rows.forEach((row) => {
            if (selectionState.allFilteredSelected) selectionState.excludedIds.add(row.id);
            else selectionState.selectedIds.delete(row.id);
        });
        emitSelectionChanged();
        render();
    }

    function selectFiltered() {
        if (selectionMode !== 'multiple' || selection.selectFiltered !== true) return;

        selectionState = { allFilteredSelected: true, excludedIds: new Set(), selectedIds: new Set() };
        emitSelectionChanged();
        render();
    }

    function clearSelection(shouldRender = true) {
        const hadSelection = selectionState.allFilteredSelected || selectionState.selectedIds.size > 0 || selectionState.excludedIds.size > 0;

        selectionState = { allFilteredSelected: false, excludedIds: new Set(), selectedIds: new Set() };
        if (hadSelection) emitSelectionChanged();
        if (shouldRender) render();
    }

    async function saveEdit(row, column, value) {
        const originalRow = { ...row.original };
        const payload = {
            column: column.id,
            dirty: { [column.id]: value },
            row: originalRow,
            rowId: row.id,
            value,
        };
        let nextRow = { ...originalRow, [column.columnDef.accessorKey]: value };
        const editAbortController = editable?.endpoint ? new AbortController() : null;

        if (editAbortController) {
            editAbortControllers.add(editAbortController);
        }

        try {
            if (editable?.endpoint) {
                const endpoint = editable.endpoint.toString()
                    .replaceAll('{rowId}', encodeURIComponent(row.id))
                    .replaceAll('%7BrowId%7D', encodeURIComponent(row.id));
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };

                if (csrfToken && editable.endpoint.origin === window.location.origin) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                const response = await fetch(endpoint, {
                    body: JSON.stringify(payload),
                    credentials: 'same-origin',
                    headers,
                    method: editable.method,
                    signal: editAbortController.signal,
                });

                if (!response.ok) {
                    throw new Error('The table edit could not be saved.');
                }

                const responsePayload = await response.json();

                if (!responsePayload?.row || Array.isArray(responsePayload.row) || typeof responsePayload.row !== 'object') {
                    throw new Error('The table edit response must include the updated row.');
                }

                nextRow = { ...nextRow, ...responsePayload.row };
            }

            if (!active) return;
            updateRow(row.id, nextRow);
            editing = null;
            render();
            emit(root, 'edited', { column: column.id, row: nextRow, rowId: row.id, value });
        } catch (error) {
            if (!active || (error instanceof DOMException && error.name === 'AbortError')) return;
            updateStatus(root, error instanceof Error ? error.message : 'The table edit could not be saved.');
            emit(root, 'error', { column: column.id, reason: 'edit-failed', rowId: row.id });
        } finally {
            if (editAbortController) {
                editAbortControllers.delete(editAbortController);
            }
        }
    }

    function render() {
        const head = tableElement.tHead;
        const body = tableElement.tBodies.item(0);

        if (!head || !body) {
            return;
        }

        head.replaceChildren();
        body.replaceChildren();
        filter.value = state.globalFilter;
        let visibilityControls = content.querySelector('[data-daisy-kit-table-column-controls]');
        if (configuration.columnVisibility === false) {
            visibilityControls?.closest('details')?.remove();
            visibilityControls?.remove();
            visibilityControls = null;
        } else if (!visibilityControls) {
            visibilityControls = document.createElement('fieldset');
            visibilityControls.className = 'fieldset border border-base-300 rounded-box p-3';
            visibilityControls.setAttribute('data-daisy-kit-table-column-controls', '');
            const legend = document.createElement('legend');
            legend.textContent = 'Columns';
            visibilityControls.append(legend);
            tableElement.parentElement.insertAdjacentElement('beforebegin', visibilityControls);
        }
        visibilityControls?.replaceChildren(...[...table.getAllLeafColumns()].flatMap((column) => {
            const label = document.createElement('label');
            const control = document.createElement('input');
            label.className = 'label gap-2';
            control.className = 'checkbox checkbox-sm';
            control.checked = column.getIsVisible();
            control.dataset.daisyKitTableColumnVisibility = column.id;
            control.type = 'checkbox';
            control.addEventListener('change', () => column.toggleVisibility(control.checked));
            const pin = document.createElement('select');
            pin.className = 'select select-bordered select-sm';
            pin.dataset.daisyKitTableColumnPinning = column.id;
            [['false', 'Normal'], ['start', 'Pin start'], ['end', 'Pin end']].forEach(([value, text]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = text;
                pin.append(option);
            });
            pin.value = column.getIsPinned() || 'false';
            pin.addEventListener('change', () => column.pin(pin.value === 'false' ? false : pin.value));
            label.append(control, document.createTextNode(String(column.columnDef.header ?? column.id)), pin);
            return [label];
        }));
        toolbarFilters.forEach((control) => {
            const activeFilter = state.columnFilters.find((item) => item.id === control.dataset.daisyKitTableFilter);
            control.value = activeFilter?.value ?? '';
        });
        let actions = content.querySelector('[data-daisy-kit-table-bulk-actions]');
        if (!actions && bulkActions.length > 0) {
            actions = document.createElement('div');
            actions.setAttribute('data-daisy-kit-table-bulk-actions', '');
            tableElement.parentElement.insertAdjacentElement('beforebegin', actions);
        }
        if (actions) {
            actions.replaceChildren(...bulkActions.map((action) => {
                const button = document.createElement('button');
                button.className = 'btn btn-sm';
                button.dataset.daisyKitTableBulkAction = action.id;
                button.textContent = action.label;
                button.type = 'button';
                button.addEventListener('click', () => {
                    const payload = selectionActionPayload();
                    emit(root, 'bulk-action', payload.mode === 'ids'
                        ? { id: action.id, ids: payload.ids }
                        : { id: action.id, selection: payload });
                });
                return button;
            }));
        }

        const visibleRows = table.getRowModel().rows;
        const headerRow = document.createElement('tr');
        const hasRowControls = rowActions.length > 0 || rowDetails !== null;

        if (selectable) {
            const selectionHeader = document.createElement('th');
            selectionHeader.scope = 'col';

            if (selectionMode === 'multiple') {
                const selectAll = document.createElement('input');
                selectAll.className = 'checkbox checkbox-sm';
                selectAll.setAttribute('aria-label', 'Select all visible rows');
                const selectedOnPage = visibleRows.filter((row) => isSelected(row.id)).length;
                selectAll.checked = visibleRows.length > 0 && selectedOnPage === visibleRows.length;
                selectAll.indeterminate = selectedOnPage > 0 && selectedOnPage < visibleRows.length;
                selectAll.type = 'checkbox';
                selectAll.addEventListener('change', () => {
                    if (selectAll.checked) {
                        selectPage();
                        return;
                    }

                    clearPage();
                });
                selectionHeader.append(selectAll);
            }

            headerRow.append(selectionHeader);
        }

        const visibleColumns = [
            ...table.getStartVisibleLeafColumns(),
            ...table.getCenterVisibleLeafColumns(),
            ...table.getEndVisibleLeafColumns(),
        ];
        visibleColumns.forEach((column) => {
            const headerCell = document.createElement('th');
            const sortDirection = column.getIsSorted();

            headerCell.scope = 'col';

            headerCell.setAttribute('aria-sort', sortDirection === 'desc' ? 'descending' : (sortDirection === 'asc' ? 'ascending' : 'none'));

            if (column.getCanSort()) {
                const button = document.createElement('button');

                button.className = 'btn btn-ghost btn-sm';

                button.type = 'button';
                button.textContent = String(column.columnDef.header ?? column.id);
                button.addEventListener('click', () => {
                    table.setSorting([{ desc: sortDirection === 'asc', id: column.id }]);
                });
                headerCell.append(button);
            } else {
                headerCell.textContent = String(column.columnDef.header ?? column.id);
            }

            headerRow.append(headerCell);
        });

        if (hasRowControls) {
            const actionsHeader = document.createElement('th');
            actionsHeader.scope = 'col';
            actionsHeader.textContent = 'Actions';
            headerRow.append(actionsHeader);
        }

        head.append(headerRow);

        const filterRow = document.createElement('tr');
        let hasFilters = false;

        if (selectable) filterRow.append(document.createElement('td'));

        table.getAllLeafColumns().forEach((column) => {
            const cell = document.createElement('td');
            const { filterOptions, filterPlacement, filterType } = column.columnDef.meta ?? {};

            if (filterType && filterPlacement === 'column') {
                hasFilters = true;
                const control = filterType === 'select' ? document.createElement('select') : document.createElement('input');

                control.className = filterType === 'select' ? 'select select-bordered select-sm w-full' : 'input input-bordered input-sm w-full';

                control.dataset.daisyKitTableColumnFilter = column.id;
                control.setAttribute('aria-label', `Filter ${String(column.columnDef.header ?? column.id)}`);
                if (control instanceof HTMLInputElement) control.type = filterType === 'number' ? 'number' : 'search';
                if (control instanceof HTMLSelectElement) {
                    const emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.textContent = 'All';
                    control.append(emptyOption);
                    filterOptions.forEach((option) => {
                        const element = document.createElement('option');
                        element.value = option.value;
                        element.textContent = option.label;
                        control.append(element);
                    });
                }
                control.value = column.getFilterValue() ?? '';
                control.addEventListener(control instanceof HTMLSelectElement ? 'change' : 'input', () => column.setFilterValue(control.value));
                cell.append(control);
            }
            filterRow.append(cell);
        });

        if (hasFilters) head.append(filterRow);

        visibleRows.forEach((row) => {
            const tableRow = document.createElement('tr');

            if (selectable) {
                const selectionCell = document.createElement('td');
                const selectRow = document.createElement('input');

                selectRow.className = 'checkbox checkbox-sm';

                selectRow.setAttribute('aria-label', `Select row ${row.id}`);
                selectRow.dataset.daisyKitTableRowSelect = row.id;
                selectRow.checked = isSelected(row.id);
                selectRow.type = 'checkbox';
                selectRow.addEventListener('change', () => {
                    toggleRowSelection(row.id, selectRow.checked);
                });
                selectionCell.append(selectRow);
                tableRow.append(selectionCell);
            }

            const cellsByColumn = new Map(row.getVisibleCells().map((cell) => [cell.column.id, cell]));
            visibleColumns.forEach((column) => {
                const cell = cellsByColumn.get(column.id);
                const tableCell = document.createElement('td');
                const editKey = `${row.id}:${column.id}`;
                const canEdit = editable !== null && (editable.columns.length === 0 || editable.columns.includes(column.id));

                if (canEdit && editing?.key === editKey) {
                    const input = document.createElement('input');
                    const save = document.createElement('button');
                    const cancel = document.createElement('button');
                    const editor = document.createElement('div');
                    const editorActions = document.createElement('div');

                    input.className = 'input input-bordered input-sm';
                    save.className = 'btn btn-primary btn-sm';
                    cancel.className = 'btn btn-ghost btn-sm';
                    editor.className = 'daisy-kit-table__cell-editor';
                    editorActions.className = 'daisy-kit-table__cell-editor-actions';

                    input.dataset.daisyKitTableEditInput = editKey;
                    input.value = editing.value;
                    save.dataset.daisyKitTableEditSave = editKey;
                    save.textContent = 'Save';
                    save.type = 'button';
                    save.addEventListener('click', () => saveEdit(row, column, input.value));
                    cancel.dataset.daisyKitTableEditCancel = editKey;
                    cancel.textContent = 'Cancel';
                    cancel.type = 'button';
                    cancel.addEventListener('click', () => {
                        editing = null;
                        render();
                    });
                    editorActions.append(save, cancel);
                    editor.append(input, editorActions);
                    tableCell.append(editor);
                } else if (canEdit) {
                    const value = cell ? formatCell(cell.getValue()) : '';
                    const output = document.createElement('span');
                    const edit = document.createElement('button');
                    const display = document.createElement('div');

                    edit.className = 'btn btn-ghost btn-sm';
                    display.className = 'daisy-kit-table__cell-display';

                    output.textContent = value;
                    edit.setAttribute('aria-label', `Edit ${String(column.columnDef.header ?? column.id)} in row ${row.id}`);
                    edit.dataset.daisyKitTableEdit = editKey;
                    edit.textContent = 'Edit';
                    edit.type = 'button';
                    edit.addEventListener('click', () => {
                        editing = { key: editKey, value };
                        render();
                    });
                    display.append(output, edit);
                    tableCell.append(display);
                } else {
                    tableCell.textContent = cell ? formatCell(cell.getValue()) : '';
                }
                tableRow.append(tableCell);
            });

            if (hasRowControls) {
                const actionCell = document.createElement('td');

                if (rowDetails) {
                    const toggle = document.createElement('button');

                    toggle.className = 'btn btn-ghost btn-sm';

                    toggle.setAttribute('aria-expanded', String(expandedRowIds.has(row.id)));
                    toggle.dataset.daisyKitTableDetailToggle = row.id;
                    toggle.textContent = rowDetails.label;
                    toggle.type = 'button';
                    toggle.addEventListener('click', () => {
                        if (rowDetails.mode === 'modal') {
                            const dialog = document.createElement('dialog');
                            const title = document.createElement('h2');
                            const close = document.createElement('button');

                            dialog.className = 'modal';
                            title.className = 'text-lg font-semibold';
                            close.className = 'btn btn-sm';

                            dialog.dataset.daisyKitTableDetail = row.id;
                            title.textContent = rowDetails.label;
                            close.textContent = 'Close';
                            close.type = 'button';
                            close.addEventListener('click', () => dialog.close());
                            dialog.append(title, document.createTextNode(formatCell(rowDetails.accessor ? row.original[rowDetails.accessor] : row.original)), close);
                            root.append(dialog);
                            detailDialogs.add(dialog);
                            if (typeof dialog.showModal === 'function') dialog.showModal();
                            else dialog.setAttribute('open', '');
                            return;
                        }

                        if (expandedRowIds.has(row.id)) expandedRowIds.delete(row.id);
                        else expandedRowIds.add(row.id);
                        render();
                    });
                    actionCell.append(toggle);
                }

                rowActions.forEach((action) => {
                    const button = document.createElement('button');

                    button.className = 'btn btn-sm';

                    button.dataset.daisyKitTableRowAction = action.id;
                    button.disabled = action.disabled;
                    button.textContent = action.label;
                    button.type = 'button';
                    button.addEventListener('click', () => emit(root, 'row-action', {
                        id: action.id,
                        row: { ...row.original },
                        rowId: row.id,
                    }));
                    actionCell.append(button);
                });

                tableRow.append(actionCell);
            }

            body.append(tableRow);

            if (rowDetails?.mode === 'inline' && expandedRowIds.has(row.id)) {
                const detailRow = document.createElement('tr');
                const detailCell = document.createElement('td');

                detailCell.colSpan = visibleColumns.length + Number(selectable) + Number(hasRowControls);
                detailCell.dataset.daisyKitTableDetail = row.id;
                detailCell.textContent = formatCell(rowDetails.accessor ? row.original[rowDetails.accessor] : row.original);
                detailRow.append(detailCell);
                body.append(detailRow);
            }
        });

        const filteredRows = table.getFilteredRowModel().rows;
        const pageCount = Math.max(table.getPageCount(), 1);

        const empty = filteredRows.length === 0;

        updateStatus(root, empty ? 'No table rows match the current filter.' : null);
        root.dataset.daisyKitState = empty ? 'empty' : 'ready';
        root.setAttribute('aria-busy', 'false');
        tableElement.setAttribute('aria-busy', 'false');
        previousButton.disabled = !table.getCanPreviousPage();
        nextButton.disabled = !table.getCanNextPage();
        page.textContent = `Page ${state.pagination.pageIndex + 1} of ${pageCount}`;
        if (pageSizeControl) pageSizeControl.value = String(state.pagination.pageSize);

        const resultTotal = source ? total : filteredRows.length;
        const resultStart = resultTotal === 0 ? 0 : (state.pagination.pageIndex * state.pagination.pageSize) + 1;
        const resultEnd = Math.min(resultStart + table.getRowModel().rows.length - 1, resultTotal);
        if (results) results.textContent = resultTotal === 0 ? 'No results' : `${resultStart}–${resultEnd} of ${resultTotal} results`;

        const selectionSummaryState = selectionDetails(visibleRows);
        if (selectionSummary) selectionSummary.hidden = selectionMode === 'single' && selectionSummaryState.selectedTotal === 0;
        if (selectionCount) selectionCount.textContent = String(selectionSummaryState.selectedTotal);
        if (selectionPageCount) selectionPageCount.textContent = String(selectionSummaryState.visibleSelectedCount);
        if (selectionOffPageCount) selectionOffPageCount.textContent = String(selectionSummaryState.offPageCount);
        if (selectionBreakdown) selectionBreakdown.hidden = selectionSummaryState.offPageCount === 0;
        if (selectPageButton) selectPageButton.disabled = visibleRows.length === 0 || visibleRows.every((row) => isSelected(row.id));
        if (selectFilteredButton) {
            selectFilteredButton.disabled = resultTotal === 0 || (selectionState.allFilteredSelected && selectionState.excludedIds.size === 0);
            selectFilteredButton.textContent = `Select all ${resultTotal} results`;
        }
        if (clearSelectionButton) clearSelectionButton.disabled = selectionSummaryState.selectedTotal === 0;
        root.dataset.daisyKitTableSelectionCount = String(selectionSummaryState.selectedTotal);
        root.dataset.daisyKitTableSelectionPageCount = String(selectionSummaryState.visibleSelectedCount);
        root.dataset.daisyKitTableSelectionOffPageCount = String(selectionSummaryState.offPageCount);
    }

    async function requestRows() {
        if (!source) {
            return;
        }

        abortController?.abort();
        abortController = new AbortController();
        const requestSerialAtStart = ++requestSerial;
        const request = new URL(source);
        const [sorting] = state.sorting;

        request.searchParams.set('filter', state.globalFilter);
        request.searchParams.set('page', String(state.pagination.pageIndex + 1));
        request.searchParams.set('pageSize', String(state.pagination.pageSize));
        if (sorting) {
            request.searchParams.set('sort', sorting.id);
            request.searchParams.set('direction', sorting.desc ? 'desc' : 'asc');
        }
        request.searchParams.set('columnFilters', JSON.stringify(state.columnFilters));
        request.searchParams.set('columnPinning', JSON.stringify(state.columnPinning));
        request.searchParams.set('columnVisibility', JSON.stringify(state.columnVisibility));

        root.dataset.daisyKitState = 'loading';
        root.setAttribute('aria-busy', 'true');
        tableElement.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(request, { credentials: 'same-origin', signal: abortController.signal });

            if (!response.ok) throw new Error('The table source did not respond successfully.');

            const payload = await response.json();
            if (requestSerialAtStart !== requestSerial) return;

            if (!payload || !Array.isArray(payload.rows) || !Number.isInteger(payload.total) || payload.total < 0) {
                throw new Error('The table source returned an invalid response.');
            }

            rows = normalizeRows(payload.rows);
            total = payload.total;
            table.setOptions((current) => ({
                ...current,
                data: rows,
                pageCount: Math.max(Math.ceil(total / state.pagination.pageSize), 1),
            }));
            render();
        } catch (error) {
            if (requestSerialAtStart !== requestSerial || (error instanceof DOMException && error.name === 'AbortError')) return;

            updateStatus(root, 'The table data could not be loaded.');
            root.dataset.daisyKitState = 'error';
            root.setAttribute('aria-busy', 'false');
            tableElement.setAttribute('aria-busy', 'false');
            emit(root, 'error', { reason: 'source-unavailable' });
        }
    }

    function onFilterInput(event) {
        window.clearTimeout(searchTimer);
        const value = event.currentTarget.value;

        if (searchDebounce === 0) {
            table.setGlobalFilter(value);
            return;
        }

        searchTimer = window.setTimeout(() => table.setGlobalFilter(value), searchDebounce);
    }

    function onToolbarFilter(event) {
        const column = table.getColumn(event.currentTarget.dataset.daisyKitTableFilter);
        column?.setFilterValue(event.currentTarget.value);
    }

    function onPreviousPage() {
        table.previousPage();
    }

    function onNextPage() {
        table.nextPage();
    }

    function onPageSizeChange(event) {
        const nextPageSize = Number.parseInt(event.currentTarget.value, 10);

        if (Number.isInteger(nextPageSize) && nextPageSize > 0) {
            table.setPageSize(nextPageSize);
        }
    }

    filter.addEventListener('input', onFilterInput);
    previousButton.addEventListener('click', onPreviousPage);
    nextButton.addEventListener('click', onNextPage);
    pageSizeControl?.addEventListener('change', onPageSizeChange);
    selectPageButton?.addEventListener('click', selectPage);
    selectFilteredButton?.addEventListener('click', selectFiltered);
    clearSelectionButton?.addEventListener('click', clearSelection);
    toolbarFilters.forEach((control) => control.addEventListener(
        control instanceof HTMLSelectElement ? 'change' : 'input',
        onToolbarFilter,
    ));
    render();
    requestRows();

    return () => {
        active = false;
        filter.removeEventListener('input', onFilterInput);
        previousButton.removeEventListener('click', onPreviousPage);
        nextButton.removeEventListener('click', onNextPage);
        pageSizeControl?.removeEventListener('change', onPageSizeChange);
        selectPageButton?.removeEventListener('click', selectPage);
        selectFilteredButton?.removeEventListener('click', selectFiltered);
        clearSelectionButton?.removeEventListener('click', clearSelection);
        toolbarFilters.forEach((control) => control.removeEventListener(
            control instanceof HTMLSelectElement ? 'change' : 'input',
            onToolbarFilter,
        ));
        window.clearTimeout(searchTimer);
        abortController?.abort();
        editAbortControllers.forEach((editAbortController) => editAbortController.abort());
        editAbortControllers.clear();
        requestSerial += 1;
        detailDialogs.forEach((dialog) => dialog.remove());
        detailDialogs.clear();
        content.innerHTML = initialContent;
    };
}

const module = createMountable('table', initialize);

export const { mount, mountAll, unmount } = module;
