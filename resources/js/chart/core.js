import * as echarts from 'echarts/core';
import { BarChart, LineChart, PieChart } from 'echarts/charts';
import {
    CanvasRenderer,
    SVGRenderer,
} from 'echarts/renderers';
import {
    AriaComponent,
    DataZoomComponent,
    GraphicComponent,
    GridComponent,
    LegendComponent,
    MarkLineComponent,
    MarkPointComponent,
    TitleComponent,
    ToolboxComponent,
    TooltipComponent,
} from 'echarts/components';
import { normalizeChartConfig } from './normalize';
import { buildChartOption, mergeOptions } from './presets';
import { buildChartTheme, resolveSingleColor } from './theme';

echarts.use([
    BarChart,
    LineChart,
    PieChart,
    AriaComponent,
    DataZoomComponent,
    GraphicComponent,
    CanvasRenderer,
    SVGRenderer,
    GridComponent,
    LegendComponent,
    MarkLineComponent,
    MarkPointComponent,
    TitleComponent,
    ToolboxComponent,
    TooltipComponent,
]);

const registry = new WeakMap();
const observedRoots = new WeakSet();
const drilldownHandlers = new WeakMap();
const accessibleHandlers = new WeakMap();
const circularFocusBindings = new WeakMap();

export function buildCircularFocusState(length, activeIndex = null) {
    const hasActiveIndex = Number.isInteger(activeIndex) && activeIndex >= 0 && activeIndex < length;

    return Array.from({ length }, (_, index) => {
        if (!hasActiveIndex) {
            return 'normal';
        }

        return index === activeIndex ? 'focus' : 'muted';
    });
}

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

function ensureInstance(host, renderer = 'svg') {
    const current = echarts.getInstanceByDom(host);
    return current || echarts.init(host, null, { renderer });
}

function extractPointDrilldown(params) {
    const data = params?.data;

    if (data && typeof data === 'object' && !Array.isArray(data) && data.drilldown && typeof data.drilldown === 'object') {
        return data.drilldown;
    }

    return null;
}

function resolveAction(config, params) {
    const pointAction = params?.data?.action;
    if (pointAction?.type === 'event' || pointAction?.type === 'url') {
        return pointAction;
    }

    if (config?.action?.type === 'event' || config?.action?.type === 'url') {
        return config.action;
    }

    return null;
}

export function buildDrilldownUrl(config, params) {
    const action = resolveAction(config, params);
    const legacyPointParams = extractPointDrilldown(params);
    const isLegacy = !action;
    const url = action?.type === 'url' ? action.url : config?.drilldown?.url;

    if (!url || url === '#') {
        return null;
    }

    if (isLegacy && !legacyPointParams) {
        return null;
    }

    const baseUrl = typeof window !== 'undefined' ? window.location.href : 'http://localhost/';
    const nextUrl = new URL(url, baseUrl);

    if (typeof window !== 'undefined' && nextUrl.origin !== window.location.origin) {
        return null;
    }

    const mergedParams = {
        ...(config.action?.params || config.drilldown?.params || {}),
        ...(action?.params || {}),
        ...(legacyPointParams || {}),
    };

    Object.entries(mergedParams).forEach(([key, value]) => {
        if (value == null || value === '') {
            return;
        }

        nextUrl.searchParams.set(key, String(value));
    });

    return nextUrl.toString();
}

export function buildActivationDetail(config, params, resolvedUrl = null, chartId = null) {
    const action = resolveAction(config, params);

    return {
        chartId,
        preset: config?.preset || null,
        intent: action?.intent || (resolvedUrl ? 'navigate' : 'detail'),
        target: action?.target || null,
        seriesName: params?.seriesName || null,
        name: params?.name || params?.data?.name || null,
        value: params?.value ?? params?.data?.value ?? null,
        seriesIndex: params?.seriesIndex ?? null,
        dataIndex: params?.dataIndex ?? null,
        color: params?.color || null,
        meta: params?.data?.meta && typeof params.data.meta === 'object' ? params.data.meta : {},
        resolvedUrl,
    };
}

export function openActivationTarget(action, detail) {
    if (typeof document === 'undefined' || !action?.target?.startsWith('#')) {
        return false;
    }

    const target = document.querySelector(action.target);
    if (!target) {
        return false;
    }

    const values = {
        '[data-chart-detail-name]': detail.name || '—',
        '[data-chart-detail-series]': detail.seriesName || '—',
        '[data-chart-detail-value]': detail.value ?? '—',
    };

    Object.entries(values).forEach(([selector, value]) => {
        const node = target.querySelector(selector);
        if (node) {
            node.textContent = String(value);
        }
    });

    const link = target.querySelector('[data-chart-detail-link]');
    if (link) {
        link.toggleAttribute('hidden', !detail.resolvedUrl);
        if (detail.resolvedUrl) {
            link.setAttribute('href', detail.resolvedUrl);
        }
    }

    target.dispatchEvent(new CustomEvent('daisy:chart-detail', {
        bubbles: true,
        detail,
    }));

    if (typeof target.showModal === 'function' && !target.open) {
        target.showModal();
    }

    return true;
}

