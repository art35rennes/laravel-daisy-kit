/*
 * Activation conditionnelle des plugins Leaflet fréquents.
 * Tous les plugins sont optionnels; absence = dégradé silencieux.
 */

/**
 * Ajoute les contrôles/overlays en fonction de cfg.plugins si disponibles
 * @param {L} L
 * @param {L.Map} map
 * @param {object} cfg
 */
export async function withPlugins(L, map, cfg) {
  const p = cfg?.plugins || {};

  // Gesture Handling
  try {
    if (p.gestureHandling && L?.GestureHandling) {
      map.addHandler('gestureHandling', L.GestureHandling);
      map.gestureHandling.enable();
    }
  } catch (_) {}

  // Locate control
  try {
    if (p.locateControl && L?.control?.locate) {
      L.control.locate({ keepCurrentZoomLevel: true }).addTo(map);
    }
  } catch (_) {}

  // Fullscreen
  try {
    if (p.fullscreen && L?.control?.fullscreen) {
      L.control.fullscreen({ position: 'topleft' }).addTo(map);
    }
  } catch (_) {}

  // Hash (synchro URL)
  try {
    if (p.hash && window.L && window.L.Hash) {
      // Évite d'asservir plusieurs cartes via un seul hash global
      const KEY = '__DaisyLeafletHashAttachedId';
      if (!window[KEY]) {
        // eslint-disable-next-line no-new
        new window.L.Hash(map);
        try { window[KEY] = map._leaflet_id || (map._container && map._container.id) || '1'; } catch (_) {}
      }
    }
  } catch (_) {}

  // Scale (échelle métrique/impériale)
  try {
    if (p.scale) {
      const opts = typeof p.scale === 'object' ? p.scale : { metric: true, imperial: true };
      L.control.scale(opts).addTo(map);
    }
  } catch (_) {}

  // MiniMap
  try {
    if (p.miniMap && window.L && window.L.Control && (window.L.Control.MiniMap || L.Control?.MiniMap)) {
      const MiniMapCtor = window.L.Control.MiniMap || L.Control.MiniMap;
      let layer = null;
      if (p.miniMap.url) {
        layer = L.tileLayer(p.miniMap.url, p.miniMap.options || {});
      } else if (p.miniMap.provider && L?.tileLayer?.provider) {
        layer = L.tileLayer.provider(p.miniMap.provider, p.miniMap.options || {});
      }
      if (layer) {
        const ctrl = new MiniMapCtor(layer, { toggleDisplay: true });
        map.addControl(ctrl);
      }
    }
  } catch (_) {}

  // Mesure
  try {
    if (p.measure && L?.control?.measure) {
      const opts = typeof p.measure === 'object' ? p.measure : {};
      L.control.measure(opts).addTo(map);
    }
  } catch (_) {}

  // Dessin/édition
  try {
    if (p.draw && L?.draw && L?.Control?.Draw) {
      const drawOpts = typeof p.draw === 'object' ? (p.draw || {}) : {};
      // Groupe d'édition par défaut si non fourni
      let featureGroup = drawOpts?.edit?.featureGroup;
      if (!featureGroup) {
        featureGroup = L.featureGroup().addTo(map);
        drawOpts.edit = { ...(drawOpts.edit || {}), featureGroup };
      }
      const drawControl = new L.Control.Draw(drawOpts);
      map.addControl(drawControl);
      // Ajoute automatiquement les entités créées au featureGroup
      map.on('draw:created', (e) => {
        try { featureGroup.addLayer(e.layer); } catch (_) {}
      });
    }
  } catch (_) {}

  // Clustering (les marqueurs eux-mêmes sont ajoutés dans sources.js)
  // Ici rien à faire si p.cluster: on vérifie la présence du plugin dans sources.js

  // Heatmap
  try {
    if (p.heatmap && Array.isArray(p.heatmap.points) && L?.heatLayer) {
      const layer = L.heatLayer(p.heatmap.points, p.heatmap.options || {});
      layer.addTo(map);
    }
  } catch (_) {}

  // Geocoder
  try {
    if (p.geocoder) {
      const provider = (typeof p.geocoder === 'object' ? p.geocoder.provider : p.geocoder) || 'osm';
      if (provider === 'osm' && L.Control?.geocoder) {
        const ctrl = L.Control.geocoder(typeof p.geocoder === 'object' ? (p.geocoder.options || {}) : {});
        ctrl.addTo(map);
      } else if (provider === 'esri' && window.L && window.L.esri && window.L.esri.Geocoding && window.L.esri.Geocoding.geosearch) {
        const search = window.L.esri.Geocoding.geosearch((typeof p.geocoder === 'object' ? p.geocoder.options : {}) || {});
        search.addTo(map);
      }
    }
  } catch (_) {}

  // Routing
  try {
    if (p.routing && window.L && window.L.Routing && window.L.Routing.Control) {
      const opts = typeof p.routing === 'object' ? (p.routing.options || {}) : {};
      const control = window.L.Routing.control(opts);
      control.addTo(map);
    }
  } catch (_) {}
}

