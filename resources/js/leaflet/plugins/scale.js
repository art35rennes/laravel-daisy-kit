/**
 * Scale control plugin (native Leaflet, zero external dependency).
 *
 * Adds a metric scale bar to the bottom-left corner of the map.
 *
 * @module leaflet/plugins/scale
 */

/**
 * Adds the native Leaflet scale control to the map.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {L.Map} map - The Leaflet map instance.
 * @returns {void}
 */
export function apply(L, map) {
    L.control.scale({ imperial: false }).addTo(map);
}
