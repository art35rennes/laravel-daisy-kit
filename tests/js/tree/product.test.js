import { afterEach, describe, expect, it, vi } from 'vitest';
import { initialize } from '../../../resources/js/tree/runtime.js';
import { createModel } from '../../../resources/js/tree/model.js';

const instances = [];
function fixture(configuration = {}) {
    const root = document.createElement('section');
    root.innerHTML = `<p data-daisy-kit-status hidden></p><input data-daisy-kit-tree-search>
        <button data-tree-command="applySearch">Search</button><button data-tree-command="clear">Clear</button>
        <ul data-daisy-kit-tree-root role="tree" aria-label="Areas"></ul>
        <p data-daisy-kit-tree-empty></p><p data-daisy-kit-tree-summary></p><p data-daisy-kit-tree-results></p>
        <input data-daisy-kit-tree-value name="areas" value="[]" type="hidden">`;
    document.body.append(root);
    const instance = initialize(root, configuration);
    instances.push(instance);
    return { root, instance, node: (id) => root.querySelector(`[data-daisy-kit-tree-node="${id}"]`) };
}
const items = [{ id: 'docs', label: 'Documentation', children: [
    { id: 'read', label: 'Read articles' }, { id: 'write', label: 'Write articles' },
    { id: 'admin', label: 'Administration', disabled: true },
] }, { id: 'billing', label: 'Billing' }];
function response(items) { return new Response(JSON.stringify({ items }), { headers: { 'content-type': 'application/json' } }); }
function deferred() { let resolve; const promise = new Promise((done) => { resolve = done; }); return { promise, resolve }; }

afterEach(() => {
    instances.splice(0).forEach((instance) => instance?.destroy());
    document.body.replaceChildren();
    localStorage.clear();
    vi.unstubAllGlobals();
    vi.useRealTimers();
});

