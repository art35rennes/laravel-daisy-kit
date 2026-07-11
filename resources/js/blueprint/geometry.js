const PARALLEL_GAP = 52;
const REVERSE_OFFSET = 86;
const SUPPORTED_SHAPES = new Set(['straight', 'curve', 's', 'orthogonal']);

function center(node) {
    return {
        x: node.position.x + node.width / 2,
        y: node.position.y + node.height / 2,
    };
}

function boundaryPoint(node, toward) {
    const origin = center(node);
    const deltaX = toward.x - origin.x;
    const deltaY = toward.y - origin.y;
    const horizontalScale = Math.abs(deltaX) > 0 ? (node.width / 2) / Math.abs(deltaX) : Infinity;
    const verticalScale = Math.abs(deltaY) > 0 ? (node.height / 2) / Math.abs(deltaY) : Infinity;
    const scale = Math.min(horizontalScale, verticalScale);

    return {
        x: origin.x + deltaX * scale,
        y: origin.y + deltaY * scale,
    };
}

function formatNumber(value) {
    return Number(value.toFixed(2));
}

function pointCommand(command, point) {
    return `${command} ${formatNumber(point.x)} ${formatNumber(point.y)}`;
}

function cubicPoint(start, controlOne, controlTwo, end, progress = 0.5) {
    const inverseProgress = 1 - progress;
    const startWeight = inverseProgress ** 3;
    const firstControlWeight = 3 * inverseProgress ** 2 * progress;
    const secondControlWeight = 3 * inverseProgress * progress ** 2;
    const endWeight = progress ** 3;

    return {
        x: formatNumber(
            start.x * startWeight
            + controlOne.x * firstControlWeight
            + controlTwo.x * secondControlWeight
            + end.x * endWeight,
        ),
        y: formatNumber(
            start.y * startWeight
            + controlOne.y * firstControlWeight
            + controlTwo.y * secondControlWeight
            + end.y * endWeight,
        ),
    };
}

function parallelOffset(index, count) {
    return (index - (count - 1) / 2) * PARALLEL_GAP;
}

function transitionShape(transition, fallback) {
    if (SUPPORTED_SHAPES.has(transition.shape)) {
        return transition.shape;
    }

    return SUPPORTED_SHAPES.has(fallback) ? fallback : 'curve';
}

function createLoopGeometry(transition, node, index) {
    const offset = index * 22;
    const start = {
        x: node.position.x + node.width,
        y: node.position.y + node.height / 2 - 25 + offset / 4,
    };
    const end = {
        x: node.position.x + node.width / 2 + 25 - offset / 4,
        y: node.position.y,
    };
    const reach = 58 + offset;
    const controlOne = { x: start.x + reach, y: start.y - reach };
    const controlTwo = { x: end.x + reach, y: end.y - reach };

    return {
        ...transition,
        shape: 'curve',
        offset,
        normal: { x: 0.71, y: -0.71 },
        path: [
            pointCommand('M', start),
            `${pointCommand('C', controlOne)}`,
            `${formatNumber(controlTwo.x)} ${formatNumber(controlTwo.y)}`,
            `${formatNumber(end.x)} ${formatNumber(end.y)}`,
        ].join(' '),
        label: cubicPoint(start, controlOne, controlTwo, end),
    };
}

function straightPath(start, end, offset, normal) {
    if (offset === 0) {
        return [pointCommand('M', start), pointCommand('L', end)].join(' ');
    }

    const middle = {
        x: (start.x + end.x) / 2 + normal.x * offset,
        y: (start.y + end.y) / 2 + normal.y * offset,
    };

    return [pointCommand('M', start), pointCommand('L', middle), pointCommand('L', end)].join(' ');
}

function curvePath(start, end, offset, normal) {
    const { controlOne, controlTwo } = curveControls(start, end, offset, normal);

    return [
        pointCommand('M', start),
        `C ${formatNumber(controlOne.x)} ${formatNumber(controlOne.y)}`,
        `${formatNumber(controlTwo.x)} ${formatNumber(controlTwo.y)}`,
        `${formatNumber(end.x)} ${formatNumber(end.y)}`,
    ].join(' ');
}

function curveControls(start, end, offset, normal) {
    const deltaX = end.x - start.x;
    const deltaY = end.y - start.y;

    return {
        controlOne: {
            x: start.x + deltaX / 3 + normal.x * offset,
            y: start.y + deltaY / 3 + normal.y * offset,
        },
        controlTwo: {
            x: start.x + deltaX * 2 / 3 + normal.x * offset,
            y: start.y + deltaY * 2 / 3 + normal.y * offset,
        },
    };
}

function sPath(start, end, offset, normal) {
    const { controlOne, controlTwo } = sControls(start, end, offset, normal);

    return [
        pointCommand('M', start),
        `C ${formatNumber(controlOne.x)} ${formatNumber(controlOne.y)}`,
        `${formatNumber(controlTwo.x)} ${formatNumber(controlTwo.y)}`,
        `${formatNumber(end.x)} ${formatNumber(end.y)}`,
    ].join(' ');
}

function sControls(start, end, offset, normal) {
    const horizontal = Math.abs(end.x - start.x) >= Math.abs(end.y - start.y);
    const controlOne = horizontal
        ? { x: (start.x + end.x) / 2, y: start.y }
        : { x: start.x, y: (start.y + end.y) / 2 };
    const controlTwo = horizontal
        ? { x: (start.x + end.x) / 2, y: end.y }
        : { x: end.x, y: (start.y + end.y) / 2 };

    [controlOne, controlTwo].forEach((control) => {
        control.x += normal.x * offset;
        control.y += normal.y * offset;
    });

    return { controlOne, controlTwo };
}

