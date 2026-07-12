/** @vitest-environment jsdom */

import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { init } from '../../../resources/js/treeview.js';

function treeMarkup(options = {}) {
    const selection = options.selection || 'multiple';

    return `
        <div id="tree" data-treeview="1" data-selection="${selection}" data-persist="${options.persist ? 'true' : 'false'}"
            data-name="choices" data-search-enabled="${options.search ? 'true' : 'false'}"
            ${options.lazyUrl ? `data-lazy-url="${options.lazyUrl}" data-lazy-param="node"` : ''}>
            ${options.search ? '<input data-tree-search="1"><p data-tree-status="1"></p>' : '<p data-tree-status="1"></p>'}
            <ul role="tree" aria-label="Choices" ${selection === 'multiple' ? 'aria-multiselectable="true"' : ''} data-tree="1">
                <li role="treeitem" data-id="parent" data-level="1" aria-expanded="false"
                    ${selection === 'multiple' ? 'aria-checked="false"' : 'aria-selected="false"'} tabindex="-1">
                    <div data-node-header="1">
                        <button data-tree-toggle="1" tabindex="-1"><span data-tree-collapsed-icon></span><span data-tree-expanded-icon class="hidden"></span></button>
                        <input data-tree-control="1" tabindex="-1" type="${selection === 'multiple' ? 'checkbox' : 'radio'}">
                        <span data-tree-label="1">Parent</span>
                    </div>
                    <ul role="group" data-tree-group="1" class="hidden">
                        <li role="treeitem" data-id="alpha" data-level="2" ${selection === 'multiple' ? 'aria-checked="false"' : 'aria-selected="false"'} tabindex="-1">
                            <div data-node-header="1"><span></span><input data-tree-control="1" name="choices${selection === 'multiple' ? '[]' : ''}" value="alpha" tabindex="-1" type="${selection === 'multiple' ? 'checkbox' : 'radio'}"><span data-tree-label="1">Alpha</span></div>
                        </li>
                        <li role="treeitem" data-id="unsafe" data-level="2" ${selection === 'multiple' ? 'aria-checked="false"' : 'aria-selected="false"'} tabindex="-1">
                            <div data-node-header="1"><span></span><input data-tree-control="1" name="choices${selection === 'multiple' ? '[]' : ''}" value="unsafe" tabindex="-1" type="${selection === 'multiple' ? 'checkbox' : 'radio'}"><span data-tree-label="1">&lt;img src=x onerror=alert(1)&gt;</span></div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    `;
}

function setup(options = {}) {
    document.body.innerHTML = treeMarkup(options);
    const root = document.querySelector('[data-treeview]');
    return { root, api: init(root) };
}

