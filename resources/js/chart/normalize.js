const CARTESIAN_PRESETS = new Set(['bar', 'line', 'area', 'stacked-bar', 'stacked-area', 'sparkline']);
const CIRCULAR_PRESETS = new Set(['pie', 'donut']);
const ALLOWED_PRESETS = new Set([...CARTESIAN_PRESETS, ...CIRCULAR_PRESETS]);

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
                name: value.name || categories[index] || `Item ${index + 1}`,
                value: value.value,
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
    const series = normalizeSeries(config.series);
    const circularSeries = CIRCULAR_PRESETS.has(preset) ? normalizeCircularSeries(series, categories) : series;
    const normalizedSeries = CIRCULAR_PRESETS.has(preset) ? circularSeries : series;

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
        hasData: hasAnyData(normalizedSeries),
        isCartesian: CARTESIAN_PRESETS.has(preset),
        isCircular: CIRCULAR_PRESETS.has(preset),
        isSparkline: preset === 'sparkline',
        isStacked: preset === 'stacked-bar' || preset === 'stacked-area',
        isArea: preset === 'area' || preset === 'stacked-area',
    };
}
