import { describe, expect, it } from 'vitest';
import { mount, mountAll, unmount } from '../../../resources/js/tree.js';

function treeMarkup(configuration) {
    return `
        <section data-daisy-kit-module="tree">
            <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>
            <div data-daisy-kit-content><ul data-daisy-kit-tree-root aria-label="Tree" role="tree" tabindex="0"></ul></div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;
}

describe('tree module', () => {
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
});
