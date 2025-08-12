/**
 * Daisy Kit - Transfer
 *
 * Composant de transfert d'éléments entre deux listes, avec gestion des cases à cocher, recherche, pagination, et sélection globale.
 * 
 * Ce composant est destiné à être utilisé via un composant Blade qui injecte les bons data-attributes et la structure HTML attendue.
 *
 * Data-attributes (injectés par le composant Blade) :
 * - [data-transfer="1"] : active le composant sur le conteneur
 * - data-one-way : 'true' => transfert uniquement source -> cible (pas de retour possible)
 * - data-pagination : 'true' => active la pagination sur les listes
 * - data-elements-per-page : nombre d'éléments à afficher par page (pagination)
 * - data-search : 'true' => active la recherche sur les listes
 * - data-select-all : 'false' pour désactiver la case "tout sélectionner"
 * - data-no-data-text : texte à afficher quand la liste est vide
 *
 * Sélecteurs internes (dans le HTML généré) :
 * - [data-transfer-list="source|target"] : listes visuelles source et cible
 * - [data-transfer-move="toTarget|toSource"] : boutons pour transférer les éléments sélectionnés
 * - [data-transfer-search="source|target"] : champs de recherche pour filtrer les listes
 * - [data-transfer-selectall="source|target"] : cases à cocher pour sélectionner/désélectionner tous les éléments visibles
 * - [data-transfer-pager="source|target"] : conteneurs de pagination (précédent, info, suivant)
 *
 * Evénements personnalisés :
 * - 'listChange' (detail: { source, target }) : émis après chaque modification des listes (transfert, sélection, etc.)
 *
 * API globale :
 * - window.DaisyTransfer.{ init, initAll }
 */

/**
 * Convertit le DOM d'une liste en tableau d'objets métier.
 * Chaque élément de la liste doit avoir les attributs data-id, data-label, data-disabled, data-checked.
 * @param {HTMLElement} listEl - Élément UL/OL contenant les items à parser
 * @returns {Array<{id: string, label: string, disabled: boolean, checked: boolean}>}
 */
function parseItems(listEl) {
  const items = [];
  // On parcourt chaque élément de la liste ayant l'attribut data-transfer-item
  listEl.querySelectorAll('[data-transfer-item]').forEach((li, idx) => {
    // Récupération des propriétés de l'item depuis les data-attributes ou fallback
    const id = li.getAttribute('data-id') || String(idx);
    const label = li.getAttribute('data-label') || li.textContent.trim();
    const disabled = li.getAttribute('data-disabled') === 'true';
    const checked = li.getAttribute('data-checked') === 'true';
    items.push({ id, label, disabled, checked });
  });
  return items;
}

/**
 * (Ré)rend une liste selon la recherche, la pagination, et met à jour les cases à cocher.
 * @param {HTMLElement} listEl - Élément UL/OL cible à remplir
 * @param {Array} items - Liste des objets métier à afficher
 * @param {Object} opts - Options d'affichage (pagination, recherche, etc.)
 * @returns {Object} - { total, pages }
 */
function renderList(listEl, items, opts) {
  const { page, perPage, searchTerm, noDataText } = opts;
  let filtered = items;
  // Filtrage par recherche si nécessaire
  if (searchTerm) {
    const q = searchTerm.toLowerCase();
    filtered = items.filter((it) => it.label.toLowerCase().includes(q));
  }
  const total = filtered.length;
  // Calcul de la pagination
  const start = (page - 1) * perPage;
  const end = start + perPage;
  const pageItems = opts.pagination ? filtered.slice(start, end) : filtered;
  // Nettoyage de la liste
  listEl.innerHTML = '';
  // Affichage du message "aucune donnée" si la liste est vide
  if (!pageItems.length) {
    const li = document.createElement('li');
    li.className = 'px-2 py-2 opacity-70';
    li.textContent = noDataText || 'No Data';
    listEl.appendChild(li);
  } else {
    // Pour chaque item à afficher, on crée un <li> avec une case à cocher et le label
    pageItems.forEach((it) => {
      const li = document.createElement('li');
      li.className = 'px-2';
      li.setAttribute('data-transfer-item', '');
      li.setAttribute('data-id', it.id);
      li.setAttribute('data-label', it.label);
      li.setAttribute('data-disabled', it.disabled ? 'true' : 'false');
      li.setAttribute('data-checked', it.checked ? 'true' : 'false');
      // Création du label contenant la checkbox et le texte
      const label = document.createElement('label');
      label.className = 'flex items-center gap-2 py-1';
      // Création de la checkbox
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.className = 'checkbox checkbox-xs';
      cb.checked = !!it.checked;
      cb.disabled = !!it.disabled;
      // Synchronisation de l'état checked de l'objet métier lors du changement
      cb.addEventListener('change', () => { it.checked = cb.checked; });
      // Ajout du texte du label
      const span = document.createElement('span');
      span.className = 'truncate';
      span.textContent = it.label;
      // Assemblage
      label.append(cb, span);
      li.append(label);
      listEl.append(li);
    });
  }
  // Retourne le nombre total d'éléments et le nombre de pages pour la pagination
  return { total, pages: opts.pagination ? Math.max(1, Math.ceil(total / perPage)) : 1 };
}

