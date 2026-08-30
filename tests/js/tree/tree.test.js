import { afterEach, describe, expect, it, vi } from 'vitest';
import { getInstance, mount, mountAll, unmount } from '../../../resources/js/tree.js';

function treeMarkup(configuration) {
    return `
        <section data-daisy-kit-module="tree">
            <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>
            <div data-daisy-kit-content><ul data-daisy-kit-tree-root aria-label="Tree" role="tree"></ul>${configuration.name ? `<input data-daisy-kit-tree-value name="${configuration.name}" type="hidden" value="[]">` : ''}</div>
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

describe('tree module', () => {
    afterEach(() => vi.restoreAllMocks());

    it('loads a lazy branch from its endpoint and keeps arrow-key navigation', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response(JSON.stringify({
            items: [{ id: 'api', label: 'API reference' }],
        }), { headers: { 'content-type': 'application/json' } })));
        document.body.innerHTML = treeMarkup({
            items: [{ id: 'docs', label: 'Documentation', source: '/tree/docs' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');

        mount(root);
        const docs = root.querySelector('[data-daisy-kit-tree-node="docs"]');
        expect(docs.querySelector('[data-tree-action="toggle"]').classList.contains('btn-ghost')).toBe(true);
        expect(docs.querySelector('input[type="radio"]')).not.toBeNull();
        expect(docs.getAttribute('aria-expanded')).toBe('false');
        docs.focus();
        docs.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-tree-node="api"]')).not.toBeNull());
        root.querySelector('[data-daisy-kit-tree-node="docs"]').dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));

        expect(fetch).toHaveBeenCalledOnce();
        expect(document.activeElement).toBe(root.querySelector('[data-daisy-kit-tree-node="api"]'));
        expect(root.dataset.daisyKitState).toBe('ready');
    });

    it('aborts and ignores a lazy branch response after its tree unmounts', async () => {
        const response = deferred();
        const fetch = vi.fn().mockReturnValue(response.promise);
        vi.stubGlobal('fetch', fetch);
        document.body.innerHTML = treeMarkup({
            items: [{ id: 'docs', label: 'Documentation', source: '/tree/docs' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');

        mount(root);
        root.querySelector('[data-daisy-kit-tree-node="docs"]').dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        await vi.waitFor(() => expect(fetch).toHaveBeenCalledOnce());

        unmount(root);
        response.resolve(new Response(JSON.stringify({ items: [{ id: 'api', label: 'API reference' }] }), {
            headers: { 'content-type': 'application/json' },
        }));
        await Promise.resolve();
        await Promise.resolve();

        expect(fetch.mock.calls[0][1].signal.aborted).toBe(true);
        expect(root.querySelector('[data-daisy-kit-tree-node="api"]')).toBeNull();
        expect(root.hasAttribute('aria-busy')).toBe(false);
    });

    it('debounces remote search results and expands their matching path', async () => {
        vi.useFakeTimers();
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response(JSON.stringify({
            items: [{ id: 'guides', label: 'Guides', children: [{ id: 'api', label: 'API guide' }] }],
        }), { headers: { 'content-type': 'application/json' } })));
        document.body.innerHTML = treeMarkup({
            searchSource: '/tree/search',
            searchable: true,
            items: [],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        root.querySelector('[data-daisy-kit-content]').insertAdjacentHTML('afterbegin', '<label>Search <input data-daisy-kit-tree-search type="search"></label>');

        mount(root);
        const search = root.querySelector('[data-daisy-kit-tree-search]');
        search.value = 'api';
        search.dispatchEvent(new Event('input'));
        await vi.advanceTimersByTimeAsync(200);
        await vi.waitFor(() => expect(root.querySelector('[data-daisy-kit-tree-node="api"]')).not.toBeNull());

        expect(String(fetch.mock.calls[0][0])).toContain('query=api');
        expect(root.querySelector('[data-daisy-kit-tree-node="guides"]').getAttribute('aria-expanded')).toBe('true');
        vi.useRealTimers();
    });

    it('propagates multiple selection, exposes an indeterminate parent, and binds selected IDs', () => {
        document.body.innerHTML = treeMarkup({
            multiple: true,
            name: 'permissions',
            items: [{
                id: 'content',
                label: 'Content',
                children: [{ id: 'read', label: 'Read' }, { id: 'write', label: 'Write' }],
            }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');

        mount(root);
        root.querySelector('[data-daisy-kit-tree-node="content"]').dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        root.querySelector('[data-daisy-kit-tree-node="read"]').click();

        expect(root.querySelector('[data-daisy-kit-tree-node="content"]').getAttribute('aria-checked')).toBe('mixed');
        expect(root.querySelector('[data-daisy-kit-tree-value]').value).toBe('["read"]');

        root.querySelector('[data-daisy-kit-tree-node="content"]').click();

        expect(root.querySelector('[data-daisy-kit-tree-node="content"]').getAttribute('aria-checked')).toBe('true');
        expect(root.querySelector('[data-daisy-kit-tree-value]').value).toBe('["read","write"]');
    });

    it('exposes a stable facade for selection, expansion, and focus', async () => {
        document.body.innerHTML = treeMarkup({
            multiple: true,
            name: 'permissions',
            items: [{
                id: 'content',
                label: 'Content',
                children: [{ id: 'read', label: 'Read' }, { id: 'write', label: 'Write' }],
            }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        const changes = [];
        root.addEventListener('daisy-kit:tree:change', (event) => changes.push(event.detail));

        const tree = mount(root);

        expect(tree).toBe(getInstance(root));
        expect(mount(root)).toBe(tree);
        expect(Object.keys(tree)).toEqual(expect.arrayContaining(['clear', 'collapse', 'expand', 'focus', 'getValue', 'setValue', 'getState', 'setSearch', 'applySearch', 'clearSearch', 'expandPath', 'expandAll', 'collapseAll', 'selectVisible', 'reloadBranch']));
        expect(await tree.expand('content')).toBe(true);
        expect(tree.focus('read')).toBe(true);
        expect(document.activeElement).toBe(root.querySelector('[data-daisy-kit-tree-node="read"]'));
        expect(tree.setValue(['read'])).toBe(true);
        expect(tree.getValue()).toEqual(['read']);
        expect(changes.at(-1)).toEqual({ value: ['read'], values: ['read'] });
        expect(tree.collapse('content')).toBe(true);
        expect(tree.clear()).toBe(true);
        expect(tree.setValue(['missing'])).toBe(false);

        unmount(root);

        expect(getInstance(root)).toBeNull();
    });

    it('submits one JSON array field in single mode while keeping the scalar facade value', () => {
        document.body.innerHTML = treeMarkup({
            name: 'area',
            items: [{ id: 'docs', label: 'Documentation' }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        const tree = mount(root);

        expect(tree.setValue('docs')).toBe(true);
        expect(tree.getValue()).toBe('docs');
        expect(root.querySelector('[data-daisy-kit-tree-value]').name).toBe('area');
        expect(root.querySelector('[data-daisy-kit-tree-value]').value).toBe('["docs"]');
    });

    it('rejects incomplete markup without registering a facade', () => {
        document.body.innerHTML = `
            <section data-daisy-kit-module="tree">
                <p data-daisy-kit-status hidden role="status"></p>
                <script data-daisy-kit-config type="application/json">{"items":[]}</script>
            </section>
        `;
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        const errors = [];
        root.addEventListener('daisy-kit:tree:error', (event) => errors.push(event.detail));

        expect(mount(root)).toBeNull();
        expect(getInstance(root)).toBeNull();
        expect(unmount(root)).toBe(false);
        expect(errors).toEqual([{
            code: 'missing-content',
            message: 'This tree is missing its required markup.',
        }]);
    });

    it('persists the search selection without persisting temporary search expansion', async () => {
        document.body.innerHTML = treeMarkup({
            name: 'area',
            persistenceKey: 'tree-search-fixture',
            searchable: true,
            items: [{ id: 'docs', label: 'Documentation', children: [{ id: 'api', label: 'API reference' }] }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        root.querySelector('[data-daisy-kit-content]').insertAdjacentHTML('afterbegin', '<label>Search <input data-daisy-kit-tree-search type="search"></label>');

        mount(root);
        const search = root.querySelector('[data-daisy-kit-tree-search]');
        search.value = 'api';
        search.dispatchEvent(new Event('input'));
        await getInstance(root).applySearch();
        const api = root.querySelector('[data-daisy-kit-tree-node="api"]');
        api.click();
        unmount(root);

        document.body.innerHTML = treeMarkup({
            name: 'area',
            persistenceKey: 'tree-search-fixture',
            searchable: true,
            items: [{ id: 'docs', label: 'Documentation', children: [{ id: 'api', label: 'API reference' }] }],
        });
        const restored = document.querySelector('[data-daisy-kit-module="tree"]');
        restored.querySelector('[data-daisy-kit-content]').insertAdjacentHTML('afterbegin', '<label>Search <input data-daisy-kit-tree-search type="search"></label>');
        mount(restored);
        await vi.waitFor(() => expect(getInstance(restored).getValue()).toBe('api'));

        expect(api.hidden).toBe(false);
        expect(restored.querySelector('[data-daisy-kit-tree-node="docs"]').getAttribute('aria-expanded')).toBe('false');
        expect(getInstance(restored).getState().selection).toEqual({ total: 1, visible: 0, hidden: 1 });
        expect(restored.querySelector('[data-daisy-kit-tree-node="api"]').getAttribute('aria-selected')).toBe('true');
        expect(restored.querySelector('[data-daisy-kit-tree-value]').value).toBe('["api"]');
    });

    it('expands, selects, and supports arrow-key focus navigation', () => {
        document.body.innerHTML = treeMarkup({
            items: [{ id: 'root', label: 'Root', children: [{ id: 'child', label: 'Child' }] }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        const selected = [];
        root.addEventListener('daisy-kit:tree:change', (event) => selected.push(event.detail));

        mount(root);
        const rootButton = root.querySelector('[data-daisy-kit-tree-node="root"]');
        rootButton.focus();
        rootButton.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        root.querySelector('[data-daisy-kit-tree-node="root"]').dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        document.activeElement.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'Enter' }));

        expect(root.querySelector('[data-daisy-kit-tree-node="child"]').hidden).toBe(false);
        expect(document.activeElement).toBe(root.querySelector('[data-daisy-kit-tree-node="child"]'));
        expect(selected).toEqual([{ value: 'child', values: ['child'] }]);
    });

    it('renders an accessible empty state and tears down every root', () => {
        document.body.innerHTML = `${treeMarkup({ items: [] })}${treeMarkup({ items: [] })}`;
        const roots = [...document.querySelectorAll('[data-daisy-kit-module="tree"]')];

        mountAll();
        roots.forEach(unmount);

        expect(roots.map((root) => root.dataset.daisyKitState)).toEqual([undefined, undefined]);
        expect(roots.every((root) => root.querySelector('[data-daisy-kit-tree-root]').children.length === 0)).toBe(true);
    });

    it('uses roving tab focus without making the tree container tabbable', () => {
        document.body.innerHTML = treeMarkup({
            items: [
                { id: 'first', label: 'First' },
                { id: 'second', label: 'Second' },
            ],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');

        mount(root);
        const first = root.querySelector('[data-daisy-kit-tree-node="first"]');
        const second = root.querySelector('[data-daisy-kit-tree-node="second"]');
        first.focus();
        first.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowDown' }));

        expect(root.querySelector('[data-daisy-kit-tree-root]').hasAttribute('tabindex')).toBe(false);
        expect(first.tabIndex).toBe(-1);
        expect(second.tabIndex).toBe(0);
        expect(document.activeElement).toBe(second);
    });
});
