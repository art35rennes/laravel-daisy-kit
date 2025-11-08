/*
 * Daisy Kit - Lightbox (album) avec navigation, zoom et fullscreen
 * API: window.DaisyLightbox
 * - init(root)
 * - open(root, index)
 * - close(root)
 * - next(root)
 * - prev(root)
 * - zoomIn/zoomOut/reset
 * Evénements: 'lightbox:open', 'lightbox:close', 'lightbox:slide', 'lightbox:zoom'
 *
 * Data-attributes:
 * - [data-lightbox="1"] root, [data-overlay], [data-stage], [data-image], [data-caption], [data-counter]
 * - [data-item][data-index] sur les thumbs
 * - data-loop, data-zoom, data-fullscreen, data-keyboard (true|false)
 *
 * Ce module gère l'affichage d'une lightbox d'images avec navigation, zoom, gestion du focus, et support du clavier.
 * Il est conçu pour être utilisé comme composant réutilisable dans le package Daisy Kit.
 */

/**
 * Parse les items (images) à afficher dans la lightbox à partir d'un template JSON.
 * @param {HTMLElement} root - Élément racine du lightbox
 * @returns {Array} - Liste des items (images)
 */
function parseItems(root) {
  try {
    const tpl = root.querySelector('[data-overlay] template[data-items]');
    const txt = tpl?.innerHTML || '[]';
    return JSON.parse(txt);
  } catch (_) { return []; }
}

/**
 * Affiche l'image à l'index donné dans la lightbox, met à jour l'état et l'UI.
 * @param {HTMLElement} root
 * @param {number} idx
 */
function show(root, idx) {
  const items = root.__items || [];
  if (!items.length) return;
  // Clamp l'index dans les bornes
  if (idx < 0) idx = 0;
  if (idx >= items.length) idx = items.length - 1;
  root.__index = idx;
  const overlay = root.querySelector('[data-overlay]');
  const img = root.querySelector('[data-image]');
  const cap = root.querySelector('[data-caption]');
  const counter = root.querySelector('[data-counter]');
  const it = items[idx];
  // Mise à jour de l'image et des infos associées
  img.src = it.src; img.alt = it.alt || '';
  if (cap) cap.textContent = it.caption || '';
  if (counter) counter.textContent = `${idx + 1} / ${items.length}`;
  overlay.classList.remove('hidden');
  overlay.removeAttribute('aria-hidden');
  // Empêcher le scroll du body pendant l'ouverture de la lightbox
  document.documentElement.classList.add('overflow-hidden');
  document.body.classList.add('overflow-hidden');
  resetTransform(root);
  // Focus trap : focus sur le bouton de fermeture ou l'overlay
  const focusEl = root.querySelector('[data-close]') || overlay;
  focusEl?.focus?.();
  // Sauvegarde de l'élément actif pour restauration au close
  root.__lastActive = document.activeElement;
  // Préchargement des images voisines pour une navigation fluide
  preloadNeighbors(root);
  // Événement d'ouverture
  root.dispatchEvent(new CustomEvent('lightbox:open', { detail: { index: idx }, bubbles: true }));
}

/**
 * Ferme la lightbox et restaure l'état du document.
 * @param {HTMLElement} root
 */
function hide(root) {
  const ov = root.querySelector('[data-overlay]');
  if (!ov) return;
  ov.classList.add('hidden');
  ov.setAttribute('aria-hidden', 'true');
  document.documentElement.classList.remove('overflow-hidden');
  document.body.classList.remove('overflow-hidden');
  // Restaure le focus sur l'élément précédemment actif
  try { root.__lastActive?.focus?.(); } catch (_) {}
  // Événement de fermeture
  root.dispatchEvent(new CustomEvent('lightbox:close', { bubbles: true }));
}

/**
 * Passe à l'image suivante dans la lightbox.
 * @param {HTMLElement} root
 */
function next(root) {
  const items = root.__items || [];
  if (!items.length) return;
  let idx = (root.__index ?? 0) + 1;
  // Si loop activé, revient au début
  if (idx >= items.length) idx = (root.dataset.loop === 'true') ? 0 : items.length - 1;
  show(root, idx);
  // Événement de changement de slide
  root.dispatchEvent(new CustomEvent('lightbox:slide', { detail: { index: idx }, bubbles: true }));
}

/**
 * Passe à l'image précédente dans la lightbox.
 * @param {HTMLElement} root
 */
