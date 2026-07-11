import { describe, expect, it } from 'vitest';
import { createTransitionGeometry } from '../../../resources/js/blueprint/geometry.js';

const nodes = [
    { id: 'a', position: { x: 20, y: 40 }, width: 200, height: 100 },
    { id: 'b', position: { x: 420, y: 40 }, width: 200, height: 100 },
];

describe('Blueprint SVG geometry', () => {
    it('routes a directed transition between node boundaries', () => {
        const [geometry] = createTransitionGeometry(
            [{ id: 'a-b', source: 'a', target: 'b' }],
            nodes,
        );

        expect(geometry.path).toMatch(/^M 220 90 C /);
        expect(geometry.path).toContain('420 90');
        expect(geometry.label).toEqual({ x: 320, y: 90 });
    });

    it('curves reverse transitions on opposite sides of their shared axis', () => {
        const geometry = createTransitionGeometry([
            { id: 'a-b', source: 'a', target: 'b' },
            { id: 'b-a', source: 'b', target: 'a' },
        ], nodes);

        expect(geometry[0].label.y).toBeGreaterThan(90);
        expect(geometry[1].label.y).toBeLessThan(90);
        expect(geometry[0].path).not.toBe(geometry[1].path);
    });

    it('anchors a reverse curve label on its Bezier path', () => {
        const [geometry] = createTransitionGeometry([
            { id: 'a-b', source: 'a', target: 'b' },
            { id: 'b-a', source: 'b', target: 'a' },
        ], nodes);

        expect(geometry.label).toEqual({ x: 320, y: 154.5 });
    });

    it('gives parallel transitions distinct routes', () => {
        const geometry = createTransitionGeometry([
            { id: 'first', source: 'a', target: 'b' },
            { id: 'second', source: 'a', target: 'b' },
            { id: 'third', source: 'a', target: 'b' },
        ], nodes);

        expect(new Set(geometry.map(edge => edge.offset)).size).toBe(3);
        expect(new Set(geometry.map(edge => edge.path)).size).toBe(3);
    });

    it('draws self loops outside the node card', () => {
        const [geometry] = createTransitionGeometry(
            [{ id: 'loop', source: 'a', target: 'a' }],
            nodes,
        );

        expect(geometry.path).toMatch(/^M 220 65 C /);
        expect(geometry.label.y).toBeLessThan(40);
    });

    it.each([
        ['straight', / L /],
        ['curve', / C /],
        ['s', / C /],
        ['orthogonal', / L /],
    ])('supports the %s transition shape', (shape, pathCommand) => {
        const diagonalNodes = [
            nodes[0],
            { ...nodes[1], position: { x: 420, y: 240 } },
        ];
        const [geometry] = createTransitionGeometry(
            [{ id: 'a-b', source: 'a', target: 'b', shape }],
            diagonalNodes,
        );

        expect(geometry.shape).toBe(shape);
        expect(geometry.path).toMatch(pathCommand);
    });

    it('keeps reverse labels outside the height of their node cards', () => {
        const geometry = createTransitionGeometry([
            { id: 'a-b', source: 'a', target: 'b' },
            { id: 'b-a', source: 'b', target: 'a' },
        ], nodes);

        expect(geometry[0].label.y).toBeGreaterThan(140);
        expect(geometry[1].label.y).toBeLessThan(40);
    });

    it('anchors an offset orthogonal label on its central route segment', () => {
        const diagonalNodes = [
            nodes[0],
            { ...nodes[1], position: { x: 420, y: 240 } },
        ];
        const [geometry] = createTransitionGeometry([
            { id: 'a-b', source: 'a', target: 'b', shape: 'orthogonal' },
            { id: 'b-a', source: 'b', target: 'a', shape: 'orthogonal' },
        ], diagonalNodes);

        expect(geometry.label).toEqual({ x: 320, y: 276 });
    });
});
