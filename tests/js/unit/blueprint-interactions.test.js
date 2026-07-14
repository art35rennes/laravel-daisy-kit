// @vitest-environment jsdom

import { describe, expect, it, vi } from 'vitest';
import {
    bindBlueprintInteractions,
    screenToWorld,
} from '../../../resources/js/blueprint/interactions.js';

function pointerEvent(type, options) {
    const { pointerId, ...mouseOptions } = options;
    const event = new MouseEvent(type, { bubbles: true, button: 0, ...mouseOptions });

    if (pointerId !== undefined) {
        Object.defineProperty(event, 'pointerId', { value: pointerId });
    }

    return event;
}

function interactionFixture() {
    document.body.innerHTML = '<div id="root"><div data-blueprint-canvas><div data-blueprint-world><article data-blueprint-node-id="a"><button data-blueprint-handle="right" data-blueprint-node-id="a"></button></article><article data-blueprint-node-id="b"></article></div></div></div>';
    const root = document.querySelector('#root');
    const canvas = root.querySelector('[data-blueprint-canvas]');
    canvas.getBoundingClientRect = () => ({ left: 10, top: 20, width: 800, height: 600 });
    const state = {
        editable: true,
        viewport: { x: 30, y: 40, zoom: 2 },
        workflow: {
            nodes: [
                { id: 'a', position: { x: 100, y: 80 } },
                { id: 'b', position: { x: 400, y: 80 } },
            ],
        },
    };
    const handlers = {
        getState: () => state,
        moveNode: vi.fn(),
        finishMove: vi.fn(),
        setViewport: vi.fn(),
        finishViewport: vi.fn(),
        undo: vi.fn(),
        redo: vi.fn(),
        removeSelection: vi.fn(),
        clearSelection: vi.fn(),
        cancelConnection: vi.fn(),
    };

    return { root, canvas, state, handlers };
}

describe('Blueprint pointer interactions', () => {
    it('converts screen coordinates through the persisted viewport', () => {
        const { canvas, state } = interactionFixture();

        expect(screenToWorld(canvas, state.viewport, 240, 260)).toEqual({ x: 100, y: 100 });
    });

    it('moves a node and commits once when the pointer is released', () => {
        const { root, handlers } = interactionFixture();
        const unbind = bindBlueprintInteractions(root, handlers);
        const node = root.querySelector('[data-blueprint-node-id="a"]');

        node.dispatchEvent(pointerEvent('pointerdown', { clientX: 240, clientY: 220 }));
        window.dispatchEvent(pointerEvent('pointermove', { clientX: 280, clientY: 260 }));
        window.dispatchEvent(pointerEvent('pointerup', { clientX: 280, clientY: 260 }));

        expect(handlers.moveNode).toHaveBeenCalledWith('a', { x: 120, y: 100 }, false);
        expect(handlers.finishMove).toHaveBeenCalledWith('a');
        unbind();
    });

    it('does not rerender or commit a node when the pointer did not move', () => {
        const { root, handlers } = interactionFixture();
        const unbind = bindBlueprintInteractions(root, handlers);
        const node = root.querySelector('[data-blueprint-node-id="a"]');

        node.dispatchEvent(pointerEvent('pointerdown', { clientX: 240, clientY: 220 }));
        window.dispatchEvent(pointerEvent('pointerup', { clientX: 240, clientY: 220 }));

        expect(handlers.moveNode).not.toHaveBeenCalled();
        expect(handlers.finishMove).not.toHaveBeenCalled();
        unbind();
    });

    it('leaves connection handles to their native click interaction', () => {
        const { root, handlers } = interactionFixture();
        const unbind = bindBlueprintInteractions(root, handlers);
        const handle = root.querySelector('[data-blueprint-handle]');

        handle.dispatchEvent(pointerEvent('pointerdown', { clientX: 240, clientY: 220 }));
        window.dispatchEvent(pointerEvent('pointerup', { clientX: 240, clientY: 220 }));

        expect(handlers.moveNode).not.toHaveBeenCalled();
        expect(handlers.finishMove).not.toHaveBeenCalled();
        unbind();
    });

    it('does not record a viewport change for a background click', () => {
        const { root, canvas, handlers } = interactionFixture();
        const unbind = bindBlueprintInteractions(root, handlers);

        canvas.dispatchEvent(pointerEvent('pointerdown', { clientX: 300, clientY: 300 }));
        window.dispatchEvent(pointerEvent('pointerup', { clientX: 300, clientY: 300 }));

        expect(handlers.setViewport).not.toHaveBeenCalled();
        expect(handlers.finishViewport).not.toHaveBeenCalled();
        unbind();
    });

    it('zooms and pans around the gesture center when two pointers pinch', () => {
        const { root, canvas, state, handlers } = interactionFixture();
        state.viewport = { x: 30, y: 40, zoom: 1 };
        const unbind = bindBlueprintInteractions(root, handlers);

        canvas.dispatchEvent(pointerEvent('pointerdown', { pointerId: 1, clientX: 210, clientY: 220 }));
        canvas.dispatchEvent(pointerEvent('pointerdown', { pointerId: 2, clientX: 410, clientY: 220 }));
        window.dispatchEvent(pointerEvent('pointermove', { pointerId: 2, clientX: 510, clientY: 220 }));

        expect(handlers.setViewport).toHaveBeenCalledWith({
            x: -55,
            y: -40,
            zoom: 1.5,
        }, false);

        window.dispatchEvent(pointerEvent('pointerup', { pointerId: 2, clientX: 510, clientY: 220 }));
        expect(handlers.finishViewport).toHaveBeenCalledOnce();
        unbind();
    });
});
