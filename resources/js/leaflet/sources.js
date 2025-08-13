/*
 * Ajout des données: marqueurs simples/clusterisés et couches GeoJSON.
 */

/**
 * Ajoute des marqueurs sur la carte
 * @param {L} L
 * @param {L.Map} map
 * @param {Array} markers - [[lat, lng, popup?], ...] ou [{lat, lng, popup, icon, options}]
 * @param {boolean} useCluster
 * @param {object} clusterOptions
 */
export function attachMarkers(L, map, markers, useCluster, clusterOptions) {
  if (!Array.isArray(markers) || !markers.length) return;
  let layerGroup = null;
  try {
    if (useCluster && L?.markerClusterGroup) {
      layerGroup = L.markerClusterGroup(clusterOptions || {});
    }
  } catch (_) {}
  if (!layerGroup) layerGroup = L.layerGroup();

  for (const m of markers) {
    let lat, lng, popup, icon, options;
    if (Array.isArray(m)) {
      [lat, lng, popup] = m;
      options = {};
    } else {
      lat = m.lat; lng = m.lng; popup = m.popup; icon = m.icon; options = m.options || {};
    }
    if (typeof lat !== 'number' || typeof lng !== 'number') continue;
    const marker = L.marker([lat, lng], options);
    if (icon) { try { marker.setIcon(icon); } catch (_) {} }
    if (popup) { try { marker.bindPopup(popup); } catch (_) {} }
    layerGroup.addLayer(marker);
  }
  layerGroup.addTo(map);
}

/**
 * Ajoute une couche GeoJSON (si fournie)
 * @param {L} L
 * @param {L.Map} map
 * @param {Object|string|null} geojson
 */
export function attachGeoJson(L, map, geojson) {
  if (!geojson) return;
  try {
    const data = typeof geojson === 'string' ? JSON.parse(geojson) : geojson;
    const layer = L.geoJSON(data);
    layer.addTo(map);
  } catch (_) {}
}


