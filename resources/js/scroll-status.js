/**
 * Daisy Kit - Scroll Status
 *
 * Barre de progression indiquant le niveau de scroll d'un conteneur ou de la page.
 *
 * Data-attributes attendus :
 * - [data-scrollstatus="1"] : active le composant sur l'élément
 * - data-global : 'true' => barre globale (window), sinon cherche un conteneur scrollable
 * - data-container : sélecteur explicite du conteneur scrollable (optionnel)
 * - data-color : couleur CSS de la barre de progression
 * - data-height : hauteur CSS de la barre (ex: '6px')
 * - data-offset : décalage top en px (utile pour header sticky)
 * - data-scroll : seuil (%) à partir duquel déclencher une modale (optionnel)
 * - data-target : sélecteur d'un <dialog> à ouvrir au dépassement (optionnel)
 * - data-open-once : 'false' pour permettre plusieurs ouvertures de modal (par défaut : une seule ouverture)
 *
 * API globale exposée : window.DaisyScrollStatus.{ init, initAll, dispose }
 */

/**
 * Détermine le conteneur scrollable à surveiller pour la barre de progression.
 * - Si data-global="true", on utilise window (scroll global).
 * - Si data-container est défini, on tente de sélectionner ce conteneur.
 * - Sinon, on cherche le plus proche ancêtre scrollable (overflowY: auto|scroll).
 * @param {HTMLElement} el - Élément racine de la barre de progression
 * @returns {HTMLElement|Window} - Le conteneur scrollable ou window
 */
function resolveContainer(el) {
  if (el.dataset.global === 'true') return window;
  const sel = el.dataset.container;
  if (sel) {
    try {
      const found = document.querySelector(sel);
      if (found) return found;
    } catch (_) {}
  }
  // Recherche ascendante du plus proche ancêtre scrollable
  let node = el.parentElement;
  while (node) {
    const style = getComputedStyle(node);
    const overflowY = style.overflowY;
    if ((overflowY === 'auto' || overflowY === 'scroll') && node.scrollHeight > node.clientHeight) return node;
    node = node.parentElement;
  }
  // Par défaut, on retourne window (scroll global)
  return window;
}

/**
 * Récupère les métriques de défilement normalisées pour un conteneur donné.
 * - Pour window : scrollY, hauteur totale du document, hauteur du viewport.
 * - Pour un élément : scrollTop, scrollHeight, clientHeight.
 * @param {HTMLElement|Window} container
 * @returns {{scrollTop: number, scrollHeight: number, clientHeight: number}}
 */
function getScrollMetrics(container) {
  if (container === window) {
    // Calculs robustes pour la compatibilité navigateurs
    const scrollTop = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;
    const scrollHeight = Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.offsetHeight,
      document.body.clientHeight,
      document.documentElement.clientHeight
    );
    const clientHeight = window.innerHeight || document.documentElement.clientHeight;
    return { scrollTop, scrollHeight, clientHeight };
  }
  // Cas d'un élément scrollable
  return {
    scrollTop: container.scrollTop,
    scrollHeight: container.scrollHeight,
    clientHeight: container.clientHeight
  };
}

/**
 * Applique les styles de base sur la barre de progression et crée l'élément de progression interne si besoin.
 * @param {HTMLElement} root - Élément racine de la barre
 */
function setStyles(root) {
  // Récupération des options via data-attributes ou valeurs par défaut
  const height = root.getAttribute('data-height') || '10px';
  const color = root.getAttribute('data-color') || '#3b82f6'; // Bleu Tailwind 500 par défaut
  const offset = parseInt(root.getAttribute('data-offset') || '0', 10) || 0;
  const global = root.getAttribute('data-global') === 'true';

  // Positionnement : fixed (barre globale) ou sticky (barre locale)
  root.style.position = global ? 'fixed' : 'sticky';
  root.style.top = offset + 'px';
  root.style.left = '0';
  root.style.right = '0';
  root.style.height = height;
  root.style.zIndex = '40'; // S'assure que la barre reste au-dessus du contenu
  root.style.background = 'transparent';

  // Création ou récupération de l'élément de progression interne
  const bar = root.querySelector('[data-scrollstatus-progress]') || (() => {
    const d = document.createElement('div');
    d.setAttribute('data-scrollstatus-progress', '');
    root.appendChild(d);
    return d;
  })();

  // Styles de la barre de progression
  bar.style.height = '100%';
  bar.style.width = '0%'; // Initialement vide
  bar.style.background = color;
  bar.style.transition = 'width 80ms linear'; // Animation fluide lors du scroll
}

