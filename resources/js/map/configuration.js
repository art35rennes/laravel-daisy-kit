const defaultCenter = [48.1173, -1.6778];

const providers = {
    'cartodb.darkmatter': {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
    },
    'cartodb.positron': {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    },
    'cartodb.voyager': {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        url: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
    },
    osm: {
        attribution: '&copy; OpenStreetMap contributors',
        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    },
};

function finiteNumber(value, fallback) {
    const number = Number(value);

    return Number.isFinite(number) ? number : fallback;
}

function center(value) {
    if (!Array.isArray(value) || value.length !== 2) return defaultCenter;

    const normalized = value.map(Number);

    return normalized.every(Number.isFinite) ? normalized : defaultCenter;
}

function collection(value) {
    return value?.type === 'FeatureCollection' && Array.isArray(value.features)
        ? value
        : { features: [], type: 'FeatureCollection' };
}

function featureConfiguration(value) {
    if (!value) return false;

    return value === true ? { enabled: true } : { ...value, enabled: value.enabled !== false };
}

export function normalizeConfiguration(value = {}) {
    const provider = typeof value.provider === 'string' ? providers[value.provider] ?? null : null;
    const controls = value.controls === false
        ? { enabled: false }
        : { enabled: true, ...(value.controls && typeof value.controls === 'object' ? value.controls : {}) };

    return {
        ...value,
        basemaps: Array.isArray(value.basemaps) ? value.basemaps : [],
        center: center(value.center),
        cluster: featureConfiguration(value.cluster),
        controls,
        drawing: featureConfiguration(value.drawing),
        fitBounds: value.fitBounds !== false,
        geojson: value.geojson?.type ? value.geojson : null,
        geolocation: featureConfiguration(value.geolocation),
        gestureHandling: value.gestureHandling === true,
        labels: value.labels && typeof value.labels === 'object' ? value.labels : {},
        layers: Array.isArray(value.layers) ? value.layers : [],
        markers: Array.isArray(value.markers) ? value.markers : [],
        maxZoom: value.maxZoom === null ? undefined : finiteNumber(value.maxZoom, undefined),
        measure: featureConfiguration(value.measure),
        minZoom: value.minZoom === null ? undefined : finiteNumber(value.minZoom, undefined),
        persistState: value.persistState?.enabled === true ? value.persistState : { enabled: false, key: null },
        preferCanvas: value.preferCanvas === true,
        provider,
        scale: value.scale === true,
        spatialSelection: featureConfiguration(value.spatialSelection),
        tileAttribution: typeof value.tileAttribution === 'string' ? value.tileAttribution : '',
        tileOptions: value.tileOptions && typeof value.tileOptions === 'object' ? value.tileOptions : {},
        tileUrl: typeof value.tileUrl === 'string' ? value.tileUrl : null,
        value: collection(value.value),
        zoom: finiteNumber(value.zoom, 12),
    };
}

export function escapeHtml(value) {
    const element = document.createElement('span');
    element.textContent = String(value ?? '');

    return element.innerHTML;
}

export function emptyCollection() {
    return { features: [], type: 'FeatureCollection' };
}
