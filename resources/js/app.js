// Importation des modules de base
import './bootstrap';
import { importWhenIdle, importWhenNearViewport, createLimiter } from './utils/scheduler';

/**
 * Importe dynamiquement un module seulement si un élément correspondant au sélecteur existe dans le DOM
 * @param {string} selector - Sélecteur CSS pour vérifier la présence d'éléments
 * @param {Function} loader - Fonction de chargement du module
 */
async function dynamicImportIf(selector, loader) {
  try {
    if (document.querySelector(selector)) {
      await loader();
    }
  } catch (_) {}
}

/**
 * Exécute une fonction quand le DOM est prêt
 * @param {Function} fn - Fonction à exécuter
 */
function onReady(fn) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fn);
  } else {
    fn();
  }
}

// Initialisation des fonctionnalités une fois le DOM chargé
onReady(async () => {
  // Gestion des checkbox indéterminées (DaisyUI)
  // Initialise l'état "mixed" puis normalise lors du changement
  document.querySelectorAll('input[type="checkbox"][data-indeterminate="true"]').forEach((el) => {
    try {
      el.checked = false;
      el.indeterminate = true;
      el.setAttribute('aria-checked', 'mixed');
      el.addEventListener('change', () => {
        el.indeterminate = false;
        el.setAttribute('aria-checked', el.checked ? 'true' : 'false');
      });
    } catch (e) {}
  });

  // Comportement générique des sidebars
  // Gère l'expansion/réduction avec persistance localStorage
  document.querySelectorAll('[data-sidebar-root] .sidebar-toggle').forEach((button) => {
    const aside = button.closest('[data-sidebar-root]');
    if (!aside) return;
    
    // Configuration depuis les data-attributes
    const storageKey = aside.dataset.storageKey || 'daisy.sidebar';
    const wideClass = aside.dataset.wideClass;
    const collapsedClass = aside.dataset.collapsedClass || 'w-20';
    
    /**
     * Définit l'état collapsed/expanded de la sidebar
     * @param {boolean} collapsed - État collapsed à appliquer
     */
    const setCollapsed = (collapsed) => {
      aside.dataset.collapsed = collapsed ? '1' : '0';
      if (wideClass) aside.classList.toggle(wideClass, !collapsed);
      if (collapsedClass) aside.classList.toggle(collapsedClass, collapsed);
      aside.querySelectorAll('.sidebar-label').forEach((el) => el.classList.toggle('hidden', collapsed));
      const txt = aside.querySelector('.sidebar-label-toggle');
      if (txt) txt.textContent = collapsed ? 'Expand' : 'Collapse';
      try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch (_) {}
      
      // Mise à jour de l'icône du bouton
      const icon = button.querySelector('svg');
      if (icon) {
        icon.outerHTML = collapsed
          ? '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 19.5 21 12l-7.5-7.5M3 4.5v15"/></svg>'
          : '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 4.5 3 12l7.5 7.5M21 19.5v-15"/></svg>';
      }
    };
    
    // Restauration de l'état depuis localStorage
    try {
      const persisted = localStorage.getItem(storageKey);
      if (persisted === '1' || persisted === '0') setCollapsed(persisted === '1');
    } catch (_) {}
    
    // Gestionnaire de clic pour basculer l'état
    button.addEventListener('click', () => setCollapsed(aside.dataset.collapsed !== '1'));
  });

  // Importation du composant web Cally (calendrier) si nécessaire
  await dynamicImportIf('.cally, calendar-date, calendar-range, calendar-month, calendar-multi', async () => {
    await import('cally');
  });

  // Radios "décochables" : permet de décocher un radio déjà coché si data-uncheckable="1"
  // Mémorisation de l'état AVANT le clic pour distinguer check vs uncheck
  document.addEventListener('mousedown', (e) => {
    let input = null;
    
    // Identification de l'input radio concerné (direct, via label, ou parent label)
    if (e.target instanceof HTMLInputElement && e.target.type === 'radio') input = e.target;
    else if (e.target instanceof HTMLLabelElement && e.target.control?.type === 'radio') input = e.target.control;
    else {
      const label = e.target.closest('label');
      if (label && label.control?.type === 'radio') input = label.control;
    }
    
    if (!input || input.dataset.uncheckable !== '1') return;
    
    // Sauvegarde de l'état coché avant le clic
    input.dataset.wasChecked = input.checked ? '1' : '0';
  }, { capture: true });

  // Gestion du clic pour décocher si nécessaire
  document.addEventListener('click', (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'radio' || input.dataset.uncheckable !== '1') return;
    
    const wasChecked = input.dataset.wasChecked === '1';
    delete input.dataset.wasChecked;
    
    // Décocher seulement si l'input était déjà coché avant le clic
    if (wasChecked) {
      setTimeout(() => {
        input.checked = false;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }, 0);
    }
  });

  // Support clavier (Espace) pour décocher un radio déjà coché
  document.addEventListener('keydown', (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'radio') return;
    
    if (input.dataset.uncheckable === '1' && (e.key === ' ' || e.key === 'Spacebar') && input.checked) {
      e.preventDefault();
      input.checked = false;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });

  // Configuration des limiteurs de concurrence pour éviter les surcharges
  const mediumQueue = createLimiter(4); // Modules moyens : 4 concurrent max
  const heavyQueue = createLimiter(2);   // Modules lourds : 2 seul à la fois

  // Chargement lazy des modules selon la présence DOM (non bloquants)
  // Ces modules se chargent quand le navigateur est inactif
  importWhenIdle('[data-treeview="1"]', () => { mediumQueue(() => import('./treeview')); });
  importWhenIdle('[data-scrollspy="1"]', () => { mediumQueue(() => import('./scrollspy')); });
  importWhenIdle('[data-popconfirm], [data-popconfirm-modal]', () => { mediumQueue(() => import('./popconfirm')); });
  importWhenIdle('[data-popover]', () => { mediumQueue(() => import('./popover')); });
  importWhenIdle('[data-stepper]', () => { mediumQueue(() => import('./stepper')); });
  importWhenIdle('[data-table-select]:not([data-table-select="none"])', () => { mediumQueue(() => import('./table')); });
  importWhenIdle('[data-colorpicker="1"]', () => { mediumQueue(() => import('./color-picker')); });
  importWhenIdle('[data-fileinput="1"]', () => { mediumQueue(() => import('./file-input')); });
  importWhenIdle('input[data-inputmask="1"], input[data-obfuscate="1"]', () => { mediumQueue(() => import('./input-mask')); });
  importWhenIdle('[data-scrollstatus="1"]', () => { mediumQueue(() => import('./scroll-status')); });
  importWhenIdle('[data-transfer="1"]', () => { mediumQueue(() => import('./transfer')); });

  // Modules lourds : chargement quand l'élément approche du viewport
  // Ces modules ont un impact performance important donc chargés un par un
  importWhenNearViewport('[data-lightbox="1"]', () => { heavyQueue(() => import('./lightbox')); }, { rootMargin: '600px 0px' });
  importWhenNearViewport('[data-media-gallery="1"]', () => { heavyQueue(() => import('./media-gallery')); }, { rootMargin: '600px 0px' });
  
  // Éditeurs lazy (CodeMirror & Trix) seulement quand on approche de leur zone
  importWhenNearViewport('.collapse .code-editor, .collapse trix-editor, details.collapse', () => { heavyQueue(() => import('./lazy-editors')); }, { rootMargin: '800px 0px' });
  
  // Charts (Chart.js + thème DaisyUI) : import quand proche, initialisation à la visibilité
  importWhenNearViewport('[data-chart="1"]', () => { heavyQueue(() => import('./chart')); }, { rootMargin: '800px 0px' });

  // Leaflet: charge le module seulement si un composant data-leaflet est proche du viewport
  importWhenNearViewport('[data-leaflet="1"]', () => { heavyQueue(() => import('./leaflet')); }, { rootMargin: '800px 0px' });
});
