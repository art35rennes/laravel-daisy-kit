/**
 * Daisy Kit - Scroll Status
 *
 * CSP-safe scroll progress component. Layout and colors live in CSS; the module
 * only updates the native <progress> value attribute.
 */

function resolveContainer(root) {
    if (root.dataset.global === 'true') {
        return window;
    }

    const selector = root.dataset.container;

    if (selector) {
        try {
            const found = document.querySelector(selector);

            if (found) {
                return found;
            }
        } catch (_) {}
    }

    let node = root.parentElement;

    while (node) {
        const style = getComputedStyle(node);
        const overflowY = style.overflowY;

        if ((overflowY === 'auto' || overflowY === 'scroll') && node.scrollHeight > node.clientHeight) {
            return node;
        }

        node = node.parentElement;
    }

    return window;
}

function getScrollMetrics(container) {
    if (container === window) {
        const scrollTop = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;
        const scrollHeight = Math.max(
            document.body.scrollHeight,
            document.documentElement.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.offsetHeight,
            document.body.clientHeight,
            document.documentElement.clientHeight,
        );
        const clientHeight = window.innerHeight || document.documentElement.clientHeight;

        return { scrollTop, scrollHeight, clientHeight };
    }

    return {
        scrollTop: container.scrollTop,
        scrollHeight: container.scrollHeight,
        clientHeight: container.clientHeight,
    };
}

function getProgress(root) {
    let progress = root.querySelector('[data-scrollstatus-progress]');

    if (!progress) {
        progress = document.createElement('progress');
        progress.className = 'daisy-scroll-status-progress';
        progress.dataset.scrollstatusProgress = '';
        progress.max = 100;
        progress.value = 0;
        root.appendChild(progress);
    }

    return progress;
}

export function init(root) {
    if (!root || root.__ssInit) {
        return root?.__daisyScrollStatus ?? null;
    }

    root.__ssInit = true;

    const container = resolveContainer(root);
    const progress = getProgress(root);
    const openOnce = root.getAttribute('data-open-once') !== 'false';
    const targetSelector = root.getAttribute('data-target') || '';
    const threshold = parseFloat(root.getAttribute('data-scroll') || '0');
    let openedOnce = false;

    progress.max = 100;

    function update() {
        const { scrollTop, scrollHeight, clientHeight } = getScrollMetrics(container);
        const denominator = Math.max(1, scrollHeight - clientHeight);
        const percent = Math.max(0, Math.min(100, (scrollTop / denominator) * 100));

        progress.value = Number(percent.toFixed(2));

        if (threshold > 0 && targetSelector && percent >= threshold && (!openedOnce || !openOnce)) {
            try {
                const dialog = document.querySelector(targetSelector)
                    || document.getElementById(targetSelector.replace(/^#/, ''));

                if (dialog && typeof dialog.showModal === 'function') {
                    dialog.showModal();
                }

                openedOnce = true;
            } catch (_) {}
        }
    }

    const scrollElement = container === window ? window : container;

    scrollElement.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);

    root.__ssOff = () => {
        scrollElement.removeEventListener('scroll', update);
        window.removeEventListener('resize', update);
    };

    const api = {
        dispose() {
            dispose(root);
        },
        update,
    };

    root.__daisyScrollStatus = api;
    update();

    return api;
}

export function initAll() {
    document.querySelectorAll('[data-scrollstatus="1"]').forEach(init);
}

export function dispose(root) {
    if (root?.__ssOff) {
        try {
            root.__ssOff();
        } catch (_) {}

        root.__ssOff = null;
    }

    if (root) {
        root.__ssInit = false;
        root.__daisyScrollStatus = null;
    }
}

if (typeof window !== 'undefined') {
    window.DaisyScrollStatus = { init, initAll, dispose };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
}
