import {
    normalizeIconUrl,
    sanitizeIconMarkup,
    svgToDataUrl,
} from './icons.js';

const DEFAULT_DRAW_CONFIG = {
    toolbar: true,
    point: true,
    line: true,
    polygon: true,
    rectangle: true,
    select: true,
    delete: true,
    undoRedo: true,
    groupedToolbar: true,
    actionBadge: true,
    styles: {},
};

const DEFAULT_MEASURE_CONFIG = {
    display: 'metric',
    showTooltip: true,
    maxLabels: 16,
};

const TOOL_ICONS = {
    area: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 19 19 5"/><path d="M6 5h13v13"/><path d="M6 15h4"/><path d="M6 11h8"/><path d="M6 7h12"/></svg>',
    delete: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>',
    equipment: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.8 2.8-3-3 2.8-2.8Z"/><path d="m6 18 2 2"/></svg>',
    line: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 19 19 5"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="5" r="2"/></svg>',
    pipe: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h8a4 4 0 0 1 4 4v4"/><path d="M14 16h6"/><path d="M4 6v4"/><path d="M18 14v4"/><path d="M7 6v4"/></svg>',
    point: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s6-5.33 6-11a6 6 0 1 0-12 0c0 5.67 6 11 6 11Z"/><circle cx="12" cy="10" r="2"/></svg>',
    polygon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 4 19 8l-2 11H6L3 10l4-6Z"/><circle cx="7" cy="4" r="1.5"/><circle cx="19" cy="8" r="1.5"/><circle cx="17" cy="19" r="1.5"/><circle cx="6" cy="19" r="1.5"/><circle cx="3" cy="10" r="1.5"/></svg>',
    rectangle: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="6" width="16" height="12" rx="1"/></svg>',
    redo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 14 4-4-4-4"/><path d="M19 10H9a4 4 0 0 0-4 4v2"/></svg>',
    ruler: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 2 6 6L8 22l-6-6L16 2Z"/><path d="m7 17 2-2"/><path d="m11 13 2-2"/><path d="m15 9 2-2"/></svg>',
    select: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m4 4 7 16 2-7 7-2L4 4Z"/></svg>',
    selectCircle: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="7"/><path d="M12 3v3"/><path d="M12 18v3"/><path d="M3 12h3"/><path d="M18 12h3"/></svg>',
    selectPolygon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 5 18 8l-2 11H7L4 11l2-6Z"/><path d="m9 12 2 2 4-5"/></svg>',
    selectRectangle: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="6" width="16" height="12" rx="1"/><path d="m8 12 2 2 5-5"/></svg>',
    structure: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V9l8-5 8 5v11"/><path d="M9 20v-7h6v7"/><path d="M4 9h16"/><path d="M9 9v4"/><path d="M15 9v4"/></svg>',
    undo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 14-4-4 4-4"/><path d="M5 10h10a4 4 0 0 1 4 4v2"/></svg>',
};

const OBJECT_GEOMETRY_MODES = {
    line: 'polyline',
    point: 'point',
    polygon: 'polygon',
};

const OBJECT_GEOMETRY_MARKER_MODE = 'marker';

