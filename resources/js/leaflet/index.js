/*
 * Daisy Leaflet - Intégration progressive de Leaflet et plugins courants
 *
 * Objectifs
 * - Initialisation paresseuse des cartes quand elles deviennent visibles
 * - Détection non bloquante des plugins (si non présents, on dégrade silencieusement)
 * - API de configuration via <script data-config> généré par le composant Blade
 * - Découpage en modules focalisés pour la performance (import() on‑demand si souhaité plus tard)
 */

import { initMapFromConfig, initAllLeafletMaps } from './map-core';

// API globale (utile pour debug ou usages avancés)
window.DaisyLeaflet = {
  init: initMapFromConfig,
  initAll: initAllLeafletMaps,
};

// Initialisation paresseuse à l'apparition dans le viewport
(function setupLazyInit() {
  function observe() {
    const roots = Array.from(document.querySelectorAll('[data-leaflet="1"]'));
    if (!roots.length) return;

    // File simple pour éviter les rafales d'init
    const queue = [];
    let active = false;
    async function runNext() {
      if (active) return;
      const job = queue.shift();
      if (!job) return;
      active = true;
      try { await job(); } catch (_) {}
      finally { active = false; if (queue.length) requestAnimationFrame(runNext); }
    }

    const obs = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          obs.unobserve(el);
          // Évite double init si déjà traité via fallback
          if (el.dataset.leafletInitialized === '1') return;
          queue.push(async () => {
            await initMapFromConfig(el);
            try { el.dataset.leafletInitialized = '1'; } catch (_) {}
          });
          runNext();
        }
      });
    }, { rootMargin: '400px 0px', threshold: 0.05 });

    roots.forEach((el) => obs.observe(el));

    // Fallback: si l'observer ne déclenche pas (hauteur tardive, onglet/tabs, etc.),
    // on initialise les éléments déjà visibles après un court délai.
    // Cela évite un état "carré vide" sans erreurs.
    setTimeout(() => {
      roots.forEach((el) => {
        if (el.dataset.leafletInitialized === '1') return;
        const r = el.getBoundingClientRect();
        const inViewport = r.width > 0 && r.height > 0 && r.bottom > 0 && r.top < (window.innerHeight || document.documentElement.clientHeight);
        if (inViewport) {
          queue.push(async () => {
            await initMapFromConfig(el);
            try { el.dataset.leafletInitialized = '1'; } catch (_) {}
          });
          runNext();
        }
      });
    }, 600);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', observe);
  } else {
    Promise.resolve().then(observe);
  }
})();