/**
 * Initialise une instance de Scroll Status sur un élément donné.
 * - Applique les styles, détecte le conteneur, gère la progression et l'ouverture de modale éventuelle.
 * @param {HTMLElement} root - Élément racine de la barre
 */
function init(root) {
  if (!root || root.__ssInit) return; // Empêche double initialisation
  root.__ssInit = true;

  setStyles(root);

  // Détermination du conteneur scrollable à surveiller
  const container = resolveContainer(root);

  // Options de déclenchement de modale
  const openOnce = root.getAttribute('data-open-once') !== 'false'; // true par défaut
  const targetSel = root.getAttribute('data-target') || ''; // Sélecteur de la modale à ouvrir
  const threshold = parseFloat(root.getAttribute('data-scroll') || '0'); // Seuil en pourcentage (0..100)
  let openedOnce = false; // Indique si la modale a déjà été ouverte

  /**
   * Met à jour la largeur de la barre de progression en fonction du scroll.
   * Déclenche la modale si le seuil est dépassé.
   */
  function update() {
    // Récupération des métriques de scroll
    const { scrollTop, scrollHeight, clientHeight } = getScrollMetrics(container);
    // Calcul du pourcentage de scroll (évite division par zéro)
    const denom = Math.max(1, scrollHeight - clientHeight);
    const pct = Math.max(0, Math.min(100, (scrollTop / denom) * 100));
    // Mise à jour de la largeur de la barre
    const bar = root.querySelector('[data-scrollstatus-progress]');
    if (bar) bar.style.width = pct.toFixed(2) + '%';

    // Gestion de l'ouverture de la modale si seuil atteint
    if (threshold > 0 && targetSel) {
      // Si le seuil est dépassé et qu'on n'a pas déjà ouvert (ou openOnce=false)
      if (pct >= threshold && (!openedOnce || !openOnce)) {
        try {
          // Sélectionne la modale par sélecteur CSS ou par ID
          const dlg = document.querySelector(targetSel) || document.getElementById(targetSel.replace(/^#/, ''));
          if (dlg && typeof dlg.showModal === 'function') dlg.showModal();
          openedOnce = true;
        } catch (_) {}
      }
    }
  }

  // Ajout des écouteurs sur le conteneur scrollable et sur le resize fenêtre
  const scrollEl = container === window ? window : container;
  scrollEl.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);

  // Permet de débrancher proprement les écouteurs (pour dispose)
  root.__ssOff = () => {
    scrollEl.removeEventListener('scroll', update);
    window.removeEventListener('resize', update);
  };

  // Mise à jour initiale de la barre
  update();
}

/**
 * Initialise toutes les barres de progression présentes dans le DOM.
 * À appeler au chargement de la page ou après un rendu dynamique.
 */
function initAll() {
  document.querySelectorAll('[data-scrollstatus="1"]').forEach(init);
}

/**
 * Débranche les écouteurs et réinitialise l'état d'une barre donnée.
 * À utiliser avant suppression du DOM ou re-initialisation.
 * @param {HTMLElement} root
 */
function dispose(root) {
  if (root?.__ssOff) {
    try { root.__ssOff(); } catch (_) {}
    root.__ssOff = null;
  }
  root.__ssInit = false;
}

// Exposition de l'API globale pour usage externe
window.DaisyScrollStatus = { init, initAll, dispose };

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
