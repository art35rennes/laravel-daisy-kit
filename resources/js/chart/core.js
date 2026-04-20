import * as echarts from 'echarts/core';
import { BarChart, LineChart, PieChart } from 'echarts/charts';
import {
    CanvasRenderer,
} from 'echarts/renderers';
import {
    GridComponent,
    LegendComponent,
    TitleComponent,
    ToolboxComponent,
    TooltipComponent,
} from 'echarts/components';
import { normalizeChartConfig } from './normalize';
import { buildChartOption, mergeOptions } from './presets';
import { buildChartTheme } from './theme';

echarts.use([
    BarChart,
    LineChart,
    PieChart,
    CanvasRenderer,
    GridComponent,
    LegendComponent,
    TitleComponent,
    ToolboxComponent,
    TooltipComponent,
]);

const registry = new WeakMap();
const observedRoots = new WeakSet();

function readConfigFromContainer(root) {
    const host = root.querySelector('[data-chart-canvas]');
    const configScript = root.querySelector('script[data-chart-config]');
    const emptyNode = root.querySelector('[data-chart-empty]');

    if (!host || !configScript) {
        return null;
    }

    try {
        return {
            host,
            emptyNode,
            config: JSON.parse(configScript.textContent || '{}'),
        };
    } catch (_) {
        return null;
    }
}

function setEmptyState(root, visible) {
    const emptyNode = root.querySelector('[data-chart-empty]');
    if (!emptyNode) {
        return;
    }

    emptyNode.classList.toggle('hidden', !visible);
}

function ensureInstance(host) {
    const current = echarts.getInstanceByDom(host);
    return current || echarts.init(host);
}

function applyChart(root, instance, config) {
    const normalized = normalizeChartConfig(config);
    const theme = buildChartTheme(normalized, root);

    if (!normalized.hasData && !normalized.loading) {
        instance.clear();
        instance.hideLoading();
        setEmptyState(root, true);
        return normalized;
    }

    setEmptyState(root, false);

    const baseOption = buildChartOption(normalized, theme);
    const merged = mergeOptions(baseOption, normalized.options);
    instance.setOption(merged, true);

    if (normalized.loading) {
        instance.showLoading('default', {
            text: 'Loading…',
            color: theme.palette[0],
            textColor: theme.textColor,
            maskColor: 'transparent',
        });
    } else {
        instance.hideLoading();
    }

    return normalized;
}

function createResizeObserver(root, instance) {
    if (typeof ResizeObserver === 'undefined') {
        return null;
    }

    const observer = new ResizeObserver(() => {
        instance.resize();
    });

    observer.observe(root);
    return observer;
}

export function init(root) {
    const pair = readConfigFromContainer(root);
    if (!pair) {
        return null;
    }

    const existing = registry.get(root);
    if (existing) {
        applyChart(root, existing.instance, pair.config);
        return existing.instance;
    }

    const instance = ensureInstance(pair.host);
    applyChart(root, instance, pair.config);

    registry.set(root, {
        instance,
        resizeObserver: createResizeObserver(root, instance),
    });

    return instance;
}

export function dispose(root) {
    const entry = registry.get(root);
    if (!entry) {
        return;
    }

    entry.resizeObserver?.disconnect();
    entry.instance.dispose();
    registry.delete(root);
}

export function initAll() {
    return Array.from(document.querySelectorAll('[data-daisy-chart="1"]'))
        .map((root) => init(root))
        .filter(Boolean);
}

export function updateTheme() {
    document.querySelectorAll('[data-daisy-chart="1"]').forEach((root) => {
        const entry = registry.get(root);
        if (!entry) {
            return;
        }

        const pair = readConfigFromContainer(root);
        if (!pair) {
            return;
        }

        applyChart(root, entry.instance, pair.config);
        entry.instance.resize();
    });
}

function observeChart(root) {
    if (observedRoots.has(root)) {
        return;
    }

    observedRoots.add(root);

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                observer.unobserve(entry.target);
                init(entry.target);
            }
        });
    }, { rootMargin: '300px 0px', threshold: 0.05 });

    observer.observe(root);
}

function observeCharts() {
    if (typeof document === 'undefined') {
        return;
    }

    document.querySelectorAll('[data-daisy-chart="1"]').forEach((root) => {
        observeChart(root);
    });
}

function setupThemeSync() {
    if (typeof MutationObserver === 'undefined' || typeof document === 'undefined') {
        return;
    }

    const html = document.documentElement;
    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                updateTheme();
                break;
            }
        }
    });

    observer.observe(html, { attributes: true, attributeFilter: ['data-theme'] });
}

function setupDomSync() {
    if (typeof MutationObserver === 'undefined' || typeof document === 'undefined') {
        return;
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType !== Node.ELEMENT_NODE) {
                    return;
                }

                if (node.matches?.('[data-daisy-chart="1"]')) {
                    observeChart(node);
                }

                node.querySelectorAll?.('[data-daisy-chart="1"]').forEach((root) => {
                    observeChart(root);
                });
            });

            mutation.removedNodes.forEach((node) => {
                if (node.nodeType !== Node.ELEMENT_NODE) {
                    return;
                }

                if (node.matches?.('[data-daisy-chart="1"]')) {
                    dispose(node);
                }

                node.querySelectorAll?.('[data-daisy-chart="1"]').forEach((root) => {
                    dispose(root);
                });
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            observeCharts();
            setupThemeSync();
            setupDomSync();
        }, { once: true });
    } else {
        observeCharts();
        setupThemeSync();
        setupDomSync();
    }
}

if (typeof window !== 'undefined') {
    window.DaisyChart = {
        init,
        initAll,
        dispose,
        updateTheme,
    };
}

export default init;
