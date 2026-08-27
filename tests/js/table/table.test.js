import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, mountAll, unmount } from '../../../resources/js/table.js';

function tableMarkup(configuration) {
    return `
        <section aria-busy="true" data-daisy-kit-module="table">
            <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>
            <div data-daisy-kit-content>
                <label>Filter <input data-daisy-kit-table-filter type="search"></label>
                <select data-daisy-kit-table-filter="status"><option value="">All</option><option value="true">Yes</option><option value="false">No</option></select>
                <select data-daisy-kit-table-page-size><option value="1">1</option><option value="10">10</option></select>
                <fieldset data-daisy-kit-table-column-controls></fieldset>
                <button data-daisy-kit-table-apply-filters type="button">Apply filters</button>
                <aside data-daisy-kit-table-selection>
                    <strong data-daisy-kit-table-selection-count>0</strong>
                    <span data-daisy-kit-table-selection-breakdown hidden>
                        <strong data-daisy-kit-table-selection-page-count>0</strong>
                        <strong data-daisy-kit-table-selection-off-page-count>0</strong>
                    </span>
                    <button data-daisy-kit-table-select-page type="button">Select page</button>
                    <button data-daisy-kit-table-select-filtered type="button">Select all results</button>
                    <button data-daisy-kit-table-clear-selection type="button">Clear</button>
                    <div data-daisy-kit-table-bulk-actions></div>
                </aside>
                <table data-daisy-kit-table aria-busy="true"><thead></thead><tbody></tbody></table>
                <p data-daisy-kit-table-results></p>
                <nav aria-label="Table pagination"><button data-daisy-kit-table-previous type="button">Previous</button><span data-daisy-kit-table-page></span><button data-daisy-kit-table-next type="button">Next</button></nav>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;
}

function deferred() {
    let resolve;

    const promise = new Promise((resolvePromise) => {
        resolve = resolvePromise;
    });

    return { promise, resolve };
}

describe('table module', () => {
    afterEach(() => {
        vi.restoreAllMocks();
        vi.useRealTimers();
        document.head.innerHTML = '';
        window.history.replaceState({}, '', '/');
    });

    it('composes native DaisyUI controls instead of relying on unstyled browser defaults', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name', filter: { type: 'text' } }],
            rows: [{ id: 'ada', name: 'Ada' }],
            selection: { mode: 'multiple', rowKey: 'id' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);

        expect(root.classList).toContain('card');
        expect(root.querySelector('[data-daisy-kit-table-filter]').classList).toContain('input');
        expect(root.querySelector('[data-daisy-kit-table]').classList).toContain('table');
        expect(root.querySelector('th button').classList).toContain('btn');
        expect(root.querySelector('[data-daisy-kit-table-column-filter="name"]').classList).toContain('input');
        expect(root.querySelector('[data-daisy-kit-table-row-select="ada"]').classList).toContain('checkbox');
    });

    it('supports the restored client contract for keys, page size, selection, and result feedback', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ key: 'name', label: 'Name' }],
            pageSize: 1,
            rows: [{ uuid: 'ada', name: 'Ada' }, { uuid: 'grace', name: 'Grace' }],
            selection: { mode: 'multiple', rowKey: 'uuid', selectFiltered: true },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);

        expect(root.querySelector('tbody').textContent).toContain('Ada');
        expect(root.querySelector('tbody').textContent).not.toContain('Grace');
        expect(root.querySelector('[data-daisy-kit-table-results]').textContent).toBe('1–1 of 2 results');

        root.querySelector('[data-daisy-kit-table-row-select="ada"]').click();
        expect(root.querySelector('[data-daisy-kit-table-selection]').hidden).toBe(false);
        expect(root.querySelector('[data-daisy-kit-table-selection-count]').textContent).toBe('1');

        const pageSize = root.querySelector('[data-daisy-kit-table-page-size]');
        pageSize.value = '10';
        pageSize.dispatchEvent(new Event('change'));

        expect(root.querySelectorAll('tbody tr')).toHaveLength(2);
        expect(root.querySelector('[data-daisy-kit-table-results]').textContent).toBe('1–2 of 2 results');
    });

    it('reports page and off-page selection and can select every filtered result', () => {
        document.body.innerHTML = tableMarkup({
            bulkActions: [{ id: 'archive', label: 'Archive' }],
            columns: [{ key: 'name', label: 'Name' }],
            pageSize: 1,
            rows: [{ id: 'ada', name: 'Ada' }, { id: 'grace', name: 'Grace' }, { id: 'margaret', name: 'Margaret' }],
            selection: { mode: 'multiple', rowKey: 'id', selectFiltered: true },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const bulkEvents = [];
        root.addEventListener('daisy-kit:table:bulk-action', (event) => bulkEvents.push(event.detail));

        mount(root);
        root.querySelector('[data-daisy-kit-table-row-select="ada"]').click();
        root.querySelector('[data-daisy-kit-table-next]').click();
        root.querySelector('[data-daisy-kit-table-row-select="grace"]').click();

        expect(root.querySelector('[data-daisy-kit-table-selection-count]').textContent).toBe('2');
        expect(root.querySelector('[data-daisy-kit-table-selection-page-count]').textContent).toBe('1');
        expect(root.querySelector('[data-daisy-kit-table-selection-off-page-count]').textContent).toBe('1');
        expect(root.querySelector('[data-daisy-kit-table-selection-breakdown]').hidden).toBe(false);

        root.querySelector('[data-daisy-kit-table-select-filtered]').click();
        expect(root.querySelector('[data-daisy-kit-table-selection-count]').textContent).toBe('3');
        expect(root.querySelector('[data-daisy-kit-table-row-select="grace"]').checked).toBe(true);

        root.querySelector('[data-daisy-kit-table-bulk-action="archive"]').click();
        expect(bulkEvents).toEqual([{
            id: 'archive',
            selection: {
                columnFilters: [],
                excludedIds: [],
                globalFilter: '',
                mode: 'filtered',
                sorting: [],
            },
        }]);

        root.querySelector('[data-daisy-kit-table-clear-selection]').click();
        expect(root.querySelector('[data-daisy-kit-table-selection-count]').textContent).toBe('0');
        expect(root.querySelector('[data-daisy-kit-table-row-select="grace"]').checked).toBe(false);
    });

    it('connects toolbar filters and debounced search to client data', () => {
        vi.useFakeTimers();
        document.body.innerHTML = tableMarkup({
            columns: [{ key: 'name', label: 'Name' }, { key: 'status', label: 'Active' }],
            filters: [{ id: 'status', label: 'Active only', type: 'boolean' }],
            rows: [{ name: 'Ada', status: true }, { name: 'Grace', status: false }],
            search: { debounce: 300, enabled: true, mode: 'includes' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        expect(root.querySelector('[data-daisy-kit-table-column-filter="status"]')).toBeNull();
        const status = root.querySelector('[data-daisy-kit-table-filter="status"]');
        status.value = 'true';
        status.dispatchEvent(new Event('change'));
        expect(root.querySelector('tbody').textContent).toContain('Ada');
        expect(root.querySelector('tbody').textContent).not.toContain('Grace');

        status.value = '';
        status.dispatchEvent(new Event('change'));
        const search = root.querySelector('[data-daisy-kit-table-filter=""]');
        search.value = 'Grace';
        search.dispatchEvent(new Event('input'));
        expect(root.querySelector('tbody').textContent).toContain('Ada');

        vi.advanceTimersByTime(300);
        expect(root.querySelector('tbody').textContent).not.toContain('Ada');
        expect(root.querySelector('tbody').textContent).toContain('Grace');
    });

    it('can defer filter changes until the host applies them', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ key: 'name', label: 'Name' }, { key: 'status', label: 'Status' }],
            filterMode: 'manual',
            filters: [{ id: 'status', label: 'Status', type: 'select' }],
            rows: [{ name: 'Ada', status: 'true' }, { name: 'Grace', status: 'false' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const instance = mount(root);
        const status = root.querySelector('[data-daisy-kit-table-filter="status"]');

        status.value = 'true';
        status.dispatchEvent(new Event('change'));

        expect(root.querySelector('tbody').textContent).toContain('Ada');
        expect(root.querySelector('tbody').textContent).toContain('Grace');
        expect(instance.getState().pendingColumnFilters).toEqual([{ id: 'status', value: 'true' }]);

        root.querySelector('[data-daisy-kit-table-apply-filters]').click();

        expect(root.querySelector('tbody').textContent).toContain('Ada');
        expect(root.querySelector('tbody').textContent).not.toContain('Grace');
        expect(instance.getState().columnFilters).toEqual([{ id: 'status', value: 'true' }]);
    });

    it('supports fuzzy search without changing the includes mode', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ key: 'name', label: 'Name' }],
            rows: [{ name: 'Margaret Hamilton' }, { name: 'Grace Hopper' }],
            search: { debounce: 0, enabled: true, mode: 'fuzzy' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        const search = root.querySelector('[data-daisy-kit-table-filter=""]');
        search.value = 'mrgt';
        search.dispatchEvent(new Event('input'));

        expect(root.querySelector('tbody').textContent).toContain('Margaret Hamilton');
        expect(root.querySelector('tbody').textContent).not.toContain('Grace Hopper');
    });

    it('loads a server-backed filtered page and retains selected row identifiers', async () => {
        const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({
            rows: [{ id: 'alpha', name: 'Alpha' }],
            total: 1,
        }), { headers: { 'content-type': 'application/json' } }));
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            pageSize: 10,
            endpoint: '/api/people',
            mode: 'server',
            selection: { mode: 'multiple', rowKey: 'id' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const selections = [];
        root.addEventListener('daisy-kit:table:selection-changed', (event) => selections.push(event.detail));

        mount(root);
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-table-row-select="alpha"]')).not.toBeNull());

        const filter = root.querySelector('[data-daisy-kit-table-filter]');
        filter.value = 'Alpha';
        filter.dispatchEvent(new Event('input'));
        await vi.waitFor(() => expect(String(fetch.mock.calls.at(-1)[0])).toContain('filter=Alpha'));
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-table-row-select="alpha"]')).not.toBeNull());
        root.querySelector('[data-daisy-kit-table-row-select="alpha"]').click();

        expect(String(fetch.mock.calls.at(-1)[0])).toContain('filter=Alpha');
        expect(root.querySelector('tbody td:last-child').textContent).toBe('Alpha');
        expect(root.dataset.daisyKitState).toBe('ready');
        expect(selections).toEqual([{ ids: ['alpha'] }]);
    });

    it('uses Spatie Query Builder parameters and paginator responses', async () => {
        const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({
            data: [{ id: 'alpha', name: 'Alpha', status: 'active' }],
            meta: { current_page: 2, last_page: 3, per_page: 1, total: 3 },
        }), { headers: { 'content-type': 'application/json' } }));
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            columns: [
                { key: 'name', label: 'Name', sortKey: 'users.name' },
                { key: 'status', label: 'Status' },
            ],
            endpoint: '/api/people',
            filters: [{ id: 'status', filterKey: 'state', label: 'State', type: 'select' }],
            globalFilterKey: 'people',
            initialState: {
                columnFilters: [{ id: 'status', value: 'active' }],
                globalFilter: 'alpha',
                pagination: { pageIndex: 1, pageSize: 1 },
                sorting: [{ id: 'name', desc: true }],
            },
            mode: 'server',
            pageSize: 1,
            serverAdapter: 'spatie-query-builder',
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        await vi.waitFor(() => expect(root.dataset.daisyKitState).toBe('ready'));

        const request = new URL(fetch.mock.calls[0][0]);
        expect(request.searchParams.get('filter[people]')).toBe('alpha');
        expect(request.searchParams.get('filter[state]')).toBe('active');
        expect(request.searchParams.get('sort')).toBe('-users.name');
        expect(request.searchParams.get('page[number]')).toBe('2');
        expect(request.searchParams.get('page[size]')).toBe('1');
        expect(root.querySelector('tbody').textContent).toContain('Alpha');
        expect(root.querySelector('[data-daisy-kit-table-page]').textContent).toBe('Page 2 of 3');
    });

    it('keeps a selected server row available to bulk actions after the next page replaces it', async () => {
        const fetch = vi.fn()
            .mockResolvedValueOnce(new Response(JSON.stringify({
                rows: [{ id: 'alpha', name: 'Alpha' }],
                total: 2,
            }), { headers: { 'content-type': 'application/json' } }))
            .mockResolvedValueOnce(new Response(JSON.stringify({
                rows: [{ id: 'beta', name: 'Beta' }],
                total: 2,
            }), { headers: { 'content-type': 'application/json' } }));
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            bulkActions: [{ id: 'archive', label: 'Archive' }],
            columns: [{ id: 'name', label: 'Name' }],
            endpoint: '/api/people',
            mode: 'server',
            selection: { mode: 'multiple', rowKey: 'id' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const bulkEvents = [];
        root.addEventListener('daisy-kit:table:bulk-action', (event) => bulkEvents.push(event.detail));

        mount(root);
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-table-row-select="alpha"]')).not.toBeNull());
        root.querySelector('[data-daisy-kit-table-row-select="alpha"]').click();

        const filter = root.querySelector('[data-daisy-kit-table-filter]');
        filter.value = 'Beta';
        filter.dispatchEvent(new Event('input'));
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-table-row-select="beta"]')).not.toBeNull());

        root.querySelector('[data-daisy-kit-table-bulk-action="archive"]').click();

        expect(bulkEvents).toEqual([{ id: 'archive', ids: ['alpha'] }]);
    });

    it('selects every server result without loading every page and tracks exclusions', async () => {
        const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({
            rows: [{ id: 'alpha', name: 'Alpha' }],
            total: 3,
        }), { headers: { 'content-type': 'application/json' } }));
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            bulkActions: [{ id: 'archive', label: 'Archive' }],
            columns: [{ key: 'name', label: 'Name' }],
            endpoint: '/api/people',
            mode: 'server',
            pageSize: 1,
            selection: { mode: 'multiple', rowKey: 'id', selectFiltered: true },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const bulkEvents = [];
        root.addEventListener('daisy-kit:table:bulk-action', (event) => bulkEvents.push(event.detail));

        mount(root);
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-table-row-select="alpha"]')).not.toBeNull());
        root.querySelector('[data-daisy-kit-table-select-filtered]').click();

        expect(root.querySelector('[data-daisy-kit-table-selection-count]').textContent).toBe('3');
        root.querySelector('[data-daisy-kit-table-row-select="alpha"]').click();
        expect(root.querySelector('[data-daisy-kit-table-selection-count]').textContent).toBe('2');
        expect(root.querySelector('[data-daisy-kit-table-selection-page-count]').textContent).toBe('0');
        expect(root.querySelector('[data-daisy-kit-table-selection-off-page-count]').textContent).toBe('2');

        root.querySelector('[data-daisy-kit-table-bulk-action="archive"]').click();
        expect(bulkEvents).toEqual([{
            id: 'archive',
            selection: {
                columnFilters: [],
                excludedIds: ['alpha'],
                globalFilter: '',
                mode: 'filtered',
                sorting: [],
            },
        }]);
    });

    it('filters typed text, number, and select columns independently', () => {
        document.body.innerHTML = tableMarkup({
            columns: [
                { id: 'name', label: 'Name', filter: { type: 'text' } },
                { id: 'amount', label: 'Amount', filter: { type: 'number' } },
                { id: 'status', label: 'Status', filter: { options: ['open', 'closed'], type: 'select' } },
            ],
            rows: [
                { amount: 12, name: 'Alpha', status: 'open' },
                { amount: 20, name: 'Beta', status: 'closed' },
            ],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        const textFilter = root.querySelector('[data-daisy-kit-table-column-filter="name"]');
        textFilter.value = 'alpha';
        textFilter.dispatchEvent(new Event('input'));
        const amountFilter = root.querySelector('[data-daisy-kit-table-column-filter="amount"]');
        amountFilter.value = '12';
        amountFilter.dispatchEvent(new Event('input'));
        const statusFilter = root.querySelector('[data-daisy-kit-table-column-filter="status"]');
        statusFilter.value = 'open';
        statusFilter.dispatchEvent(new Event('change'));

        expect(root.querySelectorAll('tbody tr')).toHaveLength(1);
        expect(root.querySelector('tbody td').textContent).toBe('Alpha');
    });

    it('renders only explicitly trusted or server-rendered cell markup as HTML', () => {
        document.body.innerHTML = tableMarkup({
            columns: [
                { key: 'safe', label: 'Safe' },
                { key: 'status', label: 'Status', cell: { renderer: 'trusted-html' } },
                { key: 'person', label: 'Person', cell: { renderer: 'blade', view: 'people.cell' } },
            ],
            rows: [{
                person: '<span data-person>Ada</span>',
                safe: '<img data-unsafe src=x>',
                status: '<span class="badge badge-success">Ready</span>',
            }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);

        expect(root.querySelector('[data-unsafe]')).toBeNull();
        expect(root.querySelector('tbody').textContent).toContain('<img data-unsafe src=x>');
        expect(root.querySelector('.badge-success').textContent).toBe('Ready');
        expect(root.querySelector('[data-person]').textContent).toBe('Ada');
    });

    it('lets users reveal a configured hidden column without affecting other columns', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }, { id: 'internal', label: 'Internal', visible: false }],
            rows: [{ internal: 'private', name: 'Alpha' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        expect(root.querySelector('tbody').textContent).not.toContain('private');
        const control = root.querySelector('[data-daisy-kit-table-column-visibility="internal"]');
        control.checked = true;
        control.dispatchEvent(new Event('change'));

        expect(root.querySelector('tbody').textContent).toContain('private');
    });

    it('removes column controls when column visibility is disabled', () => {
        document.body.innerHTML = tableMarkup({
            columnVisibility: false,
            columns: [{ id: 'name', label: 'Name' }],
            rows: [{ name: 'Alpha' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);

        expect(root.querySelector('[data-daisy-kit-table-column-controls]')).toBeNull();
    });

    it('pins a column through the native column controls', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }, { id: 'status', label: 'Status' }],
            rows: [{ name: 'Alpha', status: 'open' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        const pin = root.querySelector('[data-daisy-kit-table-column-pinning="status"]');
        pin.value = 'start';
        pin.dispatchEvent(new Event('change'));

        expect(root.querySelector('th button').textContent).toBe('Status');
        expect(root.querySelector('tbody td').textContent).toBe('open');
    });

    it('executes a configured bulk action for selected rows', () => {
        document.body.innerHTML = tableMarkup({ columns: [{ id: 'name', label: 'Name' }], rows: [{ id: 'a', name: 'Alpha' }], selection: { mode: 'multiple', rowKey: 'id' }, bulkActions: [{ id: 'archive', label: 'Archive' }] });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const events = [];
        root.addEventListener('daisy-kit:table:bulk-action', (event) => events.push(event.detail));
        mount(root);
        root.querySelector('[data-daisy-kit-table-row-select="a"]').click();
        root.querySelector('[data-daisy-kit-table-bulk-action="archive"]').click();
        expect(events).toEqual([{ id: 'archive', ids: ['a'] }]);
    });

    it('opens a row detail and dispatches configured row actions with the row contract', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }, { id: 'summary', label: 'Summary' }],
            rows: [{ id: 'a', name: 'Alpha', summary: 'Ready for review' }],
            rowActions: [{ id: 'approve', label: 'Approve' }],
            rowDetails: { accessor: 'summary', label: 'Details', mode: 'inline' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const events = [];
        root.addEventListener('daisy-kit:table:row-action', (event) => events.push(event.detail));

        mount(root);
        root.querySelector('[data-daisy-kit-table-detail-toggle="a"]').click();

        expect(root.querySelector('[data-daisy-kit-table-detail="a"]').textContent).toContain('Ready for review');

        root.querySelector('[data-daisy-kit-table-row-action="approve"]').click();
        expect(events).toEqual([{
            id: 'approve',
            row: { id: 'a', name: 'Alpha', summary: 'Ready for review' },
            rowId: 'a',
        }]);

        root.querySelector('[data-daisy-kit-table-detail-toggle="a"]').click();
        expect(root.querySelector('[data-daisy-kit-table-detail="a"]')).toBeNull();
    });

    it('removes a modal row detail when the table unmounts', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            rowDetails: { accessor: 'name', mode: 'modal' },
            rows: [{ id: 'a', name: 'Alpha' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        root.querySelector('[data-daisy-kit-table-detail-toggle="a"]').click();

        expect(root.querySelector('dialog[data-daisy-kit-table-detail="a"]')).not.toBeNull();

        unmount(root);

        expect(root.querySelector('dialog[data-daisy-kit-table-detail="a"]')).toBeNull();
    });

    it('edits an explicitly editable cell and enriches the remote mutation with its row contract', async () => {
        const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({
            row: { id: 'a', name: 'Approved' },
        }), { headers: { 'content-type': 'application/json' } }));
        vi.stubGlobal('fetch', fetch);
        document.head.innerHTML = '<meta name="csrf-token" content="workbench-token">';
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            editable: { columns: ['name'], endpoint: '/api/people/{rowId}', method: 'PATCH' },
            rows: [{ id: 'a', name: 'Alpha' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const edits = [];
        root.addEventListener('daisy-kit:table:edited', (event) => edits.push(event.detail));

        mount(root);
        root.querySelector('[data-daisy-kit-table-edit="a:name"]').click();
        expect(root.querySelector('[data-daisy-kit-table-edit="a:name"]')).toBeNull();
        expect(root.querySelector('[data-daisy-kit-table-edit-input="a:name"]').closest('.daisy-kit-table__cell-editor')).not.toBeNull();
        expect(root.querySelector('[data-daisy-kit-table-edit-save="a:name"]').closest('.daisy-kit-table__cell-editor-actions')).not.toBeNull();
        const input = root.querySelector('[data-daisy-kit-table-edit-input="a:name"]');
        input.value = 'Approved';
        root.querySelector('[data-daisy-kit-table-edit-save="a:name"]').click();

        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(1));
        expect(fetch.mock.calls[0][0]).toContain('/api/people/a');
        expect(fetch.mock.calls[0][1]).toMatchObject({ method: 'PATCH' });
        expect(fetch.mock.calls[0][1].headers).toMatchObject({ 'X-CSRF-TOKEN': 'workbench-token' });
        expect(JSON.parse(fetch.mock.calls[0][1].body)).toEqual({
            column: 'name',
            dirty: { name: 'Approved' },
            row: { id: 'a', name: 'Alpha' },
            rowId: 'a',
            value: 'Approved',
        });
        await vi.waitFor(() => expect(root.querySelector('tbody').textContent).toContain('Approved'));
        expect(edits).toEqual([{
            column: 'name',
            row: { id: 'a', name: 'Approved' },
            rowId: 'a',
            value: 'Approved',
        }]);
    });

    it('aborts and ignores a pending remote edit after its table unmounts', async () => {
        const response = deferred();
        const fetch = vi.fn().mockReturnValue(response.promise);
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            editable: { columns: ['name'], endpoint: '/api/people/{rowId}', method: 'PATCH' },
            rows: [{ id: 'a', name: 'Alpha' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const edits = [];
        root.addEventListener('daisy-kit:table:edited', (event) => edits.push(event.detail));

        mount(root);
        root.querySelector('[data-daisy-kit-table-edit="a:name"]').click();
        const input = root.querySelector('[data-daisy-kit-table-edit-input="a:name"]');
        input.value = 'Approved';
        root.querySelector('[data-daisy-kit-table-edit-save="a:name"]').click();
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());

        unmount(root);
        response.resolve(new Response(JSON.stringify({ row: { id: 'a', name: 'Approved' } }), {
            headers: { 'content-type': 'application/json' },
        }));
        await Promise.resolve();
        await Promise.resolve();

        expect(fetch.mock.calls[0][1].signal.aborted).toBe(true);
        expect(root.querySelector('tbody').textContent).not.toContain('Approved');
        expect(edits).toEqual([]);
    });

    it('restores and writes an instance-scoped URL persistence contract without global table state', () => {
        const parameter = 'daisy-kit-table[orders]';
        const savedState = JSON.stringify({
            columnVisibility: { name: true, status: false },
            globalFilter: 'Alpha',
            pagination: { pageIndex: 0, pageSize: 1 },
            sorting: [{ desc: false, id: 'name' }],
        });
        const query = new URLSearchParams([[parameter, savedState]]);
        window.history.replaceState({}, '', `/?${query}`);
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }, { id: 'status', label: 'Status' }],
            persistState: { key: 'orders', mode: 'url' },
            rows: [{ name: 'Beta', status: 'closed' }, { name: 'Alpha', status: 'open' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);

        expect(root.querySelector('tbody').textContent).toContain('Alpha');
        expect(root.querySelector('tbody').textContent).not.toContain('open');
        expect(root.querySelector('[data-daisy-kit-table-column-visibility="status"]').checked).toBe(false);

        const control = root.querySelector('[data-daisy-kit-table-column-visibility="status"]');
        control.checked = true;
        control.dispatchEvent(new Event('change'));
        const persisted = JSON.parse(new URL(window.location.href).searchParams.get(parameter));

        expect(persisted.columnVisibility.status).toBe(true);
        expect(persisted.globalFilter).toBe('Alpha');
    });

    it('keeps the table usable when local storage cannot be read or written', () => {
        vi.spyOn(Storage.prototype, 'getItem').mockImplementation(() => {
            throw new DOMException('Storage is unavailable.', 'SecurityError');
        });
        vi.spyOn(Storage.prototype, 'setItem').mockImplementation(() => {
            throw new DOMException('Storage quota exceeded.', 'QuotaExceededError');
        });
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            persistState: { key: 'private-context', mode: 'local' },
            rows: [{ name: 'Beta' }, { name: 'Alpha' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        expect(() => mount(root)).not.toThrow();
        expect(root.querySelector('tbody').textContent).toContain('Beta');

        expect(() => root.querySelector('th button').click()).not.toThrow();
        expect(root.querySelector('tbody').textContent).toContain('Alpha');
    });

    it('applies a declared initial state before a persistence backend is configured', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }, { id: 'status', label: 'Status' }],
            initialState: {
                columnVisibility: { status: false },
                globalFilter: 'Beta',
                sorting: [{ desc: false, id: 'name' }],
            },
            rows: [{ name: 'Beta', status: 'open' }, { name: 'Alpha', status: 'closed' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);

        expect(root.querySelector('tbody').textContent).toContain('Beta');
        expect(root.querySelector('tbody').textContent).not.toContain('open');
        expect(root.querySelector('[data-daisy-kit-table-filter]').value).toBe('Beta');
    });

    it('enriches server requests with typed filters and column state', async () => {
        const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({ rows: [], total: 0 }), {
            headers: { 'content-type': 'application/json' },
        }));
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            columns: [
                { id: 'name', label: 'Name', filter: { type: 'text' } },
                { id: 'status', label: 'Status', filter: { options: ['open'], type: 'select' } },
            ],
            endpoint: '/api/orders',
            mode: 'server',
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledTimes(1));
        const nameFilter = root.querySelector('[data-daisy-kit-table-column-filter="name"]');
        nameFilter.value = 'alpha';
        nameFilter.dispatchEvent(new Event('input'));
        const statusFilter = root.querySelector('[data-daisy-kit-table-column-filter="status"]');
        statusFilter.value = 'open';
        statusFilter.dispatchEvent(new Event('change'));
        const pin = root.querySelector('[data-daisy-kit-table-column-pinning="status"]');
        pin.value = 'start';
        pin.dispatchEvent(new Event('change'));

        await vi.waitFor(() => expect(fetch.mock.calls.length).toBeGreaterThan(2));
        const request = new URL(fetch.mock.calls.at(-1)[0]);

        expect(JSON.parse(request.searchParams.get('columnFilters'))).toEqual([
            { id: 'name', value: 'alpha' },
            { id: 'status', value: 'open' },
        ]);
        expect(JSON.parse(request.searchParams.get('columnPinning'))).toEqual({ end: [], start: ['status'] });
        expect(JSON.parse(request.searchParams.get('columnVisibility'))).toEqual({ name: true, status: true });
    });

    it('sorts, filters, and reports state changes without global state', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            rows: [{ name: 'Beta' }, { name: 'Alpha' }],
            pageSize: 10,
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const events = [];
        root.addEventListener('daisy-kit:table:sorted', (event) => events.push(event.detail));

        mount(root);
        root.querySelector('th button').click();
        root.querySelector('[data-daisy-kit-table-filter]').value = 'Alpha';
        root.querySelector('[data-daisy-kit-table-filter]').dispatchEvent(new Event('input'));

        expect(root.querySelectorAll('tbody tr')).toHaveLength(1);
        expect(root.querySelector('tbody td').textContent).toBe('Alpha');
        expect(events).toEqual([{ column: 'name', direction: 'asc' }]);
        expect(root.querySelector('th').getAttribute('aria-sort')).toBe('ascending');
        expect(root.getAttribute('aria-busy')).toBe('false');
    });

    it('exposes an instance-local facade for host filters and wrapped controls', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ key: 'name', label: 'Name' }, { key: 'status', label: 'Status' }],
            filters: [{ id: 'status', label: 'Status', type: 'select' }],
            pageSize: 1,
            rows: [
                { id: 'ada', name: 'Ada', status: 'ready' },
                { id: 'grace', name: 'Grace', status: 'review' },
                { id: 'margaret', name: 'Margaret', status: 'ready' },
            ],
            selection: { mode: 'multiple', rowKey: 'id' },
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        const instance = mount(root);

        expect(instance).toBe(getInstance(root));
        expect(mount(root)).toBe(instance);
        instance.setColumnFilter('status', 'ready');
        instance.setGlobalFilter('Margaret');
        expect(root.querySelector('tbody').textContent).toContain('Margaret');
        expect(instance.getState()).toMatchObject({
            columnFilters: [{ id: 'status', value: 'ready' }],
            globalFilter: 'Margaret',
            total: 1,
        });

        instance.clearFilters();
        instance.setPage(2);
        expect(root.querySelector('tbody').textContent).toContain('Grace');
        instance.selectPage();
        expect(instance.getState().selection).toMatchObject({ selectedIds: ['grace'], selectedTotal: 1 });

        instance.setPageSize(3);
        expect(root.querySelectorAll('tbody tr')).toHaveLength(3);
        expect(instance.getVisibleRows()).toHaveLength(3);

        unmount(root);
        expect(getInstance(root)).toBeNull();
    });

    it('mounts multiple roots idempotently and restores them on teardown', () => {
        document.body.innerHTML = `${tableMarkup({ columns: [], rows: [] })}${tableMarkup({ columns: [], rows: [] })}`;
        const roots = [...document.querySelectorAll('[data-daisy-kit-module="table"]')];

        mountAll();
        mount(roots[0]);
        roots.forEach(unmount);

        expect(roots.map((root) => root.dataset.daisyKitState)).toEqual([undefined, undefined]);
        expect(roots.every((root) => root.querySelector('[data-daisy-kit-table]').getAttribute('aria-busy') === 'true')).toBe(true);
    });

    it('keeps the sortable header keyboard-focusable', () => {
        document.body.innerHTML = tableMarkup({ columns: [{ id: 'name', label: 'Name' }], rows: [{ name: 'Alpha' }] });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        root.querySelector('th button').focus();

        expect(document.activeElement).toBe(root.querySelector('th button'));
    });
});
