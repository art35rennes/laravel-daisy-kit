import {
    createTable,
    functionalUpdate,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
} from '@tanstack/table-core';

import '../css/table.css';
import { createMountable } from './core/mountable.js';

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:table:${name}`, { bubbles: true, detail }));
}

function normalizeColumns(columns) {
    if (!Array.isArray(columns)) {
        return [];
    }

    return columns.flatMap((column, index) => {
        if (!column || Array.isArray(column) || typeof column !== 'object') {
            return [];
        }

        const id = typeof column.id === 'string' && column.id !== '' ? column.id : `column-${index}`;
        const accessorKey = typeof column.accessor === 'string' && column.accessor !== '' ? column.accessor : id;
        const label = typeof column.label === 'string' && column.label !== '' ? column.label : id;
        const filter = column.filter && typeof column.filter === 'object' && !Array.isArray(column.filter)
            ? column.filter
            : null;
        const filterType = ['number', 'select', 'text'].includes(filter?.type) ? filter.type : null;
        const filterOptions = Array.isArray(filter?.options)
            ? filter.options.filter((option) => typeof option === 'string' || typeof option === 'number').map(String)
            : [];

        return [{
            accessorKey,
            enableSorting: column.sortable !== false,
            filterFn: filterType === 'number'
                ? (row, columnId, value) => value === '' || Number(row.getValue(columnId)) === Number(value)
                : filterType === 'select'
                    ? (row, columnId, value) => value === '' || String(row.getValue(columnId)) === value
                    : filterType === 'text'
                        ? (row, columnId, value) => String(row.getValue(columnId)).toLocaleLowerCase().includes(String(value).toLocaleLowerCase())
                        : undefined,
            meta: { filterOptions, filterType },
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
    const serialized = persistence.mode === 'url'
        ? new URLSearchParams(window.location.search).get(storageKey)
        : window.localStorage.getItem(storageKey);

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

    window.localStorage.setItem(storageKey, serialized);
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

    if (!content || !tableElement || !filter || !previousButton || !nextButton || !page) {
        updateStatus(root, 'This table is missing its required markup.');
        root.dataset.daisyKitState = 'error';
        emit(root, 'error', { reason: 'missing-content' });

        return;
    }

    const initialContent = content.innerHTML;
    const source = normalizeSource(configuration.source);
    const selectable = configuration.selectable === true;
    const bulkActions = Array.isArray(configuration.bulkActions) ? configuration.bulkActions.filter((action) => action && typeof action.id === 'string' && typeof action.label === 'string') : [];
    const rowActions = normalizeRowActions(configuration.rowActions);
    const rowDetails = normalizeRowDetails(configuration.rowDetails);
    const editable = normalizeEditable(configuration.editable);
    const persistence = normalizePersistence(configuration.persistence);
    const columns = normalizeColumns(configuration.columns);
    const configuredState = configuration.initialState && !Array.isArray(configuration.initialState) && typeof configuration.initialState === 'object'
        ? configuration.initialState
        : {};
    const persistedState = readPersistedState(persistence);
    const selectedIds = new Set();
    const expandedRowIds = new Set();
    const detailDialogs = new Set();
    let abortController = null;
    const editAbortControllers = new Set();
    let editing = null;
    let requestSerial = 0;
    let active = true;
    let rows = normalizeRows(configuration.rows);
    let total = rows.length;
    const state = {
        columnPinning: {
            left: Array.isArray((persistedState.columnPinning ?? configuredState.columnPinning)?.left)
                ? (persistedState.columnPinning ?? configuredState.columnPinning).left.filter((column) => typeof column === 'string')
                : [],
            right: Array.isArray((persistedState.columnPinning ?? configuredState.columnPinning)?.right)
                ? (persistedState.columnPinning ?? configuredState.columnPinning).right.filter((column) => typeof column === 'string')
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
    };
    const table = createTable({
        columns,
        data: rows,
        getRowId: (row, index) => typeof row.id === 'string' || typeof row.id === 'number' ? String(row.id) : String(index),
        getCoreRowModel: getCoreRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        manualFiltering: source !== null,
        manualPagination: source !== null,
        manualSorting: source !== null,
        pageCount: source ? Math.max(Math.ceil(total / state.pagination.pageSize), 1) : undefined,
        onGlobalFilterChange: (updater) => {
            state.globalFilter = functionalUpdate(updater, state.globalFilter);
            state.pagination.pageIndex = 0;
            persistState(persistence, state);
            render();
            emit(root, 'filtered', { query: state.globalFilter });
            requestRows();
        },
        onColumnFiltersChange: (updater) => {
            state.columnFilters = functionalUpdate(updater, state.columnFilters);
            state.pagination.pageIndex = 0;
            persistState(persistence, state);
            render();
            emit(root, 'filtered', { filters: state.columnFilters });
            requestRows();
        },
        onColumnPinningChange: (updater) => {
            state.columnPinning = functionalUpdate(updater, state.columnPinning);
            persistState(persistence, state);
            render();
            requestRows();
        },
        onColumnVisibilityChange: (updater) => {
            state.columnVisibility = functionalUpdate(updater, state.columnVisibility);
            persistState(persistence, state);
            render();
            requestRows();
        },
        onPaginationChange: (updater) => {
            state.pagination = functionalUpdate(updater, state.pagination);
            persistState(persistence, state);
            render();
            emit(root, 'page-changed', { page: state.pagination.pageIndex + 1 });
            requestRows();
        },
        onSortingChange: (updater) => {
            state.sorting = functionalUpdate(updater, state.sorting);
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

    function updateRow(rowId, nextRow) {
        rows = rows.map((row, index) => {
            const id = typeof row.id === 'string' || typeof row.id === 'number' ? String(row.id) : String(index);

            return id === rowId ? nextRow : row;
        });
        table.setOptions((current) => ({ ...current, data: rows }));
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
                const response = await fetch(endpoint, {
                    body: JSON.stringify(payload),
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
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
        if (!visibilityControls) {
            visibilityControls = document.createElement('fieldset');
            visibilityControls.setAttribute('data-daisy-kit-table-column-controls', '');
            const legend = document.createElement('legend');
            legend.textContent = 'Columns';
            visibilityControls.append(legend);
            tableElement.parentElement.insertAdjacentElement('beforebegin', visibilityControls);
        }
        visibilityControls.replaceChildren(...[...table.getAllLeafColumns()].flatMap((column) => {
            const label = document.createElement('label');
            const control = document.createElement('input');
            control.checked = column.getIsVisible();
            control.dataset.daisyKitTableColumnVisibility = column.id;
            control.type = 'checkbox';
            control.addEventListener('change', () => column.toggleVisibility(control.checked));
            const pin = document.createElement('select');
            pin.dataset.daisyKitTableColumnPinning = column.id;
            [['false', 'Normal'], ['left', 'Pin left'], ['right', 'Pin right']].forEach(([value, text]) => {
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
        let actions = content.querySelector('[data-daisy-kit-table-bulk-actions]');
        if (!actions && bulkActions.length > 0) {
            actions = document.createElement('div');
            actions.setAttribute('data-daisy-kit-table-bulk-actions', '');
            tableElement.parentElement.insertAdjacentElement('beforebegin', actions);
        }
        if (actions) {
            actions.replaceChildren(...bulkActions.map((action) => {
                const button = document.createElement('button');
                button.dataset.daisyKitTableBulkAction = action.id;
                button.textContent = action.label;
                button.type = 'button';
                button.addEventListener('click', () => emit(root, 'bulk-action', { id: action.id, ids: [...selectedIds] }));
                return button;
            }));
        }

        const headerRow = document.createElement('tr');
        const hasRowControls = rowActions.length > 0 || rowDetails !== null;

        if (selectable) {
            const selectionHeader = document.createElement('th');
            const selectAll = document.createElement('input');
            const visibleIds = table.getRowModel().rows.map((row) => row.id);
            const selectedCount = visibleIds.filter((id) => selectedIds.has(id)).length;

            selectAll.setAttribute('aria-label', 'Select all visible rows');
            selectAll.checked = visibleIds.length > 0 && selectedCount === visibleIds.length;
            selectAll.indeterminate = selectedCount > 0 && selectedCount < visibleIds.length;
            selectAll.type = 'checkbox';
            selectionHeader.scope = 'col';
            selectAll.addEventListener('change', () => {
                visibleIds.forEach((id) => {
                    if (selectAll.checked) selectedIds.add(id);
                    else selectedIds.delete(id);
                });
                emit(root, 'selection-changed', { ids: [...selectedIds] });
                render();
            });
            selectionHeader.append(selectAll);
            headerRow.append(selectionHeader);
        }

        const visibleColumns = [
            ...table.getLeftVisibleLeafColumns(),
            ...table.getCenterVisibleLeafColumns(),
            ...table.getRightVisibleLeafColumns(),
        ];
        visibleColumns.forEach((column) => {
            const headerCell = document.createElement('th');
            const sortDirection = column.getIsSorted();

            headerCell.scope = 'col';

            if (sortDirection) {
                headerCell.setAttribute('aria-sort', sortDirection === 'desc' ? 'descending' : 'ascending');
            }

            if (column.getCanSort()) {
                const button = document.createElement('button');

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
            const { filterOptions, filterType } = column.columnDef.meta ?? {};

            if (filterType) {
                hasFilters = true;
                const control = filterType === 'select' ? document.createElement('select') : document.createElement('input');

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
                        element.value = option;
                        element.textContent = option;
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

        const rows = table.getRowModel().rows;

        rows.forEach((row) => {
            const tableRow = document.createElement('tr');

            if (selectable) {
                const selectionCell = document.createElement('td');
                const selectRow = document.createElement('input');

                selectRow.setAttribute('aria-label', `Select row ${row.id}`);
                selectRow.dataset.daisyKitTableRowSelect = row.id;
                selectRow.checked = selectedIds.has(row.id);
                selectRow.type = 'checkbox';
                selectRow.addEventListener('change', () => {
                    if (selectRow.checked) selectedIds.add(row.id);
                    else selectedIds.delete(row.id);
                    emit(root, 'selection-changed', { ids: [...selectedIds] });
                    render();
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
                    tableCell.append(input, save, cancel);
                } else if (canEdit) {
                    const value = cell ? formatCell(cell.getValue()) : '';
                    const output = document.createElement('span');
                    const edit = document.createElement('button');

                    output.textContent = value;
                    edit.setAttribute('aria-label', `Edit ${String(column.columnDef.header ?? column.id)} in row ${row.id}`);
                    edit.dataset.daisyKitTableEdit = editKey;
                    edit.textContent = 'Edit';
                    edit.type = 'button';
                    edit.addEventListener('click', () => {
                        editing = { key: editKey, value };
                        render();
                    });
                    tableCell.append(output, edit);
                } else {
                    tableCell.textContent = cell ? formatCell(cell.getValue()) : '';
                }
                tableRow.append(tableCell);
            });

            if (hasRowControls) {
                const actionCell = document.createElement('td');

                if (rowDetails) {
                    const toggle = document.createElement('button');

                    toggle.setAttribute('aria-expanded', String(expandedRowIds.has(row.id)));
                    toggle.dataset.daisyKitTableDetailToggle = row.id;
                    toggle.textContent = rowDetails.label;
                    toggle.type = 'button';
                    toggle.addEventListener('click', () => {
                        if (rowDetails.mode === 'modal') {
                            const dialog = document.createElement('dialog');
                            const title = document.createElement('h2');
                            const close = document.createElement('button');

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
        table.setGlobalFilter(event.currentTarget.value);
    }

    function onPreviousPage() {
        table.previousPage();
    }

    function onNextPage() {
        table.nextPage();
    }

    filter.addEventListener('input', onFilterInput);
    previousButton.addEventListener('click', onPreviousPage);
    nextButton.addEventListener('click', onNextPage);
    render();
    requestRows();

    return () => {
        active = false;
        filter.removeEventListener('input', onFilterInput);
        previousButton.removeEventListener('click', onPreviousPage);
        nextButton.removeEventListener('click', onNextPage);
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