function hasActivation(config) {
    if (config.action || config.drilldown?.url) {
        return true;
    }

    return config.series.some((series) => series.data.some((point) => point?.action || point?.drilldown));
}

function activate(root, config, params) {
    const action = resolveAction(config, params);
    const targetUrl = buildDrilldownUrl(config, params);
    const detail = buildActivationDetail(config, params, targetUrl, root.id || null);
    const activationEvent = new CustomEvent('daisy:chart-activate', {
        bubbles: true,
        cancelable: true,
        detail,
    });

    root.dispatchEvent(activationEvent);

    if (activationEvent.defaultPrevented) {
        return;
    }

    if (openActivationTarget(action, detail)) {
        return;
    }

    if (!targetUrl) {
        return;
    }

    window.location.assign(targetUrl);
}

function bindDrilldown(root, instance, config) {
    const previousHandler = drilldownHandlers.get(instance);

    if (previousHandler) {
        instance.off('click', previousHandler);
        drilldownHandlers.delete(instance);
    }

    if (!hasActivation(config)) {
        root.toggleAttribute('data-chart-clickable', false);
        return;
    }

    root.toggleAttribute('data-chart-clickable', true);
    const handler = (params) => activate(root, config, params);

    drilldownHandlers.set(instance, handler);
    instance.on('click', handler);
}

function bindAccessibleActions(root, config) {
    const previousHandler = accessibleHandlers.get(root);
    if (previousHandler) {
        root.removeEventListener('click', previousHandler);
    }

    const handler = (event) => {
        const trigger = event.target.closest?.('[data-chart-accessible-action]');
        if (!trigger || !root.contains(trigger)) {
            return;
        }

        const seriesIndex = Number.parseInt(trigger.dataset.seriesIndex || '', 10);
        const dataIndex = Number.parseInt(trigger.dataset.dataIndex || '', 10);
        const series = config.series[seriesIndex];
        const data = series?.data?.[dataIndex];
        if (!series || data == null) {
            return;
        }

        activate(root, config, {
            seriesName: series.name,
            seriesIndex,
            dataIndex,
            name: data?.name || config.categories[dataIndex] || null,
            value: data?.value ?? data,
            color: data?.itemStyle?.color || series.color || null,
            data,
        });
    };

    accessibleHandlers.set(root, handler);
    root.addEventListener('click', handler);
}

function getCircularSectorPaths(root, length) {
    return Array.from(root.querySelectorAll(
        'svg path[fill]:not([fill="none"]):not([fill="transparent"])',
    )).slice(0, length);
}

function applyCircularFocus(root, length, activeIndex = null) {
    const states = buildCircularFocusState(length, activeIndex);
    const paths = getCircularSectorPaths(root, length);

    paths.forEach((path, index) => {
        path.classList.toggle('daisy-chart-segment-focus', states[index] === 'focus');
        path.classList.toggle('daisy-chart-segment-muted', states[index] === 'muted');
    });

    const container = root.closest('article') || root.parentElement;
    container?.querySelectorAll('[data-chart-legend-index]').forEach((item) => {
        const index = Number.parseInt(item.dataset.chartLegendIndex || '', 10);
        item.toggleAttribute('data-chart-focus', states[index] === 'focus');
    });

    if (Number.isInteger(activeIndex) && states[activeIndex] === 'focus') {
        root.dataset.chartFocusIndex = String(activeIndex);
    } else {
        delete root.dataset.chartFocusIndex;
    }
}

