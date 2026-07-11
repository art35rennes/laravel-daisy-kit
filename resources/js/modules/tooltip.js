const placements = ['top', 'bottom', 'right', 'left'];
const viewportMargin = 8;
const tooltipGap = 8;

let activeRoot = null;
let activeTrigger = null;
let portal = null;
let observer = null;
let globalListenersInstalled = false;

function normalizedPlacement(value) {
    return placements.includes(value) ? value : 'top';
}

function oppositePlacement(placement) {
    return {
        top: 'bottom',
        bottom: 'top',
        left: 'right',
        right: 'left',
    }[placement];
}

function placementOrder(preferredPlacement) {
    const preferred = normalizedPlacement(preferredPlacement);
    const perpendicular = ['top', 'bottom'].includes(preferred)
        ? ['right', 'left']
        : ['top', 'bottom'];

    return [preferred, oppositePlacement(preferred), ...perpendicular];
}

function coordinatesFor(placement, anchorRect, tooltipRect) {
    if (placement === 'bottom') {
        return {
            left: anchorRect.left + ((anchorRect.width - tooltipRect.width) / 2),
            top: anchorRect.bottom + tooltipGap,
        };
    }

    if (placement === 'left') {
        return {
            left: anchorRect.left - tooltipRect.width - tooltipGap,
            top: anchorRect.top + ((anchorRect.height - tooltipRect.height) / 2),
        };
    }

    if (placement === 'right') {
        return {
            left: anchorRect.right + tooltipGap,
            top: anchorRect.top + ((anchorRect.height - tooltipRect.height) / 2),
        };
    }

    return {
        left: anchorRect.left + ((anchorRect.width - tooltipRect.width) / 2),
        top: anchorRect.top - tooltipRect.height - tooltipGap,
    };
}

function overflowScore(coordinates, tooltipRect, viewport) {
    return Math.max(0, viewportMargin - coordinates.left)
        + Math.max(0, viewportMargin - coordinates.top)
        + Math.max(0, coordinates.left + tooltipRect.width + viewportMargin - viewport.width)
        + Math.max(0, coordinates.top + tooltipRect.height + viewportMargin - viewport.height);
}

export function computeTooltipPosition({
    anchorRect,
    tooltipRect,
    preferredPlacement = 'top',
    viewport,
}) {
    const candidates = placementOrder(preferredPlacement).map((placement) => {
        const coordinates = coordinatesFor(placement, anchorRect, tooltipRect);

        return {
            ...coordinates,
            placement,
            score: overflowScore(coordinates, tooltipRect, viewport),
        };
    });
    const best = candidates.reduce((current, candidate) => (
        candidate.score < current.score ? candidate : current
    ));
    const maxLeft = Math.max(viewportMargin, viewport.width - tooltipRect.width - viewportMargin);
    const maxTop = Math.max(viewportMargin, viewport.height - tooltipRect.height - viewportMargin);
    const left = Math.min(Math.max(best.left, viewportMargin), maxLeft);
    const top = Math.min(Math.max(best.top, viewportMargin), maxTop);

    return {
        left,
        top,
        placement: best.placement,
        arrowX: Math.min(Math.max(anchorRect.left + (anchorRect.width / 2) - left, 8), Math.max(8, tooltipRect.width - 8)),
        arrowY: Math.min(Math.max(anchorRect.top + (anchorRect.height / 2) - top, 8), Math.max(8, tooltipRect.height - 8)),
    };
}

function tooltipLayer(root) {
    return root.closest('dialog[open]') || root.closest('[popover]') || document.body;
}

function tooltipPortal(root) {
    const layer = tooltipLayer(root);

    if (!portal?.isConnected) {
        portal = document.createElement('div');
        portal.id = 'daisy-tooltip-portal';
        portal.className = 'daisy-tooltip-floating';
        portal.dataset.daisyTooltipPortal = 'true';
        portal.setAttribute('role', 'tooltip');
        portal.hidden = true;
    }

    if (portal.parentElement !== layer) {
        layer.append(portal);
    }

    return portal;
}

function tooltipContent(root) {
    return root.querySelector(':scope > .tooltip-content');
}

function tooltipText(root) {
    return root.dataset.tip?.trim() || tooltipContent(root)?.textContent?.trim() || '';
}

function tooltipPlacement(root) {
    return placements.find((placement) => root.classList.contains(`tooltip-${placement}`)) || 'top';
}

function tooltipColor(root) {
    return ['neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error']
        .find((color) => root.classList.contains(`tooltip-${color}`)) || 'neutral';
}

