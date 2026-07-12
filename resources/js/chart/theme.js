function isCssColorLike(value) {
    if (!value || typeof value !== 'string') {
        return false;
    }

    const candidate = value.trim();
    return candidate.startsWith('#')
        || candidate.startsWith('rgb')
        || candidate.startsWith('hsl')
        || candidate.startsWith('oklch')
        || candidate.startsWith('lab')
        || candidate.startsWith('lch')
        || candidate.startsWith('color(')
        || candidate.startsWith('var(');
}

function createProbe(root) {
    if (typeof document === 'undefined') {
        return null;
    }

    const probe = document.createElement('span');
    probe.textContent = '\u200b';
    probe.className = 'daisy-chart-probe';
    (root || document.body).appendChild(probe);
    return probe;
}

function withProbeClass(probe, className) {
    probe.className = ['daisy-chart-probe', className].filter(Boolean).join(' ');
}

function resolveColorToken(token, contextEl, role = 'text') {
    if (!token || typeof document === 'undefined') {
        return null;
    }

    const variableMatch = token.trim().match(/^var\(\s*(--[A-Za-z0-9-_]+)(?:\s*,[^)]*)?\)$/);
    if (variableMatch) {
        const variableValue = getComputedStyle(contextEl || document.documentElement)
            .getPropertyValue(variableMatch[1])
            .trim();

        return variableValue || null;
    }

    if (isCssColorLike(token)) {
        return token;
    }

    const semanticValue = getComputedStyle(contextEl || document.documentElement)
        .getPropertyValue(`--color-${token}`)
        .trim();

    if (semanticValue) {
        return semanticValue;
    }

    let className = `text-${token}`;
    let readProp = 'color';

    if (role === 'bg') {
        className = `bg-${token}`;
        readProp = 'backgroundColor';
    } else if (role === 'border') {
        className = `border border-${token}`;
        readProp = 'borderTopColor';
    }

    const probe = createProbe(contextEl || document.body);
    if (!probe) {
        return null;
    }
    withProbeClass(probe, className);
    const computed = getComputedStyle(probe);
    const color = computed[readProp] || computed.color;
    probe.remove();

    return color || null;
}

function normalizeHexChannel(value) {
    return value.length === 1 ? `${value}${value}` : value;
}

function hexToRgbaString(color, alpha) {
    const value = color.replace('#', '').trim();

    if (![3, 4, 6, 8].includes(value.length)) {
        return color;
    }

    const channels = value.length <= 4
        ? [
            normalizeHexChannel(value[0]),
            normalizeHexChannel(value[1]),
            normalizeHexChannel(value[2]),
        ]
        : [value.slice(0, 2), value.slice(2, 4), value.slice(4, 6)];

    const [r, g, b] = channels.map((channel) => Number.parseInt(channel, 16));

    if ([r, g, b].some((channel) => Number.isNaN(channel))) {
        return color;
    }

    return `rgba(${r}, ${g}, ${b}, ${Math.max(0, Math.min(1, Number(alpha)))})`;
}

function toRgbaString(rgbString, alpha) {
    const match = rgbString?.match(/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i);
    if (!match) {
        return rgbString;
    }

    const [r, g, b] = match.slice(1, 4).map((value) => Number.parseInt(value, 10));
    return `rgba(${r}, ${g}, ${b}, ${Math.max(0, Math.min(1, Number(alpha)))})`;
}

export function resolveSingleColor(tokenOrColor, contextEl, role = 'text') {
    return resolveColorToken(tokenOrColor, contextEl, role);
}

export function resolveColors(tokensOrColors, contextEl, role = 'text') {
    const list = Array.isArray(tokensOrColors) ? tokensOrColors : (tokensOrColors ? [tokensOrColors] : []);
    return list.map((entry) => resolveSingleColor(entry, contextEl, role)).filter(Boolean);
}

export function applyAlpha(color, alpha) {
    if (!color) {
        return color;
    }

    const parsed = parseCssColor(color);
    if (parsed) {
        return `rgba(${parsed.r}, ${parsed.g}, ${parsed.b}, ${Math.max(0, Math.min(1, Number(alpha)))})`;
    }

    if (color.startsWith('rgba(')) {
        const parts = color.substring(5, color.length - 1).split(',').map((part) => part.trim());
        return `rgba(${parts[0]}, ${parts[1]}, ${parts[2]}, ${Math.max(0, Math.min(1, Number(alpha)))})`;
    }

    if (color.startsWith('rgb(')) {
        return toRgbaString(color, alpha);
    }

    if (color.startsWith('#')) {
        return hexToRgbaString(color, alpha);
    }

    return color;
}

