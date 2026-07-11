import { describe, expect, it } from 'vitest';
import { createHistory } from '../../../resources/js/blueprint/history.js';

describe('Blueprint history', () => {
    it('undoes and redoes immutable workflow snapshots', () => {
        const history = createHistory({ version: 1, nodes: [], transitions: [] });

        history.record({ version: 1, nodes: [{ id: 'draft' }], transitions: [] });
        history.record({ version: 1, nodes: [{ id: 'draft' }, { id: 'review' }], transitions: [] });

        expect(history.canUndo()).toBe(true);
        expect(history.undo().nodes.map(node => node.id)).toEqual(['draft']);
        expect(history.undo().nodes).toEqual([]);
        expect(history.canUndo()).toBe(false);
        expect(history.redo().nodes.map(node => node.id)).toEqual(['draft']);
    });

    it('drops the redo branch after a new command', () => {
        const history = createHistory({ nodes: [] });
        history.record({ nodes: [{ id: 'a' }] });
        history.undo();
        history.record({ nodes: [{ id: 'b' }] });

        expect(history.canRedo()).toBe(false);
        expect(history.current().nodes.map(node => node.id)).toEqual(['b']);
    });

    it('keeps only the configured number of snapshots', () => {
        const history = createHistory({ value: 0 }, 3);
        history.record({ value: 1 });
        history.record({ value: 2 });
        history.record({ value: 3 });

        expect(history.undo()).toEqual({ value: 2 });
        expect(history.undo()).toEqual({ value: 1 });
        expect(history.canUndo()).toBe(false);
    });
});