const STYLE_ALIASES = {
    point: {
        color: 'pointColor',
        fillColor: 'pointColor',
        opacity: 'pointOpacity',
        outlineColor: 'pointOutlineColor',
        outlineOpacity: 'pointOutlineOpacity',
        outlineWidth: 'pointOutlineWidth',
        size: 'pointWidth',
        width: 'pointWidth',
    },
    line: {
        color: 'lineStringColor',
        dash: 'lineStringDash',
        dashArray: 'lineStringDash',
        opacity: 'lineStringOpacity',
        width: 'lineStringWidth',
    },
    polygon: {
        color: 'polygonOutlineColor',
        fillColor: 'polygonFillColor',
        fillOpacity: 'polygonFillOpacity',
        opacity: 'polygonOutlineOpacity',
        outlineColor: 'polygonOutlineColor',
        outlineOpacity: 'polygonOutlineOpacity',
        outlineWidth: 'polygonOutlineWidth',
        strokeColor: 'polygonOutlineColor',
        strokeOpacity: 'polygonOutlineOpacity',
        strokeWidth: 'polygonOutlineWidth',
        width: 'polygonOutlineWidth',
    },
    rectangle: {
        color: 'outlineColor',
        fillColor: 'fillColor',
        fillOpacity: 'fillOpacity',
        opacity: 'outlineOpacity',
        outlineColor: 'outlineColor',
        outlineOpacity: 'outlineOpacity',
        outlineWidth: 'outlineWidth',
        strokeColor: 'outlineColor',
        strokeOpacity: 'outlineOpacity',
        strokeWidth: 'outlineWidth',
        width: 'outlineWidth',
    },
    marker: {
        height: 'markerHeight',
        iconUrl: 'markerUrl',
        markerHeight: 'markerHeight',
        markerUrl: 'markerUrl',
        markerWidth: 'markerWidth',
        url: 'markerUrl',
        width: 'markerWidth',
    },
};

const STYLE_PROPERTY_BY_MODE = {
    marker: 'point',
    point: 'point',
    polygon: 'polygon',
    polyline: 'line',
    rectangle: 'rectangle',
};

function normalizeActionBadgeConfig(actionBadge) {
    if (actionBadge === false || actionBadge?.enabled === false) {
        return { enabled: false, label: 'Outil actif' };
    }

    return {
        enabled: true,
        label: typeof actionBadge?.label === 'string' && actionBadge.label.trim()
            ? actionBadge.label.trim()
            : 'Outil actif',
    };
}

function normalizeDrawConfig(draw) {
    if (!draw) {
        return false;
    }

    const config = {
        ...DEFAULT_DRAW_CONFIG,
        ...(draw === true ? {} : draw),
    };

    return {
        ...config,
        actionBadge: normalizeActionBadgeConfig(config.actionBadge),
    };
}

function normalizeMeasureConfig(measure) {
    if (!measure) {
        return false;
    }

    return {
        ...DEFAULT_MEASURE_CONFIG,
        ...(measure === true ? {} : measure),
    };
}

function normalizeDashArray(value) {
    if (Array.isArray(value)) {
        const dash = value.map(number => Number(number)).filter(number => Number.isFinite(number) && number >= 0);

        return dash.length > 0 ? dash : undefined;
    }

    if (typeof value === 'string') {
        const dash = value
            .split(/[,\s]+/)
            .map(number => Number(number))
            .filter(number => Number.isFinite(number) && number >= 0);

        return dash.length > 0 ? dash : undefined;
    }

    return undefined;
}

function normalizeFeatureStyle(geometry, style) {
    if (!style || typeof style !== 'object') {
        return {};
    }

    const styleKey = geometry === 'line' ? 'line' : geometry;
    const aliases = STYLE_ALIASES[styleKey] || STYLE_ALIASES.point;
    const normalized = {};

    Object.entries(style).forEach(([key, value]) => {
        if (value === null || value === undefined || value === '') {
            return;
        }

        const terraKey = aliases[key] || key;

        normalized[terraKey] = terraKey === 'lineStringDash' ? normalizeDashArray(value) : value;
    });

    if (style.markerSvg && !normalized.markerUrl) {
        normalized.markerUrl = svgToDataUrl(String(style.markerSvg));
    }

    if (style.markerUrl || style.iconUrl || style.url) {
        normalized.markerUrl = normalizeIconUrl(String(style.markerUrl || style.iconUrl || style.url));
    }

    if (normalized.markerUrl) {
        normalized.markerWidth = Number(normalized.markerWidth || normalized.width || 28);
        normalized.markerHeight = Number(normalized.markerHeight || normalized.height || 28);
    }

    return Object.fromEntries(
        Object.entries(normalized).filter(([, value]) => value !== undefined && value !== null),
    );
}