function prev(root) {
  const items = root.__items || [];
  if (!items.length) return;
  let idx = (root.__index ?? 0) - 1;
  // Si loop activé, revient à la fin
  if (idx < 0) idx = (root.dataset.loop === 'true') ? items.length - 1 : 0;
  show(root, idx);
  // Événement de changement de slide
  root.dispatchEvent(new CustomEvent('lightbox:slide', { detail: { index: idx }, bubbles: true }));
}

/**
 * Réinitialise le zoom et la position de l'image.
 * @param {HTMLElement} root
 */
function resetTransform(root) {
  root.__scale = 1; root.__tx = 0; root.__ty = 0;
  applyTransform(root);
}

/**
 * Applique la transformation CSS (zoom et déplacement) à l'image.
 * @param {HTMLElement} root
 */
function applyTransform(root) {
  const img = root.querySelector('[data-image]');
  const s = root.__scale || 1, x = root.__tx || 0, y = root.__ty || 0;
  img.style.transform = `translate(${x}px, ${y}px) scale(${s})`;
  img.style.cursor = s > 1 ? 'grab' : 'default';
  // Événement de zoom
  root.dispatchEvent(new CustomEvent('lightbox:zoom', { detail: { scale: s }, bubbles: true }));
}

/**
 * Zoom avant sur l'image.
 * @param {HTMLElement} root
 * @param {number} step
 */
function zoomIn(root, step = 0.2) {
  root.__scale = Math.min(5, (root.__scale || 1) + step);
  applyTransform(root);
}

/**
 * Zoom arrière sur l'image.
 * @param {HTMLElement} root
 * @param {number} step
 */
function zoomOut(root, step = 0.2) {
  root.__scale = Math.max(0.2, (root.__scale || 1) - step);
  applyTransform(root);
}

/**
 * Gère les interactions gestuelles : drag, zoom, swipe, double-tap.
 * @param {HTMLElement} root
 */
function bindGestures(root) {
  const stage = root.querySelector('[data-stage]');
  const img = root.querySelector('[data-image]');
  let dragging = false; let sx = 0; let sy = 0;
  let touchStartX = 0; let touchStartY = 0; let lastTap = 0;

  // Drag souris pour déplacer l'image si zoomée
  img.addEventListener('mousedown', (e) => {
    if ((root.__scale || 1) <= 1) return;
    dragging = true; sx = e.clientX; sy = e.clientY; img.style.cursor = 'grabbing';
  });

  // Déplacement de l'image lors du drag
  window.addEventListener('mousemove', (e) => {
    if (!dragging) return;
    const dx = e.clientX - sx; const dy = e.clientY - sy; sx = e.clientX; sy = e.clientY;
    root.__tx = (root.__tx || 0) + dx; root.__ty = (root.__ty || 0) + dy; applyTransform(root);
  });

  // Fin du drag
  window.addEventListener('mouseup', () => {
    if (dragging) { dragging = false; img.style.cursor = 'grab'; }
  });

  // Zoom à la molette
  stage.addEventListener('wheel', (e) => {
    if (root.dataset.zoom !== 'true') return;
    e.preventDefault();
    if (e.deltaY < 0) zoomIn(root, 0.1); else zoomOut(root, 0.1);
  }, { passive: false });

  // Double-clic pour zoomer/réinitialiser
  stage.addEventListener('dblclick', () => {
    if (root.dataset.zoom !== 'true') return;
    if ((root.__scale || 1) > 1) { resetTransform(root); } else { zoomIn(root, 1); }
  });

  // Touch : swipe pour navigation, double-tap pour zoom
  stage.addEventListener('touchstart', (e) => {
    if (!e.touches?.length) return;
    touchStartX = e.touches[0].clientX; touchStartY = e.touches[0].clientY;
    const now = Date.now();
    if (now - lastTap < 300) {
      // Double tap : zoom ou reset
      if ((root.__scale || 1) > 1) { resetTransform(root); } else { zoomIn(root, 1); }
    }
    lastTap = now;
  }, { passive: true });

  // Fin du swipe : navigation gauche/droite
  stage.addEventListener('touchend', (e) => {
    const endX = (e.changedTouches?.[0]?.clientX) ?? touchStartX;
    const endY = (e.changedTouches?.[0]?.clientY) ?? touchStartY;
    const dx = endX - touchStartX; const dy = endY - touchStartY;
    if (Math.abs(dx) > 50 && Math.abs(dy) < 40) {
      if (dx < 0) next(root); else prev(root);
    }
  }, { passive: true });
}

