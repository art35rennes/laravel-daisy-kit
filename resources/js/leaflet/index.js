/**
 * Daisy Leaflet - Leaflet map integration with plugin architecture.
 *
 * Loaded by the DaisyKit module router when a [data-module="leaflet"] element
 * is present in the DOM. Leaflet and its plugins are dynamically imported so
 * pages without maps never download the library.
 *
 * @module leaflet
 */

// ============================================================================
// Tile providers (built-in lookup, zero external dependency)
// ============================================================================

/**
 * Built-in tile provider definitions.
 *
 * Each entry provides a URL template and default options (attribution, subdomains).
 * This replaces the heavy `leaflet-providers` package (~460 providers) with only
 * the most commonly used ones. For exotic providers, use the `tileUrl` prop.
 *
 * @type {Object<string, {url: string, options: Object}>}
 */
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

// ============================================================================
// Plugin registry
// ============================================================================

/**
 * Lazy-loaded plugin registry.
 *
 * Each key corresponds to a boolean config flag from the Blade component.
 * The loader function is only called when the config flag is truthy,
 * keeping the bundle cost at zero for unused plugins.
 *
 * @type {Object<string, () => Promise<{apply: Function}>>}
 */
const PLUGINS = {
    scale: () => import('./plugins/scale.js'),
    gestureHandling: () => import('./plugins/gesture-handling.js'),
    fullscreen: () => import('./plugins/fullscreen.js'),
    cluster: () => import('./plugins/cluster.js'),
};

// ============================================================================
// DOM helpers
// ============================================================================

/**
 * Removes the loading spinner from the component root.
 *
 * @param {Element} root
 * @returns {void}
 */
function hideLoading(root) {
    const el = root.querySelector('.daisy-leaflet-loading');
    if (el) {
        el.remove();
    }
}

/**
 * Shows the error overlay and hides the loading spinner.
 *
 * @param {Element} root
 * @returns {void}
 */
function showError(root) {
    hideLoading(root);
    const el = root.querySelector('.daisy-leaflet-error');
    if (el) {
        el.classList.remove('hidden');
    }
}

/**
 * Reads the JSON configuration embedded in the component root.
 *
 * @param {Element} root
 * @returns {Object|null}
 */
function readConfig(root) {
    const script = root.querySelector('script[data-config]');
    if (!script) {
        return null;
    }
    try {
        return JSON.parse(script.textContent || '{}');
    } catch {
        return null;
    }
}

// ============================================================================
// Leaflet bootstrap
// ============================================================================

/**
 * Dynamically imports Leaflet and its CSS, caching the result on window.L.
 *
 * Vite breaks Leaflet's automatic icon-path detection, so we explicitly
 * import the marker images and reconfigure L.Icon.Default.
 *
 * @returns {Promise<L>}
 */
async function loadLeaflet() {
    if (window.L) {
        return window.L;
    }

    const [mod, markerIcon, markerIcon2x, markerShadow] = await Promise.all([
        import('leaflet'),
        import('leaflet/dist/images/marker-icon.png'),
        import('leaflet/dist/images/marker-icon-2x.png'),
        import('leaflet/dist/images/marker-shadow.png'),
    ]);

    const L = mod.default || mod;
    window.L = L;

    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconUrl: markerIcon.default,
        iconRetinaUrl: markerIcon2x.default,
        shadowUrl: markerShadow.default,
    });

    try {
        await import('leaflet/dist/leaflet.css');
    } catch {
        // CSS may be bundled separately.
    }

    return L;
}

// ============================================================================
// Map creation
// ============================================================================

/**
 * Creates a Leaflet map instance with options from the Blade component config.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {HTMLElement} container - The inner map container element.
 * @param {Object} cfg - Parsed JSON configuration from the Blade component.
 * @returns {L.Map}
 */
