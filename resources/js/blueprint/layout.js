import dagre from '@dagrejs/dagre';

const DEFAULT_NODE_SIZE = Object.freeze({ width: 240, height: 112 });

function resolveSize(sizes, id) {
    const size = sizes.get(id) ?? DEFAULT_NODE_SIZE;

    return {
        width: Number.isFinite(Number(size.width)) ? Number(size.width) : DEFAULT_NODE_SIZE.width,
        height: Number.isFinite(Number(size.height)) ? Number(size.height) : DEFAULT_NODE_SIZE.height,
    };
}

function radialLevels(workflow) {
    const nodeIds = workflow.nodes.map(node => node.id);
    const outgoing = new Map(nodeIds.map(id => [id, []]));
    const indegree = new Map(nodeIds.map(id => [id, 0]));

    workflow.transitions.forEach((transition) => {
        if (!outgoing.has(transition.source) || !outgoing.has(transition.target)) {
            return;
        }

        outgoing.get(transition.source).push(transition.target);
        indegree.set(transition.target, indegree.get(transition.target) + 1);
    });

    const roots = nodeIds.filter(id => indegree.get(id) === 0);
    if (roots.length === 0) {
        return new Map(nodeIds.map(id => [id, 1]));
    }

    const levels = new Map(roots.map(id => [id, 0]));
    const queue = [...roots];

    while (queue.length > 0) {
        const source = queue.shift();

        outgoing.get(source).forEach((target) => {
            if (levels.has(target)) {
                return;
            }

            levels.set(target, levels.get(source) + 1);
            queue.push(target);
        });
    }

    const fallbackLevel = Math.max(0, ...levels.values()) + 1;
    nodeIds.forEach((id) => {
        if (!levels.has(id)) {
            levels.set(id, fallbackLevel);
        }
    });

    return levels;
}

function arrangeRadially(workflow, sizes) {
    if (workflow.nodes.length === 0) {
        return workflow;
    }

    const levels = radialLevels(workflow);
    const nodesByLevel = new Map();
    let maximumWidth = 0;
    let maximumHeight = 0;

    workflow.nodes.forEach((node) => {
        const size = resolveSize(sizes, node.id);
        const level = levels.get(node.id);
        nodesByLevel.set(level, [...(nodesByLevel.get(level) ?? []), node]);
        maximumWidth = Math.max(maximumWidth, size.width);
        maximumHeight = Math.max(maximumHeight, size.height);
    });

    const ringGap = Math.max(maximumWidth, maximumHeight) + 96;
    const hasLevelZero = nodesByLevel.has(0);
    const radiusByLevel = new Map();
    nodesByLevel.forEach((nodes, level) => {
        if (level === 0 && nodes.length === 1) {
            radiusByLevel.set(level, 0);

            return;
        }

        const circumferenceRadius = nodes.length * (maximumWidth + 48) / (2 * Math.PI);
        const hierarchyRadius = hasLevelZero
            ? ringGap * Math.max(1, level)
            : ringGap * Math.max(1, level - 1);
        radiusByLevel.set(level, Math.max(hierarchyRadius, circumferenceRadius));
    });

    const maximumRadius = Math.max(0, ...radiusByLevel.values());
    const center = {
        x: maximumRadius + maximumWidth / 2 + 64,
        y: maximumRadius + maximumHeight / 2 + 64,
    };
    const positions = new Map();

    nodesByLevel.forEach((nodes, level) => {
        const radius = radiusByLevel.get(level);

        nodes.forEach((node, index) => {
            const size = resolveSize(sizes, node.id);
            const angle = nodes.length === 1 ? -Math.PI / 2 : -Math.PI / 2 + index * Math.PI * 2 / nodes.length;
            positions.set(node.id, {
                x: Math.round(center.x + Math.cos(angle) * radius - size.width / 2),
                y: Math.round(center.y + Math.sin(angle) * radius - size.height / 2),
            });
        });
    });

    return {
        ...workflow,
        nodes: workflow.nodes.map(node => ({
            ...node,
            position: positions.get(node.id),
        })),
    };
}

export function arrangeWorkflow(workflow, sizes = new Map(), options = {}) {
    if (options.layout === 'radial') {
        return arrangeRadially(workflow, sizes);
    }

    const direction = options.direction === 'TB' ? 'TB' : 'LR';
    const graph = new dagre.graphlib.Graph({ multigraph: true, compound: false });
    graph.setGraph({
        rankdir: direction,
        nodesep: 80,
        edgesep: 40,
        ranksep: 180,
        marginx: 64,
        marginy: 64,
        acyclicer: 'greedy',
        ranker: 'network-simplex',
    });
    graph.setDefaultEdgeLabel(() => ({}));

    workflow.nodes.forEach((node) => {
        graph.setNode(node.id, resolveSize(sizes, node.id));
    });

    workflow.transitions.forEach((transition) => {
        if (!graph.hasNode(transition.source) || !graph.hasNode(transition.target)) {
            return;
        }

        graph.setEdge(transition.source, transition.target, {}, transition.id);
    });

    dagre.layout(graph);

    return {
        ...workflow,
        nodes: workflow.nodes.map((node) => {
            const result = graph.node(node.id);
            const size = resolveSize(sizes, node.id);

            return {
                ...node,
                position: {
                    x: Math.round((result?.x ?? size.width / 2) - size.width / 2),
                    y: Math.round((result?.y ?? size.height / 2) - size.height / 2),
                },
            };
        }),
    };
}

export function hasMissingPositions(workflow) {
    return workflow.nodes.some(node => (
        !node.position || !Number.isFinite(node.position.x) || !Number.isFinite(node.position.y)
    ));
}
