import { describe, expect, it } from 'vitest';
import {
    addNode,
    addTransition,
    normalizeWorkflow,
    removeNode,
    updateNode,
    validateWorkflow,
} from '../../../resources/js/blueprint/model.js';

describe('Blueprint workflow model', () => {
    it('normalizes the public workflow contract without mutating opaque data', () => {
        const input = {
            nodes: [{ id: ' review ', label: ' Révision ', data: { owner: 42 } }],
            transitions: [],
        };

        const workflow = normalizeWorkflow(input);

        expect(workflow).toEqual({
            version: 1,
            nodes: [{
                id: 'review',
                label: 'Révision',
                description: '',
                category: '',
                position: null,
                data: { owner: 42 },
            }],
            transitions: [],
            viewport: { x: 0, y: 0, zoom: 1 },
        });
        expect(input.nodes[0].data).toEqual({ owner: 42 });
    });

    it('reports duplicate nodes and dangling transitions', () => {
        const errors = validateWorkflow({
            nodes: [{ id: 'review' }, { id: 'review' }],
            transitions: [{ id: 'publish', source: 'review', target: 'missing' }],
        });

        expect(errors.map(error => error.code)).toEqual([
            'duplicate_node_id',
            'unknown_transition_target',
        ]);
    });

    it('allows self loops, reverse transitions, and parallel transitions', () => {
        const errors = validateWorkflow({
            nodes: [{ id: 'a' }, { id: 'b' }],
            transitions: [
                { id: 'a-loop', source: 'a', target: 'a' },
                { id: 'a-b-1', source: 'a', target: 'b' },
                { id: 'a-b-2', source: 'a', target: 'b' },
                { id: 'b-a', source: 'b', target: 'a' },
            ],
        });

        expect(errors).toEqual([]);
    });

    it('applies immutable node and transition commands', () => {
        const initial = normalizeWorkflow({ nodes: [{ id: 'draft', label: 'Brouillon' }] });
        const withReview = addNode(initial, { id: 'review', label: 'Révision' });
        const renamed = updateNode(withReview, 'review', { label: 'Validation' });
        const linked = addTransition(renamed, {
            id: 'draft-review',
            source: 'draft',
            target: 'review',
            label: 'Soumettre',
        });
        const removed = removeNode(linked, 'review');

        expect(initial.nodes).toHaveLength(1);
        expect(renamed.nodes.find(node => node.id === 'review').label).toBe('Validation');
        expect(linked.transitions).toHaveLength(1);
        expect(removed.nodes.map(node => node.id)).toEqual(['draft']);
        expect(removed.transitions).toEqual([]);
    });

    it('replaces opaque data when a command explicitly supplies it', () => {
        const initial = normalizeWorkflow({
            nodes: [{
                id: 'draft',
                data: {
                    permissions: { web: false },
                    opaque: { preserved: true },
                },
            }],
        });

        const updated = updateNode(initial, 'draft', {
            data: {
                permissions: { api: false },
            },
        });

        expect(updated.nodes[0].data).toEqual({
            permissions: { api: false },
        });
        expect(initial.nodes[0].data.permissions).toEqual({ web: false });
    });

    it('preserves opaque data when a command does not supply it', () => {
        const workflow = normalizeWorkflow({
            nodes: [{
                id: 'draft',
                category: 'draft',
                data: { owner: 'Ada' },
            }],
        });

        const updated = updateNode(workflow, 'draft', {
            category: 'approval',
        });

        expect(updated.nodes[0].data).toEqual({ owner: 'Ada' });
    });

    it('ignores business defaults supplied through obsolete category options', () => {
        const workflow = normalizeWorkflow();

        const updated = addNode(workflow, {
            id: 'review',
            category: 'approval',
            data: { owner: 'Ada' },
        }, {
            categories: [{ value: 'approval', defaults: { requiredApprovals: 2 } }],
        });

        expect(updated.nodes[0].data).toEqual({ owner: 'Ada' });
    });

    it('rejects commands that would break workflow integrity', () => {
        const workflow = normalizeWorkflow({ nodes: [{ id: 'draft' }, { id: 'review' }] });

        expect(() => addNode(workflow, { id: 'draft' })).toThrow('duplicate_node_id');
        expect(() => updateNode(workflow, 'missing', { id: 'draft' })).toThrow('unknown_node_id');
        expect(() => addTransition(workflow, {
            id: 'invalid',
            source: 'draft',
            target: 'missing',
        })).toThrow('unknown_transition_target');
    });
});
