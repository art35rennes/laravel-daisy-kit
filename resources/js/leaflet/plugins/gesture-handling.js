/**
 * Gesture handling plugin (third-party exception: critical UX).
 *
 * Prevents scroll-hijack on embedded maps by requiring Ctrl+scroll to zoom
 * and two-finger gestures to pan on touch devices. Without this plugin,
 * a map embedded in a scrollable page captures the entire page scroll.
 *
 * @module leaflet/plugins/gesture-handling
 */

/**
 * Activates gesture handling on the map.
 *
 * Dynamically imports `leaflet-gesture-handling` and its CSS, then enables
 * the gesture handler on the existing map instance.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {L.Map} map - The Leaflet map instance.
 * @returns {Promise<void>}
 */
export async function apply(L, map) {
    await import('leaflet-gesture-handling');

    try {
        await import('leaflet-gesture-handling/dist/leaflet-gesture-handling.css');
    } catch {
        // CSS may be bundled separately.
    }

    if (map.gestureHandling) {
        map.gestureHandling.enable();
    }
}
