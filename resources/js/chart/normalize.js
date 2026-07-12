const CARTESIAN_PRESETS = new Set(['bar', 'line', 'area', 'stacked-bar', 'stacked-area', 'sparkline']);
const CIRCULAR_PRESETS = new Set(['pie', 'donut']);
const ALLOWED_PRESETS = new Set([...CARTESIAN_PRESETS, ...CIRCULAR_PRESETS]);

function normalizeDrilldown(value) {
    return value && typeof value === 'object' && !Array.isArray(value) ? value : null;
}

function normalizeAction(value, fallbackIntent = 'navigate') {
    if (!value || typeof value !== 'object' || Array.isArray(value)) {
        return null;
    }

    const type = value.type === 'event' ? 'event' : (value.type === 'url' ? 'url' : null);
    if (!type) {
        return null;
    }

    return {
        type,
        ...(type === 'url' && typeof value.url === 'string' ? { url: value.url } : {}),
        ...(typeof value.target === 'string' && value.target.startsWith('#') ? { target: value.target } : {}),
        params: normalizeDrilldown(value.params) || {},
        intent: typeof value.intent === 'string' && value.intent.trim() !== '' ? value.intent : fallbackIntent,
    };
}

function normalizePoint(value, index, categories) {
    if (value && typeof value === 'object' && !Array.isArray(value)) {
        const point = {
            ...value,
            value: value.value,
        };

        if (!point.name && categories[index]) {
            point.name = categories[index];
        }

        if (value.color) {
            point.itemStyle = {
                ...(value.itemStyle || {}),
                color: value.color,
            };
        }

        point.drilldown = normalizeDrilldown(value.drilldown);
        point.action = normalizeAction(value.action, value.action?.type === 'event' ? 'detail' : 'navigate');
        point.meta = normalizeDrilldown(value.meta) || {};

        return point;
    }

    return value;
}

function normalizeSeries(series) {
    if (!Array.isArray(series)) {
        return [];
    }

    return series
        .filter((entry) => entry && typeof entry === 'object')
        .map((entry, index) => ({
            name: entry.name || `Series ${index + 1}`,
            data: Array.isArray(entry.data) ? entry.data : [],
            color: entry.color || null,
            axis: entry.axis === 'right' ? 'right' : 'left',
            stack: entry.stack || null,
        }));
}

function normalizeFormat(format, fallback = 'number') {
    if (!format) {
        return fallback;
    }

    return format;
}

function hasAnyData(series) {
    return series.some((entry) => Array.isArray(entry.data) && entry.data.length > 0);
}

function normalizeCircularSeries(series, categories) {
    const first = series[0] || { name: 'Series 1', data: [] };
    const data = first.data.map((value, index) => {
        if (value && typeof value === 'object' && value.value != null) {
            return {
                ...value,
                name: value.name || categories[index] || `Item ${index + 1}`,
                value: value.value,
                drilldown: normalizeDrilldown(value.drilldown),
                itemStyle: value.color ? { color: value.color } : undefined,
            };
        }

        return {
            name: categories[index] || `Item ${index + 1}`,
            value,
        };
    });

    return [{ ...first, data }];
}

export function normalizeChartConfig(config = {}) {
    const preset = ALLOWED_PRESETS.has(config.preset) ? config.preset : 'bar';
    const categories = Array.isArray(config.categories) ? config.categories : [];
    const series = normalizeSeries(config.series).map((entry) => ({
        ...entry,
        data: entry.data.map((value, index) => normalizePoint(value, index, categories)),
    }));
    const circularSeries = CIRCULAR_PRESETS.has(preset) ? normalizeCircularSeries(series, categories) : series;
    const normalizedSeries = CIRCULAR_PRESETS.has(preset) ? circularSeries : series;
    const orientation = config.orientation === 'horizontal' ? 'horizontal' : 'vertical';
    const drilldown = config.drilldown && typeof config.drilldown === 'object'
        ? {
            url: typeof config.drilldown.url === 'string' && config.drilldown.url.trim() !== '' ? config.drilldown.url : null,
            params: normalizeDrilldown(config.drilldown.params) || {},
        }
        : { url: null, params: {} };
    const action = normalizeAction(config.action);

    return {
        preset,
        categories,
        series: normalizedSeries,
        title: config.title || null,
        subtitle: config.subtitle || null,
        legend: config.legend === true || (config.legend !== false && preset !== 'sparkline'),
        toolbar: Boolean(config.toolbar),
        loading: Boolean(config.loading),
        emptyMessage: config.emptyMessage || 'No data available',
        colors: Array.isArray(config.colors) ? config.colors : null,
        palette: Array.isArray(config.palette) ? config.palette : ['primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'],
        valueFormat: normalizeFormat(config.valueFormat, 'number'),
        tooltipFormat: normalizeFormat(config.tooltipFormat, config.valueFormat || 'number'),
        options: config.options && typeof config.options === 'object' ? config.options : {},
        action,
        drilldown,
        aria: config.aria !== false,
        markers: Array.isArray(config.markers) ? config.markers : [],
        zoom: Boolean(config.zoom),
        zoomMode: config.zoomMode === 'slider' ? 'slider' : 'inside',
        orientation,
        renderer: config.renderer === 'canvas' ? 'canvas' : 'svg',
        showValues: Boolean(config.showValues),
        centerValue: config.centerValue == null ? null : String(config.centerValue),
        centerLabel: config.centerLabel == null ? null : String(config.centerLabel),
        animation: config.animation !== false,
        hasData: hasAnyData(normalizedSeries),
        isCartesian: CARTESIAN_PRESETS.has(preset),
        isCircular: CIRCULAR_PRESETS.has(preset),
        isSparkline: preset === 'sparkline',
        isStacked: preset === 'stacked-bar' || preset === 'stacked-area',
        isArea: preset === 'area' || preset === 'stacked-area',
        isHorizontal: orientation === 'horizontal' && (preset === 'bar' || preset === 'stacked-bar'),
    };
}
