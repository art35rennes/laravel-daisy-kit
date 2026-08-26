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

        return [{
            accessorKey,
            enableSorting: column.sortable !== false,
            header: label,
            id,
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
    const state = {
        columnPinning: { left: [], right: [] },
        globalFilter: '',
        pagination: { pageIndex: 0, pageSize: normalizePageSize(configuration.pageSize) },
        sorting: [],
    };
    const table = createTable({
        columns: normalizeColumns(configuration.columns),
        data: normalizeRows(configuration.rows),
        getCoreRowModel: getCoreRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        onGlobalFilterChange: (updater) => {
            state.globalFilter = functionalUpdate(updater, state.globalFilter);
            state.pagination.pageIndex = 0;
            render();
            emit(root, 'filtered', { query: state.globalFilter });
        },
        onPaginationChange: (updater) => {
            state.pagination = functionalUpdate(updater, state.pagination);
            render();
            emit(root, 'page-changed', { page: state.pagination.pageIndex + 1 });
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

        const headerRow = document.createElement('tr');

        table.getFlatHeaders().forEach((header) => {
            const headerCell = document.createElement('th');
            const column = header.column;
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

        const rows = table.getRowModel().rows;

        rows.forEach((row) => {
            const tableRow = document.createElement('tr');

            row.getVisibleCells().forEach((cell) => {
                const tableCell = document.createElement('td');

                tableCell.textContent = formatCell(cell.getValue());
                tableRow.append(tableCell);
            });

            body.append(tableRow);
        });

        const filteredRows = table.getFilteredRowModel().rows;
        const pageCount = Math.max(table.getPageCount(), 1);

        const empty = filteredRows.length === 0;

        updateStatus(root, empty ? 'No table rows match the current filter.' : null);
        root.dataset.daisyKitState = empty ? 'empty' : 'ready';
        tableElement.setAttribute('aria-busy', 'false');
        previousButton.disabled = !table.getCanPreviousPage();
        nextButton.disabled = !table.getCanNextPage();
        page.textContent = `Page ${state.pagination.pageIndex + 1} of ${pageCount}`;
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

    return () => {
        filter.removeEventListener('input', onFilterInput);
        previousButton.removeEventListener('click', onPreviousPage);
        nextButton.removeEventListener('click', onNextPage);
        content.innerHTML = initialContent;
    };
}

const module = createMountable('table', initialize);

export const { mount, mountAll, unmount } = module;