describe('tree view v2', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        sessionStorage.clear();
        vi.restoreAllMocks();
    });

    afterEach(() => {
        document.querySelectorAll('[data-treeview]').forEach((root) => root.__treeView?.destroy());
    });

    it('initializes one roving tab stop and exposes the instance API', () => {
        const { root, api } = setup();

        expect(root.querySelectorAll('[role="treeitem"][tabindex="0"]')).toHaveLength(1);
        expect(window.DaisyTreeView.get(root)).toBe(api);
        expect(api).toMatchObject({
            getValue: expect.any(Function),
            setValue: expect.any(Function),
            reset: expect.any(Function),
            reload: expect.any(Function),
            destroy: expect.any(Function),
        });
    });

    it('supports expansion, Home, End and arrow navigation', () => {
        const { root } = setup();
        const parent = root.querySelector('[data-id="parent"]');
        parent.focus();
        parent.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowRight', bubbles: true }));
        parent.dispatchEvent(new KeyboardEvent('keydown', { key: 'End', bubbles: true }));

        expect(parent.getAttribute('aria-expanded')).toBe('true');
        expect(document.activeElement.dataset.id).toBe('unsafe');

        document.activeElement.dispatchEvent(new KeyboardEvent('keydown', { key: 'Home', bubbles: true }));
        expect(document.activeElement.dataset.id).toBe('parent');
    });

    it('cascades multiple selection and returns leaf ids only', () => {
        const { root, api } = setup();
        const change = vi.fn();
        root.addEventListener('daisy:tree-change', change);
        root.querySelector('[data-id="parent"]').focus();
        root.querySelector('[data-id="parent"]').dispatchEvent(new KeyboardEvent('keydown', { key: ' ', bubbles: true }));

        expect(api.getValue()).toEqual(['alpha', 'unsafe']);
        expect(root.querySelector('[data-id="parent"]').getAttribute('aria-checked')).toBe('true');
        expect(change).toHaveBeenCalledOnce();
        expect(change.mock.calls[0][0].detail.value).toEqual(['alpha', 'unsafe']);
    });

    it('sets values atomically and resets to the initial value', () => {
        const { api } = setup();

        api.setValue(['alpha']);
        expect(api.getValue()).toEqual(['alpha']);

        api.reset();
        expect(api.getValue()).toEqual([]);
    });

    it('filters and highlights unsafe-looking text without creating elements', () => {
        const { root, api } = setup({ search: true });

        api.search('img');

        const label = root.querySelector('[data-id="unsafe"] [data-tree-label]');
        expect(label.querySelector('mark')?.textContent).toBe('img');
        expect(label.querySelector('img')).toBeNull();
    });

    it('loads normalized lazy items with DOM APIs and inherits parent selection', async () => {
        document.body.innerHTML = treeMarkup({ lazyUrl: '/tree' });
        const root = document.querySelector('[data-treeview]');
        const parent = root.querySelector('[data-id="parent"]');
        parent.dataset.lazy = '1';
        parent.querySelector('[role="group"]').replaceChildren();
        parent.setAttribute('aria-checked', 'true');
        parent.querySelector('[data-tree-control]').checked = true;
        const fetchMock = vi.spyOn(window, 'fetch').mockResolvedValue({
            ok: true,
            json: async () => ({ items: [{ id: '"><img>', label: '<img src=x onerror=alert(1)>' }] }),
        });
        const api = init(root);

        await api.expand('parent');

        const child = Array.from(root.querySelectorAll('[role="treeitem"]')).find((item) => item.dataset.id === '"><img>');
        expect(fetchMock).toHaveBeenCalledOnce();
        expect(child).not.toBeNull();
        expect(child.querySelector('[data-tree-label]').textContent).toBe('<img src=x onerror=alert(1)>');
        expect(child.querySelector('img')).toBeNull();
        expect(api.getValue()).toEqual(['"><img>']);
    });

    it('rejects lazy payloads that ambiguously include children', async () => {
        document.body.innerHTML = treeMarkup({ lazyUrl: '/tree' });
        const root = document.querySelector('[data-treeview]');
        const parent = root.querySelector('[data-id="parent"]');
        parent.dataset.lazy = '1';
        parent.querySelector('[role="group"]').replaceChildren();
        const errorListener = vi.fn();
        root.addEventListener('daisy:tree-error', errorListener);
        const fetchMock = vi.spyOn(window, 'fetch').mockResolvedValue({
            ok: true,
            json: async () => ({
                items: [{
                    id: 'preloaded',
                    label: 'Preloaded',
                    lazy: true,
                    expanded: true,
                    children: [{ id: 'child', label: 'Child' }],
                }],
            }),
        });
        const api = init(root);

        await api.expand('parent');

        expect(fetchMock).toHaveBeenCalledOnce();
        expect(errorListener).toHaveBeenCalledOnce();
        expect(root.querySelector('[data-id="preloaded"]')).toBeNull();
    });

    it('hydrates an initial selected path through complete lazy responses', async () => {
        document.body.innerHTML = `
            <div id="tree" data-treeview="1" data-selection="multiple" data-value-mode="selected-roots"
                data-initial-value='["parent"]' data-initial-expand-paths='[["parent"]]' data-lazy-url="/tree" data-lazy-param="node">
                <p data-tree-status="1"></p>
                <ul role="tree" data-tree="1">
                    <li role="treeitem" data-id="parent" data-level="1" data-lazy="1" aria-expanded="false" aria-checked="false" tabindex="-1">
                        <div data-node-header="1"><button data-tree-toggle="1"></button><input data-tree-control="1" type="checkbox"><span data-tree-label="1">Parent</span></div>
                        <ul role="group" data-tree-group="1" class="hidden"></ul>
                    </li>
                </ul>
            </div>
        `;
        const root = document.querySelector('[data-treeview]');
        const ready = vi.fn();
        root.addEventListener('daisy:tree-ready', ready);
        const fetchMock = vi.spyOn(window, 'fetch').mockResolvedValue({
            ok: true,
            json: async () => ({ items: [{ id: 'child', label: 'Child' }] }),
        });
        const api = init(root);

        await api.ready;

        expect(fetchMock).toHaveBeenCalledOnce();
        expect(api.getValue()).toEqual(['parent']);
        expect(root.querySelector('[data-id="child"] [data-tree-control]').checked).toBe(true);
        expect(ready).toHaveBeenCalledOnce();
        expect(root.hasAttribute('aria-busy')).toBe(false);
    });

    it('persists expansion but not selection', async () => {
        const { api } = setup({ persist: true });

        await api.expand('parent');
        api.setValue(['alpha']);

        expect(JSON.parse(sessionStorage.getItem('treeview:tree'))).toEqual({ expanded: ['parent'] });
    });

    it('emits an error for malformed lazy responses and retries successfully', async () => {
        document.body.innerHTML = treeMarkup({ lazyUrl: '/tree' });
        const root = document.querySelector('[data-treeview]');
        const parent = root.querySelector('[data-id="parent"]');
        parent.dataset.lazy = '1';
        parent.querySelector('[role="group"]').replaceChildren();
        const errorListener = vi.fn();
        const loadListener = vi.fn();
        root.addEventListener('daisy:tree-error', errorListener);
        root.addEventListener('daisy:tree-load', loadListener);
        const fetchMock = vi.spyOn(window, 'fetch')
            .mockResolvedValueOnce({ ok: true, json: async () => ({ items: 'invalid' }) })
            .mockResolvedValueOnce({ ok: true, json: async () => ({ items: [{ id: 'recovered', label: 'Recovered' }] }) });
        const api = init(root);

        await api.expand('parent');
        await api.reload('parent');

        expect(fetchMock).toHaveBeenCalledTimes(2);
        expect(errorListener).toHaveBeenCalledOnce();
        expect(loadListener).toHaveBeenCalledOnce();
        expect(root.textContent).toContain('Recovered');
    });

    it('can be destroyed and initialized again without duplicate handlers', () => {
        const { root, api } = setup();
        api.destroy();

        const replacement = init(root);
        const change = vi.fn();
        root.addEventListener('daisy:tree-change', change);
        root.querySelector('[data-id="alpha"] [data-tree-control]').dispatchEvent(new Event('change', { bubbles: true }));

        expect(replacement).not.toBe(api);
        expect(change).toHaveBeenCalledOnce();
    });
});
