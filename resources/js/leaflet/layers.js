/**
 * Layer state and events shared by Leaflet's native and Daisy layer controls.
 */

/**
 * @param {Object} map
 * @param {Object} layer
 * @returns {boolean}
 */
function mapHasLayer(map, layer) {
    if (typeof map.hasLayer === 'function') {
        return map.hasLayer(layer);
    }

    return layer.addedTo === map;
}

/**
 * @param {Object} map
 * @param {Object} layer
 * @returns {void}
 */
function addLayer(map, layer) {
    if (!mapHasLayer(map, layer)) {
        layer.addTo(map);
    }
}

/**
 * @param {Object} map
 * @param {Object} layer
 * @returns {void}
 */
function removeLayer(map, layer) {
    if (mapHasLayer(map, layer) && typeof map.removeLayer === 'function') {
        map.removeLayer(layer);
    }
}

/**
 * @param {Object} map
 * @param {Object<string, Object>} layers
 * @returns {string[]}
 */
function collectActiveLayerNames(map, layers) {
    return Object.entries(layers || {})
        .filter(([, layer]) => mapHasLayer(map, layer))
        .map(([name]) => name);
}

/**
 * @param {HTMLElement} root
 * @param {Object} map
 * @param {string|null} name
 * @param {string} type
 * @param {Object|null} layer
 * @param {Object} context
 * @returns {void}
 */
function dispatchLayerToggle(root, map, name, type, layer = null, context = {}) {
    root.dispatchEvent(new CustomEvent('daisy:leaflet:layer-toggle', {
        detail: {
            map,
            name,
            type,
            layer,
            activeBasemap: collectActiveLayerNames(map, context.baseLayers)[0] || null,
            activeOverlays: collectActiveLayerNames(map, context.overlayLayers),
            lockedOverlays: Object.keys(context.lockedOverlayLayers || {}),
        },
    }));
}

/**
 * Emits Daisy Kit layer toggle events from Leaflet's native layer control events.
 *
 * @param {Element} root
 * @param {Object} map
 * @returns {void}
 */
function bindLayerToggleEvents(root, map) {
    ['baselayerchange', 'overlayadd', 'overlayremove'].forEach(eventName => {
        map.on(eventName, event => {
            dispatchLayerToggle(root, map, event.name || null, event.type, event.layer || null);
        });
    });
}

export {
    addLayer,
    bindLayerToggleEvents,
    collectActiveLayerNames,
    dispatchLayerToggle,
    mapHasLayer,
    removeLayer,
};