/**
 * Relie les contrôles de pagination (précédent, suivant, info) à l'état de la liste.
 * @param {HTMLElement} pager - Conteneur de pagination
 * @param {Object} state - État courant de la pagination (page, pages, etc.)
 * @param {Function} onChange - Callback à appeler lors d'un changement de page
 */
function connectPager(pager, state, onChange) {
  if (!pager) return;
  // Sélection des boutons et de l'info de pagination
  const prev = pager.querySelector('[data-transfer-page="prev"]');
  const next = pager.querySelector('[data-transfer-page="next"]');
  const info = pager.querySelector('[data-transfer-page="info"]');
  // Fonction pour mettre à jour l'affichage de l'info de pagination
  function updateInfo() {
    if (info) info.textContent = state.page + '/' + state.pages;
  }
  // Gestion du bouton "précédent"
  if (prev) prev.addEventListener('click', () => {
    if (state.page > 1) {
      state.page -= 1;
      onChange();
    }
  });
  // Gestion du bouton "suivant"
  if (next) next.addEventListener('click', () => {
    if (state.page < state.pages) {
      state.page += 1;
      onChange();
    }
  });
  // Permet à la fonction de rendu de mettre à jour l'info de pagination
  state._updatePagerInfo = updateInfo;
}

/**
 * Initialise une instance Transfer sur un conteneur donné.
 * @param {HTMLElement} root - Élément racine du composant transfer
 */
