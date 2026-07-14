// @vitest-environment jsdom

import { beforeEach, describe, expect, it, vi } from 'vitest';
import initBlueprint from '../../../resources/js/blueprint/runtime.js';

function createRoot(value = {}) {
    document.body.innerHTML = '<div id="blueprint" data-blueprint="1" data-mode="edit" data-direction="LR"><button data-blueprint-action="add-node"></button><button data-blueprint-action="undo"></button><button data-blueprint-action="redo"></button><button data-blueprint-action="arrange"></button><button data-blueprint-action="fit"></button><input data-blueprint-search><div data-blueprint-canvas><div data-blueprint-world><svg data-blueprint-edges><defs><marker id="blueprint-arrow"></marker></defs><g data-blueprint-transition-layer></g><g data-blueprint-transition-label-layer></g></svg><div data-blueprint-nodes></div></div><p data-blueprint-empty></p></div><dialog data-blueprint-inspector class="hidden"><h2 data-blueprint-inspector-title></h2><span data-blueprint-dirty-indicator class="hidden"></span><button data-blueprint-action="close-inspector"></button><div data-blueprint-inspector-content><input data-integrator-label></div><button data-blueprint-inspector-backdrop data-blueprint-action="close-inspector"></button></dialog><dialog data-blueprint-discard-dialog><button data-blueprint-action="keep-editing"></button><button data-blueprint-action="discard-changes"></button></dialog><div data-blueprint-mobile-list></div><textarea data-blueprint-sync></textarea><textarea data-blueprint-value></textarea><textarea data-blueprint-node-categories>[{"value":"work","label":"Work"}]</textarea><textarea data-blueprint-transition-categories>[{"value":"progress","label":"Progress"}]</textarea><textarea data-blueprint-i18n>{"newNode":"New step","newTransition":"New transition","node":"Step","transition":"Transition","unnamed":"Unnamed"}</textarea></div>';
    document.querySelector('[data-blueprint-value]').value = JSON.stringify(value);

    const root = document.querySelector('#blueprint');
    const canvas = root.querySelector('[data-blueprint-canvas]');
    canvas.getBoundingClientRect = () => ({
        x: 0,
        y: 0,
        top: 0,
        left: 0,
        right: 900,
        bottom: 600,
        width: 900,
        height: 600,
    });

    return root;
}

