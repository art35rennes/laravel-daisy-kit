import { afterEach, describe, expect, it, vi } from 'vitest';
import { mount, mountAll, unmount } from '../../../resources/js/tree.js';

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
        expect(docs.classList.contains('btn-ghost')).toBe(true);
        expect(docs.classList.contains('justify-start')).toBe(true);
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

    it('searches local branches, expands matching paths, and persists the selected result', () => {
        document.body.innerHTML = treeMarkup({
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
        const api = root.querySelector('[data-daisy-kit-tree-node="api"]');
        api.click();
        unmount(root);

        document.body.innerHTML = treeMarkup({
            persistenceKey: 'tree-search-fixture',
            searchable: true,
            items: [{ id: 'docs', label: 'Documentation', children: [{ id: 'api', label: 'API reference' }] }],
        });
        const restored = document.querySelector('[data-daisy-kit-module="tree"]');
        restored.querySelector('[data-daisy-kit-content]').insertAdjacentHTML('afterbegin', '<label>Search <input data-daisy-kit-tree-search type="search"></label>');
        mount(restored);

        expect(api.hidden).toBe(false);
        expect(restored.querySelector('[data-daisy-kit-tree-node="docs"]').getAttribute('aria-expanded')).toBe('true');
        expect(restored.querySelector('[data-daisy-kit-tree-node="api"]').getAttribute('aria-selected')).toBe('true');
    });

    it('expands, selects, and supports arrow-key focus navigation', () => {
        document.body.innerHTML = treeMarkup({
            items: [{ id: 'root', label: 'Root', children: [{ id: 'child', label: 'Child' }] }],
        });
        const root = document.querySelector('[data-daisy-kit-module="tree"]');
        const selected = [];
        root.addEventListener('daisy-kit:tree:selected', (event) => selected.push(event.detail));

        mount(root);
        const rootButton = root.querySelector('[data-daisy-kit-tree-node="root"]');
        rootButton.focus();
        rootButton.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        rootButton.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        document.activeElement.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'Enter' }));

        expect(root.querySelector('[data-daisy-kit-tree-node="child"]').hidden).toBe(false);
        expect(document.activeElement).toBe(root.querySelector('[data-daisy-kit-tree-node="child"]'));
        expect(selected).toEqual([{ id: 'child', label: 'Child' }]);
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
