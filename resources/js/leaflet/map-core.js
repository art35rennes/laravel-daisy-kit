/*
 * Coeur Leaflet: création de la carte, gestion du fond (providers/URL),
 * ajout des contrôles essentiels et injection des données (markers/geojson).
 */

import { withPlugins, ensurePluginPackages } from './plugins';
import { attachMarkers, attachGeoJson } from './sources';
import { createTileLayer } from './tiles';

/**
 * Lit l'objet de configuration JSON depuis un conteneur data-leaflet
 * @param {Element} root
 * @returns {{containerId: string, ...}|null}
 */
function readConfig(root) {
  try {
    const cfgScript = root.querySelector('script[data-config]');
    if (!cfgScript) return null;
    return JSON.parse(cfgScript.textContent || '{}');
  } catch (_) { return null; }
}

/**
 * Vérifie la présence de Leaflet
 */
async function ensureLeafletBase() {
  if (typeof window === 'undefined') throw new Error('Leaflet requiert un environnement navigateur.');
  if (window.L) return window.L;
  const mod = await import('leaflet');
  const L = mod.default || mod;
  try { await import('leaflet/dist/leaflet.css'); } catch (_) {}
  window.L = L;
  // Enregistre provider helper si dispo
  try { await import('leaflet-providers'); } catch (_) {}
  return L;
}

/**
 * Crée et initialise une carte Leaflet à partir de la configuration
 * @param {Element} root - conteneur ayant data-leaflet="1"
 * @returns {Promise<L.Map|null>}
 */
export async function initMapFromConfig(root) {
  const cfg = readConfig(root);
  if (!cfg) return null;

  let L;
  try { L = await ensureLeafletBase(); } catch (e) { 
    // Sans réseau/paquets, on échoue silencieusement
    return null; 
  }

  const container = document.getElementById(String(cfg.containerId || ''));
  if (!container) return null;

  // Crée la carte
  const options = {};
  if (cfg.preferCanvas) options.preferCanvas = true;
  if (Number.isInteger(cfg.minZoom)) options.minZoom = Number(cfg.minZoom);
  if (Number.isInteger(cfg.maxZoom)) options.maxZoom = Number(cfg.maxZoom);
  const map = L.map(container, options).setView([cfg.center?.lat || 0, cfg.center?.lng || 0], cfg.zoom || 2);

  // Couches tuiles
  const tile = createTileLayer(L, cfg.tiles || {});
  if (tile) tile.addTo(map);

  // Charger les paquets de plugins nécessaires (si demandés) puis activer
  try { await ensurePluginPackages(cfg?.plugins || {}); } catch (_) {}
  await withPlugins(L, map, cfg);

  // Sources simples
  try { attachMarkers(L, map, cfg?.data?.markers || [], !!cfg?.plugins?.cluster, cfg?.plugins?.clusterOptions || {}); } catch (_) {}
  try { attachGeoJson(L, map, cfg?.data?.geojson); } catch (_) {}

  // Émet un événement d'initialisation sur le conteneur racine pour que la démo ou l'hôte puissent s'abonner
  try {
    const initEvt = new CustomEvent('daisy:leaflet:init', {
      detail: { map, config: cfg, containerId: container.id, root }
    });
    root.dispatchEvent(initEvt);
  } catch (_) {}

  return map;
}

/**
 * Initialise toutes les cartes présentes dans le document
 */
export async function initAllLeafletMaps() {
  const roots = Array.from(document.querySelectorAll('[data-leaflet="1"]'));
  const maps = [];
  for (const root of roots) {
    const map = await initMapFromConfig(root);
    if (map) maps.push(map);
  }
  return maps;
}


