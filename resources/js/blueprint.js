import { graphlib, layout } from '@dagrejs/dagre';

import '../css/blueprint.css';
import { createMountable } from './core/mountable.js';

const svgNamespace = 'http://www.w3.org/2000/svg';

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
    const nodes = validNodes(configuration.nodes);

    if (!canvas || !empty) {
        throw new Error('Blueprint markup is incomplete.');
    }

    canvas.replaceChildren();

    if (nodes.length === 0) {
        empty.hidden = false;
        root.dataset.daisyKitState = 'empty';
        root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:empty', { bubbles: true }));

        return () => {};
    }

    empty.hidden = true;
    const graph = new graphlib.Graph({ multigraph: true });
    graph.setGraph({ rankdir: 'LR', nodesep: 32, ranksep: 72, marginx: 16, marginy: 16 });
    graph.setDefaultEdgeLabel(() => ({}));
    const nodeIds = new Set(nodes.map((node) => node.id));

    nodes.forEach((node) => {
        graph.setNode(node.id, {
            height: Number.isFinite(node.height) ? Math.max(32, Number(node.height)) : 56,
            label: typeof node.label === 'string' ? node.label : node.id,
            width: Number.isFinite(node.width) ? Math.max(96, Number(node.width)) : 160,
        });
    });

    validEdges(configuration.edges, nodeIds).forEach((edge, index) => {
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

    const renderedNodes = graph.nodes().map((nodeId) => {
        const node = graph.node(nodeId);
        const group = createSvgElement('g', {
            'aria-label': node.label,
            'data-daisy-kit-blueprint-node': '',
            'data-node-id': nodeId,
            role: 'button',
            tabindex: '-1',
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

        return group;
    });

    renderedNodes[0].setAttribute('tabindex', '0');
    const selectNode = (node) => {
        renderedNodes.forEach((candidate) => candidate.setAttribute('aria-pressed', String(candidate === node)));
        root.dispatchEvent(new CustomEvent('daisy-kit:blueprint:select', {
            bubbles: true,
            detail: { id: node.dataset.nodeId },
        }));
    };
    const onClick = (event) => {
        const node = event.target.closest('[data-daisy-kit-blueprint-node]');

        if (node) {
            selectNode(node);
        }
    };
    const onKeydown = (event) => {
        const currentIndex = renderedNodes.indexOf(document.activeElement);

        if (!['ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
            return;
        }

        event.preventDefault();

        if (event.key === 'Enter' || event.key === ' ') {
            if (currentIndex >= 0) {
                selectNode(document.activeElement);
            }

            return;
        }

        const direction = ['ArrowLeft', 'ArrowUp'].includes(event.key) ? -1 : 1;
        const nextIndex = (currentIndex + direction + renderedNodes.length) % renderedNodes.length;
        renderedNodes.forEach((node, index) => node.setAttribute('tabindex', index === nextIndex ? '0' : '-1'));
        renderedNodes[nextIndex].focus();
    };

    canvas.addEventListener('click', onClick);
    canvas.addEventListener('keydown', onKeydown);
    root.dataset.daisyKitState = 'ready';

    return () => {
        canvas.removeEventListener('click', onClick);
        canvas.removeEventListener('keydown', onKeydown);
        canvas.replaceChildren();
    };
}

const module = createMountable('blueprint', renderBlueprint);

export const { mount, mountAll, unmount } = module;
