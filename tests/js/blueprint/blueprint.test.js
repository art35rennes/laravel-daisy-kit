import { describe, expect, it } from 'vitest';

import { mount, mountAll, unmount } from '../../../resources/js/blueprint.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="blueprint">
            <p data-daisy-kit-status hidden role="alert"></p>
            <div data-daisy-kit-content>
                <svg data-daisy-kit-blueprint-canvas></svg>
                <p data-daisy-kit-empty hidden></p>
            </div>
            <script data-daisy-kit-config type="application/json">${JSON.stringify(configuration)}</script>
        </section>
    `;

    return document.querySelector('[data-daisy-kit-module="blueprint"]');
}

describe('blueprint entry', () => {
    it('lays out multiple nodes once and supports keyboard selection', () => {
        const element = root({
            nodes: [{ id: 'first', label: 'First' }, { id: 'second', label: 'Second' }],
            edges: [{ source: 'first', target: 'second' }],
        });
        const selected = [];
        element.addEventListener('daisy-kit:blueprint:select', (event) => selected.push(event.detail.id));

        mountAll();
        mount(element);
        const nodes = [...element.querySelectorAll('[data-daisy-kit-blueprint-node]')];
        nodes[0].focus();
        nodes[0].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        nodes[1].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'Enter' }));

        expect(nodes).toHaveLength(2);
        expect(document.activeElement).toBe(nodes[1]);
        expect(selected).toEqual(['second']);

        unmount(element);

        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node]')).toHaveLength(0);
    });

    it('shows an empty state with no nodes', () => {
        const element = root({ nodes: [], edges: [] });

        mount(element);

        expect(element.dataset.daisyKitState).toBe('empty');
        expect(element.querySelector('[data-daisy-kit-empty]').hidden).toBe(false);
    });
});
