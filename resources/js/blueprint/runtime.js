import { createHistory } from './history.js';
import { createInspector } from './inspector.js';
import { bindBlueprintInteractions, screenToWorld } from './interactions.js';
import { arrangeWorkflow, hasMissingPositions } from './layout.js';
import {
    addNode as addNodeToWorkflow,
    addTransition as addTransitionToWorkflow,
    createEntityId,
    normalizeWorkflow,
    removeNode as removeNodeFromWorkflow,
    removeTransition as removeTransitionFromWorkflow,
    updateNode as updateNodeInWorkflow,
    updateTransition as updateTransitionInWorkflow,
    validateWorkflow,
} from './model.js';
import { animateTransform, renderWorkflow } from './rendering.js';
import { normalizeCategories } from './schema.js';

function clone(value) {
    return JSON.parse(JSON.stringify(value));
}

function readJson(root, selector, fallback) {
    const element = root.querySelector(selector);

    if (!element?.value?.trim()) {
        return fallback;
    }

    try {
        return JSON.parse(element.value);
    } catch {
        return fallback;
    }
}

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent('daisy:blueprint:' + name, {
        bubbles: true,
        detail,
    }));
}

export default function initBlueprint(root) {
    if (!(root instanceof HTMLElement)) {
        return null;
    }

    if (root.__daisyBlueprint) {
        return root.__daisyBlueprint;
    }

    const canvas = root.querySelector('[data-blueprint-canvas]');
    const world = root.querySelector('[data-blueprint-world]');
    const hiddenField = root.querySelector('[data-blueprint-sync]');
    const connectionStatus = root.querySelector('[data-blueprint-connection-status]');
    const editable = root.dataset.mode !== 'view';
    const autosave = root.dataset.autosave === 'true';
    const direction = root.dataset.direction === 'TB' ? 'TB' : 'LR';
    const layout = ['hierarchical', 'tree', 'radial'].includes(root.dataset.layout)
        ? root.dataset.layout
        : 'hierarchical';
    const transitionShape = ['straight', 'curve', 's', 'orthogonal'].includes(root.dataset.transitionShape)
        ? root.dataset.transitionShape
        : 'curve';
    const nodeColor = ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error']
        .includes(root.dataset.nodeColor)
        ? root.dataset.nodeColor
        : 'primary';
    const nodeCategories = normalizeCategories(
        readJson(root, '[data-blueprint-node-categories]', []),
        { withColor: true },
    );
    const transitionCategories = normalizeCategories(
        readJson(root, '[data-blueprint-transition-categories]', []),
        { withPresentation: true },
    );
    const i18n = readJson(root, '[data-blueprint-i18n]', {});
    let workflow = normalizeWorkflow(readJson(root, '[data-blueprint-value]', {}));
    let selection = null;
    let connectionSource = null;
    let search = '';
    let sizes = new Map();
    let destroyed = false;
    let history;
    let inspectorController;

    function state() {
        return {
            workflow,
            viewport: workflow.viewport,
            editable,
            direction,
            layout,
            transitionShape,
            nodeColor,
            selection,
            connectionSource,
            search,
            nodeCategories,
            transitionCategories,
            i18n,
        };
    }

    function applyViewport() {
        animateTransform(
            world,
            'translate(' + workflow.viewport.x + 'px, '
                + workflow.viewport.y + 'px) scale(' + workflow.viewport.zoom + ')',
        );
    }

    function render() {
        sizes = renderWorkflow(root, state());
        applyViewport();
        updateHistoryButtons();
    }

    function sync(announce = false, reason = null) {
        if (!hiddenField) {
            return;
        }

        hiddenField.value = JSON.stringify(workflow);

        if (announce) {
            hiddenField.dispatchEvent(new Event('input', { bubbles: true }));
            hiddenField.dispatchEvent(new Event('change', { bubbles: true }));
            emit(root, 'change', { value: clone(workflow), reason });
        }
    }

    function reportError(error, context = null) {
        emit(root, 'error', {
            error,
            context,
            path: context?.path ?? null,
            value: clone(workflow),
        });
    }

    function ensureValid(nextWorkflow) {
        const normalized = normalizeWorkflow(nextWorkflow);
        const errors = validateWorkflow(normalized);

        if (errors.length > 0) {
            const error = new Error(errors[0].code);
            error.errors = errors;
            throw error;
        }

        return normalized;
    }

    function updateHistoryButtons() {
        const undoButton = root.querySelector('[data-blueprint-action="undo"]');
        const redoButton = root.querySelector('[data-blueprint-action="redo"]');

        if (undoButton) {
            undoButton.disabled = !history?.canUndo();
        }

        if (redoButton) {
            redoButton.disabled = !history?.canRedo();
        }
    }

    function commit(nextWorkflow, reason) {
        try {
            workflow = ensureValid(nextWorkflow);
            history.record(workflow);
            render();
            sync(true, reason);

            return clone(workflow);
        } catch (error) {
            reportError(error, reason);
            throw error;
        }
    }

    function entityForSelection() {
        if (!selection) {
            return null;
        }

        const collection = selection.type === 'node' ? workflow.nodes : workflow.transitions;

        return collection.find(entity => entity.id === selection.id) ?? null;
    }

    function populateTargets(nodeId) {
        const select = root.querySelector('[data-blueprint-transition-target]');

        if (!select) {
            return;
        }

        const fragment = document.createDocumentFragment();
        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = i18n.selectTarget ?? '';
        fragment.append(empty);

        workflow.nodes.forEach((node) => {
            const option = document.createElement('option');
            option.value = node.id;
            option.textContent = node.label || node.id;
            fragment.append(option);
        });

        select.replaceChildren(fragment);
        select.value = '';
        select.dataset.source = nodeId;
    }

    function openInspector() {
        if (!editable) {
            return;
        }

        const entity = entityForSelection();

        if (!entity) {
            inspectorController.close();

            return;
        }

        const isNode = selection.type === 'node';
        root.querySelector('[data-blueprint-node-transition]')?.classList.toggle('hidden', !isNode);

        if (isNode) {
            populateTargets(entity.id);
        }

        inspectorController.open({
            selection,
            entity,
            categories: isNode ? nodeCategories : transitionCategories,
            titlePrefix: isNode ? i18n.node : i18n.transition,
        });
    }

    function selectNow(type, id) {
        const collection = type === 'node' ? workflow.nodes : workflow.transitions;

        if (!collection.some(entity => entity.id === id)) {
            return;
        }

        selection = { type, id };
        render();
        openInspector();
        emit(root, 'select', { selection: { ...selection }, value: clone(workflow) });
    }

    function select(type, id) {
        if (selection?.type === type && selection.id === id) {
            return;
        }

        inspectorController.request(() => selectNow(type, id));
    }

    function clearSelectionNow() {
        selection = null;
        inspectorController.close();
        render();
    }

    function clearSelection() {
        inspectorController.request(clearSelectionNow);
    }

    function resetConnectionSource() {
        connectionSource = null;
        if (connectionStatus) {
            connectionStatus.textContent = '';
        }
    }

    function cancelConnection() {
        if (!connectionSource) {
            return;
        }

        resetConnectionSource();
        render();
    }

    function addNode(input = {}) {
        const ids = workflow.nodes.map(node => node.id);
        const id = input.id || createEntityId('step', ids);
        const center = screenToWorld(
            canvas,
            workflow.viewport,
            canvas.getBoundingClientRect().left + canvas.clientWidth / 2,
            canvas.getBoundingClientRect().top + canvas.clientHeight / 2,
        );
        const node = {
            id,
            label: input.label ?? i18n.newNode ?? 'New step',
            description: input.description ?? '',
            category: input.category ?? nodeCategories[0]?.value ?? '',
            position: input.position ?? { x: Math.round(center.x - 120), y: Math.round(center.y - 56) },
            data: input.data ?? {},
        };
        const value = commit(
            addNodeToWorkflow(workflow, node, { categories: nodeCategories }),
            'addNode',
        );
        select('node', id);

        return value.nodes.find(item => item.id === id);
    }

    function updateNode(id, changes) {
        const updatesConnectionSource = connectionSource?.nodeId === id && changes.id;
        const value = commit(
            updateNodeInWorkflow(workflow, id, changes, { categories: nodeCategories }),
            'updateNode',
        );

        if (updatesConnectionSource) {
            connectionSource.nodeId = changes.id;
        }

        if (selection?.id === id) {
            selection.id = changes.id ?? id;
            render();
            openInspector();
        } else if (updatesConnectionSource) {
            render();
        }

        return value.nodes.find(node => node.id === (changes.id ?? id));
    }

    function removeNode(id) {
        const targetId = id ?? (selection?.type === 'node' ? selection.id : null);

        if (!targetId) {
            return clone(workflow);
        }

        selection = null;
        if (connectionSource?.nodeId === targetId) {
            resetConnectionSource();
        }
        inspectorController.close();

        return commit(removeNodeFromWorkflow(workflow, targetId), 'removeNode');
    }

    function addTransition(input = {}) {
        resetConnectionSource();
        const ids = workflow.transitions.map(transition => transition.id);
        const id = input.id || createEntityId('transition', ids);
        const transition = {
            id,
            source: input.source,
            target: input.target,
            label: input.label ?? i18n.newTransition ?? 'New transition',
            description: input.description ?? '',
            category: input.category ?? transitionCategories[0]?.value ?? '',
            data: input.data ?? {},
        };
        const value = commit(
            addTransitionToWorkflow(workflow, transition, { categories: transitionCategories }),
            'addTransition',
        );
        select('transition', id);

        return value.transitions.find(item => item.id === id);
    }

    function updateTransition(id, changes) {
        const value = commit(
            updateTransitionInWorkflow(workflow, id, changes, { categories: transitionCategories }),
            'updateTransition',
        );

        if (selection?.id === id) {
            selection.id = changes.id ?? id;
            render();
            openInspector();
        }

        return value.transitions.find(transition => transition.id === (changes.id ?? id));
    }

    function removeTransition(id) {
        const targetId = id ?? (selection?.type === 'transition' ? selection.id : null);

        if (!targetId) {
            return clone(workflow);
        }

        selection = null;
        inspectorController.close();

        return commit(removeTransitionFromWorkflow(workflow, targetId), 'removeTransition');
    }

    function arrange() {
        const arrangedWorkflow = arrangeWorkflow(workflow, sizes, { direction, layout });

        return commit(fittedWorkflow(arrangedWorkflow), 'arrange');
    }

    function fittedWorkflow(value = workflow) {
        if (value.nodes.length === 0) {
            return value;
        }

        const bounds = canvas.getBoundingClientRect();
        const padding = 48;
        const left = Math.min(...value.nodes.map(node => node.position.x));
        const top = Math.min(...value.nodes.map(node => node.position.y));
        const right = Math.max(...value.nodes.map(node => (
            node.position.x + (sizes.get(node.id)?.width ?? 240)
        )));
        const bottom = Math.max(...value.nodes.map(node => (
            node.position.y + (sizes.get(node.id)?.height ?? 112)
        )));
        const width = Math.max(1, right - left);
        const height = Math.max(1, bottom - top);
        const zoom = Math.min(1.4, Math.max(0.2, Math.min(
            (bounds.width - padding * 2) / width,
            (bounds.height - padding * 2) / height,
        )));
        return {
            ...value,
            viewport: {
                x: Math.round((bounds.width - width * zoom) / 2 - left * zoom),
                y: Math.round((bounds.height - height * zoom) / 2 - top * zoom),
                zoom,
            },
        };
    }

    function fit() {
        if (workflow.nodes.length === 0) {
            return clone(workflow);
        }

        workflow = fittedWorkflow();
        history.record(workflow);
        applyViewport();
        sync(true, 'fit');
        updateHistoryButtons();

        return clone(workflow);
    }

    function undo() {
        if (!history.canUndo()) {
            return clone(workflow);
        }

        workflow = normalizeWorkflow(history.undo());
        selection = null;
        resetConnectionSource();
        inspectorController.close();
        render();
        sync(true, 'undo');

        return clone(workflow);
    }

    function redo() {
        if (!history.canRedo()) {
            return clone(workflow);
        }

        workflow = normalizeWorkflow(history.redo());
        selection = null;
        resetConnectionSource();
        inspectorController.close();
        render();
        sync(true, 'redo');

        return clone(workflow);
    }

    function setValue(value) {
        try {
            workflow = ensureValid(value);
            history = createHistory(workflow);
            selection = null;
            resetConnectionSource();
            inspectorController.close();
            render();
            sync(true, 'setValue');

            return clone(workflow);
        } catch (error) {
            reportError(error, 'setValue');
            throw error;
        }
    }

    function moveNode(id, position, finished) {
        workflow = updateNodeInWorkflow(workflow, id, { position });
        render();

        if (finished) {
            history.record(workflow);
            sync(true, 'moveNode');
        }
    }

    function removeSelectionNow() {
        if (selection?.type === 'node') {
            removeNode(selection.id);
        } else if (selection?.type === 'transition') {
            removeTransition(selection.id);
        }
    }

    function removeSelection() {
        inspectorController.request(removeSelectionNow);
    }

    function setViewport(viewport, finished) {
        workflow = normalizeWorkflow({ ...workflow, viewport });
        applyViewport();

        if (finished) {
            history.record(workflow);
            sync(true, 'viewport');
            updateHistoryButtons();
        }
    }

    function connectFromHandle(handle) {
        const nodeId = handle.dataset.blueprintNodeId;

        if (!nodeId) {
            return;
        }

        if (!connectionSource) {
            connectionSource = {
                nodeId,
                side: handle.dataset.blueprintHandle,
            };
            if (connectionStatus) {
                connectionStatus.textContent = i18n.selectConnectionTarget ?? i18n.selectTarget ?? '';
            }
            render();
            Array.from(root.querySelectorAll('[data-blueprint-handle]'))
                .find(candidate => (
                    candidate.dataset.blueprintNodeId === connectionSource.nodeId
                    && candidate.dataset.blueprintHandle === connectionSource.side
                ))
                ?.focus({ preventScroll: true });

            return;
        }

        const source = connectionSource.nodeId;
        connectionSource = null;
        addTransition({ source, target: nodeId });
    }

    function focusFirstMatch() {
        const node = workflow.nodes.find(item => (
            [item.label, item.description, item.category]
                .join(' ')
                .toLocaleLowerCase()
                .includes(search.toLocaleLowerCase())
        ));

        if (!node) {
            return;
        }

        const bounds = canvas.getBoundingClientRect();
        workflow = {
            ...workflow,
            viewport: {
                ...workflow.viewport,
                x: bounds.width / 2 - (node.position.x + 120) * workflow.viewport.zoom,
                y: bounds.height / 2 - (node.position.y + 56) * workflow.viewport.zoom,
            },
        };
        applyViewport();
        select('node', node.id);
    }

    function onClick(event) {
        const action = event.target.closest?.('[data-blueprint-action]')?.dataset.blueprintAction;

        if (action === 'add-node') {
            addNode();
            return;
        }

        if (action === 'undo') {
            undo();
            return;
        }

        if (action === 'redo') {
            redo();
            return;
        }

        if (action === 'arrange') {
            arrange();
            return;
        }

        if (action === 'fit') {
            fit();
            return;
        }

        if (action === 'close-inspector') {
            clearSelection();
            return;
        }

        if (action === 'delete') {
            removeSelection();
            return;
        }

        if (action === 'add-transition') {
            const target = root.querySelector('[data-blueprint-transition-target]')?.value;
            const source = selection?.type === 'node' ? selection.id : null;

            if (source && target) {
                addTransition({ source, target });
            }

            return;
        }

        const handle = event.target.closest?.('[data-blueprint-handle]');
        if (handle) {
            connectFromHandle(handle);

            return;
        }

        const transition = event.target.closest?.('[data-blueprint-transition-id]');
        const node = event.target.closest?.('[data-blueprint-node-id]');

        if (transition) {
            cancelConnection();
            select('transition', transition.dataset.blueprintTransitionId);
        } else if (node) {
            cancelConnection();
            select('node', node.dataset.blueprintNodeId);
        } else if (connectionSource) {
            cancelConnection();
        }
    }

    function onRootKeydown(event) {
        if (event.target.closest?.('input, textarea, select')) {
            return;
        }

        const transition = event.target.closest?.('[data-blueprint-transition-id]');
        const node = event.target.closest?.('[data-blueprint-node-id]');
        const handle = event.target.closest?.('[data-blueprint-handle]');

        if (event.key === 'Enter' || event.key === ' ') {
            if (handle) {
                return;
            }

            if (transition) {
                event.preventDefault();
                select('transition', transition.dataset.blueprintTransitionId);
            } else if (node) {
                event.preventDefault();
                select('node', node.dataset.blueprintNodeId);
            }

            return;
        }

        const movement = {
            ArrowUp: [0, -10],
            ArrowRight: [10, 0],
            ArrowDown: [0, 10],
            ArrowLeft: [-10, 0],
        }[event.key];

        if (!editable || !node || !movement) {
            return;
        }

        const current = workflow.nodes.find(item => item.id === node.dataset.blueprintNodeId);
        if (!current) {
            return;
        }

        event.preventDefault();
        const distance = event.shiftKey ? 1 : 10;
        updateNode(current.id, {
            position: {
                x: current.position.x + Math.sign(movement[0]) * distance,
                y: current.position.y + Math.sign(movement[1]) * distance,
            },
        });
    }

    function onSearch(event) {
        search = event.target.value.trim();
        render();
    }

    function onSearchKeydown(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            focusFirstMatch();
        }
    }

    const api = {
        getValue: () => clone(workflow),
        setValue,
        addNode,
        updateNode,
        removeNode,
        addTransition,
        updateTransition,
        removeTransition,
        arrange,
        fit,
        undo,
        redo,
        destroy() {
            if (destroyed) {
                return;
            }

            destroyed = true;
            unbindInteractions();
            root.removeEventListener('click', onClick);
            root.removeEventListener('keydown', onRootKeydown);
            searchInput?.removeEventListener('input', onSearch);
            searchInput?.removeEventListener('keydown', onSearchKeydown);
            inspectorController.destroy();
            delete root.__daisyBlueprint;
        },
    };

    const searchInput = root.querySelector('[data-blueprint-search]');
    inspectorController = createInspector(root, {
        autosave,
        enhanceRichControls: root.dataset.blueprintEnhanceRichControls !== 'false',
        onCommit(currentSelection, changes) {
            currentSelection.type === 'node'
                ? updateNode(currentSelection.id, changes)
                : updateTransition(currentSelection.id, changes);
        },
        onError(error, context) {
            reportError(error, context);
        },
    });
    root.addEventListener('click', onClick);
    root.addEventListener('keydown', onRootKeydown);
    searchInput?.addEventListener('input', onSearch);
    searchInput?.addEventListener('keydown', onSearchKeydown);

    render();
    const missingPositions = hasMissingPositions(workflow);
    if (missingPositions) {
        workflow = arrangeWorkflow(workflow, sizes, { direction, layout });
        render();
        workflow = fittedWorkflow();
        render();
    }
    history = createHistory(workflow);

    const unbindInteractions = bindBlueprintInteractions(root, {
        getState: state,
        moveNode(id, position) {
            moveNode(id, position, false);
        },
        finishMove(id) {
            const node = workflow.nodes.find(item => item.id === id);
            if (node) {
                moveNode(id, node.position, true);
            }
        },
        setViewport,
        finishViewport() {
            history.record(workflow);
            sync(true, 'viewport');
            updateHistoryButtons();
        },
        undo,
        redo,
        removeSelection,
        clearSelection,
        cancelConnection,
    });

    root.__daisyBlueprint = api;
    sync(false);

    updateHistoryButtons();

    const initialErrors = validateWorkflow(workflow);
    if (initialErrors.length > 0) {
        reportError(Object.assign(new Error(initialErrors[0].code), { errors: initialErrors }), 'init');
    }

    emit(root, 'init', { value: clone(workflow), api });

    return api;
}
