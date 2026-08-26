import { describe, expect, it } from 'vitest';
import { mount, mountAll, unmount } from '../../../resources/js/table.js';

function tableMarkup(configuration) {
    return `
        <section data-daisy-kit-module="table">
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
