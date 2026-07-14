import { createTransitionGeometry } from './geometry.js';

const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
const DEFAULT_NODE_SIZE = Object.freeze({ width: 240, height: 112 });

export function animateTransform(element, transform) {
    if (typeof element?.animate !== 'function') {
        return;
    }

    element.__daisyBlueprintTransform?.cancel();
    element.__daisyBlueprintTransform = element.animate(
        [{ transform }, { transform }],
        { duration: 0, fill: 'forwards' },
    );
}

function createElement(tagName, className, text) {
    const element = document.createElement(tagName);

    if (className) {
        element.className = className;
    }

    if (text !== undefined) {
        element.textContent = text;
    }

    return element;
}

function createSvgElement(tagName, attributes = {}) {
    const element = document.createElementNS(SVG_NAMESPACE, tagName);

    Object.entries(attributes).forEach(([name, value]) => {
        element.setAttribute(name, String(value));
    });

    return element;
}

function categoryLabel(categories, value) {
    return categories.find(category => category.value === value)?.label ?? value;
}

function transitionPresentation(transition, state) {
    const category = state.transitionCategories.find(item => item.value === transition.category);

    return {
        ...transition,
        shape: category?.shape ?? state.transitionShape,
        color: category?.color ?? null,
    };
}

function nodePresentation(node, state) {
    const category = state.nodeCategories.find(item => item.value === node.category);

    return {
        color: category?.color ?? state.nodeColor,
    };
}

function matchesSearch(node, search) {
    if (!search) {
        return true;
    }

    return [node.label, node.description, node.category]
        .join(' ')
        .toLocaleLowerCase()
        .includes(search.toLocaleLowerCase());
}

function createHandle(side, node, state) {
    const handle = createElement('button', 'daisy-blueprint-handle');
    handle.type = 'button';
    handle.dataset.blueprintHandle = side;
    handle.dataset.blueprintNodeId = node.id;
    handle.dataset.connectionSource = state.connectionSource?.nodeId === node.id
        && state.connectionSource.side === side
        ? 'true'
        : 'false';
    handle.setAttribute(
        'aria-label',
        (state.i18n.newTransition ?? 'New transition') + ': ' + (node.label || node.id),
    );

    return handle;
}

function createNodeCard(node, state) {
    const card = createElement('article', 'daisy-blueprint-node card border border-base-300 bg-base-100 shadow-sm');
    const presentation = nodePresentation(node, state);
    card.dataset.blueprintNodeId = node.id;
    card.dataset.nodeColor = presentation.color;
    card.dataset.selected = state.selection?.type === 'node' && state.selection.id === node.id ? 'true' : 'false';
    card.dataset.connectionSource = state.connectionSource?.nodeId === node.id ? 'true' : 'false';
    card.dataset.searchMatch = matchesSearch(node, state.search) ? 'true' : 'false';
    card.tabIndex = 0;
    card.setAttribute('role', 'button');
    card.setAttribute('aria-label', (state.i18n.node ?? 'Step') + ': ' + (node.label || node.id));
    animateTransform(
        card,
        'translate(' + (node.position?.x ?? 0) + 'px, ' + (node.position?.y ?? 0) + 'px)',
    );

    const body = createElement('div', 'card-body gap-2 p-4');
    const heading = createElement('div', 'flex min-w-0 items-start justify-between gap-2');
    const title = createElement('h3', 'line-clamp-2 min-w-0 text-sm font-semibold', node.label || state.i18n.unnamed);
    heading.append(title);

    if (node.category) {
        heading.append(createElement(
            'span',
            'badge badge-sm badge-outline shrink-0',
            categoryLabel(state.nodeCategories, node.category),
        ));
    }

    body.append(heading);

    if (node.description) {
        body.append(createElement('p', 'daisy-blueprint-node-description line-clamp-2 text-xs text-base-content/65', node.description));
    }

    card.append(body);

    if (state.editable) {
        ['top', 'right', 'bottom', 'left'].forEach((side) => {
            card.append(createHandle(side, node, state));
        });
    }

    return card;
}

function renderNodes(root, state) {
    const layer = root.querySelector('[data-blueprint-nodes]');
    const fragment = document.createDocumentFragment();

    state.workflow.nodes.forEach((node) => {
        fragment.append(createNodeCard(node, state));
    });

    layer.replaceChildren(fragment);

    return new Map(state.workflow.nodes.map((node) => {
        const card = Array.from(layer.children).find(element => (
            element.dataset.blueprintNodeId === node.id
        ));

        return [node.id, {
            width: card?.offsetWidth || DEFAULT_NODE_SIZE.width,
            height: card?.offsetHeight || DEFAULT_NODE_SIZE.height,
        }];
    }));
}

