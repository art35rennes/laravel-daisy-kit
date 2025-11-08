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
  const isCoarsePointer = (('maxTouchPoints' in navigator && navigator.maxTouchPoints > 0) ||
    (window.matchMedia && window.matchMedia('(pointer: coarse)').matches));
  let pinnedByClick = false;

  // Mappings de classes utilitaires par position
  const POSITION_CLASSES = {
    top: ['bottom-full', 'left-1/2', '-translate-x-1/2', 'mb-2'],
    right: ['left-full', 'top-1/2', '-translate-y-1/2', 'ml-2'],
    bottom: ['top-full', 'left-1/2', '-translate-x-1/2', 'mt-2'],
    left: ['right-full', 'top-1/2', '-translate-y-1/2', 'mr-2'],
  };

  const ARROW_CLASSES = {
    top: ['left-1/2', '-translate-x-1/2', '-bottom-1', 'border-t-0', 'border-l-0'],
    right: ['-left-1', 'top-1/2', '-translate-y-1/2', 'border-t-0', 'border-r-0'],
    bottom: ['left-1/2', '-translate-x-1/2', '-top-1', 'border-b-0', 'border-r-0'],
    left: ['-right-1', 'top-1/2', '-translate-y-1/2', 'border-b-0', 'border-l-0'],
  };

  function applyPanelPosition(pos) {
    Object.values(POSITION_CLASSES).forEach(list => list.forEach(cls => panel.classList.remove(cls)));
    (POSITION_CLASSES[pos] || POSITION_CLASSES.top).forEach(cls => panel.classList.add(cls));
    panel.dataset.currentPosition = pos;
    const arrow = panel.querySelector('.popover-arrow');
    if (arrow) {
      Object.values(ARROW_CLASSES).forEach(list => list.forEach(cls => arrow.classList.remove(cls)));
      (ARROW_CLASSES[pos] || ARROW_CLASSES.top).forEach(cls => arrow.classList.add(cls));
    }
  }

  function isInViewport(rect, margin = 6) {
    const w = window.innerWidth; const h = window.innerHeight;
    return rect.left >= margin && rect.top >= margin && rect.right <= w - margin && rect.bottom <= h - margin;
  }

  function candidatesFor(pos) {
    if (pos === 'auto') return ['bottom', 'top', 'right', 'left'];
    switch (pos) {
      case 'top': return ['top', 'bottom', 'right', 'left'];
      case 'right': return ['right', 'left', 'top', 'bottom'];
      case 'bottom': return ['bottom', 'top', 'right', 'left'];
      case 'left': return ['left', 'right', 'top', 'bottom'];
      default: return ['bottom', 'top', 'right', 'left'];
    }
  }

  function adjustPlacement() {
    const desired = root.getAttribute('data-position') || 'top';
    const list = candidatesFor(desired);
    let chosen = list[0];
    // Essaye chaque position et vérifie le débordement
    for (const pos of list) {
      applyPanelPosition(pos);
      const r = panel.getBoundingClientRect();
      if (isInViewport(r)) { chosen = pos; break; }
    }
    applyPanelPosition(chosen);
  }

  function clampIntoViewport() {
    const margin = 8;
    try {
      panel.style.maxWidth = `${Math.max(0, window.innerWidth - margin * 2)}px`;
    } catch (_) {}
    const r = panel.getBoundingClientRect();
    const pos = panel.dataset.currentPosition || (root.getAttribute('data-position') || 'top');
    if (pos === 'top' || pos === 'bottom') {
      let dx = 0;
      if (r.left < margin) dx += (margin - r.left);
      if (r.right > window.innerWidth - margin) dx -= (r.right - (window.innerWidth - margin));
      if (dx !== 0) {
        panel.style.left = `calc(50% + ${Math.round(dx)}px)`;
      } else {
        panel.style.left = '';
      }
      // Nettoyer styles non utilisés
      panel.style.right = '';
    } else if (pos === 'left' || pos === 'right') {
      // Clamp vertical (déjà existant)
      let dy = 0;
      if (r.top < margin) dy += (margin - r.top);
      if (r.bottom > window.innerHeight - margin) dy -= (r.bottom - (window.innerHeight - margin));
      if (dy !== 0) {
        panel.style.top = `calc(50% + ${Math.round(dy)}px)`;
      } else {
        panel.style.top = '';
      }
      // Clamp horizontal spécifique left/right
      if (pos === 'right') {
        const overflowRight = r.right - (window.innerWidth - margin);
        if (overflowRight > 0) {
          panel.style.left = `calc(100% - ${Math.round(overflowRight)}px)`;
        } else {
          panel.style.left = '';
        }
        panel.style.right = '';
      } else if (pos === 'left') {
        const overflowLeft = margin - r.left;
        if (overflowLeft > 0) {
          panel.style.right = `calc(100% - ${Math.round(overflowLeft)}px)`;
        } else {
          panel.style.right = '';
        }
        panel.style.left = '';
      }
    }
  }

  const positionNow = () => {
    adjustPlacement();
    clampIntoViewport();
  };

  /**
   * Ouvre le popover (affiche le panneau) et ferme les autres popovers ouverts.
   */
  let onReposition = null;
  const open = () => {
    hideAllPopoversExcept(panel);
    panel.classList.remove('hidden');
    // Ajuste la position si le panneau déborde (auto flip) puis clamp dans le viewport
    positionNow();
    onReposition = () => positionNow();
    window.addEventListener('resize', onReposition, { passive: true });
    // Écoute les scroll pour repositionner (capture pour remonter la phase)
    window.addEventListener('scroll', onReposition, true);
  };

  /**
   * Ferme le popover (masque le panneau).
   */
  const close = () => {
    panel.classList.add('hidden');
    if (onReposition) {
      window.removeEventListener('resize', onReposition, { passive: true });
      window.removeEventListener('scroll', onReposition, true);
      onReposition = null;
    }
    panel.style.left = '';
    panel.style.top = '';
    panel.style.maxWidth = '';
    panel.style.right = '';
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
      pinnedByClick = false;
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
      if (!pinnedByClick) {
        hoverTimeout = setTimeout(close, 120);
      }
    };
    // Activer le vrai hover uniquement quand le pointeur n'est pas "coarse"
    if (!isCoarsePointer) {
      root.addEventListener('mouseenter', enter);
      root.addEventListener('mouseleave', leave);
    }
    // Sur tous périphériques : clic pour épingler/désépingler
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      pinnedByClick = !pinnedByClick;
      if (pinnedByClick) {
        open();
        document.addEventListener('click', onOutside, { capture: true });
      } else {
        close();
        document.removeEventListener('click', onOutside, { capture: true });
      }
    });
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

// API globale + initialisation automatique (même si importé après DOMContentLoaded)
function initAllPopovers() {
  document.querySelectorAll('[data-popover]').forEach(setupPopover);
}

window.DaisyPopover = { init: setupPopover, initAll: initAllPopovers };

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllPopovers);
} else {
  initAllPopovers();
}
