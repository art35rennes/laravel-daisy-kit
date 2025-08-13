/**
 * Daisy Kit - Stepper
 *
 * Composant de navigation par étapes (stepper), supportant les modes linéaire ou non, la persistance de l'étape courante, et une API simple.
 *
 * Data-attributes (à placer sur l'élément racine [data-stepper="true"]):
 * - data-current: index 1..N de l'étape actuellement active (défaut: 1)
 * - data-linear: 'true' => progression linéaire obligatoire (on ne peut avancer qu'à l'étape suivante disponible)
 * - data-allow-click: 'true' => permet de cliquer sur l'entête d'une étape pour la sélectionner
 * - data-persist: 'true' => persiste l'étape courante dans sessionStorage (si root possède un id)
 *
 * Slots requis dans le HTML :
 * - [data-stepper-headers] : conteneur des entêtes d'étapes, chaque étape ayant [data-step-index]
 * - [data-stepper-contents] : conteneur des panneaux de contenu, chaque panneau ayant [data-step-content][data-step-index]
 *
 * API JS globale : window.DaisyStepper.{ init, initAll, setCurrent(root, index) }
 */

/**
 * Récupère l'index de l'étape courante à partir du data-attribute, ou retourne 1 par défaut.
 * @param {HTMLElement} root
 * @returns {number}
 */
function getCurrent(root) {
  const cur = parseInt(root.dataset.current || '1', 10);
  return Number.isFinite(cur) && cur > 0 ? cur : 1;
}

/**
 * Définit l'étape courante, met à jour l'affichage des entêtes, des panneaux, des boutons, et persiste si besoin.
 * Déclenche l'événement 'stepper:change'.
 * @param {HTMLElement} root
 * @param {number} index
 */
function setCurrent(root, index) {
  const headers = root.querySelector('[data-stepper-headers]');
  const contents = root.querySelector('[data-stepper-contents]');
  if (!headers || !contents) return;

  // Récupère toutes les étapes (entêtes) et panneaux de contenu
  const steps = Array.from(headers.querySelectorAll('[data-step-index]'));
  const panels = Array.from(contents.querySelectorAll('[data-step-content]'));
  const max = steps.length;
  // Clamp l'index entre 1 et max
  const i = Math.min(Math.max(1, index), max);

  // Met à jour l'index courant dans le data-attribute
  root.dataset.current = String(i);

  // Met à jour l'état visuel et ARIA des entêtes (step-primary = étape atteinte)
  steps.forEach((li, idx) => {
    const sIdx = idx + 1;
    const done = sIdx <= i;
    li.classList.toggle('step-primary', done);
    // aria-current sur l'étape active
    if (sIdx === i) li.setAttribute('aria-current', 'step');
    else li.removeAttribute('aria-current');
  });

  // Affiche le panneau correspondant à l'étape courante, masque les autres + ARIA
  panels.forEach((p) => {
    const sIdx = parseInt(p.dataset.stepIndex || '0', 10);
    const isActive = sIdx === i;
    p.classList.toggle('hidden', !isActive);
    p.setAttribute('aria-hidden', isActive ? 'false' : 'true');
  });

  // Met à jour l'état des boutons de navigation (précédent, suivant, terminer)
  const btnPrev = root.querySelector('[data-stepper-prev]');
  const btnNext = root.querySelector('[data-stepper-next]');
  const btnFinish = root.querySelector('[data-stepper-finish]');
  if (btnPrev) btnPrev.toggleAttribute('disabled', i <= 1);
  if (btnNext) btnNext.classList.toggle('hidden', i >= max);
  if (btnFinish) btnFinish.classList.toggle('hidden', i < max);

  // Persistance de l'étape courante dans sessionStorage si demandé
  if (root.dataset.persist === 'true' && root.id) {
    try { sessionStorage.setItem('stepper:'+root.id, String(i)); } catch(_) {}
  }

  // Déclenche un événement personnalisé pour signaler le changement d'étape
  root.dispatchEvent(new CustomEvent('stepper:change', { detail: { current: i }, bubbles: true }));
}

/**
 * Restaure l'étape courante depuis sessionStorage si la persistance est activée, sinon utilise l'attribut data-current.
 * @param {HTMLElement} root
 */
function restore(root) {
  if (root.dataset.persist === 'true' && root.id) {
    try {
      const v = sessionStorage.getItem('stepper:'+root.id);
      if (v) setCurrent(root, parseInt(v, 10));
    } catch(_) {}
  } else {
    setCurrent(root, getCurrent(root));
  }
}

/**
 * Retourne la liste des éléments d'entête d'étape ([data-step-index]) pour un stepper donné.
 * @param {HTMLElement} root
 * @returns {HTMLElement[]}
 */
