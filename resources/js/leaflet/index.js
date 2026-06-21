/**
 * Daisy Leaflet - Leaflet map integration with plugin architecture.
 *
 * Loaded by the DaisyKit module router when a [data-module="leaflet"] element
 * is present in the DOM. Leaflet and its plugins are dynamically imported so
 * pages without maps never download the library.
 *
 * @module leaflet
 */

import { createLeafletApi } from './api.js';
import {
    addGeoJson,
    addMarkersToMap,
    applyFitBounds,
    createMap,
    createMarkers,
    parseGeoJson,
} from './core.js';
import {
    TILE_PROVIDERS,
    addBasemaps,
    addLayerControl,
    addOverlays,
    createLegacyBasemapDefinition,
    createOverlayLayer,
    createRasterLayer,
    loadGeoJsonData,
    normalizeLayerControlConfig,
    shouldUseNativeLayerControl,
} from './map-layers.js';
import {
    addUserControls,
    applyControlsState,
    createControlChoice,
    createControlsSection,
    createDefaultControlsState,
    createIconButton,
    getControlsPositionClasses,
    getOrCreateControlStack,
    getControlsStorageKey,
    loadControlsState,
    normalizeControlsConfig,
    persistControlsState,
} from './controls.js';
import {
    addLayer,
    bindLayerToggleEvents,
    collectActiveLayerNames,
    dispatchLayerToggle,
    mapHasLayer,
    removeLayer,
} from './layers.js';

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
    geolocation: () => import('./plugins/geolocation.js'),
    cluster: () => import('./plugins/cluster.js'),
    draw: () => import('./plugins/draw.js'),
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
// User controls menu
// ============================================================================

/**
 * @param {Object} map
 * @param {Object<string, Object>} overlayLayers
 * @param {string} selectedName
 * @returns {void}
 */
function activateSingleOverlay(map, overlayLayers, selectedName) {
    Object.entries(overlayLayers).forEach(([name, layer]) => {
        if (name === selectedName) {
            addLayer(map, layer);
        } else {
            removeLayer(map, layer);
        }
    });
}

