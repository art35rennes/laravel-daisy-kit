/**
 * Daisy Kit - Media Gallery
 *
 * Galerie d'images avec vignettes synchronisées, activation au clic ou survol,
 * et option de zoom au survol sur l'image principale.
 *
 * Data-attributes attendus :
 * - [data-media-gallery="1"] : élément racine de la galerie
 * - [data-main] : image principale affichée
 * - [data-main-wrapper] : conteneur de l'image principale (pour le zoom)
 * - [data-thumb][data-index] : vignettes cliquables, chaque vignette doit avoir un index unique
 * - data-activation : 'click' (par défaut) ou 'mouseenter' pour changer l'image principale
 * - data-zoom : 'true' pour activer le zoom au survol sur l'image principale
 * - Template JSON [template[data-items]] : liste des images [{ src, thumb, alt }]
 *
 * API exposée : window.DaisyMediaGallery.{ init(root), initAll() }
 */

/**
 * Initialise une galerie média sur un élément racine donné.
 * @param {HTMLElement} root - Élément racine de la galerie
 */
function initMediaGallery(root) {
  // Vérifie si l'élément est valide et n'a pas déjà été initialisé
  if (!root || root.__mgInit) return;
  root.__mgInit = true;

  // Récupère la liste des items (images) à partir du template JSON
  const items = (() => {
    try {
      return JSON.parse(root.querySelector('template[data-items]')?.innerHTML || '[]');
    } catch(_) {
      return [];
    }
  })();

  // Sélectionne l'image principale, les vignettes et le wrapper pour le zoom
  const main = root.querySelector('[data-main]');
  const thumbs = root.querySelectorAll('[data-thumb]');
  const wrapper = root.querySelector('[data-main-wrapper]');

  // Détermine le mode d'activation (clic ou survol)
  const activation = root.dataset.activation || 'click';

  // Détermine si le zoom est activé
  const zoom = root.dataset.zoom === 'true';

  /**
   * Met à jour l'image principale et l'état actif des vignettes.
   * @param {number} idx - Index de l'image à activer
   */
  function setActive(idx) {
    const it = items[idx];
    if (!it || !main) return;
    // Met à jour la source et l'alternative de l'image principale
    main.src = it.src;
    main.alt = it.alt || '';
    // Retire la bordure active de toutes les vignettes
    thumbs.forEach((t) => t.classList.remove('border-primary'));
    // Ajoute la bordure active à la vignette sélectionnée
    const btn = root.querySelector(`[data-thumb][data-index="${idx}"]`);
    btn?.classList.add('border-primary');
  }

  // Ajoute les gestionnaires d'événements sur chaque vignette
  thumbs.forEach((btn) => {
    // Récupère l'index de la vignette
    const idx = parseInt(btn.dataset.index || '0', 10);
    // Handler pour activer l'image correspondante
    const handler = () => setActive(idx);
    // Selon le mode d'activation, ajoute l'événement approprié
    if (activation === 'mouseenter') {
      btn.addEventListener('mouseenter', handler);
    } else {
      btn.addEventListener('click', handler);
    }
  });

  // Gestion du zoom au survol sur l'image principale
  if (zoom && wrapper && main) {
    let scale = 1.4; // Facteur de zoom simple
    // Lors du déplacement de la souris, ajuste l'origine du zoom pour suivre le curseur
    wrapper.addEventListener('mousemove', (e) => {
      const rect = wrapper.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      main.style.transformOrigin = `${x}% ${y}%`;
      main.style.transform = `scale(${scale})`;
    });
    // Réinitialise le zoom lorsque la souris quitte la zone
    wrapper.addEventListener('mouseleave', () => {
      main.style.transform = 'scale(1)';
    });
  }
}

/**
 * Initialise toutes les galeries médias présentes dans le document.
 */
function initAllMediaGalleries() {
  document.querySelectorAll('[data-media-gallery="1"]').forEach(initMediaGallery);
}

// Expose l'API globale pour usage externe
window.DaisyMediaGallery = {
  init: initMediaGallery,
  initAll: initAllMediaGalleries
};

// Export pour le système data-module (kit/index.js)
export default initMediaGallery;
export { initMediaGallery, initAllMediaGalleries };

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllMediaGalleries);
} else {
  initAllMediaGalleries();
}