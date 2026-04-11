/**
 * Daisy Leaflet - Minimal Leaflet map integration.
 *
 * Loaded by the DaisyKit module router when a [data-module="leaflet"] element
 * is present in the DOM. Leaflet is dynamically imported so pages without maps
 * never download the library.
 */

/** @type {string} */
const OSM_TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

/** @type {string} */
const OSM_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

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
 * Creates a Leaflet map from a component configuration object.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {HTMLElement} container - The inner map container element.
 * @param {Object} cfg - Parsed JSON configuration from the Blade component.
 * @returns {L.Map}
 */
function createMap(L, container, cfg) {
    const map = L.map(container).setView(
        [cfg.center?.lat ?? 0, cfg.center?.lng ?? 0],
        cfg.zoom ?? 2,
    );

    L.tileLayer(OSM_TILE_URL, { attribution: OSM_ATTRIBUTION }).addTo(map);

    return map;
}

/**
 * Adds simple markers to the map.
 *
 * Accepts both array format [[lat, lng, popup?]] and object format [{lat, lng, popup?}].
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Array} markers
 * @returns {void}
 */
function addMarkers(L, map, markers) {
    if (!Array.isArray(markers) || markers.length === 0) {
        return;
    }

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

        marker.addTo(map);
    }
}

/**
 * Adds a GeoJSON layer to the map.
 *
 * @param {L} L
 * @param {L.Map} map
 * @param {Object|string|null} geojson
 * @returns {void}
 */
function addGeoJson(L, map, geojson) {
    if (!geojson) {
        return;
    }

    const data = typeof geojson === 'string' ? JSON.parse(geojson) : geojson;
    L.geoJSON(data).addTo(map);
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
    } catch (error) {
        console.warn('[DaisyLeaflet] Map creation failed:', error);
        showError(root);
        return null;
    }

    try {
        addMarkers(L, map, cfg.markers);
    } catch (error) {
        console.warn('[DaisyLeaflet] Markers failed:', error);
    }

    try {
        addGeoJson(L, map, cfg.geojson);
    } catch (error) {
        console.warn('[DaisyLeaflet] GeoJSON failed:', error);
    }

    // Ensure tiles render correctly when the container becomes visible.
    requestAnimationFrame(() => map.invalidateSize({ animate: false }));
    window.setTimeout(() => map.invalidateSize({ animate: false }), 200);

    hideLoading(root);

    root.dispatchEvent(new CustomEvent('daisy:leaflet:init', {
        detail: { map, config: cfg },
    }));

    return map;
}

export default init;
