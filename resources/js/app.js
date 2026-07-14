// Importation des modules de base
import './bootstrap';
import { init as initializeDataModules } from './kit';
import { importWhenIdle, importWhenNearViewport, createLimiter } from './utils/scheduler';

// Keep the generic data-module router in the production bundle.
initializeDataModules();

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
  // Les tooltips sont portés au niveau du document pour échapper aux conteneurs scrollables.
  const { initAllTooltips } = await import('./modules/tooltip');
  initAllTooltips(document);

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
  const sidebars = document.querySelectorAll('[data-sidebar-root]');
  const sidebarModule = sidebars.length > 0 ? await import('./modules/sidebar') : null;

  sidebars.forEach((aside) => {
    sidebarModule?.initSidebar(aside);
  });

  // Greffon copyable : chargement direct (léger, observe aussi les contenus injectés après le boot)
  await import('./modules/copyable');

  // Importation du composant web Cally (calendrier) si nécessaire
  await dynamicImportIf('.cally, calendar-date, calendar-range, calendar-month, calendar-multi', async () => {
    await import('cally');
  });

  // Color picker : chargement direct (pas de lazy loading)
  await dynamicImportIf('[data-colorpicker="1"]', async () => {
    await import('./color-picker');
    // Initialisation explicite après l'import
    if (window.DaisyColorPicker) {
      window.DaisyColorPicker.initAll();
    }
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
  // Legacy popovers are bootstrapped by resources/js/popover.js.
  // Exclude kit-managed instances to avoid double initialization with data-module="popover".
  importWhenIdle('[data-popover]:not([data-module])', () => { mediumQueue(() => import('./popover')); });
  importWhenIdle('[data-stepper]', () => { mediumQueue(() => import('./stepper')); });
  importWhenIdle('[data-onboarding="1"]', () => { mediumQueue(() => import('./onboarding')); });
  importWhenIdle('[data-fileinput="1"]', () => { mediumQueue(() => import('./file-input')); });
  importWhenIdle('input[data-inputmask="1"], input[data-obfuscate="1"]', () => { mediumQueue(() => import('./input-mask')); });
  importWhenIdle('[data-scrollstatus="1"]', () => { mediumQueue(() => import('./scroll-status')); });
  importWhenIdle('[data-transfer="1"]', () => { mediumQueue(() => import('./transfer')); });
  importWhenIdle('[data-ordered-list="1"]', () => { mediumQueue(() => import('./modules/ordered-list')); });
  importWhenIdle('[data-sign="1"]', () => { mediumQueue(() => import('./modules/sign')); });

  // Modules lourds : chargement quand l'élément approche du viewport
  // Ces modules ont un impact performance important donc chargés un par un
  importWhenNearViewport('[data-lightbox="1"]', () => { heavyQueue(() => import('./lightbox')); }, { rootMargin: '600px 0px' });
  importWhenNearViewport('[data-media-gallery="1"]', () => { heavyQueue(() => import('./media-gallery')); }, { rootMargin: '600px 0px' });
  importWhenNearViewport('[data-blueprint="1"]', () => { heavyQueue(() => import('./modules/blueprint')); }, { rootMargin: '800px 0px' });
  
  // Éditeurs lazy (CodeMirror & Trix) seulement quand on approche de leur zone
  importWhenNearViewport('.code-editor, trix-editor, details.collapse', () => { heavyQueue(() => import('./lazy-editors')); }, { rootMargin: '800px 0px' });
  
  // Charts (ECharts + thème DaisyUI) : import quand proche, puis auto-observation des nouveaux charts.
  let chartModuleRequested = false;
  const requestChartModule = () => {
    if (chartModuleRequested) return;
    chartModuleRequested = true;
    heavyQueue(() => import('./chart'));
  };
  importWhenNearViewport('[data-daisy-chart="1"]', requestChartModule, { rootMargin: '800px 0px' });
  if (typeof MutationObserver !== 'undefined') {
    const chartObserver = new MutationObserver((mutations) => {
      for (const mutation of mutations) {
        for (const node of mutation.addedNodes) {
          if (!(node instanceof Element)) continue;
          if (node.matches('[data-daisy-chart="1"]') || node.querySelector('[data-daisy-chart="1"]')) {
            requestChartModule();
            return;
          }
        }
      }
    });
    chartObserver.observe(document.body, { childList: true, subtree: true });
  }

  // Calendar Full: composant interne (sans lib externe) – lazy près du viewport
  importWhenNearViewport('[data-calendar-full="1"]', () => { heavyQueue(() => import('./calendar-full')); }, { rootMargin: '800px 0px' });
});
