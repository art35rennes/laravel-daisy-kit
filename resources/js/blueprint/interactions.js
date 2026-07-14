function closestWithin(root, target, selector) {
    const element = target instanceof Element ? target.closest(selector) : null;

    return element && root.contains(element) ? element : null;
}

export function screenToWorld(canvas, viewport, clientX, clientY) {
    const bounds = canvas.getBoundingClientRect();

    return {
        x: (clientX - bounds.left - viewport.x) / viewport.zoom,
        y: (clientY - bounds.top - viewport.y) / viewport.zoom,
    };
}

export function bindBlueprintInteractions(root, handlers) {
    const canvas = root.querySelector('[data-blueprint-canvas]');
    const dragThreshold = 4;
    const pointers = new Map();
    let gesture = null;

    function pointerKey(event) {
        return event.pointerId ?? 'primary';
    }

    function startPinch(state) {
        const pointerIds = [...pointers.keys()].slice(0, 2);
        const [first, second] = pointerIds.map(id => pointers.get(id));
        const distance = Math.hypot(second.x - first.x, second.y - first.y);

        if (distance === 0) {
            return;
        }

        const bounds = canvas.getBoundingClientRect();
        const center = {
            x: (first.x + second.x) / 2 - bounds.left,
            y: (first.y + second.y) / 2 - bounds.top,
        };
        gesture = {
            type: 'pinch',
            pointerIds,
            distance,
            viewport: { ...state.viewport },
            world: {
                x: (center.x - state.viewport.x) / state.viewport.zoom,
                y: (center.y - state.viewport.y) / state.viewport.zoom,
            },
        };
    }

    function onPointerDown(event) {
        if (event.button !== 0) {
            return;
        }

        const state = handlers.getState();
        pointers.set(pointerKey(event), { x: event.clientX, y: event.clientY });

        if (pointers.size >= 2) {
            event.preventDefault();
            startPinch(state);

            return;
        }

        const handle = closestWithin(root, event.target, '[data-blueprint-handle]');
        const node = closestWithin(root, event.target, '[data-blueprint-node-id]');

        if (handle && state.editable) {
            return;
        }

        if (node && state.editable && !closestWithin(root, event.target, 'button, input, select, textarea, a')) {
            const model = state.workflow.nodes.find(item => item.id === node.dataset.blueprintNodeId);

            if (!model) {
                return;
            }

            gesture = {
                type: 'move',
                id: model.id,
                start: screenToWorld(canvas, state.viewport, event.clientX, event.clientY),
                screenStart: { x: event.clientX, y: event.clientY },
                position: { ...model.position },
                moved: false,
            };

            return;
        }

        if (event.target === canvas || event.target.closest?.('[data-blueprint-world]')) {
            event.preventDefault();
            gesture = {
                type: 'pan',
                start: { x: event.clientX, y: event.clientY },
                viewport: { ...state.viewport },
                moved: false,
            };
        }
    }

    function onPointerMove(event) {
        if (!gesture) {
            return;
        }

        const key = pointerKey(event);
        if (pointers.has(key)) {
            pointers.set(key, { x: event.clientX, y: event.clientY });
        }

        const state = handlers.getState();

        if (gesture.type === 'pinch') {
            const [first, second] = gesture.pointerIds.map(id => pointers.get(id));

            if (!first || !second) {
                return;
            }

            const bounds = canvas.getBoundingClientRect();
            const distance = Math.hypot(second.x - first.x, second.y - first.y);
            const nextZoom = Math.min(2, Math.max(0.2, gesture.viewport.zoom * distance / gesture.distance));
            const center = {
                x: (first.x + second.x) / 2 - bounds.left,
                y: (first.y + second.y) / 2 - bounds.top,
            };
            handlers.setViewport({
                x: center.x - gesture.world.x * nextZoom,
                y: center.y - gesture.world.y * nextZoom,
                zoom: nextZoom,
            }, false);

            return;
        }

        if (gesture.type === 'move') {
            const distance = Math.hypot(
                event.clientX - gesture.screenStart.x,
                event.clientY - gesture.screenStart.y,
            );

            if (!gesture.moved && distance < dragThreshold) {
                return;
            }

            gesture.moved = true;
            const point = screenToWorld(canvas, state.viewport, event.clientX, event.clientY);
            handlers.moveNode(gesture.id, {
                x: Math.round(gesture.position.x + point.x - gesture.start.x),
                y: Math.round(gesture.position.y + point.y - gesture.start.y),
            }, false);

            return;
        }

        const distance = Math.hypot(
            event.clientX - gesture.start.x,
            event.clientY - gesture.start.y,
        );

        if (!gesture.moved && distance < dragThreshold) {
            return;
        }

        gesture.moved = true;
        handlers.setViewport({
            ...gesture.viewport,
            x: gesture.viewport.x + event.clientX - gesture.start.x,
            y: gesture.viewport.y + event.clientY - gesture.start.y,
        }, false);
    }

    function onPointerUp(event) {
        pointers.delete(pointerKey(event));

        if (!gesture) {
            return;
        }

        if (gesture.type === 'pinch') {
            gesture = null;
            pointers.clear();
            handlers.finishViewport();

            return;
        }

        if (gesture.type === 'move') {
            if (gesture.moved) {
                handlers.finishMove(gesture.id);
            }
        } else if (gesture.moved) {
            handlers.finishViewport();
        }

        gesture = null;
    }

    function onWheel(event) {
        if (!event.ctrlKey && !event.metaKey) {
            return;
        }

        event.preventDefault();
        const state = handlers.getState();
        const bounds = canvas.getBoundingClientRect();
        const nextZoom = Math.min(2, Math.max(0.2, state.viewport.zoom * Math.exp(-event.deltaY * 0.002)));
        const worldX = (event.clientX - bounds.left - state.viewport.x) / state.viewport.zoom;
        const worldY = (event.clientY - bounds.top - state.viewport.y) / state.viewport.zoom;

        handlers.setViewport({
            x: event.clientX - bounds.left - worldX * nextZoom,
            y: event.clientY - bounds.top - worldY * nextZoom,
            zoom: nextZoom,
        }, true);
    }

    function onPointerCancel() {
        gesture = null;
        pointers.clear();
    }

    function onKeyDown(event) {
        const modifier = event.ctrlKey || event.metaKey;

        if (modifier && event.key.toLocaleLowerCase() === 'z') {
            event.preventDefault();
            event.shiftKey ? handlers.redo() : handlers.undo();

            return;
        }

        if (modifier && event.key.toLocaleLowerCase() === 'y') {
            event.preventDefault();
            handlers.redo();

            return;
        }

        if (event.key === 'Delete' || event.key === 'Backspace') {
            if (!closestWithin(root, event.target, 'input, textarea, select')) {
                event.preventDefault();
                handlers.removeSelection();
            }
        } else if (event.key === 'Escape') {
            handlers.cancelConnection();
            handlers.clearSelection();
        }
    }

    canvas.addEventListener('pointerdown', onPointerDown);
    canvas.addEventListener('wheel', onWheel, { passive: false });
    root.addEventListener('keydown', onKeyDown);
    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);
    window.addEventListener('pointercancel', onPointerCancel);

    return () => {
        canvas.removeEventListener('pointerdown', onPointerDown);
        canvas.removeEventListener('wheel', onWheel);
        root.removeEventListener('keydown', onKeyDown);
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('pointerup', onPointerUp);
        window.removeEventListener('pointercancel', onPointerCancel);
    };
}