function addLayerMenuControl(L, map, cfg, context) {
    const layerConfig = context.layerControlConfig;

    if (!layerConfig?.enabled || layerConfig.native) {
        return null;
    }

    const hasBasemaps = Object.keys(context.baseLayers || {}).length > 0;
    const hasOverlays = Object.keys(context.overlayLayers || {}).length > 0;

    if (!hasBasemaps && !hasOverlays) {
        return null;
    }

    const root = context.root;
    const storageKey = context.controlsStorageKey;
    const state = context.controlsState || createDefaultControlsState();
    const stack = getOrCreateControlStack(root, layerConfig.position);
    const wrapper = document.createElement('div');
    const panel = document.createElement('div');

    wrapper.className = [
        'daisy-leaflet-layer-controls',
        'relative',
        'flex',
        'flex-col',
        'gap-2',
        'items-end',
    ].join(' ');

    const trigger = createIconButton(wrapper, 'layers', 'Couches');
    trigger.classList.add('bg-base-100', 'shadow');
    trigger.setAttribute('aria-expanded', 'false');

    panel.className = [
        'hidden',
        'w-64',
        'max-w-[calc(100vw-2rem)]',
        'space-y-3',
        'rounded-box',
        'bg-base-100',
        'p-3',
        'text-base-content',
        'shadow-lg',
        'ring-1',
        'ring-base-300',
    ].join(' ');

    const title = document.createElement('div');
    title.className = 'text-sm font-semibold';
    title.textContent = 'Couches';
    panel.appendChild(title);

    if (layerConfig.mode === 'single' && hasOverlays) {
        const overlayEntries = Object.entries(context.overlayLayers);
        const storedOverlay = Object.entries(state.overlays || {}).find(([name, visible]) => visible && context.overlayLayers[name])?.[0];
        const visibleOverlay = overlayEntries.find(([, layer]) => mapHasLayer(map, layer))?.[0];
        const activeOverlay = storedOverlay || visibleOverlay || overlayEntries[0]?.[0];

        if (activeOverlay) {
            activateSingleOverlay(map, context.overlayLayers, activeOverlay);
            state.overlays = Object.fromEntries(overlayEntries.map(([name]) => [name, name === activeOverlay]));
        }
    }

    trigger.addEventListener('click', () => {
        const open = panel.classList.toggle('hidden') === false;
        trigger.setAttribute('aria-expanded', String(open));
    });

    const updateState = (patch) => {
        Object.assign(state, patch);
        persistControlsState(storageKey, state);
        applyControlsState(root, map, cfg, context, state);
    };

    if (hasBasemaps) {
        const section = createControlsSection(panel, 'Fonds');
        const radioName = `daisy-leaflet-basemap-${cfg.containerId}`;

        Object.entries(context.baseLayers).forEach(([name, layer]) => {
            createControlChoice(section, {
                type: 'radio',
                name: radioName,
                value: name,
                label: name,
                checked: state.basemap ? state.basemap === name : mapHasLayer(map, layer),
                onChange(input) {
                    if (input.checked) {
                        updateState({ basemap: name });
                        dispatchLayerToggle(root, map, name, 'baselayerchange', layer, context);
                    }
                },
            });
        });
    }

    if (hasOverlays) {
        const section = createControlsSection(panel, layerConfig.mode === 'single' ? 'Couche active' : 'Couches');
        const radioName = `daisy-leaflet-overlay-${cfg.containerId}`;

        Object.entries(context.overlayLayers).forEach(([name, layer]) => {
            const checked = Object.prototype.hasOwnProperty.call(state.overlays || {}, name)
                ? state.overlays[name]
                : mapHasLayer(map, layer);

            createControlChoice(section, {
                type: layerConfig.mode === 'single' ? 'radio' : 'checkbox',
                name: layerConfig.mode === 'single' ? radioName : '',
                value: name,
                label: name,
                checked,
                onChange(input) {
                    if (layerConfig.mode === 'single') {
                        if (!input.checked) {
                            return;
                        }

                        const previouslyActive = collectActiveLayerNames(map, context.overlayLayers).filter(layerName => layerName !== name);
                        const overlays = Object.fromEntries(Object.keys(context.overlayLayers).map(layerName => [layerName, layerName === name]));
                        activateSingleOverlay(map, context.overlayLayers, name);
                        updateState({ overlays });
                        previouslyActive.forEach(layerName => {
                            dispatchLayerToggle(root, map, layerName, 'overlayremove', context.overlayLayers[layerName], context);
                        });
                        dispatchLayerToggle(root, map, name, 'overlayadd', layer, context);

                        return;
                    }

                    updateState({
                        overlays: {
                            ...(state.overlays || {}),
                            [name]: input.checked,
                        },
                    });
                    dispatchLayerToggle(root, map, name, input.checked ? 'overlayadd' : 'overlayremove', layer, context);
                },
            });
        });
    }

    if (Object.keys(context.lockedOverlayLayers || {}).length > 0) {
        const section = createControlsSection(panel, 'Couches imposées');

        Object.keys(context.lockedOverlayLayers).forEach(name => {
            const row = document.createElement('div');

            row.className = 'flex min-h-7 items-center gap-2 rounded px-1 text-xs text-base-content/70';
            row.textContent = name;
            section.appendChild(row);
        });
    }

    wrapper.appendChild(panel);
    stack.appendChild(wrapper);

    return wrapper;
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

    if (root.__daisyLeafletInitializing || root.daisyLeaflet) {
        return null;
    }
    root.__daisyLeafletInitializing = true;

    const cfg = readConfig(root);
    if (!cfg) {
        showError(root);
        delete root.__daisyLeafletInitializing;
        return null;
    }

    let L;
    try {
        L = await loadLeaflet();
    } catch (error) {
        console.warn('[DaisyLeaflet] Failed to load Leaflet:', error);
        showError(root);
        delete root.__daisyLeafletInitializing;
        return null;
    }

    const container = document.getElementById(cfg.containerId);
    if (!container) {
        console.warn('[DaisyLeaflet] Container not found:', cfg.containerId);
        showError(root);
        delete root.__daisyLeafletInitializing;
        return null;
    }

    let map;
    let baseLayers = {};
    let overlayLayers = {};
    let lockedOverlayLayers = {};
    let renderedOverlayLayers = [];
    let editableCollections = [];
    let layerControl = null;
    const layerControlConfig = normalizeLayerControlConfig(cfg.layerControl);
    cfg.layerControl = layerControlConfig;

    try {
        map = createMap(L, container, cfg);
        ({ baseLayers } = addBasemaps(L, map, cfg));
        ({
            overlayLayers,
            lockedOverlayLayers,
            renderedLayers: renderedOverlayLayers,
            editableCollections,
        } = await addOverlays(L, map, cfg));
        layerControl = addLayerControl(L, map, cfg, baseLayers, overlayLayers);

        if (layerControl) {
            bindLayerToggleEvents(root, map);
        }
    } catch (error) {
        console.warn('[DaisyLeaflet] Map creation failed:', error);
        showError(root);
        delete root.__daisyLeafletInitializing;
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
    const controlsConfig = normalizeControlsConfig(cfg.controls);
    const controlsStorageKey = getControlsStorageKey(root, cfg, controlsConfig);
    const controlsState = loadControlsState(controlsStorageKey);

    cfg.controls = controlsConfig;
    cfg.controlsState = controlsState;

    const context = {
        root,
        container,
        markers,
        baseLayers,
        geojsonLayer,
        overlayLayers,
        lockedOverlayLayers,
        renderedOverlayLayers,
        editableCollections,
        layerControl,
        layerControlConfig,
        controlsConfig,
        controlsStorageKey,
        controlsState,
        cleanups: [],
    };

    try {
        await applyPlugins(L, map, cfg, context);
        context.layerMenuControl = addLayerMenuControl(L, map, cfg, context);
        if (context.layerMenuControl) {
            context.cleanups.push(() => context.layerMenuControl?.remove());
        }

        context.userControls = addUserControls(L, map, cfg, context);
        if (context.userControls) {
            context.cleanups.push(() => context.userControls?.remove());
        }
    } catch (error) {
        console.warn('[DaisyLeaflet] Plugins failed:', error);
    }

    // When clustering is not active, add markers directly to the map.
    if (!cfg.cluster) {
        addMarkersToMap(map, markers);
    }

    const fitLayers = [geojsonLayer, ...renderedOverlayLayers].filter(Boolean);
    const fitCollections = [cfg.value, ...editableCollections].filter(Boolean);

    applyFitBounds(L, map, cfg, markers, fitLayers, fitCollections);

    const animationFrame = requestAnimationFrame(() => map.invalidateSize({ animate: false }));
    const resizeTimeout = window.setTimeout(() => map.invalidateSize({ animate: false }), 200);

    context.cleanups.push(() => {
        cancelAnimationFrame(animationFrame);
        window.clearTimeout(resizeTimeout);
    });

    hideLoading(root);

    const api = createLeafletApi(root, map, cfg, context);

    root.daisyLeaflet = api;
    root.dataset.leafletReady = '1';
    delete root.__daisyLeafletInitializing;

    root.dispatchEvent(new CustomEvent('daisy:leaflet:init', {
        detail: api,
    }));

    return map;
}

export default init;

export {
    TILE_PROVIDERS,
    addBasemaps,
    addLayerControl,
    addLayerMenuControl,
    addOverlays,
    addUserControls,
    bindLayerToggleEvents,
    createMap,
    createLeafletApi,
    createLegacyBasemapDefinition,
    createOverlayLayer,
    createRasterLayer,
    collectActiveLayerNames,
    loadControlsState,
    loadGeoJsonData,
    getControlsPositionClasses,
    getOrCreateControlStack,
    normalizeLayerControlConfig,
    normalizeControlsConfig,
    persistControlsState,
    parseGeoJson,
    readConfig,
    shouldUseNativeLayerControl,
};
