/**
 * Marker cluster plugin (third-party exception: performance-critical).
 *
 * Without clustering, maps with 50+ markers become unusable due to lag and
 * visual overlap. This plugin groups nearby markers into expandable clusters.
 *
 * When active, markers are NOT added directly to the map by the core module.
 * Instead, this plugin receives the pre-built markers via `context.markers`
 * and adds them to a `L.markerClusterGroup`.
 *
 * @module leaflet/plugins/cluster
 */

/**
 * Adds markers to the map via a cluster group.
 *
 * @param {L} L - The Leaflet namespace.
 * @param {L.Map} map - The Leaflet map instance.
 * @param {Object} cfg - Parsed component config (includes `clusterOptions`).
 * @param {Object} context - Shared context from the init orchestrator.
 * @param {L.Marker[]} context.markers - Pre-built marker instances.
 * @returns {Promise<void>}
 */
export async function apply(L, map, cfg, context) {
    await import('leaflet.markercluster');

    try {
        await import('leaflet.markercluster/dist/MarkerCluster.css');
        await import('leaflet.markercluster/dist/MarkerCluster.Default.css');
    } catch {
        // CSS may be bundled separately.
    }

    const group = L.markerClusterGroup(cfg.clusterOptions || {});

    for (const marker of (context.markers || [])) {
        group.addLayer(marker);
    }

    map.addLayer(group);
}
