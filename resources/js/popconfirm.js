/**
 * Daisy Kit - Popconfirm
 *
 * Ce module gère l'affichage d'une confirmation contextuelle (popconfirm) en deux modes :
 * - Inline : confirmation affichée à proximité d'un élément déclencheur, sans modal.
 *   Utilise [data-popconfirm] sur le conteneur, avec .popconfirm-trigger (déclencheur) et .popconfirm-panel (panneau de confirmation).
 * - Modal : confirmation dans une boîte de dialogue modale native <dialog>.
 *   Utilise [data-popconfirm-modal] sur le bouton déclencheur et [data-popconfirm-modal-target] sur les boutons d'action dans la modale.
 *
 * Evénements personnalisés émis :
 * - 'popconfirm:confirm' : l'utilisateur confirme l'action.
 * - 'popconfirm:cancel'  : l'utilisateur annule l'action.
 *
 * Ce script est conçu pour être utilisé dans le package Daisy Kit.
 */

/**
 * Masque tous les panneaux de confirmation (popconfirm-panel) sauf un éventuel panneau d'exception.
 * @param {HTMLElement|null} exceptionPanel - Le panneau à ne pas masquer (optionnel)
 */
function hideAllPanelsExcept(exceptionPanel) {
  document.querySelectorAll('.popconfirm-panel').forEach((panel) => {
    if (panel !== exceptionPanel) {
      panel.classList.add('hidden');
    }
  });
}

/**
 * Initialise le comportement "inline" d'un popconfirm sur un élément racine donné.
 * @param {HTMLElement} root - Élément racine portant l'attribut [data-popconfirm]
 */
function setupInlinePopconfirm(root) {
  // Sélectionne le déclencheur et le panneau de confirmation
  const trigger = root.querySelector('.popconfirm-trigger');
  const panel = root.querySelector('.popconfirm-panel');
  if (!trigger || !panel) return; // Si l'un des deux est absent, on ne fait rien

  /**
   * Gestionnaire pour masquer le panneau si clic à l'extérieur du popconfirm.
   * @param {MouseEvent} e
   */
  const onOutside = (e) => {
    if (!root.contains(e.target)) {
      panel.classList.add('hidden');
      document.removeEventListener('click', onOutside, { capture: true });
      document.removeEventListener('keydown', onKeyDown);
    }
  };

  /**
   * Gestionnaire pour masquer le panneau si touche "Escape" pressée.
   * @param {KeyboardEvent} e
   */
  const onKeyDown = (e) => {
    if (e.key === 'Escape') {
      panel.classList.add('hidden');
      document.removeEventListener('click', onOutside, { capture: true });
      document.removeEventListener('keydown', onKeyDown);
    }
  };

  // Affiche ou masque le panneau au clic sur le déclencheur
  trigger.addEventListener('click', (e) => {
    e.preventDefault();
    const isHidden = panel.classList.contains('hidden');
    // Masque tous les autres panneaux ouverts
    hideAllPanelsExcept(panel);
    if (isHidden) {
      // Affiche le panneau
      panel.classList.remove('hidden');
      // Met le focus sur le premier bouton d'action (accessibilité)
      const focusable = panel.querySelector('[data-popconfirm-action]');
      if (focusable) focusable.focus();
      // Ajoute les gestionnaires pour fermer le panneau si clic extérieur ou "Escape"
      document.addEventListener('click', onOutside, { capture: true });
      document.addEventListener('keydown', onKeyDown);
    } else {
      // Masque le panneau si déjà visible
      panel.classList.add('hidden');
    }
  });

  // Gère le clic sur un bouton d'action dans le panneau (confirmer ou annuler)
  panel.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-popconfirm-action]');
    if (!btn) return;
    const type = btn.getAttribute('data-popconfirm-action');
    // Détermine l'événement à émettre selon l'action
    const evName = type === 'confirm' ? 'popconfirm:confirm' : 'popconfirm:cancel';
    // Émet l'événement personnalisé sur le root
    root.dispatchEvent(new CustomEvent(evName, { bubbles: true }));
    // Masque le panneau après action
    panel.classList.add('hidden');
  });
}

/**
 * Initialise la gestion des popconfirms en mode modal (dialog).
 * Utilise la délégation d'événements sur le document pour gérer tous les popconfirms modaux.
 */
function setupModalPopconfirm() {
  // Délégation globale sur le document pour gérer tous les popconfirms modaux
  document.addEventListener('click', (e) => {
    // Détection du bouton déclencheur d'ouverture de la modale
    const trigger = e.target.closest('[data-popconfirm-modal]');
    if (trigger) {
      const id = trigger.getAttribute('data-popconfirm-modal');
      const dialog = document.getElementById(id);
      // Ouvre la modale si elle existe et supporte showModal()
      if (dialog && dialog.showModal) dialog.showModal();
    }
    // Détection d'un bouton d'action dans la modale (confirmer ou annuler)
    const actionBtn = e.target.closest('[data-popconfirm-modal-target][data-popconfirm-action]');
    if (actionBtn) {
      const action = actionBtn.getAttribute('data-popconfirm-action');
      const targetId = actionBtn.getAttribute('data-popconfirm-modal-target');
      const dialog = document.getElementById(targetId);
      const evName = action === 'confirm' ? 'popconfirm:confirm' : 'popconfirm:cancel';
      if (dialog) {
        // Émet l'événement personnalisé sur la modale
        dialog.dispatchEvent(new CustomEvent(evName, { bubbles: true }));
        // Ferme la modale via la méthode close() (le bouton n'est pas forcément de type method="dialog")
        if (dialog.close) dialog.close();
      }
    }
  });
}

// API globale + initialisation automatique (robuste à l'import tardif)
function initAllPopconfirms() {
  document.querySelectorAll('[data-popconfirm]').forEach(setupInlinePopconfirm);
  setupModalPopconfirm();
}

window.DaisyPopconfirm = { initAll: initAllPopconfirms };

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllPopconfirms);
} else {
  initAllPopconfirms();
}

