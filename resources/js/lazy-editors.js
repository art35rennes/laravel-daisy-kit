// Initialisation différée des éditeurs lourds (CodeMirror et Trix) lorsqu'un collapse DaisyUI s'ouvre

/**
 * Exécute une fonction quand le DOM est prêt
 * @param {Function} fn - Fonction à exécuter
 */
function onReady(fn) {
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn); else fn();
}

/**
 * Détermine le type de mécanisme de collapse utilisé
 * @param {Element} root - Élément racine du collapse
 * @returns {string} - 'details', 'checkbox' ou 'focus'
 */
function collapseMethod(root) {
  if (!root) return 'unknown';
  // Collapse basé sur l'élément <details>
  if (root.tagName && root.tagName.toLowerCase() === 'details') return 'details';
  // Collapse basé sur une checkbox
  if (root.querySelector(':scope > input[type="checkbox"]')) return 'checkbox';
  // Collapse basé sur le focus
  return 'focus';
}

/**
 * Vérifie si un collapse est actuellement ouvert
 * @param {Element} root - Élément racine du collapse
 * @returns {boolean}
 */
function isCollapseOpen(root) {
  if (!root) return false;
  // Classe forcée d'ouverture
  if (root.classList.contains('collapse-open')) return true;
  
  const method = collapseMethod(root);
  if (method === 'details') return !!root.open;
  if (method === 'checkbox') {
    const input = root.querySelector(':scope > input[type="checkbox"]');
    return !!(input && input.checked);
  }
  if (method === 'focus') return root.matches(':focus-within');
  return false;
}

/**
 * Vérifie si un élément est à l'intérieur d'un collapse fermé
 * @param {Element} el - Élément à vérifier
 * @returns {boolean}
 */
function isInsideClosedCollapse(el) {
  if (!el) return false;
  
  // Vérification pour les collapses de type details
  const det = el.closest('details.collapse');
  if (det && !det.open && !det.classList.contains('collapse-open')) return true;
  
  // Vérification pour les collapses de type checkbox/focus
  const div = el.closest('div.collapse');
  if (div) {
    if (div.classList.contains('collapse-open')) return false;
    const input = div.querySelector(':scope > input[type="checkbox"]');
    if (input && !input.checked) return true;
  }
  return false;
}

/**
 * Assure le chargement et la disponibilité de CodeMirror
 * @returns {Promise} API de DaisyCodeEditor
 */
async function ensureCodeMirror() {
  if (window.DaisyCodeEditor) return window.DaisyCodeEditor;
  await import('./code-editor');
  return window.DaisyCodeEditor;
}

let trixSetupOnce = false;
/**
 * Assure le chargement et la configuration de Trix
 * @returns {Promise}
 */
async function ensureTrix() {
  await import('trix');
  if (trixSetupOnce) return;
  trixSetupOnce = true;
  
  // Bloque les pièces jointes quand le wrapper l'interdit
  document.addEventListener('trix-file-accept', (event) => {
    try {
      const editor = event.target;
      const wrapper = editor.closest('.trix-wrapper');
      const allow = wrapper && wrapper.getAttribute('data-trix-attachments') === '1';
      if (!allow) event.preventDefault();
    } catch (_) {}
  }, { capture: true });
}

/**
 * Initialise tous les éditeurs présents dans un conteneur donné
 * @param {Element} root - Conteneur racine
 */
async function initEditorsIn(root) {
  if (!root) return;
  
  // Initialisation des éditeurs CodeMirror
  const codeBlocks = Array.from(root.querySelectorAll('.code-editor'));
  if (codeBlocks.length) {
    try {
      const api = await ensureCodeMirror();
      codeBlocks.forEach((el) => api.init(el));
    } catch (_) {}
  }
  
  // Initialisation des éditeurs Trix
  const trixBlocks = Array.from(root.querySelectorAll('.trix-wrapper'));
  if (trixBlocks.length) {
    try {
      await ensureTrix();
      // Révèle les conteneurs différés lorsque demandé par le bouton utilisateur
      trixBlocks.forEach((wrap) => {
        if (wrap.getAttribute('data-trix-deferred') === '1') return; // le flux de bouton gérera cela
        const container = wrap.querySelector('[data-trix-container]');
        if (container) container.classList.remove('hidden');
      });
    } catch (_) {}
  }
}

/**
 * Met en place la surveillance d'un collapse pour initialiser les éditeurs à l'ouverture
 * @param {Element} root - Élément collapse à surveiller
 */
function watchCollapse(root) {
  if (!root || root.__lazyWatch) return;
  root.__lazyWatch = true;
  
  const method = collapseMethod(root);
  const openNow = isCollapseOpen(root);
  
  // Si déjà ouvert, initialise immédiatement
  if (openNow) initEditorsIn(root);
  
  // Configure les écouteurs d'événements selon le type de collapse
  if (method === 'details') {
    root.addEventListener('toggle', () => { if (root.open) initEditorsIn(root); }, { passive: true });
  } else if (method === 'checkbox') {
    const input = root.querySelector(':scope > input[type="checkbox"]');
    if (input) input.addEventListener('change', () => { if (input.checked || root.classList.contains('collapse-open')) initEditorsIn(root); }, { passive: true });
    // Les clics sur le titre basculent la checkbox ; pas besoin de dupliquer
  } else {
    // Pour les collapses basés sur le focus
    root.addEventListener('focusin', () => initEditorsIn(root), { once: true, passive: true });
  }
}

/**
 * Initialise les éditeurs visibles qui ne sont pas dans des collapses fermés
 */
function initVisibleOutsideCollapses() {
  // Initialise les éditeurs visibles qui ne sont pas dans des collapses fermés
  const editors = Array.from(document.querySelectorAll('.code-editor')).filter((el) => !isInsideClosedCollapse(el));
  const trix = Array.from(document.querySelectorAll('trix-editor')).filter((el) => !isInsideClosedCollapse(el));
  
  if (editors.length) ensureCodeMirror().then((api) => editors.forEach((el) => api.init(el))).catch(() => {});
  if (trix.length) ensureTrix().catch(() => {});
}

// Initialisation au chargement du DOM
onReady(() => {
  // Surveille tous les composants collapse
  document.querySelectorAll('.collapse').forEach(watchCollapse);
  document.querySelectorAll('details.collapse').forEach(watchCollapse);
  
  // Passe initiale pour les éditeurs visibles
  initVisibleOutsideCollapses();
  
  // Initialisation Trix déclenchée par bouton
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-trix-init-button]');
    if (!btn) return;
    const wrapper = btn.closest('.trix-wrapper');
    if (!wrapper) return;
    
    try {
      await ensureTrix();
      const container = wrapper.querySelector('[data-trix-container]');
      if (container) container.classList.remove('hidden');
      wrapper.removeAttribute('data-trix-deferred');
      btn.closest('div')?.remove();
    } catch (_) {}
  });
});