describe('Blueprint runtime', () => {
    beforeEach(() => {
        vi.restoreAllMocks();
    });

    it('exposes only the documented workflow API and initializes the hidden field', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const initEvent = vi.fn();
        root.addEventListener('daisy:blueprint:init', initEvent);

        const api = initBlueprint(root);

        expect(Object.keys(api).sort()).toEqual([
            'addNode',
            'addTransition',
            'arrange',
            'destroy',
            'fit',
            'getValue',
            'openInspector',
            'redo',
            'removeNode',
            'removeTransition',
            'cancelInspector',
            'commitInspector',
            'setValue',
            'setInspectorDraft',
            'undo',
            'updateNode',
            'updateTransition',
        ].sort());
        expect(JSON.parse(root.querySelector('[data-blueprint-sync]').value).nodes[0].id).toBe('draft');
        expect(root.querySelector('[style]')).toBeNull();
        expect(initEvent).toHaveBeenCalledOnce();
    });

    it('does not render a second mobile-only representation of the workflow', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });

        initBlueprint(root);

        expect(root.querySelector('[data-blueprint-mobile-list]').children).toHaveLength(0);
        expect(root.querySelectorAll('[data-blueprint-nodes] > [data-blueprint-node-id="draft"]')).toHaveLength(1);
    });

    it('auto-arranges missing positions without creating a user change or undo entry', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft' }, { id: 'review', label: 'Review' }],
            transitions: [{ id: 'submit', source: 'draft', target: 'review' }],
        });
        const changeEvent = vi.fn();
        root.addEventListener('daisy:blueprint:change', changeEvent);

        const api = initBlueprint(root);

        expect(api.getValue().nodes.every(node => node.position !== null)).toBe(true);
        expect(changeEvent).not.toHaveBeenCalled();
        expect(root.querySelector('[data-blueprint-action="undo"]').disabled).toBe(true);
    });

    it('fits the viewport after arranging cards so labels and nodes remain visible', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', position: { x: 40, y: 40 } },
                { id: 'review', position: { x: 1800, y: 40 } },
            ],
            transitions: [{ id: 'submit', source: 'draft', target: 'review' }],
            viewport: { x: -900, y: -900, zoom: 0.2 },
        });
        const api = initBlueprint(root);

        const arranged = api.arrange();

        expect(arranged.viewport.zoom).toBeGreaterThan(0.2);
        expect(arranged.viewport.x).not.toBe(-900);
        expect(arranged.viewport.y).not.toBe(-900);
    });

    it('renders label leader lines behind cards and transition labels', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', position: { x: 0, y: 0 } },
                { id: 'blocker', position: { x: 320, y: 0 } },
                { id: 'review', position: { x: 640, y: 0 } },
            ],
            transitions: [{
                id: 'submit',
                source: 'draft',
                target: 'review',
                label: 'Submit for review',
            }],
        });

        initBlueprint(root);

        expect(root.querySelector('[data-blueprint-transition-layer] .daisy-blueprint-transition-label-leader'))
            .not.toBeNull();
        expect(root.querySelector('[data-blueprint-transition-label-layer] .daisy-blueprint-transition-label-leader'))
            .toBeNull();
    });

    it('synchronizes forms and change events for API mutations', () => {
        const root = createRoot();
        const api = initBlueprint(root);
        const inputEvent = vi.fn();
        const changeEvent = vi.fn();
        const blueprintEvent = vi.fn();
        const hiddenField = root.querySelector('[data-blueprint-sync]');
        hiddenField.addEventListener('input', inputEvent);
        hiddenField.addEventListener('change', changeEvent);
        root.addEventListener('daisy:blueprint:change', blueprintEvent);

        api.addNode({ id: 'review', label: 'Review' });
        api.addNode({ id: 'published', label: 'Published' });
        api.addTransition({
            id: 'approve',
            source: 'review',
            target: 'published',
            label: 'Approve',
        });

        expect(api.getValue().nodes).toHaveLength(2);
        expect(api.getValue().transitions).toHaveLength(1);
        expect(inputEvent).toHaveBeenCalledTimes(3);
        expect(changeEvent).toHaveBeenCalledTimes(3);
        expect(blueprintEvent).toHaveBeenCalledTimes(3);
    });

    it('ignores obsolete category defaults when entities are created through the public API', () => {
        const root = createRoot();
        root.querySelector('[data-blueprint-node-categories]').value = JSON.stringify([{
            value: 'work',
            label: 'Work',
            defaults: {
                forwardable: true,
                permissions: { web: true },
            },
        }]);
        const api = initBlueprint(root);

        const node = api.addNode({
            id: 'review',
            category: 'work',
            data: { permissions: { api: true } },
        });

        expect(node.data).toEqual({ permissions: { api: true } });
    });

    it('renders the category color before the default node color', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', label: 'Draft', category: 'draft', position: { x: 40, y: 40 } },
                { id: 'approved', label: 'Approved', category: 'approved', position: { x: 340, y: 40 } },
            ],
            transitions: [],
        });
        root.dataset.nodeColor = 'neutral';
        root.querySelector('[data-blueprint-node-categories]').value = JSON.stringify([
            { value: 'draft', label: 'Draft' },
            { value: 'approved', label: 'Approved', color: 'success' },
        ]);

        initBlueprint(root);

        expect(root.querySelector('[data-blueprint-node-id="draft"]').dataset.nodeColor).toBe('neutral');
        expect(root.querySelector('[data-blueprint-node-id="approved"]').dataset.nodeColor).toBe('success');
    });

    it('selects, removes, undoes, and redoes workflow entities', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const selectEvent = vi.fn();
        root.addEventListener('daisy:blueprint:select', selectEvent);
        const api = initBlueprint(root);

        root.querySelector('[data-blueprint-node-id="draft"]').click();
        api.removeNode('draft');

        expect(selectEvent).toHaveBeenCalledOnce();
        expect(api.getValue().nodes).toEqual([]);
        expect(api.undo().nodes[0].id).toBe('draft');
        expect(api.redo().nodes).toEqual([]);
    });

    it('opens the node inspector after a pointer click without moving it', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        initBlueprint(root);
        const node = root.querySelector('[data-blueprint-node-id="draft"]');

        node.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            button: 0,
            clientX: 80,
            clientY: 80,
        }));
        window.dispatchEvent(new MouseEvent('pointerup', {
            bubbles: true,
            button: 0,
            clientX: 80,
            clientY: 80,
        }));
        node.click();

        expect(root.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(false);
        expect(root.querySelector('[data-blueprint-inspector-title]').textContent).toContain('Draft');
        expect(root.querySelector('[data-blueprint-inspector-content]')).not.toBeNull();
    });

    it('opens and closes the inspector through the configured modal mode', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const inspector = root.querySelector('[data-blueprint-inspector]');
        inspector.showModal = vi.fn(() => {
            inspector.open = true;
        });
        inspector.close = vi.fn(() => {
            inspector.open = false;
        });

        initBlueprint(root);
        root.querySelector('[data-blueprint-node-id="draft"]').click();

        expect(inspector.showModal).toHaveBeenCalledOnce();
        expect(inspector.open).toBe(true);

        root.querySelector('[data-blueprint-action="close-inspector"]').click();

        expect(inspector.close).toHaveBeenCalledOnce();
        expect(inspector.open).toBe(false);
    });

    it('discards an uncommitted node when creation is abandoned', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const api = initBlueprint(root);
        const hiddenField = root.querySelector('[data-blueprint-sync]');

        root.querySelector('[data-blueprint-action="add-node"]').click();

        expect(api.getValue().nodes).toHaveLength(2);
        expect(root.querySelectorAll('[data-blueprint-nodes] > [data-blueprint-node-id]')).toHaveLength(2);
        expect(JSON.parse(hiddenField.value).nodes).toHaveLength(1);
        expect(root.querySelector('[data-blueprint-action="undo"]').disabled).toBe(true);
        expect(root.querySelector('[data-blueprint-inspector]').dataset.dirty).toBe('true');
        api.setInspectorDraft({
            label: 'Changed',
            description: '',
            category: 'work',
            data: {},
        });
        expect(api.cancelInspector()).toBe(false);
        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(true);

        root.querySelector('[data-blueprint-action="discard-changes"]').click();

        expect(api.getValue().nodes).toHaveLength(1);
        expect(root.querySelectorAll('[data-blueprint-nodes] > [data-blueprint-node-id]')).toHaveLength(1);
        expect(JSON.parse(hiddenField.value).nodes).toHaveLength(1);
        expect(root.querySelector('[data-blueprint-action="undo"]').disabled).toBe(true);
    });

    it('commits a newly configured node exactly once when creation is saved', () => {
        const root = createRoot();
        root.querySelector('[data-blueprint-node-categories]').value = JSON.stringify([
            { value: 'work', label: 'Work' },
            { value: 'done', label: 'Done' },
        ]);
        const changes = [];
        const opens = [];
        root.addEventListener('daisy:blueprint:change', event => changes.push(event.detail));
        root.addEventListener('daisy:blueprint:inspector-open', event => opens.push(event.detail));
        const api = initBlueprint(root);

        root.querySelector('[data-blueprint-action="add-node"]').click();
        expect(opens[0]).toMatchObject({
            selection: { type: 'node', id: 'step-1' },
            isNew: true,
            value: {
                label: 'New step',
                description: '',
                category: 'work',
                data: {},
            },
        });
        expect(opens[0].commit({
            label: 'Published',
            description: '',
            category: 'done',
            data: { owner: 'Ada' },
        })).toMatchObject({ label: 'Published', category: 'done' });

        expect(api.getValue().nodes).toHaveLength(1);
        expect(api.getValue().nodes[0]).toMatchObject({ label: 'Published', category: 'done' });
        expect(JSON.parse(root.querySelector('[data-blueprint-sync]').value).nodes).toHaveLength(1);
        expect(changes).toHaveLength(1);
        expect(changes[0].reason).toBe('addNode');
        expect(root.querySelector('[data-blueprint-action="undo"]').disabled).toBe(false);
        expect(api.undo().nodes).toHaveLength(0);
    });

    it('asks before discarding dirty inspector fields from the backdrop', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const api = initBlueprint(root);
        root.querySelector('[data-blueprint-node-id="draft"]').click();
        api.setInspectorDraft({
            label: 'Changed draft',
            description: '',
            category: '',
            data: {},
        });

        expect(root.querySelector('[data-blueprint-inspector]').dataset.dirty).toBe('true');
        expect(root.querySelector('[data-blueprint-dirty-indicator]').classList.contains('hidden')).toBe(false);

        root.querySelector('[data-blueprint-inspector-backdrop]').click();

        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(true);
        expect(root.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(false);
        expect(api.getValue().nodes[0].label).toBe('Draft');

        root.querySelector('[data-blueprint-action="keep-editing"]').click();
        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(false);
        expect(api.setInspectorDraft({
            label: 'Changed again',
            description: '',
            category: '',
            data: {},
        }).label).toBe('Changed again');

        root.querySelector('[data-blueprint-inspector-backdrop]').click();
        root.querySelector('[data-blueprint-action="discard-changes"]').click();

        expect(root.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(true);
        expect(api.getValue().nodes[0].label).toBe('Draft');
    });

    it('does not replace a dirty draft when the same inspector is opened again', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const api = initBlueprint(root);
        api.openInspector({ type: 'node', id: 'draft' });
        api.setInspectorDraft({
            label: 'Changed draft',
            description: '',
            category: '',
            data: {},
        });

        expect(api.openInspector({ type: 'node', id: 'draft' })).toBeNull();
        expect(root.querySelector('[data-blueprint-discard-dialog]').hasAttribute('open')).toBe(true);
        expect(api.commitInspector()).toMatchObject({ label: 'Changed draft' });
    });

    it('emits open, commit, and cancel events for the generic inspector lifecycle', () => {
        const root = createRoot({
            nodes: [{ id: 'draft', label: 'Draft', position: { x: 40, y: 40 } }],
            transitions: [],
        });
        const lifecycle = [];
        root.addEventListener('daisy:blueprint:inspector-open', event => lifecycle.push(['open', event.detail]));
        root.addEventListener('daisy:blueprint:inspector-commit', event => lifecycle.push(['commit', event.detail]));
        root.addEventListener('daisy:blueprint:inspector-cancel', event => lifecycle.push(['cancel', event.detail]));
        const api = initBlueprint(root);

        api.openInspector({ type: 'node', id: 'draft' });
        api.commitInspector({
            label: 'Committed',
            description: '',
            category: '',
            data: { owner: 'Ada' },
        });
        api.openInspector({ type: 'node', id: 'draft' });
        expect(api.cancelInspector()).toBe(true);

        expect(lifecycle.map(([name]) => name)).toEqual(['open', 'commit', 'open', 'cancel']);
        expect(lifecycle[1][1]).toMatchObject({
            selection: { type: 'node', id: 'draft' },
            value: { label: 'Committed', data: { owner: 'Ada' } },
            isNew: false,
        });
        expect(lifecycle[3][1].reason).toBe('api');
    });

    it('commits integrator data once and restores it through workflow history', () => {
        const root = createRoot({
            nodes: [{
                id: 'review',
                label: 'Review',
                category: 'work',
                position: { x: 40, y: 40 },
                data: { owner: 'Ada', opaque: { preserved: true } },
            }],
            transitions: [],
        });
        const changes = [];
        root.addEventListener('daisy:blueprint:change', event => changes.push(event.detail));
        const api = initBlueprint(root);

        api.openInspector({ type: 'node', id: 'review' });
        api.commitInspector({
            label: 'Review',
            description: '',
            category: 'work',
            data: { owner: 'Grace', readonly: true },
        });

        expect(changes).toHaveLength(1);
        expect(changes[0].reason).toBe('updateNode');
        expect(api.getValue().nodes[0].data).toEqual({
            owner: 'Grace',
            readonly: true,
        });
        expect(api.undo().nodes[0].data).toEqual({
            owner: 'Ada',
            opaque: { preserved: true },
        });
        expect(api.redo().nodes[0].data.owner).toBe('Grace');
    });

    it('preserves existing opaque data when inspector commit omits it', () => {
        const root = createRoot({
            nodes: [{
                id: 'review',
                label: 'Review',
                category: 'work',
                position: { x: 40, y: 40 },
                data: { owner: 'Ada', opaque: true },
            }],
            transitions: [],
        });
        const api = initBlueprint(root);

        api.openInspector({ type: 'node', id: 'review' });
        api.commitInspector({
            label: 'Approved',
            description: '',
            category: 'done',
        });

        expect(api.getValue().nodes[0]).toMatchObject({
            label: 'Approved',
            category: 'done',
            data: { owner: 'Ada', opaque: true },
        });
    });

    it('edits transition category data through the same inspector contract', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', position: { x: 40, y: 40 } },
                { id: 'review', position: { x: 400, y: 40 } },
            ],
            transitions: [{
                id: 'submit',
                source: 'draft',
                target: 'review',
                category: 'progress',
                data: { guarded: true },
            }],
        });
        const api = initBlueprint(root);

        api.openInspector({ type: 'transition', id: 'submit' });
        api.commitInspector({
            label: '',
            description: '',
            category: 'progress',
            data: { guarded: false },
        });

        expect(api.getValue().transitions[0].data).toEqual({ guarded: false });
    });

    it('keeps the inspector open and emits an actionable path for invalid generic data', () => {
        const root = createRoot({
            nodes: [{
                id: 'review',
                label: 'Review',
                category: 'work',
                position: { x: 40, y: 40 },
                data: { owner: '' },
            }],
            transitions: [],
        });
        const errors = [];
        root.addEventListener('daisy:blueprint:error', event => errors.push(event.detail));
        const api = initBlueprint(root);

        api.openInspector({ type: 'node', id: 'review' });
        expect(api.commitInspector({
            label: 'Review',
            description: '',
            category: 'work',
            data: [],
        })).toBeNull();

        expect(errors).toHaveLength(1);
        expect(errors[0].path).toBe('nodes.review.data');
        expect(root.querySelector('[data-blueprint-inspector]').classList.contains('hidden')).toBe(false);
    });

    it('creates a directed transition by clicking a source dot then a target dot', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', label: 'Draft', position: { x: 40, y: 40 } },
                { id: 'review', label: 'Review', position: { x: 400, y: 40 } },
            ],
            transitions: [],
        });
        const api = initBlueprint(root);

        root.querySelector('[data-blueprint-node-id="draft"] [data-blueprint-handle="right"]').click();

        expect(api.getValue().transitions).toHaveLength(0);
        expect(root.querySelector('[data-blueprint-node-id="draft"]').dataset.connectionSource).toBe('true');
        expect(root.querySelector('[data-blueprint-node-id="draft"] [data-blueprint-handle="right"]')
            .dataset.connectionSource).toBe('true');

        root.querySelector('[data-blueprint-node-id="review"] [data-blueprint-handle="left"]').click();

        expect(api.getValue().transitions).toHaveLength(1);
        expect(api.getValue().transitions[0]).toMatchObject({ source: 'draft', target: 'review' });
        expect(root.querySelector('[data-blueprint-inspector-title]').textContent).toContain('New transition');
    });

    it('removes an uncommitted transition when its inspector is cancelled', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', label: 'Draft', position: { x: 40, y: 40 } },
                { id: 'review', label: 'Review', position: { x: 400, y: 40 } },
            ],
            transitions: [],
        });
        const changes = [];
        root.addEventListener('daisy:blueprint:change', event => changes.push(event.detail));
        const api = initBlueprint(root);

        root.querySelector('[data-blueprint-node-id="draft"] [data-blueprint-handle="right"]').click();
        root.querySelector('[data-blueprint-node-id="review"] [data-blueprint-handle="left"]').click();

        expect(api.getValue().transitions).toHaveLength(1);
        expect(api.cancelInspector()).toBe(false);
        root.querySelector('[data-blueprint-action="discard-changes"]').click();
        expect(api.getValue().transitions).toHaveLength(0);
        expect(changes).toHaveLength(0);
    });

    it('clears a pending connection when its source node is removed through the API', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', position: { x: 40, y: 40 } },
                { id: 'review', position: { x: 400, y: 40 } },
            ],
            transitions: [],
        });
        const api = initBlueprint(root);

        root.querySelector('[data-blueprint-node-id="draft"] [data-blueprint-handle="right"]').click();
        api.removeNode('draft');
        root.querySelector('[data-blueprint-node-id="review"] [data-blueprint-handle="left"]').click();

        expect(api.getValue().transitions).toHaveLength(0);
        expect(root.querySelector('[data-blueprint-node-id="review"]').dataset.connectionSource).toBe('true');
    });

    it('selects steps and transitions with the keyboard', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', label: 'Draft', position: { x: 40, y: 40 } },
                { id: 'review', label: 'Review', position: { x: 400, y: 40 } },
            ],
            transitions: [{ id: 'submit', source: 'draft', target: 'review', label: 'Submit' }],
        });
        const selected = [];
        root.addEventListener('daisy:blueprint:select', event => selected.push(event.detail.selection));
        initBlueprint(root);
        const node = root.querySelector('[data-blueprint-node-id="draft"]');

        node.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
        const transition = root.querySelector('[data-blueprint-transition-id="submit"]');
        transition.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));

        expect(transition.getAttribute('tabindex')).toBe('0');
        expect(transition.getAttribute('role')).toBe('button');
        expect(selected).toEqual([
            { type: 'node', id: 'draft' },
            { type: 'transition', id: 'submit' },
        ]);
    });

    it('keeps opaque data when common fields are edited', () => {
        const root = createRoot({
            nodes: [{
                id: 'review',
                label: 'Review',
                position: { x: 40, y: 40 },
                data: { owner: 42 },
            }],
            transitions: [],
        });
        const api = initBlueprint(root);

        api.updateNode('review', { label: 'Approval' });

        expect(api.getValue().nodes[0]).toMatchObject({
            label: 'Approval',
            data: { owner: 42 },
        });
    });

    it('applies category presentation without persisting it into workflow data', () => {
        const root = createRoot({
            nodes: [
                { id: 'draft', position: { x: 40, y: 40 } },
                { id: 'review', position: { x: 400, y: 40 } },
            ],
            transitions: [{ id: 'return', source: 'draft', target: 'review', category: 'progress' }],
        });
        root.dataset.transitionShape = 'straight';
        root.querySelector('[data-blueprint-transition-categories]').value = JSON.stringify([{
            value: 'progress',
            label: 'Progress',
            shape: 's',
            color: 'warning',
        }]);
        const api = initBlueprint(root);
        const transition = root.querySelector('[data-blueprint-transition-id="return"]');

        expect(transition.dataset.transitionShape).toBe('s');
        expect(transition.dataset.transitionColor).toBe('warning');
        expect(api.getValue().transitions[0]).not.toHaveProperty('shape');
        expect(api.getValue().transitions[0]).not.toHaveProperty('color');
    });

    it('grows the SVG surface for workflows wider than a fixed canvas', () => {
        const root = createRoot({
            nodes: [
                { id: 'start', position: { x: 40, y: 40 } },
                { id: 'far-away', position: { x: 15000, y: 40 } },
            ],
            transitions: [{ id: 'long', source: 'start', target: 'far-away' }],
        });

        initBlueprint(root);

        expect(Number(root.querySelector('[data-blueprint-edges]').getAttribute('width')))
            .toBeGreaterThan(15000);
    });
});
