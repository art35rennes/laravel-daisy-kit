import { describe, expect, it } from 'vitest';
import {
    resolveTransitionLabelPosition,
    transitionLabelLeader,
} from '../../../resources/js/blueprint/rendering.js';

function overlapsLabelAndNode(position, width, node) {
    return position.x - width / 2 < node.position.x + node.width
        && position.x + width / 2 > node.position.x
        && position.y - 13 < node.position.y + node.height
        && position.y + 13 > node.position.y;
}

describe('Blueprint transition labels', () => {
    it('moves a label away from workflow cards when its default position collides', () => {
        const nodes = [{
            id: 'review',
            position: { x: 0, y: 0 },
            width: 240,
            height: 112,
        }];
        const geometry = {
            label: { x: 120, y: 56 },
            normal: { x: 0, y: 1 },
            offset: 0,
        };
        const label = 'Demander l’approbation';
        const width = Math.max(48, label.length * 7 + 20);
        const position = resolveTransitionLabelPosition(geometry, label, nodes, []);

        expect(overlapsLabelAndNode(position, width, nodes[0])).toBe(false);
    });

    it('keeps parallel labels apart after avoiding cards', () => {
        const geometry = {
            label: { x: 320, y: 90 },
            normal: { x: 0, y: 1 },
            offset: 0,
        };
        const first = resolveTransitionLabelPosition(geometry, 'Transition A', [], []);
        const second = resolveTransitionLabelPosition(geometry, 'Transition B', [], [{
            x: first.x,
            y: first.y,
            width: 104,
            height: 26,
        }]);

        expect(second).not.toEqual(first);
    });

    it('draws an attachment line when a label had to move away from its transition', () => {
        expect(transitionLabelLeader(
            { x: 100, y: 100 },
            { x: 180, y: 100 },
            { width: 80, height: 26 },
        )).toEqual({ x1: 100, y1: 100, x2: 140, y2: 100 });
    });

    it('does not place a label over another orthogonal transition', () => {
        const position = resolveTransitionLabelPosition({
            id: 'yellow',
            label: { x: 100, y: 100 },
            normal: { x: 0, y: 1 },
            offset: 0,
        }, 'Approve', [], [], [{
            id: 'green',
            route: [{ x: 0, y: 136 }, { x: 240, y: 136 }],
        }]);

        expect(position.y).not.toBe(136);
    });
});
