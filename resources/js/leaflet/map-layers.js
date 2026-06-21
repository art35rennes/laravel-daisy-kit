import { parseGeoJson } from './core.js';

const TILE_PROVIDERS = {
    osm: {
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        options: {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        },
    },
    'cartodb.positron': {
        url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        options: {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
        },
    },
    'cartodb.darkmatter': {
        url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
        options: {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
        },
    },
    'cartodb.voyager': {
        url: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
        options: {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
        },
    },
    'stamen.toner': {
        url: 'https://tiles.stadiamaps.com/tiles/stamen_toner/{z}/{x}/{y}{r}.png',
        options: {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://stamen.com/">Stamen Design</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 20,
        },
    },
    'stamen.watercolor': {
        url: 'https://tiles.stadiamaps.com/tiles/stamen_watercolor/{z}/{x}/{y}.jpg',
        options: {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://stamen.com/">Stamen Design</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 16,
        },
    },
};

function createLegacyBasemapDefinition(cfg) {
    if (!cfg.tiles) {
        return null;
    }

    if (cfg.tileUrl) {
        return {
            id: 'default',
            label: 'Fond de carte',
            type: 'xyz',
            url: cfg.tileUrl,
            options: cfg.tileOptions || {},
            active: true,
        };
    }

    const providerKey = (cfg.provider || 'osm').toLowerCase();
    const provider = TILE_PROVIDERS[providerKey] || TILE_PROVIDERS.osm;

    return {
        id: providerKey,
        label: cfg.provider || 'OpenStreetMap',
        type: 'xyz',
        url: provider.url,
        options: { ...provider.options, ...(cfg.tileOptions || {}) },
        active: true,
    };
}

function createRasterLayer(L, layer) {
    if (!layer || !layer.url) {
        return null;
    }

    if (layer.type === 'wms') {
        return L.tileLayer.wms(layer.url, layer.options || {});
    }

    return L.tileLayer(layer.url, layer.options || {});
}

function isLayerDefinition(value) {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

function getLayerLabel(layer, fallback) {
    return String(layer.label || layer.id || fallback);
}

function addBasemaps(L, map, cfg) {
    const definitions = (Array.isArray(cfg.basemaps) && cfg.basemaps.length > 0
        ? cfg.basemaps
        : [createLegacyBasemapDefinition(cfg)])
        .filter(isLayerDefinition);

    const baseLayers = {};
    let activeLayer = null;

    definitions.forEach((definition, index) => {
        const layer = createRasterLayer(L, definition);

        if (!layer) {
            return;
        }

        const label = getLayerLabel(definition, `Fond ${index + 1}`);
        baseLayers[label] = layer;

        if (!activeLayer || definition.active) {
            activeLayer = layer;
        }
    });

    if (activeLayer) {
        activeLayer.addTo(map);
    }

    return { baseLayers, activeLayer };
}

async function loadGeoJsonData(overlay) {
    const inlineData = parseGeoJson(overlay.data || overlay.geojson);

    if (inlineData) {
        return inlineData;
    }

    if (!overlay.url) {
        return null;
    }

    try {
        const response = await fetch(overlay.url, { headers: { Accept: 'application/geo+json, application/json' } });

        if (!response.ok) {
            return null;
        }

        return parseGeoJson(await response.text());
    } catch {
        return null;
    }
}

async function createOverlayLayer(L, overlay) {
    if (!overlay) {
        return null;
    }

    if (overlay.type === 'geojson') {
        const data = await loadGeoJsonData(overlay);

        return data ? L.geoJSON(data, overlay.style ? { style: overlay.style } : {}) : null;
    }

    return createRasterLayer(L, overlay);
}

function normalizeLayerControlConfig(layerControl) {
    if (!layerControl) {
        return false;
    }

    const config = layerControl === true ? {} : layerControl;

    return {
        enabled: config.enabled !== false,
        mode: config.mode === 'single' ? 'single' : 'multiple',
        lockedOverlays: Array.isArray(config.lockedOverlays) ? config.lockedOverlays.map(String) : [],
        native: config.native === true,
        position: config.position || 'topright',
    };
}

function isOverlayLocked(overlay, label, layerControlConfig) {
    const lockedNames = layerControlConfig?.lockedOverlays || [];

    return overlay.control === false
        || overlay.controllable === false
        || overlay.locked === true
        || lockedNames.includes(String(overlay.id || ''))
        || lockedNames.includes(label);
}

async function addOverlays(L, map, cfg) {
    const overlayLayers = {};
    const lockedOverlayLayers = {};
    const renderedLayers = [];
    const editableCollections = [];

    if (!Array.isArray(cfg.overlays)) {
        return { overlayLayers, renderedLayers, editableCollections, lockedOverlayLayers };
    }

    for (const [index, overlay] of cfg.overlays.entries()) {
        if (!isLayerDefinition(overlay)) {
            continue;
        }

        if (overlay.type === 'geojson' && overlay.editable && cfg.draw) {
            const data = await loadGeoJsonData(overlay);

            if (data) {
                editableCollections.push(data);
            }

            continue;
        }

        const layer = await createOverlayLayer(L, overlay);

        if (!layer) {
            continue;
        }

        const label = getLayerLabel(overlay, `Couche ${index + 1}`);
        renderedLayers.push(layer);

        if (isOverlayLocked(overlay, label, cfg.layerControl)) {
            lockedOverlayLayers[label] = layer;
            layer.addTo(map);
            continue;
        }

        overlayLayers[label] = layer;

        if (overlay.visible !== false) {
            layer.addTo(map);
        }
    }

    return { overlayLayers, renderedLayers, editableCollections, lockedOverlayLayers };
}

function shouldUseNativeLayerControl(cfg) {
    return Boolean(cfg.layerControl?.enabled && cfg.layerControl.native);
}

function addLayerControl(L, map, cfg, baseLayers, overlayLayers) {
    if (!shouldUseNativeLayerControl(cfg)) {
        return null;
    }

    if (Object.keys(baseLayers).length === 0 && Object.keys(overlayLayers).length === 0) {
        return null;
    }

    return L.control.layers(baseLayers, overlayLayers, { collapsed: false }).addTo(map);
}

export {
    TILE_PROVIDERS,
    addBasemaps,
    addLayerControl,
    addOverlays,
    createLegacyBasemapDefinition,
    createOverlayLayer,
    createRasterLayer,
    getLayerLabel,
    isLayerDefinition,
    loadGeoJsonData,
    normalizeLayerControlConfig,
    shouldUseNativeLayerControl,
};