/**
 * Charge dynamiquement les paquets des plugins demandés (si installés via NPM).
 * On importe aussi les CSS quand pertinents.
 */
export async function ensurePluginPackages(p) {
  const jobs = [];
  try {
    if (p.gestureHandling) {
      jobs.push(import('leaflet-gesture-handling').then(async (mod) => {
        try { await import('leaflet-gesture-handling/dist/leaflet-gesture-handling.css'); } catch (_) {}
        const GH = (mod && (mod.default || mod.GestureHandling)) || (window.L && window.L.GestureHandling);
        if (GH && window.L) window.L.GestureHandling = GH;
      }));
    }
  } catch (_) {}
  try {
    if (p.locateControl) {
      jobs.push(import('leaflet.locatecontrol').then(async () => {
        try { await import('leaflet.locatecontrol/dist/L.Control.Locate.min.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  try {
    if (p.fullscreen) {
      jobs.push(import('leaflet.fullscreen').then(async () => {
        try { await import('leaflet.fullscreen/Control.FullScreen.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  try {
    if (p.hash) {
      jobs.push(import('leaflet-hash'));
    }
  } catch (_) {}
  try {
    if (p.miniMap) {
      jobs.push(import('leaflet-minimap').then(async () => {
        try { await import('leaflet-minimap/dist/Control.MiniMap.min.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  try {
    if (p.measure) {
      jobs.push(import('leaflet-measure').then(async () => {
        try { await import('leaflet-measure/dist/leaflet-measure.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  try {
    if (p.draw) {
      jobs.push(import('leaflet-draw').then(async () => {
        try { await import('leaflet-draw/dist/leaflet.draw.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  try {
    if (p.cluster) {
      jobs.push(import('leaflet.markercluster').then(async () => {
        try { await import('leaflet.markercluster/dist/MarkerCluster.css'); } catch (_) {}
        try { await import('leaflet.markercluster/dist/MarkerCluster.Default.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  try {
    if (p.heatmap) {
      jobs.push(import('leaflet.heat'));
    }
  } catch (_) {}
  try {
    if (p.geocoder) {
      const provider = (typeof p.geocoder === 'object' ? p.geocoder.provider : p.geocoder) || 'osm';
      if (provider === 'osm') {
        jobs.push(import('leaflet-control-geocoder').then(async () => {
          try { await import('leaflet-control-geocoder/dist/Control.Geocoder.css'); } catch (_) {}
        }));
      } else if (provider === 'esri') {
        jobs.push(import('esri-leaflet'));
        jobs.push(import('esri-leaflet-geocoder').then(async () => {
          try { await import('esri-leaflet-geocoder/dist/esri-leaflet-geocoder.css'); } catch (_) {}
        }));
      }
    }
  } catch (_) {}
  try {
    if (p.routing) {
      jobs.push(import('leaflet-routing-machine').then(async () => {
        try { await import('leaflet-routing-machine/dist/leaflet-routing-machine.css'); } catch (_) {}
      }));
    }
  } catch (_) {}
  await Promise.allSettled(jobs);
}


