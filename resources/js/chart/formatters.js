function normalizeFormatOptions(format) {
    if (!format) {
        return { type: 'number' };
    }

    if (typeof format === 'string') {
        return { type: format };
    }

    if (typeof format === 'object') {
        return {
            type: format.type || 'number',
            currency: format.currency || 'EUR',
            locale: format.locale,
            minimumFractionDigits: format.minimumFractionDigits,
            maximumFractionDigits: format.maximumFractionDigits,
        };
    }

    return { type: 'number' };
}

function getFormatterConfig(format) {
    const config = normalizeFormatOptions(format);
    const options = {};

    if (config.minimumFractionDigits != null) {
        options.minimumFractionDigits = Number(config.minimumFractionDigits);
    }

    if (config.maximumFractionDigits != null) {
        options.maximumFractionDigits = Number(config.maximumFractionDigits);
    }

    switch (config.type) {
        case 'compact-number':
            return {
                locale: config.locale,
                options: { ...options, notation: 'compact', maximumFractionDigits: options.maximumFractionDigits ?? 1 },
            };
        case 'currency':
            return {
                locale: config.locale,
                options: { ...options, style: 'currency', currency: config.currency || 'EUR', maximumFractionDigits: options.maximumFractionDigits ?? 0 },
            };
        case 'percent':
            return {
                locale: config.locale,
                options: { ...options, style: 'percent', maximumFractionDigits: options.maximumFractionDigits ?? 0 },
            };
        case 'number':
        default:
            return {
                locale: config.locale,
                options: { ...options, maximumFractionDigits: options.maximumFractionDigits ?? 2 },
            };
    }
}

function getIntlFormatter(format) {
    const config = getFormatterConfig(format);

    try {
        return new Intl.NumberFormat(config.locale, config.options);
    } catch (_) {
        return new Intl.NumberFormat(undefined, config.options);
    }
}

function coerceNumericValue(value) {
    if (typeof value === 'number') {
        return value;
    }

    const numeric = Number(value);
    return Number.isFinite(numeric) ? numeric : null;
}

export function formatValue(value, format) {
    const numeric = coerceNumericValue(value);

    if (numeric == null) {
        return value == null ? '' : String(value);
    }

    const formatter = getIntlFormatter(format);
    const normalized = normalizeFormatOptions(format);
    const formattedValue = normalized.type === 'percent' && Math.abs(numeric) > 1
        ? numeric / 100
        : numeric;

    return formatter.format(formattedValue);
}

export function createAxisLabelFormatter(format) {
    return (value) => formatValue(value, format);
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

export function createTooltipFormatter(format) {
    return (params) => {
        const points = Array.isArray(params) ? params : [params];
        const rows = points.map((point) => {
            const marker = point.marker || '';
            const label = point.seriesName || point.name || '';
            const value = Array.isArray(point.value) ? point.value[1] : point.value;
            return `<div>${marker}<span>${escapeHtml(label)}</span>: <strong>${escapeHtml(formatValue(value, format))}</strong></div>`;
        }).join('');

        const axisLabel = Array.isArray(params) ? params[0]?.axisValueLabel || params[0]?.name || '' : params?.name || '';
        const title = axisLabel ? `<div class="mb-1 font-semibold">${escapeHtml(axisLabel)}</div>` : '';

        return `${title}${rows}`;
    };
}

export function createPieLabelFormatter(format) {
    return (params) => `${params.name}: ${formatValue(params.value, format)}`;
}
