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
    const selectedIds = new Set();
    let abortController = null;
    let requestSerial = 0;
    let rows = normalizeRows(configuration.rows);
    let total = rows.length;
    const state = {
        columnPinning: { left: [], right: [] },
        columnFilters: [],
        columnVisibility: Object.fromEntries(normalizeColumns(configuration.columns).map((column) => [column.id, column.initialVisible])),
        globalFilter: '',
        pagination: { pageIndex: 0, pageSize: normalizePageSize(configuration.pageSize) },
        sorting: [],
    };
    const table = createTable({
        columns: normalizeColumns(configuration.columns),
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
            render();
            emit(root, 'filtered', { query: state.globalFilter });
            requestRows();
        },
        onColumnFiltersChange: (updater) => {
            state.columnFilters = functionalUpdate(updater, state.columnFilters);
            state.pagination.pageIndex = 0;
            render();
            emit(root, 'filtered', { filters: state.columnFilters });
        },
        onColumnPinningChange: (updater) => {
            state.columnPinning = functionalUpdate(updater, state.columnPinning);
            render();
        },
        onColumnVisibilityChange: (updater) => {
            state.columnVisibility = functionalUpdate(updater, state.columnVisibility);
            render();
        },
        onPaginationChange: (updater) => {
            state.pagination = functionalUpdate(updater, state.pagination);
            render();
            emit(root, 'page-changed', { page: state.pagination.pageIndex + 1 });
            requestRows();
        },
        onSortingChange: (updater) => {
            state.sorting = functionalUpdate(updater, state.sorting);
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

    function render() {
        const head = tableElement.tHead;
        const body = tableElement.tBodies.item(0);

        if (!head || !body) {
            return;
        }

        head.replaceChildren();
        body.replaceChildren();
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

        const headerRow = document.createElement('tr');

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

                tableCell.textContent = cell ? formatCell(cell.getValue()) : '';
                tableRow.append(tableCell);
            });

            body.append(tableRow);
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
        filter.removeEventListener('input', onFilterInput);
        previousButton.removeEventListener('click', onPreviousPage);
        nextButton.removeEventListener('click', onNextPage);
        abortController?.abort();
        requestSerial += 1;
        content.innerHTML = initialContent;
    };
}

const module = createMountable('table', initialize);

export const { mount, mountAll, unmount } = module;