function getBaseContentColor(contextEl) {
    return resolveSingleColor('base-content', contextEl) || 'rgb(30, 30, 30)';
}

function getBase300Color(contextEl) {
    return resolveSingleColor('base-300', contextEl, 'bg') || 'rgb(200, 200, 200)';
}

function getBase200Color(contextEl) {
    return resolveSingleColor('base-200', contextEl, 'bg') || 'rgb(245, 245, 245)';
}

function parseRgb(rgbString) {
    const match = rgbString?.match(/rgba?\s*\(\s*(\d+(?:\.\d+)?)\s*[, ]\s*(\d+(?:\.\d+)?)\s*[, ]\s*(\d+(?:\.\d+)?)(?:\s*[,\/]\s*([0-9.]+%?))?\s*\)/i);
    if (!match) {
        return null;
    }

    return {
        r: Math.round(Number(match[1])),
        g: Math.round(Number(match[2])),
        b: Math.round(Number(match[3])),
        a: match[4] != null ? Number.parseFloat(match[4]) / (match[4].endsWith('%') ? 100 : 1) : 1,
    };
}

function parseOklch(color) {
    const match = color?.match(/oklch\(\s*([0-9.]+%?)\s+([0-9.]+)\s+(-?[0-9.]+)(?:deg)?(?:\s*\/\s*([0-9.]+%?))?\s*\)/i);
    if (!match) {
        return null;
    }

    const lightness = Number.parseFloat(match[1]) / (match[1].endsWith('%') ? 100 : 1);
    const chroma = Number.parseFloat(match[2]);
    const hue = Number.parseFloat(match[3]) * Math.PI / 180;
    const alpha = match[4] == null
        ? 1
        : Number.parseFloat(match[4]) / (match[4].endsWith('%') ? 100 : 1);
    const a = chroma * Math.cos(hue);
    const b = chroma * Math.sin(hue);
    const lPrime = lightness + 0.3963377774 * a + 0.2158037573 * b;
    const mPrime = lightness - 0.1055613458 * a - 0.0638541728 * b;
    const sPrime = lightness - 0.0894841775 * a - 1.291485548 * b;
    const l = lPrime ** 3;
    const m = mPrime ** 3;
    const s = sPrime ** 3;

    return {
        r: linearChannelToSrgb(4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s),
        g: linearChannelToSrgb(-1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s),
        b: linearChannelToSrgb(-0.0041960863 * l - 0.7034186147 * m + 1.707614701 * s),
        a: alpha,
    };
}

function linearChannelToSrgb(channel) {
    const clamped = Math.max(0, Math.min(1, channel));
    const value = clamped <= 0.0031308
        ? 12.92 * clamped
        : 1.055 * clamped ** (1 / 2.4) - 0.055;

    return Math.round(value * 255);
}

function parseCssColor(color) {
    return parseRgb(color) || parseOklch(color);
}

function srgbToLinear(channel) {
    const value = channel / 255;
    return value <= 0.04045 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
}

function relativeLuminance(rgb) {
    if (!rgb) {
        return 1;
    }

    const r = srgbToLinear(rgb.r);
    const g = srgbToLinear(rgb.g);
    const b = srgbToLinear(rgb.b);

    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function isDarkTheme(contextEl) {
    const baseColor = resolveSingleColor('base-100', contextEl, 'bg') || 'rgb(255, 255, 255)';
    return isDarkColor(baseColor);
}

export function isDarkColor(color) {
    return relativeLuminance(parseCssColor(color)) < 0.45;
}

function buildPalette(tokens, contextEl) {
    const input = Array.isArray(tokens) && tokens.length
        ? tokens
        : ['primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];

    const resolved = resolveColors(input, contextEl);
    return resolved.length ? resolved : ['#2563eb', '#db2777', '#14b8a6', '#0ea5e9', '#22c55e', '#f59e0b', '#ef4444'];
}

export function buildChartTheme(config, contextEl) {
    const dark = isDarkTheme(contextEl);
    const palette = config.colors?.length ? resolveColors(config.colors, contextEl) : buildPalette(config.palette, contextEl);
    const border = getBase300Color(contextEl);

    return {
        dark,
        palette,
        textColor: getBaseContentColor(contextEl),
        textMutedColor: applyAlpha(getBaseContentColor(contextEl), dark ? 0.7 : 0.62),
        axisColor: applyAlpha(border, dark ? 0.55 : 0.8),
        gridColor: applyAlpha(border, dark ? 0.22 : 0.42),
        tooltipBackground: applyAlpha(getBase200Color(contextEl), dark ? 0.98 : 0.95),
    };
}
