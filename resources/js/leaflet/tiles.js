/*
 * Gestion du fond de carte: via leaflet-providers si disponible ou via URL directe
 */

/**
 * Crée une tuile Leaflet à partir du provider ou de l'URL fournie
 * @param {L} L
 * @param {{provider?: string, url?: string, options?: object}} cfg
 */
export function createTileLayer(L, cfg) {
  try {
    const options = cfg.options || {};
    if (cfg.url) {
      return L.tileLayer(cfg.url, options);
    }
    const provider = cfg.provider || 'OpenStreetMap.Mapnik';
    // leaflet-providers expose L.tileLayer.provider
    if (L?.tileLayer?.provider) {
      return L.tileLayer.provider(provider, options);
    }
    // fallback OSM standard si providers indisponible
    return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', options);
  } catch (_) { return null; }
}