function getSteps(root) {
  const headers = root.querySelector('[data-stepper-headers]');
  return headers ? Array.from(headers.querySelectorAll('[data-step-index]')) : [];
}

/**
 * Indique si une étape (entête) est désactivée (par convention via la classe pointer-events-none).
 * @param {HTMLElement} li
 * @returns {boolean}
 */
function isDisabledStep(li) {
  return li.classList.contains('pointer-events-none');
}

/**
 * Calcule l'index de la prochaine étape disponible (non désactivée) dans la direction donnée.
 * Si aucune étape n'est disponible, retourne l'index courant.
 * @param {HTMLElement} root
 * @param {number} dir - direction (+1 pour suivant, -1 pour précédent)
 * @returns {number}
 */
function nextAvailableIndex(root, dir) {
  const steps = getSteps(root);
  const cur = getCurrent(root);
  let i = cur + dir;
  while (i >= 1 && i <= steps.length) {
    const li = steps[i - 1];
    if (!isDisabledStep(li)) return i;
    i += dir;
  }
  return cur;
}

/**
 * Détermine si l'on peut naviguer vers une étape donnée.
 * - En mode linéaire : on ne peut avancer que d'une étape à la fois (sauf retour libre), et seulement sur une étape non désactivée.
 * - En mode non-linéaire : navigation libre.
 * @param {HTMLElement} root
 * @param {number} targetIndex
 * @returns {boolean}
 */
function canGoTo(root, targetIndex) {
  const linear = root.dataset.linear === 'true';
  if (!linear) return true;
  const cur = getCurrent(root);
  // En linéaire: retour libre, avance seulement d'une étape valide (ignore les étapes désactivées)
  if (targetIndex <= cur) return true;
  const allowedForward = nextAvailableIndex(root, 1);
  return targetIndex === allowedForward;
}

/**
 * Initialise le stepper sur un élément racine donné.
 * - Restaure l'étape courante (persistance ou data-current)
 * - Ajoute les gestionnaires d'événements pour la navigation (clic sur entête, boutons)
 * @param {HTMLElement} root
 */
function init(root) {
  if (!root || root.__stepperInit) return; // Empêche double initialisation
  root.__stepperInit = true;
  restore(root);

  const headers = root.querySelector('[data-stepper-headers]');
  const contents = root.querySelector('[data-stepper-contents]');
  if (!headers || !contents) return;

  // Navigation par clic sur les entêtes d'étape (si autorisé)
  headers.addEventListener('click', (e) => {
    const target = e.target;
    const li = target && typeof target.closest === 'function' ? target.closest('[data-step-index]') : null;
    if (!li || !headers.contains(li)) return;
    if (root.dataset.allowClick !== 'true') return;
    if (isDisabledStep(li)) return;
    const idx = parseInt(li.dataset.stepIndex || '0', 10);
    if (!idx) return;
    if (!canGoTo(root, idx)) return;
    setCurrent(root, idx);
  });

  // Navigation clavier (Enter/Space) sur les entêtes
  headers.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar') return;
    const target = e.target;
    const li = target && typeof target.closest === 'function' ? target.closest('[data-step-index]') : null;
    if (!li || !headers.contains(li)) return;
    if (root.dataset.allowClick !== 'true') return;
    if (isDisabledStep(li)) return;
    const idx = parseInt(li.dataset.stepIndex || '0', 10);
    if (!idx) return;
    if (!canGoTo(root, idx)) return;
    e.preventDefault();
    setCurrent(root, idx);
  });

  // Gestion des boutons de navigation (précédent, suivant, terminer)
  const btnPrev = root.querySelector('[data-stepper-prev]');
  const btnNext = root.querySelector('[data-stepper-next]');
  const btnFinish = root.querySelector('[data-stepper-finish]');
  if (btnPrev) btnPrev.addEventListener('click', () => {
    const target = nextAvailableIndex(root, -1);
    if (target !== getCurrent(root)) setCurrent(root, target);
  });
  if (btnNext) btnNext.addEventListener('click', () => {
    const target = nextAvailableIndex(root, 1);
    if (target !== getCurrent(root)) setCurrent(root, target);
  });
  if (btnFinish) btnFinish.addEventListener('click', () => {
    // Déclenche un événement personnalisé pour signaler la fin du stepper
    root.dispatchEvent(new CustomEvent('stepper:finish', { bubbles: true }));
  });
}

/**
 * Initialise tous les steppers présents dans le document (ayant [data-stepper="true"]).
 */
function initAll() {
  document.querySelectorAll('[data-stepper]').forEach(init);
}

// Expose l'API globale DaisyStepper
window.DaisyStepper = { init, initAll, setCurrent };

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
