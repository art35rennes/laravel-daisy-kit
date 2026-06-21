import {
    OBJECT_GEOMETRY_MARKER_MODE,
    objectTypeUsesMarkerMode,
} from './config.js';

const INTERNAL_PROPERTIES = [
    'coordinatePointFeatureId',
    'coordinatePointIds',
    'selectionPointFeatureId',
    'selected',
];

const TECHNICAL_FEATURE_PROPERTIES = [
    'closingPoint',
    'coordinatePoint',
    'currentlyDrawing',
    'midPoint',
    'selectionPoint',
    'snappingPoint',
];

function createFeatureId() {
    if (globalThis.crypto?.randomUUID) {
        return globalThis.crypto.randomUUID();
    }

    return `daisy-${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

function parseCollection(collection) {
    if (!collection) {
        return null;
    }

    if (typeof collection === 'string') {
        try {
            return JSON.parse(collection);
        } catch {
            return null;
        }
    }

    return collection;
}

function featuresFromCollection(collection) {
    const parsed = parseCollection(collection);

    if (!parsed) {
        return [];
    }

    if (parsed.type === 'FeatureCollection' && Array.isArray(parsed.features)) {
        return parsed.features;
    }

    if (parsed.type === 'Feature') {
        return [parsed];
    }

    return [];
}

function modeFromFeature(feature, objectTypes = []) {
    if (!feature || !feature.geometry) {
        return null;
    }

    if (feature.properties?.mode) {
        return ['line', 'linestring'].includes(feature.properties.mode) ? 'polyline' : feature.properties.mode;
    }

    if (feature.geometry.type === 'Point') {
        const objectType = objectTypes.find(type => type.id === feature.properties?.objectType);

        if (objectTypeUsesMarkerMode(objectType)) {
            return OBJECT_GEOMETRY_MARKER_MODE;
        }

        return 'point';
    }

    if (feature.geometry.type === 'LineString') {
        return 'polyline';
    }

    if (feature.geometry.type === 'Polygon') {
        return 'polygon';
    }

    return null;
}

function isPosition(coordinates) {
    return Array.isArray(coordinates)
        && coordinates.length >= 2
        && Number.isFinite(Number(coordinates[0]))
        && Number.isFinite(Number(coordinates[1]));
}

function hasValidGeometryCoordinates(geometry) {
    if (!geometry) {
        return false;
    }

    if (geometry.type === 'Point') {
        return isPosition(geometry.coordinates);
    }

    if (geometry.type === 'LineString') {
        return Array.isArray(geometry.coordinates)
            && geometry.coordinates.length >= 2
            && geometry.coordinates.every(isPosition);
    }

    if (geometry.type === 'Polygon') {
        return Array.isArray(geometry.coordinates)
            && geometry.coordinates.some(ring => (
                Array.isArray(ring)
                && ring.length >= 4
                && ring.every(isPosition)
            ));
    }

    return false;
}

function isPersistableFeature(feature) {
    if (!feature || feature.type !== 'Feature' || !feature.geometry) {
        return false;
    }

    if (!['Point', 'LineString', 'Polygon'].includes(feature.geometry.type)) {
        return false;
    }

    return hasValidGeometryCoordinates(feature.geometry)
        && !TECHNICAL_FEATURE_PROPERTIES.some(property => feature.properties?.[property]);
}

function prepareFeature(feature, objectTypes = []) {
    if (!isPersistableFeature(feature)) {
        return null;
    }

    const mode = modeFromFeature(feature, objectTypes);

    if (!mode) {
        return null;
    }

    return {
        ...feature,
        id: feature.id || createFeatureId(),
        properties: {
            ...Object.fromEntries(
                Object.entries(feature.properties || {}).filter(([key]) => !INTERNAL_PROPERTIES.includes(key)),
            ),
            mode,
        },
    };
}

function toFeatureCollection(features) {
    return {
        type: 'FeatureCollection',
        features: features
            .filter(isPersistableFeature)
            .map(feature => ({
                ...feature,
                properties: Object.fromEntries(
                    Object.entries(feature.properties || {}).filter(([key]) => !INTERNAL_PROPERTIES.includes(key)),
                ),
            })),
    };
}

function collectInitialFeatures(cfg, context, objectTypes = []) {
    const features = [
        ...featuresFromCollection(cfg.value),
        ...(context.editableCollections || []).flatMap(collection => featuresFromCollection(collection)),
    ];

    return features.map(feature => prepareFeature(feature, objectTypes)).filter(Boolean);
}

export {
    collectInitialFeatures,
    createFeatureId,
    featuresFromCollection,
    hasValidGeometryCoordinates,
    isPersistableFeature,
    isPosition,
    modeFromFeature,
    parseCollection,
    prepareFeature,
    toFeatureCollection,
};
