/**
 * Core Leaflet map primitives shared by the component entry point and tests.
 */

/**
 * Creates a Leaflet map instance with options from the Blade component config.
 *
 * @param {Object} L - The Leaflet namespace.
 * @param {HTMLElement} container - The inner map container element.
 * @param {Object} cfg - Parsed JSON configuration from the Blade component.
 * @returns {Object}
 */
function createMap(L, container, cfg) {
    const mapOptions = {};

    if (cfg.minZoom != null) {
        mapOptions.minZoom = cfg.minZoom;
    }
    if (cfg.maxZoom != null) {
        mapOptions.maxZoom = cfg.maxZoom;
    } else if (cfg.cluster) {
        mapOptions.maxZoom = 18;
    }
    if (cfg.preferCanvas) {
        mapOptions.preferCanvas = true;
    }

    return L.map(container, mapOptions).setView(
        [cfg.center?.lat ?? 0, cfg.center?.lng ?? 0],
        cfg.zoom ?? 2,
    );
}

/**
 * Parses GeoJSON-like input from Blade props.
 *
 * @param {Object|string|null} geojson
 * @returns {Object|null}
 */
function parseGeoJson(geojson) {
    if (!geojson) {
        return null;
    }

    try {
        return typeof geojson === 'string' ? JSON.parse(geojson) : geojson;
    } catch {
        return null;
    }
}

/**
 * Creates Leaflet markers from the config without adding them to the map.
 *
 * @param {Object} L
 * @param {Array} markers
 * @returns {Object[]}
 */
function createMarkers(L, markers) {
    if (!Array.isArray(markers) || markers.length === 0) {
        return [];
    }

    const result = [];

    for (const m of markers) {
        let lat, lng, popup;

        if (Array.isArray(m)) {
            [lat, lng, popup] = m;
        } else {
            ({ lat, lng, popup } = m);
        }

        if (typeof lat !== 'number' || typeof lng !== 'number') {
            continue;
        }

        const marker = L.marker([lat, lng]);

        if (popup) {
            marker.bindPopup(String(popup));
        }

        result.push(marker);
    }

    return result;
}

/**
 * @param {Object} map
 * @param {Object[]} markers
 * @returns {void}
 */
function addMarkersToMap(map, markers) {
    for (const marker of markers) {
        marker.addTo(map);
    }
}

/**
 * @param {Object} L
 * @param {Object} map
 * @param {Object|string|null} geojson
 * @returns {Object|null}
 */
function addGeoJson(L, map, geojson) {
    if (!geojson) {
        return null;
    }

    const data = parseGeoJson(geojson);
    const layer = L.geoJSON(data);
    layer.addTo(map);

    return layer;
}

/**
 * @param {Object} L
 * @param {Object} map
 * @param {Object} cfg
 * @param {Object[]} markers
 * @param {Object[]} layers
 * @param {Object[]} geojsonCollections
 * @returns {void}
 */
function applyFitBounds(L, map, cfg, markers, layers = [], geojsonCollections = []) {
    if (!cfg.fitBounds) {
        return;
    }

    const bounds = L.latLngBounds([]);

    for (const marker of markers) {
        bounds.extend(marker.getLatLng());
    }

    for (const layer of layers) {
        if (typeof layer.getBounds === 'function') {
            const layerBounds = layer.getBounds();

            if (layerBounds.isValid()) {
                bounds.extend(layerBounds);
            }
        }
    }

    for (const collection of geojsonCollections) {
        try {
            const layer = L.geoJSON(collection);
            const layerBounds = layer.getBounds();

            if (layerBounds.isValid()) {
                bounds.extend(layerBounds);
            }
        } catch {
            // Invalid GeoJSON should not prevent the map from rendering.
        }
    }

    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
}

export {
    addGeoJson,
    addMarkersToMap,
    applyFitBounds,
    createMap,
    createMarkers,
    parseGeoJson,
};
