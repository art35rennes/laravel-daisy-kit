import { afterEach, describe, expect, it, vi } from 'vitest';
import { mount, mountAll, unmount } from '../../../resources/js/table.js';

function tableMarkup(configuration) {
    return `
        <section aria-busy="true" data-daisy-kit-module="table">
            <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>
            <div data-daisy-kit-content>
                <label>Filter <input data-daisy-kit-table-filter type="search"></label>
                <table data-daisy-kit-table aria-busy="true"><thead></thead><tbody></tbody></table>
                <nav aria-label="Table pagination"><button data-daisy-kit-table-previous type="button">Previous</button><span data-daisy-kit-table-page></span><button data-daisy-kit-table-next type="button">Next</button></nav>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;
}

describe('table module', () => {
    afterEach(() => vi.restoreAllMocks());

    it('loads a server-backed filtered page and retains selected row identifiers', async () => {
        const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({
            rows: [{ id: 'alpha', name: 'Alpha' }],
            total: 1,
        }), { headers: { 'content-type': 'application/json' } }));
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }],
            pageSize: 10,
            selectable: true,
            source: '/api/people',
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

    it('pins a column through the native column controls', () => {
        document.body.innerHTML = tableMarkup({
            columns: [{ id: 'name', label: 'Name' }, { id: 'status', label: 'Status' }],
            rows: [{ name: 'Alpha', status: 'open' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="table"]');

        mount(root);
        const pin = root.querySelector('[data-daisy-kit-table-column-pinning="status"]');
        pin.value = 'left';
        pin.dispatchEvent(new Event('change'));

        expect(root.querySelector('th button').textContent).toBe('Status');
        expect(root.querySelector('tbody td').textContent).toBe('open');
    });

    it('executes a configured bulk action for selected rows', () => {
        document.body.innerHTML = tableMarkup({ columns: [{ id: 'name', label: 'Name' }], rows: [{ id: 'a', name: 'Alpha' }], selectable: true, bulkActions: [{ id: 'archive', label: 'Archive' }] });
        const root = document.querySelector('[data-daisy-kit-module="table"]');
        const events = [];
        root.addEventListener('daisy-kit:table:bulk-action', (event) => events.push(event.detail));
        mount(root);
        root.querySelector('[data-daisy-kit-table-row-select="a"]').click();
        root.querySelector('[data-daisy-kit-table-bulk-action="archive"]').click();
        expect(events).toEqual([{ id: 'archive', ids: ['a'] }]);
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
