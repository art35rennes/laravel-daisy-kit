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
        expect(controls.every((control) => control.classList.contains('btn-outline'))).toBe(true);
        expect(element.querySelector('[data-daisy-kit-blueprint-view="arrange"]').classList.contains('btn')).toBe(true);
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

    it('keeps structural controls out of read-only blueprints while arranging and fitting the diagram', () => {
        const element = root({
            edges: [{ source: 'first', target: 'second' }],
            nodes: [{ id: 'first', label: 'First' }, { id: 'second', label: 'Second' }],
        });
        const events = [];
        element.addEventListener('daisy-kit:blueprint:arrange', (event) => events.push(event.type));
        element.addEventListener('daisy-kit:blueprint:fit', (event) => events.push(event.type));

        mount(element);
        const canvas = element.querySelector('[data-daisy-kit-blueprint-canvas]');
        element.querySelector('[data-daisy-kit-blueprint-view="arrange"]').click();
        element.querySelector('[data-daisy-kit-blueprint-view="fit"]').click();

        expect(element.querySelector('[data-daisy-kit-blueprint-structure]')).toBeNull();
        expect(events).toEqual(['daisy-kit:blueprint:arrange', 'daisy-kit:blueprint:fit']);
        expect(canvas.getAttribute('preserveAspectRatio')).toBe('xMidYMid meet');
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes).toHaveLength(2);
    });

    it('edits labels with undo and redo while synchronizing hidden JSON', () => {
        const element = root({
            editable: true,
            edges: [],
            nodes: [{ id: 'first', label: 'First', value: { state: 'draft' } }, { id: 'second', label: 'Second' }],
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

        const search = element.querySelector('[data-daisy-kit-blueprint-search]');
        search.value = 'second';
        search.dispatchEvent(new Event('input'));
        expect(element.querySelector('[data-node-id="first"]').hasAttribute('hidden')).toBe(true);
        expect(element.querySelector('[data-node-id="second"]').hasAttribute('hidden')).toBe(false);

        search.value = '';
        search.dispatchEvent(new Event('input'));
        element.querySelector('[data-daisy-kit-blueprint-structure="add-node"]').click();
        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node-control]')).toHaveLength(3);

        element.querySelector('[data-daisy-kit-blueprint-history="undo"]').click();
        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node-control]')).toHaveLength(2);
        element.querySelector('[data-daisy-kit-blueprint-history="redo"]').click();
        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node-control]')).toHaveLength(3);

        element.querySelector('[data-daisy-kit-blueprint-node-control]').click();
        element.querySelector('[data-daisy-kit-blueprint-transition-target]').value = 'second';
        element.querySelector('[data-daisy-kit-blueprint-structure="add-transition"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).edges).toContainEqual({ source: 'first', target: 'second' });

        element.querySelector('[data-daisy-kit-blueprint-node-control]').click();
        element.querySelector('[data-daisy-kit-blueprint-structure="remove-node"]').click();
        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node-control]')).toHaveLength(2);
    });

    it('keeps its named hidden JSON field synchronized after a structural edit', () => {
        const element = root({
            editable: true,
            edges: [],
            name: 'workflow',
            nodes: [{ id: 'first', label: 'First' }],
        });
        const value = element.querySelector('[data-daisy-kit-blueprint-value]');
        value.name = 'workflow';
        const changes = [];
        value.addEventListener('change', () => changes.push(value.value));

        mount(element);
        element.querySelector('[data-daisy-kit-blueprint-structure="add-node"]').click();

        expect(value.name).toBe('workflow');
        expect(JSON.parse(value.value).nodes).toHaveLength(2);
        expect(changes).toHaveLength(1);
    });

    it('uses an initial JSON value as the named graph field contract', () => {
        const element = root({
            edges: [],
            name: 'workflow',
            nodes: [],
            value: JSON.stringify({ edges: [], nodes: [{ id: 'from-value', label: 'From value' }] }),
        });

        mount(element);

        expect(element.querySelector('[data-daisy-kit-blueprint-node-control]').textContent).toBe('From value');
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes[0].id).toBe('from-value');
    });

    it('retains structural edits, history, and hidden JSON after remounting an initial value graph', () => {
        const element = root({
            editable: true,
            value: JSON.stringify({
                edges: [],
                nodes: [{ id: 'first', label: 'First' }, { id: 'second', label: 'Second' }],
            }),
        });

        mount(element);
        element.querySelector('[data-daisy-kit-blueprint-structure="add-node"]').click();

        expect(element.querySelectorAll('[data-daisy-kit-blueprint-node-control]')).toHaveLength(3);
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes.map((node) => node.id))
            .toEqual(['first', 'second', 'node-3']);

        element.querySelector('[data-daisy-kit-blueprint-history="undo"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes).toHaveLength(2);

        element.querySelector('[data-daisy-kit-blueprint-history="redo"]').click();
        element.querySelector('[data-daisy-kit-blueprint-node-control][data-node-id="first"]').click();
        element.querySelector('[data-daisy-kit-blueprint-transition-target]').value = 'second';
        element.querySelector('[data-daisy-kit-blueprint-structure="add-transition"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).edges)
            .toContainEqual({ source: 'first', target: 'second' });

        element.querySelector('[data-daisy-kit-blueprint-node-control][data-node-id="node-3"]').click();
        element.querySelector('[data-daisy-kit-blueprint-structure="remove-node"]').click();
        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value).nodes.map((node) => node.id))
            .toEqual(['first', 'second']);

        unmount(element);
        mount(element);

        expect(JSON.parse(element.querySelector('[data-daisy-kit-blueprint-value]').value)).toEqual({
            edges: [{ source: 'first', target: 'second' }],
            nodes: [{ id: 'first', label: 'First' }, { id: 'second', label: 'Second' }],
        });
    });

    it('keeps value-backed structural state isolated across multiple Blueprint instances', () => {
        const first = root({
            editable: true,
            value: JSON.stringify({ edges: [], nodes: [{ id: 'first', label: 'First' }] }),
        });
        const second = first.cloneNode(true);

        second.querySelector('[data-daisy-kit-config]').textContent = JSON.stringify({
            editable: true,
            value: JSON.stringify({ edges: [], nodes: [{ id: 'second', label: 'Second' }] }),
        });
        document.body.append(second);

        mountAll();
        first.querySelector('[data-daisy-kit-blueprint-structure="add-node"]').click();

        expect(JSON.parse(first.querySelector('[data-daisy-kit-blueprint-value]').value).nodes.map((node) => node.id))
            .toEqual(['first', 'node-2']);
        expect(JSON.parse(second.querySelector('[data-daisy-kit-blueprint-value]').value).nodes.map((node) => node.id))
            .toEqual(['second']);
    });
});