/**
 * Active/désactive le mode plein écran sur la lightbox.
 * @param {HTMLElement} root
 */
function toggleFullscreen(root) {
  if (document.fullscreenElement) { document.exitFullscreen?.(); return; }
  root.querySelector('[data-overlay]')?.requestFullscreen?.();
}

/**
 * Attache les contrôles (boutons, overlay, clavier) à la lightbox.
 * @param {HTMLElement} root
 */
function attachControls(root) {
  const overlay = root.querySelector('[data-overlay]');
  // Ferme la lightbox si clic sur l'overlay (hors image)
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) hide(root);
  });
  // Boutons de contrôle
  root.querySelector('[data-close]')?.addEventListener('click', () => hide(root));
  root.querySelector('[data-next]')?.addEventListener('click', () => next(root));
  root.querySelector('[data-prev]')?.addEventListener('click', () => prev(root));
  root.querySelector('[data-zoom-in]')?.addEventListener('click', () => zoomIn(root));
  root.querySelector('[data-zoom-out]')?.addEventListener('click', () => zoomOut(root));
  root.querySelector('[data-zoom-reset]')?.addEventListener('click', () => resetTransform(root));
  root.querySelector('[data-fullscreen]')?.addEventListener('click', () => toggleFullscreen(root));

  // Navigation clavier si activée
  if (root.dataset.keyboard === 'true') {
    document.addEventListener('keydown', (e) => {
      if (overlay.classList.contains('hidden')) return;
      if (e.key === 'Escape') hide(root);
      if (e.key === 'ArrowRight') next(root);
      if (e.key === 'ArrowLeft') prev(root);
      if ((e.key === '+' || e.key === '=') && root.dataset.zoom === 'true') zoomIn(root);
      if ((e.key === '-' || e.key === '_') && root.dataset.zoom === 'true') zoomOut(root);
      if (e.key === '0' && root.dataset.zoom === 'true') resetTransform(root);
      if (e.key.toLowerCase() === 'f' && root.dataset.fullscreen === 'true') toggleFullscreen(root);
    });
  }

  // Focus trap : empêche de sortir du modal avec Tab
  overlay.addEventListener('keydown', (e) => {
    if (e.key !== 'Tab') return;
    const focusables = overlay.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');
    const list = Array.from(focusables).filter((el) => !el.hasAttribute('disabled'));
    if (!list.length) return;
    const first = list[0];
    const last = list[list.length - 1];
    if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
    else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
  });
}

/**
 * Attache les événements sur les miniatures (thumbnails) pour ouvrir la lightbox à l'index correspondant.
 * @param {HTMLElement} root
 */
function bindThumbs(root) {
  root.querySelectorAll('[data-item]')?.forEach((btn) => {
    btn.addEventListener('click', () => {
      const idx = parseInt(btn.dataset.index || '0', 10);
      show(root, idx);
    });
  });
}

/**
 * Initialise la lightbox sur un élément racine donné.
 * @param {HTMLElement} root
 */
function init(root) {
  if (!root || root.__lbInit) return;
  root.__lbInit = true;
  root.__items = parseItems(root);
  root.__index = 0;
  resetTransform(root);
  attachControls(root);
  bindThumbs(root);
  bindGestures(root);
}

/**
 * Initialise toutes les lightbox présentes dans le document.
 */
function initAll() {
  document.querySelectorAll('[data-lightbox="1"]').forEach(init);
}

/**
 * Précharge les images voisines (précédente et suivante) pour une navigation fluide.
 * @param {HTMLElement} root
 */
function preloadNeighbors(root) {
  const items = root.__items || [];
  if (!items.length) return;
  const i = root.__index ?? 0;
  [i - 1, i + 1].forEach((k) => {
    if (k < 0 || k >= items.length) return;
    const src = items[k]?.src;
    if (!src) return;
    const image = new Image();
    image.src = src;
  });
}

// Expose l'API globale DaisyLightbox
window.DaisyLightbox = {
  init,
  initAll,
  open: (r, i) => show(r, i || 0),
  close: hide,
  next,
  prev,
  zoomIn,
  zoomOut,
  reset: resetTransform
};

// Export pour le système data-module (kit/index.js)
export default init;
export { init, initAll };

// Initialisation automatique (aussi si importé après DOMContentLoaded)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
