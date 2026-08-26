import { graphlib, layout } from '@dagrejs/dagre';

import '../css/blueprint.css';
import { createMountable } from './core/mountable.js';

const svgNamespace = 'http://www.w3.org/2000/svg';
const structuralHistory = new WeakMap();

function createSvgElement(name, attributes = {}) {
    const element = document.createElementNS(svgNamespace, name);

    Object.entries(attributes).forEach(([attribute, value]) => element.setAttribute(attribute, String(value)));

    return element;
}

function validNodes(nodes) {
    if (!Array.isArray(nodes)) {
        return [];
    }

    return nodes.filter((node) => node && typeof node.id === 'string' && node.id.length > 0);
}

function validEdges(edges, nodeIds) {
    if (!Array.isArray(edges)) {
        return [];
    }

    return edges.filter((edge) => (
        edge
        && typeof edge.source === 'string'
        && typeof edge.target === 'string'
        && nodeIds.has(edge.source)
        && nodeIds.has(edge.target)
    ));
}

function renderBlueprint(root, configuration) {
    const canvas = root.querySelector('[data-daisy-kit-blueprint-canvas]');
    const empty = root.querySelector('[data-daisy-kit-empty]');
    const value = root.querySelector('[data-daisy-kit-blueprint-value]');
    const nodes = validNodes(configuration.nodes).map((node) => ({ ...node }));
    const editable = configuration.editable === true;

    if (!canvas || !empty) {
        throw new Error('Blueprint markup is incomplete.');
    }

    canvas.replaceChildren();
    canvas.removeAttribute('aria-label');
    canvas.removeAttribute('role');
    canvas.removeAttribute('tabindex');
    canvas.setAttribute('aria-hidden', 'true');
    canvas.setAttribute('focusable', 'false');

    if (nodes.length === 0) {
        empty.hidden = false;
        root.dataset.daisyKitState = 'empty';
        root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:empty', { bubbles: true }));

        return () => {};
    }

    empty.hidden = true;
    const controls = document.createElement('div');
    controls.setAttribute('aria-label', typeof configuration.label === 'string' ? configuration.label : 'Blueprint nodes');
    controls.setAttribute('data-daisy-kit-blueprint-controls', '');
    controls.setAttribute('role', 'group');
    canvas.insertAdjacentElement('afterend', controls);
    const graph = new graphlib.Graph({ multigraph: true });
    graph.setGraph({ rankdir: 'LR', nodesep: 32, ranksep: 72, marginx: 16, marginy: 16 });
    graph.setDefaultEdgeLabel(() => ({}));
    const nodeIds = new Set(nodes.map((node) => node.id));
    const edges = validEdges(configuration.edges, nodeIds).map((edge) => ({ ...edge }));

    nodes.forEach((node) => {
        graph.setNode(node.id, {
            height: Number.isFinite(node.height) ? Math.max(32, Number(node.height)) : 56,
            label: typeof node.label === 'string' ? node.label : node.id,
            width: Number.isFinite(node.width) ? Math.max(96, Number(node.width)) : 160,
        });
    });

    edges.forEach((edge, index) => {
        graph.setEdge(edge.source, edge.target, { label: typeof edge.label === 'string' ? edge.label : '' }, String(index));
    });

    layout(graph);
    const graphOptions = graph.graph();
    canvas.setAttribute('viewBox', `0 0 ${graphOptions.width} ${graphOptions.height}`);

    graph.edges().forEach((edgeReference) => {
        const edge = graph.edge(edgeReference);
        const points = edge.points.map((point) => `${point.x},${point.y}`).join(' ');
        const line = createSvgElement('polyline', {
            'data-daisy-kit-blueprint-edge': '',
            points,
        });
        canvas.append(line);
    });

    const renderedNodes = graph.nodes().map((nodeId, index) => {
        const node = graph.node(nodeId);
        const group = createSvgElement('g', {
            'data-daisy-kit-blueprint-node': '',
            'data-node-id': nodeId,
            transform: `translate(${node.x - (node.width / 2)}, ${node.y - (node.height / 2)})`,
        });
        const rectangle = createSvgElement('rect', { height: node.height, rx: 6, width: node.width });
        const label = createSvgElement('text', {
            'dominant-baseline': 'middle',
            'pointer-events': 'none',
            x: node.width / 2,
            y: node.height / 2,
            'text-anchor': 'middle',
        });
        label.textContent = node.label;
        group.append(rectangle, label);
        canvas.append(group);

        const control = document.createElement('button');
        control.setAttribute('aria-pressed', 'false');
        control.setAttribute('data-daisy-kit-blueprint-node-control', '');
        control.setAttribute('data-node-id', nodeId);
        control.setAttribute('tabindex', index === 0 ? '0' : '-1');
        control.type = 'button';
        control.textContent = node.label;
        controls.append(control);

        return { control, group, id: nodeId, label };
    });

    const arrange = document.createElement('button');
    arrange.setAttribute('data-daisy-kit-blueprint-view', 'arrange');
    arrange.type = 'button';
    arrange.textContent = 'Arrange diagram';
    controls.append(arrange);

    const fit = document.createElement('button');
    fit.setAttribute('data-daisy-kit-blueprint-view', 'fit');
    fit.type = 'button';
    fit.textContent = 'Fit diagram';
    controls.append(fit);

    let selectedId = null;
    const restoredHistory = structuralHistory.get(root);
    structuralHistory.delete(root);
    const history = restoredHistory?.entries ?? [];
    let historyIndex = restoredHistory?.index ?? -1;
    let editor = null;
    let valueEditor = null;
    let search = null;
    let transitionTarget = null;
    let undo = null;
    let redo = null;

    const synchronizeValue = () => {
        if (value instanceof HTMLInputElement) {
            value.value = JSON.stringify({ edges, nodes });
        }
    };

    const synchronizeHistory = () => {
        undo?.toggleAttribute('disabled', historyIndex <= 0);
        redo?.toggleAttribute('disabled', historyIndex >= history.length - 1);
    };

    const applySnapshot = (snapshot, emitChange = true) => {
        const nextNodes = validNodes(snapshot.nodes).map((node) => ({ ...node }));
        const nextNodeIds = new Set(nextNodes.map((node) => node.id));
        const nextEdges = validEdges(snapshot.edges, nextNodeIds).map((edge) => ({ ...edge }));
        const changedStructure = (
            nodes.length !== nextNodes.length
            || nodes.some((node, index) => node.id !== nextNodes[index]?.id)
            || JSON.stringify(edges) !== JSON.stringify(nextEdges)
        );

        if (changedStructure) {
            const configurationNode = root.querySelector('[data-daisy-kit-config]');

            if (configurationNode instanceof HTMLScriptElement) {
                configurationNode.textContent = JSON.stringify({ ...configuration, edges: nextEdges, nodes: nextNodes });
                structuralHistory.set(root, { entries: history, index: historyIndex });
                module.unmount(root);
                module.mount(root);
            }

            return true;
        }

        nodes.forEach((node, index) => {
            const next = nextNodes[index] ?? {};
            node.label = next.label ?? node.id;
            node.value = next.value;
            const rendered = renderedNodes.find((candidate) => candidate.id === node.id);

            if (rendered) {
                rendered.label.textContent = node.label;
                rendered.control.textContent = node.label;
            }
        });
        synchronizeValue();

        if (emitChange) {
            root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:change', {
                bubbles: true,
                detail: { value: value instanceof HTMLInputElement ? value.value : JSON.stringify({ edges, nodes }) },
            }));
        }

        return false;
    };

    const remember = () => {
        history.splice(historyIndex + 1);
        history.push({
            edges: edges.map((edge) => ({ ...edge })),
            nodes: nodes.map((node) => ({ ...node })),
        });
        historyIndex = history.length - 1;
        synchronizeHistory();
    };

    const selectNode = (nodeId) => {
        renderedNodes.forEach((candidate) => {
            const selected = candidate.id === nodeId;
            candidate.control.setAttribute('aria-pressed', String(selected));
            candidate.group.toggleAttribute('data-daisy-kit-selected', selected);
        });
        selectedId = nodeId;

        if (editor instanceof HTMLInputElement) {
            editor.disabled = false;
            editor.value = renderedNodes.find((candidate) => candidate.id === nodeId)?.label.textContent ?? '';
        }
        if (valueEditor instanceof HTMLTextAreaElement) {
            valueEditor.disabled = false;
            valueEditor.value = JSON.stringify(nodes.find((node) => node.id === nodeId)?.value ?? null);
        }
        if (transitionTarget instanceof HTMLSelectElement) {
            transitionTarget.replaceChildren();
            nodes.filter((node) => node.id !== nodeId).forEach((node) => {
                const option = document.createElement('option');
                option.value = node.id;
                option.textContent = node.label ?? node.id;
                transitionTarget.append(option);
            });
            transitionTarget.disabled = transitionTarget.options.length === 0;
        }
        root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:select', {
            bubbles: true,
            detail: { id: nodeId },
        }));
    };
    const onClick = (event) => {
        const node = event.target.closest('[data-daisy-kit-blueprint-node]');

        if (node) {
            selectNode(node.dataset.nodeId);
        }
    };
    const onControlsClick = (event) => {
        const view = event.target.closest('[data-daisy-kit-blueprint-view]')?.dataset.daisyKitBlueprintView;

        if (view === 'arrange' || view === 'fit') {
            canvas.setAttribute('viewBox', `0 0 ${graphOptions.width} ${graphOptions.height}`);

            if (view === 'fit') {
                canvas.setAttribute('preserveAspectRatio', 'xMidYMid meet');
            }

            root.dispatchEvent(new CustomEvent(`daisy-kit:blueprint:${view}`, {
                bubbles: true,
                detail: { viewBox: canvas.getAttribute('viewBox') },
            }));

            return;
        }

        const action = event.target.closest('[data-daisy-kit-blueprint-history]');

        if (action && history.length > 0) {
            const nextIndex = action.dataset.daisyKitBlueprintHistory === 'undo' ? historyIndex - 1 : historyIndex + 1;

            if (nextIndex >= 0 && nextIndex < history.length) {
                historyIndex = nextIndex;
                const remounted = applySnapshot(history[historyIndex]);

                if (!remounted) {
                    synchronizeHistory();
                }
            }

            return;
        }

        const control = event.target.closest('[data-daisy-kit-blueprint-node-control]');

        if (control) selectNode(control.dataset.nodeId);
    };
    const onEditorChange = () => {
        if (!editable || !selectedId || !(editor instanceof HTMLInputElement)) return;

        const nextLabel = editor.value.trim();
        const index = nodes.findIndex((node) => node.id === selectedId);

        if (index < 0 || nextLabel.length === 0 || nodes[index].label === nextLabel) return;

        nodes[index].label = nextLabel;
        remember();
        applySnapshot(history[historyIndex]);
    };
    const onValueChange = () => {
        if (!editable || !selectedId || !(valueEditor instanceof HTMLTextAreaElement)) return;

        try {
            const nextValue = JSON.parse(valueEditor.value);
            const node = nodes.find((candidate) => candidate.id === selectedId);

            if (!node || JSON.stringify(node.value ?? null) === JSON.stringify(nextValue)) return;

            node.value = nextValue;
            remember();
            applySnapshot(history[historyIndex]);
        } catch {
            valueEditor.setAttribute('aria-invalid', 'true');
        }
    };
    const onSearchInput = () => {
        if (!(search instanceof HTMLInputElement)) return;

        const query = search.value.trim().toLocaleLowerCase();
        renderedNodes.forEach((candidate) => {
            const matches = query.length === 0 || candidate.label.textContent.toLocaleLowerCase().includes(query);
            candidate.control.hidden = !matches;
            candidate.group.toggleAttribute('hidden', !matches);
        });
        root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:search', {
            bubbles: true,
            detail: { query },
        }));
    };
    const persistStructure = () => {
        const configurationNode = root.querySelector('[data-daisy-kit-config]');

        if (!(configurationNode instanceof HTMLScriptElement)) return;

        remember();
        configurationNode.textContent = JSON.stringify({ ...configuration, edges, nodes });
        root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:change', {
            bubbles: true,
            detail: { value: configurationNode.textContent },
        }));
        structuralHistory.set(root, { entries: history, index: historyIndex });
        module.unmount(root);
        module.mount(root);
    };
    const onStructureClick = (event) => {
        const action = event.target.closest('[data-daisy-kit-blueprint-structure]')?.dataset.daisyKitBlueprintStructure;

        if (!action) return;

        if (action === 'add-node') {
            let index = nodes.length + 1;
            let id = `node-${index}`;

            while (nodeIds.has(id)) {
                index += 1;
                id = `node-${index}`;
            }

            nodes.push({ id, label: `Node ${index}` });
            nodeIds.add(id);
            persistStructure();
        }

        if (action === 'remove-node' && selectedId) {
            const index = nodes.findIndex((node) => node.id === selectedId);

            if (index >= 0) {
                nodes.splice(index, 1);
                nodeIds.delete(selectedId);
                edges.splice(0, edges.length, ...edges.filter((edge) => edge.source !== selectedId && edge.target !== selectedId));
                persistStructure();
            }
        }

        if (action === 'add-transition' && selectedId && transitionTarget instanceof HTMLSelectElement && transitionTarget.value !== '') {
            const exists = edges.some((edge) => edge.source === selectedId && edge.target === transitionTarget.value);

            if (!exists) {
                edges.push({ source: selectedId, target: transitionTarget.value });
                persistStructure();
            }
        }
    };
    const onKeydown = (event) => {
        const currentIndex = renderedNodes.findIndex(({ control }) => control === document.activeElement);

        if (!['ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
            return;
        }

        event.preventDefault();

        if (event.key === 'Enter' || event.key === ' ') {
            if (currentIndex >= 0) {
                selectNode(renderedNodes[currentIndex].id);
            }

            return;
        }

        const direction = ['ArrowLeft', 'ArrowUp'].includes(event.key) ? -1 : 1;
        const nextIndex = (currentIndex + direction + renderedNodes.length) % renderedNodes.length;
        renderedNodes.forEach(({ control }, index) => control.setAttribute('tabindex', index === nextIndex ? '0' : '-1'));
        renderedNodes[nextIndex].control.focus();
    };

    canvas.addEventListener('click', onClick);
    controls.addEventListener('click', onControlsClick);
    controls.addEventListener('keydown', onKeydown);

    if (editable) {
        editor = document.createElement('input');
        editor.disabled = true;
        editor.setAttribute('aria-label', 'Selected node label');
        editor.setAttribute('data-daisy-kit-blueprint-editor', '');
        editor.type = 'text';
        controls.append(editor);

        undo = document.createElement('button');
        undo.disabled = true;
        undo.setAttribute('data-daisy-kit-blueprint-history', 'undo');
        undo.type = 'button';
        undo.textContent = 'Undo';
        controls.append(undo);

        redo = document.createElement('button');
        redo.disabled = true;
        redo.setAttribute('data-daisy-kit-blueprint-history', 'redo');
        redo.type = 'button';
        redo.textContent = 'Redo';
        controls.append(redo);

        editor.addEventListener('change', onEditorChange);

        valueEditor = document.createElement('textarea');
        valueEditor.disabled = true;
        valueEditor.setAttribute('aria-label', 'Selected node value as JSON');
        valueEditor.setAttribute('data-daisy-kit-blueprint-value-editor', '');
        controls.append(valueEditor);
        valueEditor.addEventListener('change', onValueChange);

        search = document.createElement('input');
        search.setAttribute('aria-label', 'Find a blueprint node');
        search.setAttribute('data-daisy-kit-blueprint-search', '');
        search.type = 'search';
        controls.append(search);
        search.addEventListener('input', onSearchInput);

        const addNode = document.createElement('button');
        addNode.setAttribute('data-daisy-kit-blueprint-structure', 'add-node');
        addNode.type = 'button';
        addNode.textContent = 'Add node';
        controls.append(addNode);

        const removeNode = document.createElement('button');
        removeNode.setAttribute('data-daisy-kit-blueprint-structure', 'remove-node');
        removeNode.type = 'button';
        removeNode.textContent = 'Remove selected node';
        controls.append(removeNode);

        transitionTarget = document.createElement('select');
        transitionTarget.disabled = true;
        transitionTarget.setAttribute('aria-label', 'Transition target');
        transitionTarget.setAttribute('data-daisy-kit-blueprint-transition-target', '');
        controls.append(transitionTarget);

        const addTransition = document.createElement('button');
        addTransition.setAttribute('data-daisy-kit-blueprint-structure', 'add-transition');
        addTransition.type = 'button';
        addTransition.textContent = 'Add transition';
        controls.append(addTransition);
        controls.addEventListener('click', onStructureClick);
        if (history.length === 0) {
            remember();
        } else {
            synchronizeHistory();
        }
    }
    synchronizeValue();
    root.dataset.daisyKitState = 'ready';

    return () => {
        canvas.removeEventListener('click', onClick);
        controls.removeEventListener('click', onControlsClick);
        controls.removeEventListener('keydown', onKeydown);
        editor?.removeEventListener('change', onEditorChange);
        valueEditor?.removeEventListener('change', onValueChange);
        search?.removeEventListener('input', onSearchInput);
        controls.removeEventListener('click', onStructureClick);
        controls.remove();
        canvas.replaceChildren();
    };
}

const module = createMountable('blueprint', renderBlueprint);

export const { mount, mountAll, unmount } = module;