describe('tree product outcomes', () => {
    it('rejects malformed and reparented payloads without partially mutating the tree', () => {
        const model = createModel({ items });
        expect(() => model.merge([{ id: 'new', label: 'New' }, { id: 'read', label: 'Moved' }])).toThrow();
        expect(model.nodes.has('new')).toBe(false);
        expect(model.nodes.get('read').parentId).toBe('docs');
        expect(() => model.merge([{ id: 'new', label: 'New', source: 'javascript:alert(1)' }])).toThrow();
    });

    it('keeps instance state detached and invalid assignments transactional', () => {
        const first = fixture({ items, multiple: true, value: ['billing'] });
        const second = fixture({ items, multiple: true });
        first.instance.getState().values.push('read');
        first.instance.getState().expandedIds.push('docs');
        expect(first.instance.setValue(['read', 'unknown'])).toBe(false);
        expect(first.instance.getValue()).toEqual(['billing']);
        expect(first.instance.getState().expandedIds).toEqual([]);
        expect(second.instance.getValue()).toEqual([]);
    });

    it('honors an explicit empty initial value over saved selection', async () => {
        const first = fixture({ items, persistenceKey: 'empty-override' });
        first.instance.setValue('billing');
        const second = fixture({ items, persistenceKey: 'empty-override', value: null });
        await Promise.resolve();
        expect(second.instance.getValue()).toBeNull();
    });

    it('debounces automatic fuzzy search and keeps hierarchical order', async () => {
        vi.useFakeTimers();
        const { instance } = fixture({ items, searchMatch: 'fuzzy', searchDebounce: 100 });
        instance.setSearch('artcls');
        await vi.advanceTimersByTimeAsync(99);
        expect(instance.getState().query).toBe('');
        await vi.advanceTimersByTimeAsync(1);
        expect(instance.getState().visibleIds).toEqual(['docs', 'read', 'write']);
        instance.setSearch('Billing');
        instance.clearSearch();
        await vi.advanceTimersByTimeAsync(100);
        expect(instance.getState().query).toBe('');
        expect(instance.getState().visibleIds).toEqual(['docs', 'billing']);
    });

    it('deduplicates lazy requests and opens only the requested ancestor path', async () => {
        const pending = deferred();
        vi.stubGlobal('fetch', vi.fn().mockReturnValue(pending.promise));
        const { instance } = fixture({ items: [{ id: 'remote', label: 'Remote', source: '/remote' }, { id: 'other', label: 'Other', source: '/other' }] });
        instance.expandAll();
        expect(fetch).not.toHaveBeenCalled();
        const path = instance.expandPath(['remote', 'child']);
        const again = instance.expand('remote');
        expect(fetch).toHaveBeenCalledOnce();
        pending.resolve(response([{ id: 'child', label: 'Child' }]));
        expect(await path).toBe(true);
        expect(await again).toBe(true);
        expect(instance.getState().expandedIds).toEqual(['remote']);
    });

    it('discloses branches without selecting them, and submits loaded leaves only', async () => {
        const { root, instance, node } = fixture({ items, multiple: true });
        node('docs').querySelector('[data-tree-action="toggle"]').click();
        expect(node('read').hidden).toBe(false);
        expect(instance.getValue()).toEqual([]);
        node('docs').querySelector('[data-tree-action="select"]').click();
        expect(instance.getValue()).toEqual(['read', 'write']);
        expect(node('docs').getAttribute('aria-checked')).toBe('true');
        node('read').click();
        expect(node('docs').getAttribute('aria-checked')).toBe('mixed');
        expect(root.querySelector('[data-daisy-kit-tree-value]').value).toBe('["write"]');
    });

    it('keeps a disabled whole-tree initial value but rejects editing it', () => {
        const { root, instance } = fixture({ items, value: 'billing', disabled: true });
        expect(instance.getValue()).toBe('billing');
        expect(instance.setValue('read')).toBe(false);
        expect(root.querySelector('[data-daisy-kit-tree-value]').disabled).toBe(true);
    });

    it('counts hidden selections and keeps manual search pending until applied', async () => {
        const { root, instance } = fixture({ items, multiple: true, value: ['read', 'billing'], searchMode: 'manual' });
        expect(instance.getState().selection).toEqual({ total: 2, visible: 1, hidden: 1 });
        instance.setSearch('Read');
        expect(instance.getState().query).toBe('');
        await instance.applySearch();
        expect(instance.getState().visibleIds).toEqual(['docs', 'read']);
        expect(instance.getState().selection).toEqual({ total: 2, visible: 1, hidden: 1 });
        expect(root.querySelector('[data-daisy-kit-tree-summary]').textContent).toContain('2 selected');
        instance.clearSearch();
        expect(instance.getState().expandedIds).toEqual([]);
        expect(instance.getValue()).toEqual(['read', 'billing']);
    });

    it('selects only visible loaded leaves without selecting collapsed descendants', () => {
        const { instance } = fixture({ items, multiple: true });
        instance.selectVisible();
        expect(instance.getValue()).toEqual(['billing']);
        instance.expandAll();
        instance.selectVisible();
        expect(instance.getValue()).toEqual(['billing', 'read', 'write']);
    });

    it('merges server results without losing off-result values or accepting remote HTML', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(response([{ id: 'remote', label: '<img src=x onerror=alert(1)>', html: '<b>bad</b>' }])));
        const { root, instance } = fixture({ items, multiple: true, value: ['billing'], searchSource: '/search', searchMode: 'manual' });
        instance.setSearch('remote');
        await instance.applySearch();
        expect(instance.getState().visibleIds).toEqual(['remote']);
        expect(instance.getValue()).toEqual(['billing']);
        expect(root.querySelector('img')).toBeNull();
        expect(root.querySelector('b')).toBeNull();
        instance.clearSearch();
        expect(instance.getState().visibleIds).toEqual(['docs', 'billing', 'remote']);
    });

    it('ignores obsolete search results even if the server does not honor abort', async () => {
        const older = deferred();
        vi.stubGlobal('fetch', vi.fn().mockReturnValueOnce(older.promise).mockResolvedValueOnce(response([{ id: 'fresh', label: 'Fresh' }])));
        const { instance } = fixture({ items, searchSource: '/search', searchMode: 'manual' });
        instance.setSearch('old');
        const first = instance.applySearch();
        instance.setSearch('fresh');
        await instance.applySearch();
        older.resolve(response([{ id: 'stale', label: 'Stale' }]));
        await first;
        expect(instance.getState().visibleIds).toEqual(['fresh']);
        expect(instance.getState().searching).toBe(false);
    });

    it('caches empty lazy responses and restores focus after loading', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(response([])));
        const { instance, node } = fixture({ items: [{ id: 'remote', label: 'Remote', source: '/remote' }] });
        node('remote').focus();
        expect(await instance.expand('remote')).toBe(true);
        expect(await instance.expand('remote')).toBe(false);
        expect(fetch).toHaveBeenCalledOnce();
        expect(document.activeElement).toBe(node('remote'));
    });

    it('reports a branch failure locally and recovers through its retry control', async () => {
        vi.stubGlobal('fetch', vi.fn().mockRejectedValueOnce(new Error('offline')).mockResolvedValueOnce(response([{ id: 'child', label: 'Child' }])));
        const { root, instance, node } = fixture({ items: [{ id: 'remote', label: 'Remote', source: '/remote' }] });
        expect(await instance.expand('remote')).toBe(false);
        expect(node('remote').textContent).toContain('could not be loaded');
        node('remote').querySelector('[data-tree-action="retry"]').click();
        await vi.waitFor(() => expect(node('child')?.hidden).toBe(false));
        expect(root.querySelector('[data-tree-action="retry"]')).toBeNull();
    });

    it('keeps a root selection when lazy children arrive and supports exclusions', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(response([{ id: 'a', label: 'A' }, { id: 'b', label: 'B' }])));
        const { instance, node } = fixture({ items: [{ id: 'remote', label: 'Remote', source: '/remote' }], multiple: true, valueMode: 'selected-roots' });
        instance.setValue(['remote']);
        await instance.expand('remote');
        expect(node('a').getAttribute('aria-checked')).toBe('true');
        node('a').click();
        expect(instance.getValue()).toEqual(['b']);
    });

    it('removes disappeared branch children and their selected values on reload', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValueOnce(response([{ id: 'a', label: 'A' }])).mockResolvedValueOnce(response([{ id: 'b', label: 'B' }])));
        const { instance, node } = fixture({ items: [{ id: 'remote', label: 'Remote', source: '/remote' }], multiple: true });
        await instance.expand('remote');
        instance.setValue(['a']);
        await instance.reloadBranch('remote');
        expect(node('a')).toBeNull();
        expect(instance.getValue()).toEqual([]);
        expect(node('b')).not.toBeNull();
    });

    it('aborts concurrent branch requests and ignores all late results on destruction', async () => {
        const pending = deferred();
        vi.stubGlobal('fetch', vi.fn().mockReturnValue(pending.promise));
        const { root, instance } = fixture({ items: [{ id: 'a', label: 'A', source: '/a' }, { id: 'b', label: 'B', source: '/b' }] });
        const work = [instance.expand('a'), instance.expand('b')];
        expect(instance.getState().loadingIds).toEqual(['a', 'b']);
        instance.destroy();
        expect(fetch.mock.calls.every((call) => call[1].signal.aborted)).toBe(true);
        pending.resolve(response([{ id: 'late', label: 'Late' }]));
        await Promise.all(work);
        expect(root.querySelector('[data-daisy-kit-tree-node]')).toBeNull();
    });

    it('hydrates a persisted lazy path before restoring its selected value', async () => {
        const config = { items: [{ id: 'remote', label: 'Remote', source: '/remote' }], persistenceKey: 'restore', multiple: true };
        vi.stubGlobal('fetch', vi.fn().mockImplementation(() => Promise.resolve(response([{ id: 'a', label: 'A' }]))));
        const first = fixture(config);
        await first.instance.expand('remote');
        first.instance.setValue(['a']);
        first.instance.destroy();
        const second = fixture(config);
        await vi.waitFor(() => expect(second.instance.getValue()).toEqual(['a']));
        expect(second.node('a').hidden).toBe(false);
    });

    it('restores a collapsed branch without revealing its persisted selection', async () => {
        const config = { items, persistenceKey: 'collapsed', multiple: true };
        const first = fixture(config);
        first.instance.expandAll();
        first.instance.setValue(['read']);
        first.instance.collapseAll();
        const second = fixture(config);
        await vi.waitFor(() => expect(second.instance.getValue()).toEqual(['read']));
        expect(second.instance.getState().expandedIds).toEqual([]);
        expect(second.instance.getState().selection).toEqual({ total: 1, visible: 0, hidden: 1 });
    });

    it('uses roving focus, Home/End and typeahead without selecting on focus', async () => {
        const { instance, node } = fixture({ items });
        instance.expandAll();
        instance.focus('read');
        node('read').dispatchEvent(new KeyboardEvent('keydown', { key: 'End', bubbles: true }));
        expect(document.activeElement).toBe(node('billing'));
        node('billing').dispatchEvent(new KeyboardEvent('keydown', { key: 'w', bubbles: true }));
        expect(document.activeElement).toBe(node('write'));
        instance.collapse('docs');
        expect(document.activeElement).toBe(node('docs'));
        expect(instance.getValue()).toBeNull();
    });
});