function createMap(L, container, cfg) {
    const mapOptions = {};

    if (cfg.minZoom != null) {
        mapOptions.minZoom = cfg.minZoom;
    }
    if (cfg.maxZoom != null) {
        mapOptions.maxZoom = cfg.maxZoom;
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
 * Adds a tile layer to the map using the built-in provider lookup or a custom URL.
 *
 * Priority: cfg.tileUrl (explicit URL) > cfg.provider (named lookup) > OSM fallback.
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Object} cfg
 * @returns {void}
 */
function addTileLayer(L, map, cfg) {
    if (cfg.tileUrl) {
        L.tileLayer(cfg.tileUrl, cfg.tileOptions || {}).addTo(map);
        return;
    }

    const providerKey = (cfg.provider || 'osm').toLowerCase();
    const provider = TILE_PROVIDERS[providerKey] || TILE_PROVIDERS.osm;

    L.tileLayer(provider.url, { ...provider.options, ...(cfg.tileOptions || {}) }).addTo(map);
}

// ============================================================================
// Data layers
// ============================================================================

/**
 * Creates Leaflet markers from the config without adding them to the map.
 *
 * Returns the array of L.Marker instances so the cluster plugin can
 * intercept them if clustering is enabled.
 *
 * Accepts both array format [[lat, lng, popup?]] and object format [{lat, lng, popup?}].
 *
 * @param {L} L
 * @param {Array} markers - Raw marker definitions from Blade config.
 * @returns {L.Marker[]}
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
 * Adds markers directly to the map (used when clustering is disabled).
 *
 * @param {L.Map} map
 * @param {L.Marker[]} markers
 * @returns {void}
 */
function addMarkersToMap(map, markers) {
    for (const marker of markers) {
        marker.addTo(map);
    }
}

/**
 * Adds a GeoJSON layer to the map.
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Object|string|null} geojson
 * @returns {L.GeoJSON|null}
 */
function addGeoJson(L, map, geojson) {
    if (!geojson) {
        return null;
    }

    const data = typeof geojson === 'string' ? JSON.parse(geojson) : geojson;
    const layer = L.geoJSON(data);
    layer.addTo(map);

    return layer;
}

// ============================================================================
// Fit bounds
// ============================================================================

/**
 * Auto-fits the map viewport to encompass all markers and GeoJSON features.
 *
 * Only applies when cfg.fitBounds is truthy and there is content to fit.
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Object} cfg
 * @param {L.Marker[]} markers
 * @param {L.GeoJSON|null} geojsonLayer
 * @returns {void}
 */
function applyFitBounds(L, map, cfg, markers, geojsonLayer) {
    if (!cfg.fitBounds) {
        return;
    }

    const bounds = L.latLngBounds([]);

    for (const marker of markers) {
        bounds.extend(marker.getLatLng());
    }

    if (geojsonLayer) {
        const geoBounds = geojsonLayer.getBounds();
        if (geoBounds.isValid()) {
            bounds.extend(geoBounds);
        }
    }

    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
}

// ============================================================================
// Plugin loader
// ============================================================================

/**
 * Loads and applies all plugins whose config flags are truthy.
 *
 * Plugins are loaded in parallel for performance. Each plugin module must
 * export an `apply(L, map, cfg, context)` function.
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Object} cfg
 * @param {Object} context - Shared context (e.g. markers array for cluster plugin).
 * @returns {Promise<void>}
 */
async function applyPlugins(L, map, cfg, context) {
    const tasks = [];

    for (const [key, loader] of Object.entries(PLUGINS)) {
        if (cfg[key]) {
            tasks.push(
                loader()
                    .then(mod => mod.apply(L, map, cfg, context))
                    .catch(error => {
                        console.warn(`[DaisyLeaflet] Plugin "${key}" failed:`, error);
                    }),
            );
        }
    }

    await Promise.all(tasks);
}

// ============================================================================
// Module entry point
// ============================================================================

/**
 * Initializes a single Leaflet map from a [data-module="leaflet"] root element.
 *
 * Called by the DaisyKit module router (kit/index.js).
 *
 * @param {Element} root - The component root element.
 * @returns {Promise<L.Map|null>}
 */
async function init(root) {
    if (!(root instanceof Element)) {
        return null;
    }

    if (root.dataset.leafletReady === '1') {
        return null;
    }
    root.dataset.leafletReady = '1';

    const cfg = readConfig(root);
    if (!cfg) {
        showError(root);
        return null;
    }

    let L;
    try {
        L = await loadLeaflet();
    } catch (error) {
        console.warn('[DaisyLeaflet] Failed to load Leaflet:', error);
        showError(root);
        return null;
    }

    const container = document.getElementById(cfg.containerId);
    if (!container) {
        console.warn('[DaisyLeaflet] Container not found:', cfg.containerId);
        showError(root);
        return null;
    }

    let map;
    try {
        map = createMap(L, container, cfg);
        addTileLayer(L, map, cfg);
    } catch (error) {
        console.warn('[DaisyLeaflet] Map creation failed:', error);
        showError(root);
        return null;
    }

    let markers = [];
    try {
        markers = createMarkers(L, cfg.markers);
    } catch (error) {
        console.warn('[DaisyLeaflet] Markers failed:', error);
    }

    let geojsonLayer = null;
    try {
        geojsonLayer = addGeoJson(L, map, cfg.geojson);
    } catch (error) {
        console.warn('[DaisyLeaflet] GeoJSON failed:', error);
    }

    // Shared context passed to plugins (cluster needs the markers array).
    const context = { markers, geojsonLayer };

    try {
        await applyPlugins(L, map, cfg, context);
    } catch (error) {
        console.warn('[DaisyLeaflet] Plugins failed:', error);
    }

    // When clustering is not active, add markers directly to the map.
    if (!cfg.cluster) {
        addMarkersToMap(map, markers);
    }

    applyFitBounds(L, map, cfg, markers, geojsonLayer);

    requestAnimationFrame(() => map.invalidateSize({ animate: false }));
    window.setTimeout(() => map.invalidateSize({ animate: false }), 200);

    hideLoading(root);

    root.dispatchEvent(new CustomEvent('daisy:leaflet:init', {
        detail: { map, config: cfg },
    }));

    return map;
}

export default init;

export { TILE_PROVIDERS, readConfig };