function tooltipTrigger(root) {
    if (root.matches('a, button, input, select, textarea, [tabindex]')) {
        return root;
    }

    return root.querySelector('a, button, input, select, textarea, [tabindex]') || root;
}

function copyTooltipContent(root, target) {
    const source = tooltipContent(root);
    target.replaceChildren();

    if (source) {
        source.childNodes.forEach((node) => target.append(node.cloneNode(true)));
        return;
    }

    target.textContent = root.dataset.tip || '';
}

function updatePosition() {
    if (!activeRoot || !portal || portal.hidden) {
        return;
    }

    const anchorRect = activeRoot.getBoundingClientRect();
    const tooltipRect = portal.getBoundingClientRect();
    const position = computeTooltipPosition({
        anchorRect,
        tooltipRect,
        preferredPlacement: tooltipPlacement(activeRoot),
        viewport: {
            width: document.documentElement.clientWidth || window.innerWidth,
            height: document.documentElement.clientHeight || window.innerHeight,
        },
    });

    portal.style.left = `${position.left}px`;
    portal.style.top = `${position.top}px`;
    portal.style.setProperty('--daisy-tooltip-arrow-x', `${position.arrowX}px`);
    portal.style.setProperty('--daisy-tooltip-arrow-y', `${position.arrowY}px`);
    portal.dataset.placement = position.placement;
    portal.style.visibility = 'visible';
}

function installGlobalListeners() {
    if (globalListenersInstalled) {
        return;
    }

    globalListenersInstalled = true;
    window.addEventListener('resize', updatePosition, { passive: true });
    window.addEventListener('scroll', updatePosition, { capture: true, passive: true });
    document.addEventListener('pointerdown', () => {
        if (activeRoot) {
            hideTooltip(activeRoot, true);
        }
    }, { capture: true });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && activeRoot) {
            hideTooltip(activeRoot, true);
        }
    }, { capture: true });
}

function showTooltip(root) {
    const text = tooltipText(root);

    if (!text) {
        return;
    }

    const target = tooltipPortal(root);
    const theme = root.closest('[data-theme]')?.getAttribute('data-theme');

    if (activeRoot !== root && activeTrigger?.getAttribute('aria-describedby') === target.id) {
        activeTrigger.removeAttribute('aria-describedby');
    }

    activeRoot = root;
    activeTrigger = tooltipTrigger(root);
    copyTooltipContent(root, target);
    target.dataset.color = tooltipColor(root);
    target.toggleAttribute('data-theme', Boolean(theme));

    if (theme) {
        target.setAttribute('data-theme', theme);
    }

    target.hidden = false;
    target.style.visibility = 'hidden';
    activeTrigger.setAttribute('aria-describedby', target.id);
    updatePosition();
}

function hideTooltip(root, force = false) {
    if (activeRoot !== root || (!force && root.classList.contains('tooltip-open'))) {
        return;
    }

    if (activeTrigger?.getAttribute('aria-describedby') === portal?.id) {
        activeTrigger.removeAttribute('aria-describedby');
    }

    portal.hidden = true;
    portal.style.visibility = 'hidden';
    activeRoot = null;
    activeTrigger = null;
}

function hideWhenInactive(root) {
    window.setTimeout(() => {
        if (!root.matches(':hover') && !root.contains(document.activeElement)) {
            hideTooltip(root);
        }
    });
}

export default function initTooltip(root) {
    if (!(root instanceof HTMLElement) || root.dataset.tooltipInitialized === 'true' || !tooltipText(root)) {
        return;
    }

    root.dataset.tooltipInitialized = 'true';
    root.dataset.tooltipReady = 'true';
    root.classList.add('daisy-tooltip-managed');

    const trigger = tooltipTrigger(root);
    const nativeTitle = trigger.getAttribute('title');

    if (nativeTitle) {
        trigger.dataset.tooltipNativeTitle = nativeTitle;
        trigger.removeAttribute('title');
    }

    root.addEventListener('pointerenter', () => showTooltip(root));
    root.addEventListener('pointerleave', () => hideWhenInactive(root));
    root.addEventListener('focusin', () => showTooltip(root));
    root.addEventListener('focusout', () => hideWhenInactive(root));
    root.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideTooltip(root);
        }
    });

    installGlobalListeners();

    if (root.classList.contains('tooltip-open')) {
        showTooltip(root);
    }
}

export function initAllTooltips(scope = document) {
    const roots = [];

    if (scope instanceof Element && scope.matches('.tooltip')) {
        roots.push(scope);
    }

    scope.querySelectorAll?.('.tooltip').forEach((root) => roots.push(root));
    roots.forEach(initTooltip);

    if (!observer && document.body) {
        observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node instanceof Element) {
                        initAllTooltips(node);
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }
}