function transitionLabelMetrics(textValue) {
    const shortened = textValue.length > 42 ? textValue.slice(0, 39) + '…' : textValue;
    return {
        text: shortened,
        width: Math.max(48, shortened.length * 7 + 20),
        height: 26,
    };
}

function rectangle(position, width, height) {
    return {
        left: position.x - width / 2,
        right: position.x + width / 2,
        top: position.y - height / 2,
        bottom: position.y + height / 2,
    };
}

function overlaps(first, second) {
    return first.left < second.right
        && first.right > second.left
        && first.top < second.bottom
        && first.bottom > second.top;
}

function routeOverlapsLabel(route, bounds) {
    if (!route) {
        return false;
    }

    return route.slice(1).some((point, index) => {
        const previous = route[index];

        if (previous.y === point.y) {
            return previous.y >= bounds.top
                && previous.y <= bounds.bottom
                && Math.max(previous.x, point.x) >= bounds.left
                && Math.min(previous.x, point.x) <= bounds.right;
        }

        return previous.x >= bounds.left
            && previous.x <= bounds.right
            && Math.max(previous.y, point.y) >= bounds.top
            && Math.min(previous.y, point.y) <= bounds.bottom;
    });
}

export function resolveTransitionLabelPosition(geometry, textValue, nodes, occupiedLabels, transitionGeometries = []) {
    const metrics = transitionLabelMetrics(textValue);
    const normal = geometry.normal ?? { x: 0, y: -1 };
    const tangent = { x: -normal.y, y: normal.x };
    const distances = [36, 72, 108, 144];
    const preferredOffsets = geometry.offset === 0
        ? [...distances, ...distances.map(distance => -distance), 0]
        : [0, ...distances, ...distances.map(distance => -distance)];
    const candidates = preferredOffsets.map(offset => ({
        x: geometry.label.x + normal.x * offset,
        y: geometry.label.y + normal.y * offset,
    }));

    distances.flatMap(distance => [distance, -distance]).forEach((offset) => {
        candidates.push({
            x: geometry.label.x + tangent.x * offset,
            y: geometry.label.y + tangent.y * offset,
        });
    });

    const nodeBounds = nodes.map(node => rectangle(
        { x: node.position.x + node.width / 2, y: node.position.y + node.height / 2 },
        node.width,
        node.height,
    ));
    const occupiedBounds = occupiedLabels.map(label => rectangle(
        { x: label.x, y: label.y },
        label.width,
        label.height,
    ));

    return candidates.find((candidate) => {
        const candidateBounds = rectangle(candidate, metrics.width, metrics.height);

        return !nodeBounds.some(bounds => overlaps(candidateBounds, bounds))
            && !occupiedBounds.some(bounds => overlaps(candidateBounds, bounds))
            && !transitionGeometries.some((transition) => (
                transition.id !== geometry.id && routeOverlapsLabel(transition.route, candidateBounds)
            ));
    }) ?? geometry.label;
}

export function transitionLabelLeader(anchor, position, metrics) {
    const deltaX = position.x - anchor.x;
    const deltaY = position.y - anchor.y;
    const distance = Math.hypot(deltaX, deltaY);

    if (distance < 18) {
        return null;
    }

    const scale = Math.min(
        Math.abs(deltaX) > 0 ? (metrics.width / 2) / Math.abs(deltaX) : Infinity,
        Math.abs(deltaY) > 0 ? (metrics.height / 2) / Math.abs(deltaY) : Infinity,
    );

    return {
        x1: anchor.x,
        y1: anchor.y,
        x2: position.x - deltaX * scale,
        y2: position.y - deltaY * scale,
    };
}

function appendTransitionLabelLeader(group, metrics, position, anchor) {
    const leader = transitionLabelLeader(anchor, position, metrics);

    if (leader) {
        group.append(createSvgElement('line', {
            class: 'daisy-blueprint-transition-label-leader',
            ...leader,
        }));
    }
}

