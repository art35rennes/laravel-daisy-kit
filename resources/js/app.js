// Importation des modules de base
import './bootstrap';
import './kit'; // Système de modules data-module
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
  document.querySelectorAll('[data-sidebar-root]').forEach((aside) => {
    const button = aside.querySelector('.sidebar-toggle');
    
    // Configuration depuis les data-attributes
    const storageKey = aside.dataset.storageKey || 'daisy.sidebar';
    const wideClass = aside.dataset.wideClass;
    const collapsedClass = aside.dataset.collapsedClass || 'w-20';
    const expandedLabel = aside.dataset.expandedLabel || 'Collapse';
    const collapsedLabel = aside.dataset.collapsedLabel || 'Expand';
    const expandOnHover = aside.dataset.expandOnHover === '1';
    let hoverCloseTimeout = null;

    if (!button && !expandOnHover) return;

    const classListFrom = (value) => (value || '').split(/\s+/).filter(Boolean);

    const removeClasses = (classes) => {
      classListFrom(classes).forEach((cls) => aside.classList.remove(cls));
    };

    const addClasses = (classes) => {
      classListFrom(classes).forEach((cls) => aside.classList.add(cls));
    };
    
    /**
     * Définit l'état collapsed/expanded de la sidebar
     * @param {boolean} collapsed - État collapsed à appliquer
     */
    const setCollapsed = (collapsed) => {
      aside.dataset.collapsed = collapsed ? '1' : '0';
      
      // Gestion des classes de largeur selon la stratégie
      const widthStrategy = aside.dataset.widthStrategy || 'wide';
      
      // Nettoyer d'abord toutes les classes de largeur existantes
      ['w-20', 'w-64', 'w-fit', 'min-w-48', 'max-w-80', 'sidebar-auto', 'sidebar-fit', 'sidebar-adaptive'].forEach((cls) => {
        aside.classList.remove(cls);
      });
      removeClasses(wideClass);
      removeClasses(collapsedClass);
      
      if (collapsed) {
        // Mode collapsed : utiliser la largeur compacte configurée.
        addClasses(collapsedClass);
      } else {
        // Mode expanded : appliquer les classes selon la stratégie
        addClasses(wideClass);
        
        // Ajouter les classes spéciales selon la stratégie
        switch (widthStrategy) {
          case 'auto':
            aside.classList.add('sidebar-auto', 'sidebar-adaptive');
            break;
          case 'fit':
            aside.classList.add('sidebar-fit', 'sidebar-adaptive');
            break;
        }
      }
      
      // Masquer/afficher les labels
      aside.querySelectorAll('.sidebar-label').forEach((el) => el.classList.toggle('hidden', collapsed));
      aside.querySelector('[data-sidebar-brand]')?.classList.toggle('justify-center', collapsed);
      aside.querySelector('[data-sidebar-brand]')?.classList.toggle('justify-between', !collapsed);
      aside.querySelector('[data-sidebar-brand]')?.classList.toggle('px-2', collapsed);
      aside.querySelector('[data-sidebar-brand]')?.classList.toggle('px-4', !collapsed);
      aside.querySelector('[data-sidebar-brand-expanded]')?.classList.toggle('hidden', collapsed);
      aside.querySelector('[data-sidebar-brand-expanded]')?.classList.toggle('flex', !collapsed);
      aside.querySelector('[data-sidebar-brand-collapsed]')?.classList.toggle('hidden', !collapsed);
      aside.querySelector('[data-sidebar-brand-collapsed]')?.classList.toggle('flex', collapsed);
      aside.querySelector('[data-sidebar-brand-collapsed]')?.setAttribute('aria-hidden', collapsed ? 'false' : 'true');
      aside.querySelectorAll('[data-sidebar-row]').forEach((row) => {
        row.classList.toggle('justify-center', collapsed);
        row.classList.toggle('gap-0', collapsed);
        row.classList.toggle('gap-2', !collapsed);
      });
      
      // Mettre à jour le texte du bouton
      const label = collapsed ? collapsedLabel : expandedLabel;
      const txt = aside.querySelector('.sidebar-label-toggle');
      if (txt) {
        txt.textContent = label;
        txt.classList.toggle('sr-only', collapsed);
      }
      if (button) {
        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
        button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        button.classList.toggle('btn-square', collapsed);
        button.classList.toggle('mx-auto', collapsed);
        button.classList.toggle('justify-center', collapsed);
        button.classList.toggle('w-full', !collapsed);
        button.classList.toggle('justify-between', !collapsed);
        button.querySelector('[data-sidebar-icon-collapsed]')?.classList.toggle('hidden', !collapsed);
        button.querySelector('[data-sidebar-icon-expanded]')?.classList.toggle('hidden', collapsed);
      }
      aside.querySelectorAll('[data-sidebar-submenu]').forEach((submenu) => {
        submenu.setAttribute('aria-hidden', collapsed ? 'true' : 'false');
      });
      aside.querySelectorAll('[data-sidebar-hover-content]').forEach((content) => {
        content.setAttribute('aria-hidden', collapsed ? 'true' : 'false');
      });
      
      // Sauvegarder l'état
      if (!expandOnHover) {
        try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch (_) {}
      }
    };

    const closeHoverSidebar = () => {
      if (!expandOnHover) return;

      hoverCloseTimeout = window.setTimeout(() => {
        if (aside.matches(':hover') || aside.contains(document.activeElement)) return;

        setCollapsed(true);
      }, 160);
    };

    const openHoverSidebar = () => {
      if (!expandOnHover) return;

      window.clearTimeout(hoverCloseTimeout);
      setCollapsed(false);
    };
    
    // Restauration de l'état depuis localStorage
    if (expandOnHover) {
      setCollapsed(true);
    } else {
      try {
        const persisted = localStorage.getItem(storageKey);
        if (persisted === '1' || persisted === '0') setCollapsed(persisted === '1');
      } catch (_) {}
    }
    
    // Gestionnaire de clic pour basculer l'état
    button?.addEventListener('click', () => setCollapsed(aside.dataset.collapsed !== '1'));
    aside.addEventListener('pointerenter', openHoverSidebar);
    aside.addEventListener('pointerleave', closeHoverSidebar);
    aside.addEventListener('focusin', openHoverSidebar);
    aside.addEventListener('focusout', closeHoverSidebar);
  });

  // Greffon copyable : chargement direct (léger, s'initialise automatiquement)
  await dynamicImportIf('.copyable', async () => {
    await import('./modules/copyable');
  });

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
  importWhenIdle('[data-daisy-table="1"]', () => { mediumQueue(() => import('./table-kit')); });
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
