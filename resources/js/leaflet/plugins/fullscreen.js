/**
 * Fullscreen control plugin (third-party exception: standard UX).
 *
 * Adds a fullscreen toggle button to the map. There is no native Leaflet
 * fullscreen control, making this plugin the standard solution.
 *
 * @module leaflet/plugins/fullscreen
 */

/**
 * Adds the fullscreen control to the map.
 *
 * Dynamically imports `leaflet.fullscreen` and its CSS, then adds
 * the control to the map.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {L.Map} map - The Leaflet map instance.
 * @returns {Promise<void>}
 */
export async function apply(L, map) {
    await import('leaflet.fullscreen');

    try {
        await import('leaflet.fullscreen/Control.FullScreen.css');
    } catch {
        // CSS may be bundled separately.
    }

    L.control.fullscreen({ position: 'topleft' }).addTo(map);
}
