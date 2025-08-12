/**
 * Daisy Kit - Table selection
 *
 * Gestion de la sélection de lignes dans un tableau, avec support des modes simple ou multiple,
 * et émission d'un événement personnalisé pour l'agrégation des sélections.
 *
 * Data-attributes attendus :
 * - table[data-table-select="none|single|multiple"] : définit le mode de sélection
 * - inputs par ligne : [data-row-select] (checkbox ou radio)
 *
 * Événement émis :
 * - 'table:select' (detail.selected = [{ index, tr }]) : liste des lignes sélectionnées
 */

/**
 * Initialise la gestion de sélection sur un conteneur racine donné.
 * @param {HTMLElement} root - Élément racine contenant le tableau à gérer
 */
function initTable(root) {
  // Empêche une double initialisation sur le même conteneur
  if (!root || root.__tableInit) return;
  root.__tableInit = true;

  // Recherche le tableau cible avec l'attribut data-table-select
  const table = root.querySelector('table[data-table-select]');
  if (!table) return;

  // Récupère le mode de sélection (none, single, multiple)
  const mode = table.getAttribute('data-table-select') || 'none';
  if (mode === 'none') return;

  /**
   * Récupère la liste des lignes sélectionnées dans le tableau.
   * @returns {Array<{index: number, tr: HTMLTableRowElement}>}
   */
  const getSelectedRows = () => {
    const inputs = table.querySelectorAll('[data-row-select]');
    const selected = [];
    // On parcourt chaque input de sélection (checkbox ou radio)
    inputs.forEach((el, idx) => {
      // On ne retient que les inputs cochés
      if ((el.type === 'checkbox' && el.checked) || (el.type === 'radio' && el.checked)) {
        const tr = el.closest('tr');
        if (tr) selected.push({ index: idx, tr });
      }
    });
    return selected;
  };

  /**
   * Gestionnaire d'événement sur le tableau pour la sélection de lignes.
   * - Met à jour l'état visuel de la ligne sélectionnée.
   * - Normalise le comportement en mode "single" (checkbox => radio-like).
   * - Émet l'événement personnalisé 'table:select' avec la liste des lignes sélectionnées.
   */
  table.addEventListener('change', (e) => {
    const input = e.target;
    // On ne traite que les changements sur les inputs de sélection
    if (!(input instanceof HTMLInputElement)) return;
    if (!input.matches('[data-row-select]')) return;

    // En mode "single" avec des checkbox, on force le comportement radio (une seule sélection possible)
    if (mode === 'single' && input.type === 'checkbox') {
      table.querySelectorAll('[data-row-select]').forEach((el) => {
        if (el !== input) el.checked = false;
      });
      input.checked = true;
    }

    // Ajoute ou retire la classe visuelle sur la ligne sélectionnée
    const tr = input.closest('tr');
    if (tr) tr.classList.toggle('bg-base-200', input.checked);

    // Émet l'événement personnalisé avec la liste des lignes sélectionnées
    root.dispatchEvent(
      new CustomEvent('table:select', {
        detail: { selected: getSelectedRows() },
        bubbles: true
      })
    );
  });
}

/**
 * Initialise la sélection sur tous les tableaux présents dans le document.
 * À appeler au chargement de la page ou après un rendu dynamique.
 */
function initAllTables() {
  // Pour chaque tableau avec data-table-select, on initialise sur son conteneur parent
  document.querySelectorAll('[data-table-select]').forEach((el) => {
    // On cherche le conteneur racine (div parent ou parentElement)
    initTable(el.closest('div') || el.parentElement);
  });
}

// Expose l'API globale DaisyTable pour usage externe
window.DaisyTable = { init: initTable, initAll: initAllTables };

// Initialisation automatique au chargement du DOM
document.addEventListener('DOMContentLoaded', initAllTables);
