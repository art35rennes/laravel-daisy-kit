import { describe, expect, it } from 'vitest';
import { arrangeWorkflow } from '../../../resources/js/blueprint/layout.js';

describe('Blueprint Dagre layout', () => {
    it('arranges variable-sized nodes from left to right', () => {
        const workflow = {
            nodes: [{ id: 'a' }, { id: 'b' }, { id: 'c' }],
            transitions: [
                { id: 'a-b', source: 'a', target: 'b' },
                { id: 'b-c', source: 'b', target: 'c' },
            ],
        };
        const sizes = new Map([
            ['a', { width: 180, height: 90 }],
            ['b', { width: 260, height: 120 }],
            ['c', { width: 210, height: 100 }],
        ]);

        const arranged = arrangeWorkflow(workflow, sizes, { direction: 'LR' });

        expect(arranged.nodes[0].position.x).toBeLessThan(arranged.nodes[1].position.x);
        expect(arranged.nodes[1].position.x).toBeLessThan(arranged.nodes[2].position.x);
    });

    it('supports cycles, disconnected components, and top-to-bottom direction', () => {
        const workflow = {
            nodes: [{ id: 'a' }, { id: 'b' }, { id: 'orphan' }],
            transitions: [
                { id: 'a-b', source: 'a', target: 'b' },
                { id: 'b-a', source: 'b', target: 'a' },
            ],
        };

        const arranged = arrangeWorkflow(workflow, new Map(), { direction: 'TB' });

        expect(arranged.nodes.every(node => Number.isFinite(node.position.x))).toBe(true);
        expect(arranged.nodes.every(node => Number.isFinite(node.position.y))).toBe(true);
        expect(arranged.nodes.find(node => node.id === 'a').position.y)
            .not.toBe(arranged.nodes.find(node => node.id === 'b').position.y);
    });

    it('arranges cyclic workflows radially without overlapping their centers', () => {
        const workflow = {
            nodes: [{ id: 'a' }, { id: 'b' }, { id: 'c' }, { id: 'd' }],
            transitions: [
                { id: 'a-b', source: 'a', target: 'b' },
                { id: 'b-c', source: 'b', target: 'c' },
                { id: 'c-d', source: 'c', target: 'd' },
                { id: 'd-a', source: 'd', target: 'a' },
            ],
        };

        const arranged = arrangeWorkflow(workflow, new Map(), { layout: 'radial' });
        const positions = arranged.nodes.map(node => `${node.position.x}:${node.position.y}`);
        const centers = arranged.nodes.map(node => ({
            x: node.position.x + 120,
            y: node.position.y + 56,
        }));
        const centroid = {
            x: centers.reduce((sum, point) => sum + point.x, 0) / centers.length,
            y: centers.reduce((sum, point) => sum + point.y, 0) / centers.length,
        };
        const radii = centers.map(point => Math.hypot(point.x - centroid.x, point.y - centroid.y));
        const width = Math.max(...arranged.nodes.map(node => node.position.x + 240))
            - Math.min(...arranged.nodes.map(node => node.position.x));
        const height = Math.max(...arranged.nodes.map(node => node.position.y + 112))
            - Math.min(...arranged.nodes.map(node => node.position.y));

        expect(new Set(positions).size).toBe(workflow.nodes.length);
        expect(arranged.nodes.every(node => Number.isFinite(node.position.x))).toBe(true);
        expect(arranged.nodes.every(node => Number.isFinite(node.position.y))).toBe(true);
        expect(Math.max(...radii) - Math.min(...radii)).toBeLessThan(2);
        expect(width).toBeGreaterThan(800);
        expect(height).toBeGreaterThan(700);
    });

    it('keeps hierarchical layout for cyclic workflows when requested', () => {
        const workflow = {
            nodes: [{ id: 'a' }, { id: 'b' }, { id: 'c' }, { id: 'd' }],
            transitions: [
                { id: 'a-b', source: 'a', target: 'b' },
                { id: 'b-c', source: 'b', target: 'c' },
                { id: 'c-d', source: 'c', target: 'd' },
                { id: 'd-a', source: 'd', target: 'a' },
            ],
        };

        const arranged = arrangeWorkflow(workflow, new Map(), { layout: 'hierarchical' });
        const centers = arranged.nodes.map(node => ({
            x: node.position.x + 120,
            y: node.position.y + 56,
        }));
        const centroid = {
            x: centers.reduce((sum, point) => sum + point.x, 0) / centers.length,
            y: centers.reduce((sum, point) => sum + point.y, 0) / centers.length,
        };
        const radii = centers.map(point => Math.hypot(point.x - centroid.x, point.y - centroid.y));

        expect(Math.max(...radii) - Math.min(...radii)).toBeGreaterThan(2);
    });

    it('accepts tree as the explicit hierarchical layout name', () => {
        const workflow = {
            nodes: [{ id: 'root' }, { id: 'child' }],
            transitions: [{ id: 'edge', source: 'root', target: 'child' }],
        };

        const arranged = arrangeWorkflow(workflow, new Map(), { layout: 'tree', direction: 'TB' });

        expect(arranged.nodes[0].position.y).toBeLessThan(arranged.nodes[1].position.y);
    });

    it('lays out 200 nodes and 400 transitions in under one second', () => {
        const nodes = Array.from({ length: 200 }, (_, index) => ({ id: `node-${index}` }));
        const transitions = Array.from({ length: 400 }, (_, index) => ({
            id: `transition-${index}`,
            source: `node-${index % 200}`,
            target: `node-${(index * 7 + 11) % 200}`,
        }));
        const startedAt = performance.now();

        const arranged = arrangeWorkflow({ nodes, transitions }, new Map(), { direction: 'LR' });

        expect(arranged.nodes).toHaveLength(200);
        expect(performance.now() - startedAt).toBeLessThan(1000);
    });
});