function bindCircularFocus(root, instance, config) {
    circularFocusBindings.get(root)?.();
    circularFocusBindings.delete(root);

    if (!config.isCircular || !config.series[0]) {
        return;
    }

    const series = config.series[0];
    const length = series.data.length;
    const host = root.querySelector('[data-chart-canvas]');
    const container = root.closest('article') || root.parentElement;

    if (!host || !container || length === 0) {
        return;
    }

    const clear = () => applyCircularFocus(root, length);
    const focus = (index) => applyCircularFocus(root, length, index);
    const chartMouseOver = (params) => {
        if (params.componentType === 'series' && Number.isInteger(params.dataIndex)) {
            focus(params.dataIndex);
        }
    };
    const hostPointerOver = (event) => {
        const path = event.target.closest?.('svg path[fill]:not([fill="none"]):not([fill="transparent"])');
        const dataIndex = getCircularSectorPaths(root, length).indexOf(path);

        if (dataIndex >= 0) {
            focus(dataIndex);
        }
    };
    const hostPointerLeave = () => clear();
    const legendPointerOver = (event) => {
        const item = event.target.closest?.('[data-chart-legend-index]');
        if (!item || !container.contains(item)) {
            return;
        }

        focus(Number.parseInt(item.dataset.chartLegendIndex || '', 10));
    };
    const legendPointerOut = (event) => {
        const item = event.target.closest?.('[data-chart-legend-index]');
        if (!item || item.contains(event.relatedTarget)) {
            return;
        }

        const index = item.dataset.chartLegendIndex;
        setTimeout(() => {
            if (
                root.dataset.chartFocusIndex === index
                && !host.matches(':hover')
                && !item.matches(':hover')
                && !item.contains(document.activeElement)
            ) {
                clear();
            }
        }, 0);
    };
    const legendFocusIn = (event) => legendPointerOver(event);
    const legendFocusOut = (event) => legendPointerOut(event);
    const legendClick = (event) => {
        const item = event.target.closest?.('[data-chart-legend-index]');
        if (!item || !container.contains(item)) {
            return;
        }

        const dataIndex = Number.parseInt(item.dataset.chartLegendIndex || '', 10);
        const data = series.data[dataIndex];
        if (data == null) {
            return;
        }

        activate(root, config, {
            seriesName: series.name,
            seriesIndex: 0,
            dataIndex,
            name: data?.name || config.categories[dataIndex] || null,
            value: data?.value ?? data,
            color: data?.itemStyle?.color || series.color || null,
            data,
        });
    };

    instance.on('mouseover', chartMouseOver);
    host.addEventListener('pointerover', hostPointerOver);
    host.addEventListener('pointerleave', hostPointerLeave);
    container.addEventListener('pointerover', legendPointerOver);
    container.addEventListener('pointerout', legendPointerOut);
    container.addEventListener('focusin', legendFocusIn);
    container.addEventListener('focusout', legendFocusOut);
    container.addEventListener('click', legendClick);

    circularFocusBindings.set(root, () => {
        instance.off('mouseover', chartMouseOver);
        host.removeEventListener('pointerover', hostPointerOver);
        host.removeEventListener('pointerleave', hostPointerLeave);
        container.removeEventListener('pointerover', legendPointerOver);
        container.removeEventListener('pointerout', legendPointerOut);
        container.removeEventListener('focusin', legendFocusIn);
        container.removeEventListener('focusout', legendFocusOut);
        container.removeEventListener('click', legendClick);
        clear();
    });
}

function applyChart(root, instance, config) {
    const normalized = normalizeChartConfig(config);
    if (typeof window !== 'undefined' && window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) {
        normalized.animation = false;
    }

    const theme = buildChartTheme(normalized, root);
    normalized.series.forEach((series) => {
        if (series.color) {
            series.color = resolveSingleColor(series.color, root) || series.color;
        }

        series.data.forEach((point) => {
            if (!point || typeof point !== 'object' || Array.isArray(point) || !point.color) {
                return;
            }

            point.itemStyle = {
                ...(point.itemStyle || {}),
                color: resolveSingleColor(point.color, root) || point.color,
            };
        });
    });
    bindDrilldown(root, instance, normalized);
    bindAccessibleActions(root, normalized);
    bindCircularFocus(root, instance, normalized);

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

function createResizeObserver(host, instance) {
    if (typeof ResizeObserver === 'undefined') {
        return null;
    }

    const observer = new ResizeObserver(() => {
        instance.resize();
    });

    observer.observe(host);
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

    const normalized = normalizeChartConfig(pair.config);
    const instance = ensureInstance(pair.host, normalized.renderer);
    applyChart(root, instance, pair.config);

    registry.set(root, {
        instance,
        resizeObserver: createResizeObserver(pair.host, instance),
    });

    return instance;
}

export function dispose(root) {
    const entry = registry.get(root);
    if (!entry) {
        return;
    }

    entry.resizeObserver?.disconnect();
    const accessibleHandler = accessibleHandlers.get(root);
    if (accessibleHandler) {
        root.removeEventListener('click', accessibleHandler);
        accessibleHandlers.delete(root);
    }
    circularFocusBindings.get(root)?.();
    circularFocusBindings.delete(root);
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

    observer.observe(html, {
        attributes: true,
        attributeFilter: ['data-theme'],
        subtree: true,
    });
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