function initTransfer(root) {
  // Empêche la double initialisation
  if (!root || root.__tfInit) return;
  root.__tfInit = true;

  // Lecture des options depuis les data-attributes
  const oneWay = root.getAttribute('data-one-way') === 'true';
  const pagination = root.getAttribute('data-pagination') === 'true';
  const perPage = parseInt(root.getAttribute('data-elements-per-page') || '5', 10) || 5;
  const search = root.getAttribute('data-search') === 'true';
  const selectAll = root.getAttribute('data-select-all') !== 'false';
  const noDataText = root.getAttribute('data-no-data-text') || 'No Data';

  // Sélection des éléments DOM internes
  const listSource = root.querySelector('[data-transfer-list="source"]');
  const listTarget = root.querySelector('[data-transfer-list="target"]');
  const btnToTarget = root.querySelector('[data-transfer-move="toTarget"]');
  const btnToSource = root.querySelector('[data-transfer-move="toSource"]');
  const pagerSource = root.querySelector('[data-transfer-pager="source"]');
  const pagerTarget = root.querySelector('[data-transfer-pager="target"]');
  const searchSource = root.querySelector('[data-transfer-search="source"]');
  const searchTarget = root.querySelector('[data-transfer-search="target"]');
  const selAllSource = root.querySelector('[data-transfer-selectall="source"]');
  const selAllTarget = root.querySelector('[data-transfer-selectall="target"]');

  // Initialisation des listes d'objets métier (source et cible)
  let itemsSource = parseItems(listSource);
  let itemsTarget = parseItems(listTarget);

  // États de pagination et de recherche pour chaque liste
  const stateSource = { page: 1, pages: 1, perPage, pagination, searchTerm: '', noDataText };
  const stateTarget = { page: 1, pages: 1, perPage, pagination, searchTerm: '', noDataText };

  /**
   * Rend les deux listes (source et cible) et met à jour la pagination.
   * Émet l'événement 'listChange' à chaque modification.
   */
  function renderAll() {
    // Rendu de la liste source
    const s = renderList(listSource, itemsSource, stateSource);
    stateSource.pages = s.pages;
    if (stateSource._updatePagerInfo) stateSource._updatePagerInfo();
    // Rendu de la liste cible
    const t = renderList(listTarget, itemsTarget, stateTarget);
    stateTarget.pages = t.pages;
    if (stateTarget._updatePagerInfo) stateTarget._updatePagerInfo();
    // Émission de l'événement personnalisé pour signaler le changement
    root.dispatchEvent(new CustomEvent('listChange', {
      detail: { source: itemsSource, target: itemsTarget },
      bubbles: true
    }));
  }

  /**
   * Déplace les éléments sélectionnés d'une liste vers l'autre.
   * @param {Array} fromItems - Liste source (d'où on retire)
   * @param {Array} toItems - Liste cible (où on ajoute)
   * @returns {Array|undefined} - Nouvelle liste source (sans les éléments déplacés)
   */
  function moveSelected(fromItems, toItems) {
    // On sélectionne les éléments cochés et non désactivés
    const moving = fromItems.filter((it) => it.checked && !it.disabled);
    if (!moving.length) return;
    // On décoche les éléments déplacés
    moving.forEach((m) => { m.checked = false; });
    // On retire les éléments déplacés de la source
    const keep = fromItems.filter((it) => !moving.includes(it));
    // On ajoute les éléments déplacés à la cible (à la fin)
    toItems.push(...moving);
    return keep;
  }

  // Connexion des contrôles de pagination aux listes
  connectPager(pagerSource, stateSource, renderAll);
  connectPager(pagerTarget, stateTarget, renderAll);

  // Gestion de la recherche sur la liste source
  if (search && searchSource) {
    searchSource.addEventListener('input', () => {
      stateSource.searchTerm = searchSource.value || '';
      stateSource.page = 1;
      renderAll();
    });
  }
  // Gestion de la recherche sur la liste cible
  if (search && searchTarget) {
    searchTarget.addEventListener('input', () => {
      stateTarget.searchTerm = searchTarget.value || '';
      stateTarget.page = 1;
      renderAll();
    });
  }

  // Gestion du "tout sélectionner" sur la source
  if (selectAll && selAllSource) {
    selAllSource.addEventListener('change', () => {
      const want = selAllSource.checked;
      itemsSource.forEach((it) => { if (!it.disabled) it.checked = want; });
      renderAll();
    });
  }
  // Gestion du "tout sélectionner" sur la cible
  if (selectAll && selAllTarget) {
    selAllTarget.addEventListener('change', () => {
      const want = selAllTarget.checked;
      itemsTarget.forEach((it) => { if (!it.disabled) it.checked = want; });
      renderAll();
    });
  }

  // Gestion du transfert source -> cible
  if (btnToTarget) {
    btnToTarget.addEventListener('click', () => {
      const keep = moveSelected(itemsSource, itemsTarget);
      if (keep) itemsSource = keep;
      renderAll();
    });
  }
  // Gestion du transfert cible -> source (si mode bidirectionnel)
  if (btnToSource && !oneWay) {
    btnToSource.addEventListener('click', () => {
      const keep = moveSelected(itemsTarget, itemsSource);
      if (keep) itemsTarget = keep;
      renderAll();
    });
  }
  // Si mode unidirectionnel, on désactive le bouton de retour
  if (oneWay && btnToSource) btnToSource.setAttribute('disabled', 'true');

  // Premier rendu initial
  renderAll();
}

/**
 * Initialise tous les composants Transfer présents dans le DOM.
 */
function initAllTransfers() {
  document.querySelectorAll('[data-transfer="1"]').forEach(initTransfer);
}

// Expose l'API globale pour usage externe
window.DaisyTransfer = { init: initTransfer, initAll: initAllTransfers };

// Initialisation automatique à la fin du chargement du DOM
document.addEventListener('DOMContentLoaded', initAllTransfers);
