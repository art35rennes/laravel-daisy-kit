import { describe, expect, it } from 'vitest';

import { mount, mountAll, unmount } from '../../../resources/js/blueprint.js';

function root(configuration) {
    document.body.innerHTML = `
        <section data-daisy-kit-module="blueprint">
            <p data-daisy-kit-status hidden role="alert"></p>
            <div data-daisy-kit-content>
                <svg data-daisy-kit-blueprint-canvas></svg>
                <p data-daisy-kit-empty hidden></p>
                <input data-daisy-kit-blueprint-value type="hidden">
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
        const controls = [...element.querySelectorAll('[data-daisy-kit-blueprint-node-control]')];
        controls[0].focus();
        controls[0].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'ArrowRight' }));
        controls[1].dispatchEvent(new KeyboardEvent('keydown', { bubbles: true, key: 'Enter' }));
        nodes[0].dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(nodes).toHaveLength(2);
        expect(controls).toHaveLength(2);
        expect(nodes.every((node) => !node.hasAttribute('role') && !node.hasAttribute('tabindex'))).toBe(true);
        expect(document.activeElement).toBe(controls[1]);
        expect(selected).toEqual(['second', 'first']);

        unmount(element);

        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node]')).toHaveLength(0);
    });

    it('shows an empty state with no nodes', () => {
        const element = root({ nodes: [], edges: [] });

        mount(element);

        expect(element.dataset.daisyKitState).toBe('empty');
        expect(element.querySelector('[data-daisy-kit-empty]').hidden).toBe(false);
    });

    it('edits labels with undo and redo while synchronizing hidden JSON', () => {
        const element = root({
            editable: true,
            edges: [],
            nodes: [{ id: 'first', label: 'First', value: { state: 'draft' } }],
        });
        const changes = [];
        element.addEventListener('daisy-kit:blueprint:change', (event) => changes.push(event.detail.value));

        mount(element);
        element.querySelector('[data-daisy-kit-blueprint-node-control]').click();
        const editor = element.querySelector('[data-daisy-kit-blueprint-editor]');
        editor.value = 'Updated';
        editor.dispatchEvent(new Event('change'));
        const valueEditor = element.querySelector('[data-daisy-kit-blueprint-value-editor]');
        valueEditor.value = '{"state":"published"}';
        valueEditor.dispatchEvent(new Event('change'));

        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes[0].label).toBe('Updated');
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes[0].value).toEqual({ state: 'published' });
        expect(changes).toHaveLength(2);

        element.querySelector('[data-daisy-kit-blueprint-history="undo"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes[0].value).toEqual({ state: 'draft' });
        element.querySelector('[data-daisy-kit-blueprint-history="undo"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes[0].label).toBe('First');

        element.querySelector('[data-daisy-kit-blueprint-history="redo"]').click();
        element.querySelector('[data-daisy-kit-blueprint-history="redo"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes[0].label).toBe('Updated');
    });
});
