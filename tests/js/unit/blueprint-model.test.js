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

    it('applies category defaults and deeply merges partial integrator data', () => {
        const categories = [{
            value: 'workflow-step',
            defaults: {
                forwardable: true,
                permissions: { web: true, api: true },
            },
        }];
        const initial = normalizeWorkflow({
            nodes: [{
                id: 'draft',
                category: 'workflow-step',
                data: {
                    forwardable: false,
                    permissions: { web: false },
                    opaque: { preserved: true },
                },
            }],
        });

        const updated = updateNode(initial, 'draft', {
            data: {
                permissions: { api: false },
            },
        }, { categories });

        expect(updated.nodes[0].data).toEqual({
            forwardable: false,
            permissions: { web: false, api: false },
            opaque: { preserved: true },
        });
        expect(initial.nodes[0].data.permissions).toEqual({ web: false });
    });

    it('adds missing defaults when an entity category changes', () => {
        const workflow = normalizeWorkflow({
            nodes: [{
                id: 'draft',
                category: 'draft',
                data: { owner: 'Ada' },
            }],
        });

        const updated = updateNode(workflow, 'draft', {
            category: 'approval',
        }, {
            categories: [{
                value: 'approval',
                defaults: { requiredApprovals: 2, owner: 'Default owner' },
            }],
        });

        expect(updated.nodes[0].data).toEqual({
            owner: 'Ada',
            requiredApprovals: 2,
        });
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
