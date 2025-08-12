/**
 * Daisy Kit - Popover
 *
 * Ce module gère l'affichage d'un popover contextuel, avec différents modes d'ouverture :
 * - 'click' (par défaut) : ouverture/fermeture au clic sur le déclencheur
 * - 'hover' : ouverture au survol, fermeture à la sortie
 * - 'focus' : ouverture au focus, fermeture au blur
 *
 * Structure HTML requise :
 * <div data-popover ...>
 *   <button class="popover-trigger">...</button>
 *   <div class="popover-panel hidden">...</div>
 * </div>
 *
 * Le panneau doit être masqué par défaut via la classe 'hidden'.
 * Ce script est conçu pour être utilisé dans le package Daisy Kit.
 */

/**
 * Masque tous les popovers ouverts sauf éventuellement un panneau d'exception.
 * Permet de garantir qu'un seul popover est ouvert à la fois sur la page.
 * @param {HTMLElement|null} exceptionPanel - Le panneau à ne pas masquer (optionnel)
 */
function hideAllPopoversExcept(exceptionPanel) {
  document.querySelectorAll('.popover-panel').forEach((panel) => {
    if (panel !== exceptionPanel) panel.classList.add('hidden');
  });
}

/**
 * Initialise le comportement d'un popover sur un élément racine donné.
 * @param {HTMLElement} root - Élément racine portant l'attribut [data-popover]
 */
function setupPopover(root) {
  // Sélectionne le déclencheur et le panneau du popover
  const trigger = root.querySelector('.popover-trigger');
  const panel = root.querySelector('.popover-panel');
  if (!trigger || !panel) return; // Si l'un des deux est absent, on ne fait rien

  // Détermine le mode d'ouverture du popover ('click', 'hover', 'focus')
  const triggerMode = root.getAttribute('data-trigger') || 'click';

  /**
   * Ouvre le popover (affiche le panneau) et ferme les autres popovers ouverts.
   */
  const open = () => {
    hideAllPopoversExcept(panel);
    panel.classList.remove('hidden');
  };

  /**
   * Ferme le popover (masque le panneau).
   */
  const close = () => {
    panel.classList.add('hidden');
  };

  /**
   * Bascule l'état du popover (ouvre si fermé, ferme si ouvert).
   */
  const toggle = () => {
    const hidden = panel.classList.contains('hidden');
    if (hidden) open(); else close();
  };

  /**
   * Gestionnaire pour fermer le popover si clic à l'extérieur du composant.
   * Utilisé uniquement en mode 'click'.
   * @param {MouseEvent} e
   */
  const onOutside = (e) => {
    if (!root.contains(e.target)) {
      close();
      if (triggerMode === 'click') {
        document.removeEventListener('click', onOutside, { capture: true });
      }
    }
  };

  // Gestion du mode d'ouverture selon le triggerMode
  if (triggerMode === 'click') {
    // Mode clic : ouverture/fermeture au clic sur le déclencheur
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      toggle();
      // Si le panneau vient d'être ouvert, on écoute les clics extérieurs pour le refermer
      if (!panel.classList.contains('hidden')) {
        document.addEventListener('click', onOutside, { capture: true });
      }
    });
  } else if (triggerMode === 'hover') {
    // Mode survol : ouverture au mouseenter, fermeture au mouseleave (avec délai pour éviter les fermetures accidentelles)
    let hoverTimeout;
    const enter = () => {
      clearTimeout(hoverTimeout);
      open();
    };
    const leave = () => {
      hoverTimeout = setTimeout(close, 120);
    };
    root.addEventListener('mouseenter', enter);
    root.addEventListener('mouseleave', leave);
  } else if (triggerMode === 'focus') {
    // Mode focus : ouverture au focus, fermeture au blur
    trigger.addEventListener('focus', open);
    trigger.addEventListener('blur', close);
  }

  // Accessibilité : fermeture du popover à la touche "Escape"
  root.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}

// Initialisation automatique de tous les popovers présents dans le DOM au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-popover]').forEach(setupPopover);
});