function appendTransitionLabel(group, metrics, position, selected) {
    const label = createSvgElement('g', {
        class: 'daisy-blueprint-transition-label',
        transform: 'translate(' + position.x + ' ' + position.y + ')',
        'data-selected': selected ? 'true' : 'false',
    });
    const rect = createSvgElement('rect', {
        x: -metrics.width / 2,
        y: -13,
        width: metrics.width,
        height: metrics.height,
        rx: 13,
    });
    const text = createSvgElement('text', {
        'text-anchor': 'middle',
        'dominant-baseline': 'central',
    });
    text.textContent = metrics.text;
    label.append(rect, text);
    group.append(label);
}

function renderTransitions(root, state, sizes) {
    const layer = root.querySelector('[data-blueprint-transition-layer]');
    const labelLayer = root.querySelector('[data-blueprint-transition-label-layer]');
    const svg = root.querySelector('[data-blueprint-edges]');
    const markerId = root.querySelector('marker')?.id;
    const measuredNodes = state.workflow.nodes.map(node => ({
        ...node,
        ...(sizes.get(node.id) ?? DEFAULT_NODE_SIZE),
    }));
    const width = Math.max(1000, ...measuredNodes.map(node => (
        (node.position?.x ?? 0) + node.width + 240
    )));
    const height = Math.max(800, ...measuredNodes.map(node => (
        (node.position?.y ?? 0) + node.height + 240
    )));
    svg.setAttribute('width', String(Math.ceil(width)));
    svg.setAttribute('height', String(Math.ceil(height)));
    const presentedTransitions = state.workflow.transitions.map(transition => (
        transitionPresentation(transition, state)
    ));
    const geometries = createTransitionGeometry(presentedTransitions, measuredNodes, {
        shape: state.transitionShape,
    });
    const transitionById = new Map(state.workflow.transitions.map(transition => [transition.id, transition]));
    const nodeById = new Map(state.workflow.nodes.map(node => [node.id, node]));
    const transitionFragment = document.createDocumentFragment();
    const labelFragment = document.createDocumentFragment();
    const occupiedLabels = [];

    geometries.forEach((geometry) => {
        const transition = transitionById.get(geometry.id);
        const selected = state.selection?.type === 'transition' && state.selection.id === geometry.id;
        const group = createSvgElement('g', {
            class: 'daisy-blueprint-transition',
            'data-blueprint-transition-id': geometry.id,
            'data-selected': selected ? 'true' : 'false',
            'data-category': transition?.category ?? '',
            'data-transition-shape': geometry.shape,
            'data-transition-color': geometry.color ?? '',
            tabindex: 0,
            role: 'button',
            'aria-label': (state.i18n.transition ?? 'Transition') + ': '
                + (transition?.label || transition?.id) + ', '
                + (nodeById.get(transition?.source)?.label || transition?.source) + ' → '
                + (nodeById.get(transition?.target)?.label || transition?.target),
        });
        const visiblePath = createSvgElement('path', {
            class: 'daisy-blueprint-transition-line',
            d: geometry.path,
            'marker-end': markerId ? 'url(#' + markerId + ')' : '',
        });
        const hitPath = createSvgElement('path', {
            class: 'daisy-blueprint-transition-hit',
            d: geometry.path,
        });
        group.append(visiblePath, hitPath);

        if (transition?.label) {
            const metrics = transitionLabelMetrics(transition.label);
            const labelPosition = resolveTransitionLabelPosition(
                geometry,
                transition.label,
                measuredNodes,
                occupiedLabels,
                geometries,
            );
            const labelGroup = createSvgElement('g', {
                class: 'daisy-blueprint-transition',
                'data-blueprint-transition-id': geometry.id,
                'data-selected': selected ? 'true' : 'false',
                'data-category': transition?.category ?? '',
                'data-transition-shape': geometry.shape,
                'data-transition-color': geometry.color ?? '',
            });
            appendTransitionLabelLeader(group, metrics, labelPosition, geometry.label);
            appendTransitionLabel(labelGroup, metrics, labelPosition, selected);
            labelFragment.append(labelGroup);
            occupiedLabels.push({
                ...labelPosition,
                width: metrics.width,
                height: metrics.height,
            });
        }

        transitionFragment.append(group);
    });

    layer.replaceChildren(transitionFragment);
    labelLayer?.replaceChildren(labelFragment);
}

export function renderWorkflow(root, state) {
    const sizes = renderNodes(root, state);
    renderTransitions(root, state, sizes);

    const empty = root.querySelector('[data-blueprint-empty]');
    if (empty) {
        empty.hidden = state.workflow.nodes.length > 0;
    }

    return sizes;
}