function normalizeDrawStyles(styles) {
    if (!styles || typeof styles !== 'object') {
        return {};
    }

    return {
        marker: normalizeFeatureStyle('marker', styles.marker || styles.point || {}),
        point: normalizeFeatureStyle('point', styles.point || {}),
        polyline: normalizeFeatureStyle('line', styles.line || styles.polyline || {}),
        polygon: normalizeFeatureStyle('polygon', styles.polygon || {}),
        rectangle: normalizeFeatureStyle('rectangle', styles.rectangle || styles.polygon || {}),
        select: styles.select && typeof styles.select === 'object' ? styles.select : {},
    };
}

function objectTypeUsesMarkerMode(objectType) {
    return objectType?.geometry === 'point' && Boolean(objectType?.style?.markerUrl);
}

function normalizeObjectTypes(objectTypes) {
    if (!Array.isArray(objectTypes)) {
        return [];
    }

    return objectTypes
        .filter(type => type && typeof type === 'object')
        .map((type, index) => {
            const id = String(type.id || `object-${index + 1}`).trim() || `object-${index + 1}`;
            const rawGeometry = String(type.geometry || 'point').toLowerCase();
            const geometry = ['polyline', 'linestring'].includes(rawGeometry) ? 'line' : rawGeometry;
            const normalizedGeometry = ['point', 'line', 'polygon'].includes(geometry) ? geometry : 'point';
            const style = normalizeFeatureStyle(normalizedGeometry, {
                ...(type.style && typeof type.style === 'object' ? type.style : {}),
                ...(type.markerUrl ? { markerUrl: type.markerUrl } : {}),
                ...(type.markerSvg ? { markerSvg: type.markerSvg } : {}),
                ...(type.markerWidth ? { markerWidth: type.markerWidth } : {}),
                ...(type.markerHeight ? { markerHeight: type.markerHeight } : {}),
            });

            return {
                ...type,
                id,
                label: String(type.label || id),
                geometry: normalizedGeometry,
                icon: type.icon ? String(type.icon) : normalizedGeometry,
                iconHtml: type.iconHtml ? String(type.iconHtml) : null,
                iconSvg: type.iconSvg ? String(type.iconSvg) : null,
                properties: type.properties && typeof type.properties === 'object' ? type.properties : {},
                style,
            };
        });
}

function resolveToolIcon(definition) {
    if (definition.iconHtml) {
        return sanitizeIconMarkup(definition.iconHtml) || TOOL_ICONS.select;
    }

    if (definition.iconSvg) {
        return sanitizeIconMarkup(definition.iconSvg) || TOOL_ICONS.select;
    }

    if (definition.icon && String(definition.icon).trim().startsWith('<svg')) {
        return sanitizeIconMarkup(String(definition.icon)) || TOOL_ICONS.select;
    }

    return TOOL_ICONS[definition.icon] || TOOL_ICONS.select;
}

function modeFromObjectType(objectType) {
    if (objectTypeUsesMarkerMode(objectType)) {
        return OBJECT_GEOMETRY_MARKER_MODE;
    }

    return OBJECT_GEOMETRY_MODES[objectType?.geometry] || 'point';
}

function propertiesFromObjectType(objectType) {
    return {
        ...objectType.properties,
        objectType: objectType.id,
        objectLabel: objectType.label,
        ...(Object.keys(objectType.style || {}).length > 0 ? { style: objectType.style } : {}),
    };
}

export {
    OBJECT_GEOMETRY_MARKER_MODE,
    STYLE_PROPERTY_BY_MODE,
    TOOL_ICONS,
    modeFromObjectType,
    normalizeActionBadgeConfig,
    normalizeDrawConfig,
    normalizeDrawStyles,
    normalizeFeatureStyle,
    normalizeMeasureConfig,
    normalizeObjectTypes,
    objectTypeUsesMarkerMode,
    propertiesFromObjectType,
    resolveToolIcon,
};