function straightLabel(start, end, offset, normal) {
    if (offset === 0) {
        return {
            x: formatNumber((start.x + end.x) / 2),
            y: formatNumber((start.y + end.y) / 2),
        };
    }

    return {
        x: formatNumber((start.x + end.x) / 2 + normal.x * offset),
        y: formatNumber((start.y + end.y) / 2 + normal.y * offset),
    };
}

function curveLabel(start, end, offset, normal) {
    const { controlOne, controlTwo } = curveControls(start, end, offset, normal);

    return cubicPoint(start, controlOne, controlTwo, end);
}

function sLabel(start, end, offset, normal) {
    const { controlOne, controlTwo } = sControls(start, end, offset, normal);

    return cubicPoint(start, controlOne, controlTwo, end);
}

function orthogonalRoute(start, end, offset) {
    const horizontal = Math.abs(end.x - start.x) >= Math.abs(end.y - start.y);

    if (horizontal) {
        const direction = Math.sign(end.x - start.x) || 1;
        const lead = Math.min(32, Math.abs(end.x - start.x) / 4);
        const routeY = (start.y + end.y) / 2 + offset;

        return [
            start,
            { x: start.x + direction * lead, y: start.y },
            { x: start.x + direction * lead, y: routeY },
            { x: end.x - direction * lead, y: routeY },
            { x: end.x - direction * lead, y: end.y },
            end,
        ];
    }

    const direction = Math.sign(end.y - start.y) || 1;
    const lead = Math.min(32, Math.abs(end.y - start.y) / 4);
    const routeX = (start.x + end.x) / 2 - offset;

    return [
        start,
        { x: start.x, y: start.y + direction * lead },
        { x: routeX, y: start.y + direction * lead },
        { x: routeX, y: end.y - direction * lead },
        { x: end.x, y: end.y - direction * lead },
        end,
    ];
}

function orthogonalPath(start, end, offset) {
    return orthogonalRoute(start, end, offset)
        .map((point, index) => pointCommand(index === 0 ? 'M' : 'L', point))
        .join(' ');
}

function orthogonalLabel(start, end, offset) {
    const horizontal = Math.abs(end.x - start.x) >= Math.abs(end.y - start.y);

    if (horizontal) {
        const direction = Math.sign(end.x - start.x) || 1;
        const lead = Math.min(32, Math.abs(end.x - start.x) / 4);

        return {
            x: formatNumber((start.x + direction * lead + end.x - direction * lead) / 2),
            y: formatNumber((start.y + end.y) / 2 + offset),
        };
    }

    const direction = Math.sign(end.y - start.y) || 1;
    const lead = Math.min(32, Math.abs(end.y - start.y) / 4);

    return {
        x: formatNumber((start.x + end.x) / 2 - offset),
        y: formatNumber((start.y + direction * lead + end.y - direction * lead) / 2),
    };
}

function createLineGeometry(transition, source, target, offset, fallbackShape) {
    const sourceCenter = center(source);
    const targetCenter = center(target);
    const start = boundaryPoint(source, targetCenter);
    const end = boundaryPoint(target, sourceCenter);
    const deltaX = end.x - start.x;
    const deltaY = end.y - start.y;
    const length = Math.max(1, Math.hypot(deltaX, deltaY));
    const normal = { x: -deltaY / length, y: deltaX / length };
    const shape = transitionShape(transition, fallbackShape);
    const pathFactory = {
        straight: straightPath,
        curve: curvePath,
        s: sPath,
        orthogonal: orthogonalPath,
    }[shape];
    const label = {
        straight: straightLabel,
        curve: curveLabel,
        s: sLabel,
        orthogonal: orthogonalLabel,
    }[shape](start, end, offset, normal);
    const route = shape === 'orthogonal' ? orthogonalRoute(start, end, offset) : null;

    return {
        ...transition,
        shape,
        offset,
        normal,
        path: pathFactory(start, end, offset, normal),
        label,
        ...(route ? { route } : {}),
    };
}

export function createTransitionGeometry(transitions, nodes, options = {}) {
    const nodeById = new Map(nodes.map(node => [node.id, node]));
    const directionGroups = new Map();
    const loopIndexes = new Map();

    transitions.forEach((transition) => {
        const directionKey = `${transition.source}\u0000${transition.target}`;
        directionGroups.set(directionKey, [...(directionGroups.get(directionKey) ?? []), transition]);
    });

    return transitions.flatMap((transition) => {
        const source = nodeById.get(transition.source);
        const target = nodeById.get(transition.target);

        if (!source?.position || !target?.position) {
            return [];
        }

        if (transition.source === transition.target) {
            const index = loopIndexes.get(transition.source) ?? 0;
            loopIndexes.set(transition.source, index + 1);

            return [createLoopGeometry(transition, source, index)];
        }

        const directionKey = `${transition.source}\u0000${transition.target}`;
        const reverseKey = `${transition.target}\u0000${transition.source}`;
        const directionTransitions = directionGroups.get(directionKey) ?? [];
        const directionIndex = directionTransitions.findIndex(item => item.id === transition.id);
        const hasReverse = (directionGroups.get(reverseKey) ?? []).length > 0;
        let offset = parallelOffset(directionIndex, directionTransitions.length);

        if (hasReverse) {
            offset += REVERSE_OFFSET;
        }

        return [createLineGeometry(transition, source, target, offset, options.shape)];
    });
}
