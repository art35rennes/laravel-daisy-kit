/*
 * Daisy Kit - TreeView (agnostique backend)
 * 
 * Ce module fournit un composant d'arbre interactif, accessible et agnostique du backend.
 * L'API publique est exposée sur window.DaisyTreeView :
 *   - init(root) : initialise un arbre sur un élément racine
 *   - expand(nodeId), collapse(nodeId), toggle(nodeId) : contrôle l'ouverture d'un nœud
 *   - getSelected(root) => array d'ids : retourne les ids sélectionnés
 *   - setData(root, data) : hook pour lazy-loading (l'intégrateur gère le HTML)
 *   - reset(root) : réinitialise la sélection et l'état ouvert
 * 
 * Fonctionnalités :
 *   - Sélection simple ou multiple (radio/checkbox)
 *   - Navigation clavier (flèches, espace, entrée)
 *   - Persistance de l'état ouvert (sessionStorage)
 *   - Lazy-loading via événements personnalisés
 *   - Support du tri-état pour les cases à cocher
 *   - Désactivation possible de l'arbre
 * 
 * Data-attributes (sur la racine [data-treeview="1"]) :
 *   - data-selection: 'single'|'multiple' (mode de sélection)
 *   - data-return: 'nodes'|'leaves' (pour multiple, ce qui est retourné par getSelected)
 *   - data-persist: 'true' => mémorise les nœuds ouverts (sessionStorage)
 *   - data-persist-key: clé personnalisée de persistance si pas d'id
 *   - data-select-label: 'true' => clic sur le label sélectionne le nœud
 *   - ul[role="tree"][data-disabled]: désactive toutes les interactions
 */

const STORAGE_PREFIX = 'treeview:';

// Sélecteurs utilitaires pour le DOM, scoping sur la racine
function $(root, selector) { return root.querySelector(selector); }
function $all(root, selector) { return Array.from(root.querySelectorAll(selector)); }

// Persistance de l'état ouvert dans sessionStorage
function readStorage(key) {
  try { return JSON.parse(sessionStorage.getItem(key) || 'null'); } catch (_) { return null; }
}
function writeStorage(key, value) {
  try { sessionStorage.setItem(key, JSON.stringify(value)); } catch (_) {}
}

// Retourne la liste des treeitems visibles (pour navigation clavier)
function visibleTreeItems(root) {
  const items = [];
  // Parcours en profondeur, ne descend que dans les groupes ouverts
  function dfs(ul) {
    const lis = Array.from(ul.children).filter((el) => el.matches('li[role="treeitem"]'));
    for (const li of lis) {
      items.push(li);
      const group = li.querySelector(':scope > ul[role="group"]');
      const expanded = li.getAttribute('aria-expanded') === 'true';
      if (group && expanded && !group.classList.contains('hidden')) dfs(group);
    }
  }
  const tree = $(root, 'ul[role="tree"]');
  if (tree) dfs(tree);
  return items;
}

// --- Indexation du DOM pour grands arbres ---
// Ajoute des propriétés __treeParent, __cb, __group, __childrenLis sur chaque li
function indexTree(root) {
  const tree = $(root, 'ul[role="tree"]');
  if (!tree) return;
  function indexUL(ul, parentLi) {
    const lis = Array.from(ul.querySelectorAll(':scope > li[role="treeitem"]'));
    for (const li of lis) {
      li.__treeParent = parentLi || null; // parent direct dans l'arbre
      li.__cb = li.querySelector(':scope input[type="checkbox"]'); // case à cocher éventuelle
      const group = li.querySelector(':scope > ul[role="group"]');
      li.__group = group || null; // sous-groupe éventuel
      li.__childrenLis = group ? Array.from(group.querySelectorAll(':scope > li[role="treeitem"]')) : []; // enfants directs
      if (group) indexUL(group, li);
    }
  }
  indexUL(tree, null);
  root.__treeIndexed = true;
  root.__indexStale = false; // index à jour
}

// S'assure que l'index est présent et à jour
function ensureIndex(root) {
  if (!root.__treeIndexed || root.__indexStale) indexTree(root);
}

// Gère le focus clavier sur un treeitem
function setFocus(root, li, doFocus = true) {
  if (!li) return;
  const tree = $(root, 'ul[role="tree"]');
  if (!tree) return;
  // On retire le tabIndex de tous les items, puis on le met sur l'élément ciblé
  $all(root, 'li[role="treeitem"]').forEach((n) => n.tabIndex = -1);
  li.tabIndex = 0;
  if (doFocus) li.focus();
}

// Déclenche un événement CustomEvent sur la racine
function fireEvent(root, type, detail) {
  root.dispatchEvent(new CustomEvent(type, { detail, bubbles: true }));
}

// Persiste l'état des nœuds ouverts si data-persist="true"
function persistStateIfNeeded(root) {
  const persist = root.dataset.persist === 'true';
  if (!persist) return;
  const id = root.id || root.dataset.persistKey || 'default';
  // On stocke la liste des ids des nœuds ouverts
  const expandedIds = $all(root, 'li[role="treeitem"][aria-expanded="true"]').map((li) => li.dataset.id);
  writeStorage(STORAGE_PREFIX + id, { expanded: expandedIds });
}

// Restaure l'état ouvert des nœuds depuis le storage
function restoreState(root) {
  const persist = root.dataset.persist === 'true';
  if (!persist) return;
  const id = root.id || root.dataset.persistKey || 'default';
  const state = readStorage(STORAGE_PREFIX + id);
  if (!state || !state.expanded) return;
  const set = new Set(state.expanded);
  $all(root, 'li[role="treeitem"]').forEach((li) => {
    // On ne traite que les nœuds ayant des enfants ou un placeholder lazy
    const hasChildren = !!li.querySelector(':scope > ul[role="group"]') || li.querySelector('[data-lazy-placeholder]');
    if (!hasChildren) return;
    const btn = li.querySelector('[data-toggle]');
    const children = li.querySelector(':scope > ul[role="group"]');
    const wantOpen = set.has(li.dataset.id);
    if (wantOpen) {
      li.setAttribute('aria-expanded', 'true');
      if (children) children.classList.remove('hidden');
      if (btn) {
        // Affiche l'icône "ouvert", masque "fermé"
        const c = btn.querySelector('[data-icon-collapsed]');
        const e = btn.querySelector('[data-icon-expanded]');
        if (c) c.classList.add('hidden');
        if (e) e.classList.remove('hidden');
      }
    }
  });
}

// Ouvre/ferme un nœud (li) selon l'état demandé (ou toggle si open non précisé)
function toggleNode(root, li, open = undefined) {
  // On ne toggle que si le nœud a des enfants ou un placeholder lazy
  const hasChildren = !!li.querySelector(':scope > ul[role="group"]') || li.querySelector('[data-lazy-placeholder]');
  if (!hasChildren) return;
  const isOpen = li.getAttribute('aria-expanded') === 'true';
  const willOpen = open === undefined ? !isOpen : !!open;
  const btn = li.querySelector('[data-toggle]');
  const group = li.querySelector(':scope > ul[role="group"]');
  li.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  if (group) group.classList.toggle('hidden', !willOpen);
  if (btn) {
    // Affiche/masque les icônes d'état
    const c = btn.querySelector('[data-icon-collapsed]');
    const e = btn.querySelector('[data-icon-expanded]');
    if (c) c.classList.toggle('hidden', willOpen);
    if (e) e.classList.toggle('hidden', !willOpen);
  }
  persistStateIfNeeded(root);
  // Si on ouvre un nœud lazy, on déclenche l'événement pour lazy-loading
  if (willOpen && li.querySelector('[data-lazy-placeholder]')) {
    // L'index peut devenir obsolète après lazy loading externe
    root.__indexStale = true;
    fireEvent(root, 'tree:lazy', { root, nodeId: li.dataset.id, li });
  }
}

// Vérifie si un nœud ou un de ses ancêtres est désactivé (input[disabled])
function hasDisabledAncestor(li) {
  let cur = li;
  while (cur) {
    const disabledCtrl = cur.querySelector(':scope > div input[disabled]');
    if (disabledCtrl) return true;
    cur = cur.parentElement?.closest('li[role="treeitem"]');
  }
  return false;
}

// Met à jour l'état visuel d'une checkbox (checked, unchecked, mixed)
function setCheckboxState(checkbox, state) {
  if (!checkbox) return;
  if (state === 'checked') {
    checkbox.checked = true;
    checkbox.indeterminate = false;
    checkbox.setAttribute('aria-checked', 'true');
  } else if (state === 'unchecked') {
    checkbox.checked = false;
    checkbox.indeterminate = false;
    checkbox.setAttribute('aria-checked', 'false');
  } else if (state === 'mixed') {
    checkbox.checked = false;
    checkbox.indeterminate = true;
    checkbox.setAttribute('aria-checked', 'mixed');
  }
}

// Met à jour le tri-état des ancêtres d'un nœud après modification d'une case
function updateAncestorsTriState(root, li) {
  let parent = li.parentElement?.closest('li[role="treeitem"]');
  while (parent) {
    const children = parent.querySelector(':scope > ul[role="group"]');
    const boxes = children ? Array.from(children.querySelectorAll(':scope > li input[type="checkbox"]')) : [];
    const numChecked = boxes.filter((b) => b.checked).length;
    const numIndet = boxes.filter((b) => b.indeterminate).length;
    const parentBox = parent.querySelector(':scope input[type="checkbox"]');
    if (boxes.length === 0) {
      // Si pas d'enfants, on considère le parent comme décoché
      setCheckboxState(parentBox, 'unchecked');
    } else if (numChecked === boxes.length && numIndet === 0) {
      setCheckboxState(parentBox, 'checked');
    } else if (numChecked === 0 && numIndet === 0) {
      setCheckboxState(parentBox, 'unchecked');
    } else {
      setCheckboxState(parentBox, 'mixed');
    }
    parent = parent.parentElement?.closest('li[role="treeitem"]');
  }
}

// Applique l'état checked/unchecked à tous les descendants d'un nœud
function setDescendantsState(li, checked) {
  // Utilise l'index si dispo pour éviter de requêter tout le sous-arbre
  const apply = (node) => {
    const children = node.__childrenLis || [];
    for (const child of children) {
      const cb = child.__cb || child.querySelector(':scope input[type="checkbox"]');
      if (cb && !cb.disabled) setCheckboxState(cb, checked ? 'checked' : 'unchecked');
      apply(child);
    }
  };
  apply(li);
}

// Met à jour le tri-état d'un nœud en fonction de ses enfants
function updateNodeTriState(li) {
  const group = li.__group || li.querySelector(':scope > ul[role="group"]');
  const parentBox = li.__cb || li.querySelector(':scope input[type="checkbox"]');
  if (!group || !parentBox) return;
  // On récupère toutes les cases enfants
  const boxes = (li.__childrenLis || Array.from(group.querySelectorAll(':scope > li input[type="checkbox"]')))
    .map((child) => child.__cb || child.querySelector(':scope input[type="checkbox"]'))
    .filter(Boolean);
  const numChecked = boxes.filter((b) => b.checked).length;
  const numIndet = boxes.filter((b) => b.indeterminate).length;
  if (boxes.length === 0) {
    setCheckboxState(parentBox, 'unchecked');
  } else if (numChecked === boxes.length && numIndet === 0) {
    setCheckboxState(parentBox, 'checked');
  } else if (numChecked === 0 && numIndet === 0) {
    setCheckboxState(parentBox, 'unchecked');
  } else {
    setCheckboxState(parentBox, 'mixed');
  }
}

// Met à jour récursivement le tri-état de toute une sous-branche
function updateSubtreeTriState(li) {
  const children = li.__childrenLis || [];
  children.forEach((child) => updateSubtreeTriState(child));
  updateNodeTriState(li);
}

// Gère la sélection d'un nœud (clic, clavier, input)
function selectNode(root, li, viaKeyboard = false, fromInput = false) {
  // On ne sélectionne rien si l'arbre est désactivé ou si le nœud est désactivé
  if ($(root, 'ul[role="tree"]').dataset.disabled === 'true') return;
  if (hasDisabledAncestor(li)) return;
  ensureIndex(root);
  const mode = root.dataset.selection || 'single';
  const input = li.querySelector(':scope input[type="radio"], :scope input[type="checkbox"]');
  if (mode === 'single' && input && input.type === 'radio') {
    // Sélection simple : on coche le radio et on marque aria-selected
    input.checked = true;
    $all(root, 'li[role="treeitem"]').forEach((node) => node.setAttribute('aria-selected', node === li ? 'true' : 'false'));
  } else if (mode === 'multiple' && input && input.type === 'checkbox') {
    // Sélection multiple : on coche/décoche, cascade descendants, met à jour tri-état
    const willCheck = fromInput ? !!input.checked : !(input.checked || input.indeterminate);
    setCheckboxState(input, willCheck ? 'checked' : 'unchecked');
    setDescendantsState(li, willCheck); // cascade descendants
    updateSubtreeTriState(li); // recalcul tri-état sous-branche
    updateAncestorsTriState(root, li); // met à jour ancêtres
    li.setAttribute('aria-selected', willCheck ? 'true' : 'false');
  } else {
    // Pas de cases : sélection visuelle uniquement
    $all(root, 'li[role="treeitem"]').forEach((node) => node.setAttribute('aria-selected', node === li ? 'true' : 'false'));
  }
  // Ajoute une classe visuelle sur l'item sélectionné
  li.classList.toggle('bg-base-200', li.getAttribute('aria-selected') === 'true');
  // Déclenche un événement de sélection
  fireEvent(root, 'tree:select', { nodeId: li.dataset.id, viaKeyboard });
}

// Gère la navigation clavier sur l'arbre
function onKeyDown(root, e) {
  const current = document.activeElement?.closest('li[role="treeitem"]');
  const items = visibleTreeItems(root);
  if (!items.length) return;
  let idx = current ? items.indexOf(current) : -1;
  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault();
      setFocus(root, items[Math.min(items.length - 1, (idx + 1))]);
      break;
    case 'ArrowUp':
      e.preventDefault();
      setFocus(root, items[Math.max(0, (idx - 1))]);
      break;
    case 'ArrowRight':
      if (!current) return;
      e.preventDefault();
      // Si le nœud est fermé, on l'ouvre, sinon on focus le premier enfant
      if (current.getAttribute('aria-expanded') === 'false') toggleNode(root, current, true);
      else {
        const group = current.querySelector(':scope > ul[role="group"]');
        if (group && !group.classList.contains('hidden')) {
          const firstChild = group.querySelector('li[role="treeitem"]');
          if (firstChild) setFocus(root, firstChild);
        }
      }
      break;
    case 'ArrowLeft':
      if (!current) return;
      e.preventDefault();
      // Si le nœud est ouvert, on le ferme, sinon on focus le parent
      if (current.getAttribute('aria-expanded') === 'true') toggleNode(root, current, false);
      else {
        const parent = current.parentElement?.closest('li[role="treeitem"]');
        if (parent) setFocus(root, parent);
      }
      break;
    case 'Enter':
    case ' ': // Space
      if (!current) return;
      e.preventDefault();
      selectNode(root, current, true);
      break;
  }
}

// Attache tous les gestionnaires d'événements sur l'arbre
function attachInteractions(root) {
  const tree = $(root, 'ul[role="tree"]');
  if (!tree) return;
  // Indexation initiale du DOM pour accélérer les accès
  indexTree(root);

  // Navigation clavier
  tree.addEventListener('keydown', (e) => onKeyDown(root, e));

  // Gestion du clic sur les nœuds, les toggles, les labels
  tree.addEventListener('click', (e) => {
    const t = e.target;
    const header = t.closest('[data-node-header]');
    const li = t.closest('li[role="treeitem"]');
    if (!li) return;
    // Si l'arbre est désactivé, on ne permet que le toggle
    if ($(root, 'ul[role="tree"]').dataset.disabled === 'true') {
      if (t.closest('[data-toggle]')) {
        toggleNode(root, li);
      }
      return;
    }
    // Clic sur le bouton toggle
    if (t.closest('[data-toggle]')) {
      toggleNode(root, li);
      setFocus(root, li);
      return;
    }
    // Clic sur le label (si activé)
    if (header && t.closest('[data-label]')) {
      if (root.dataset.selectLabel === 'true') toggleNode(root, li);
      if (!hasDisabledAncestor(li)) selectNode(root, li, false);
      setFocus(root, li);
    }
  });

  // Gestion du clic direct sur une case à cocher : on mémorise si elle était indéterminée
  tree.addEventListener('mousedown', (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'checkbox') return;
    // On mémorise si la case était mixed avant l'action utilisateur
    input.dataset.wasIndeterminate = input.indeterminate ? '1' : '0';
  }, { capture: true });

  // Gestion du changement d'état d'une case à cocher ou radio
  tree.addEventListener('change', (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'checkbox' && input.type !== 'radio') return;
    const li = input.closest('li[role="treeitem"]');
    if (!li) return;
    if ($(root, 'ul[role="tree"]').dataset.disabled === 'true') return;
    if (hasDisabledAncestor(li)) {
      // Si le nœud est désactivé, on annule visuellement le changement
      if (input.type === 'checkbox') setCheckboxState(input, 'unchecked');
      else input.checked = false;
      return;
    }
    // Si la case était mixed, un clic doit la décocher et normaliser l'état aria
    if (input.type === 'checkbox' && input.dataset.wasIndeterminate === '1') {
      setCheckboxState(input, 'unchecked');
      input.indeterminate = false;
      input.setAttribute('aria-checked', 'false');
      delete input.dataset.wasIndeterminate;
      selectNode(root, li, false, true);
      return;
    }
    // Normalise aria-checked après tout changement
    if (input.type === 'checkbox') {
      input.indeterminate = false;
      input.setAttribute('aria-checked', input.checked ? 'true' : 'false');
    }
    selectNode(root, li, false, true);
  });

  // Double-clic sur un nœud : toggle ouverture/fermeture
  tree.addEventListener('dblclick', (e) => {
    const li = e.target.closest('li[role="treeitem"]');
    if (!li) return;
    toggleNode(root, li);
  });
}

// Retourne la liste des ids sélectionnés selon le mode de sélection
function getSelected(root) {
  const mode = root.dataset.selection || 'single';
  const returnMode = $(root, 'ul[role="tree"]').dataset.return || 'nodes';
  if (mode === 'multiple') {
    if (returnMode === 'leaves') {
      // Ne renvoyer que les feuilles cochées (pas de sous-nœuds)
      return $all(root, 'li[role="treeitem"] input[type="checkbox"]:checked')
        .map((i) => i.closest('li'))
        .filter((li) => !li.querySelector(':scope > ul[role="group"] li[role="treeitem"]'))
        .map((li) => li?.dataset.id)
        .filter(Boolean);
    }
    // Par défaut : tous les nœuds cochés
    return $all(root, 'li[role="treeitem"] input[type="checkbox"]:checked').map((i) => i.closest('li')?.dataset.id).filter(Boolean);
  }
  // Mode single : radio ou aria-selected
  const checked = root.querySelector('li[role="treeitem"] input[type="radio"]:checked');
  if (checked) return [checked.closest('li')?.dataset.id].filter(Boolean);
  const selected = root.querySelector('li[role="treeitem"][aria-selected="true"]');
  return selected ? [selected.dataset.id] : [];
}

// Hook pour lazy-loading : l'intégrateur écoute 'tree:setData'
function setData(root, data) {
  // API minimaliste: l'intégrateur regénère le HTML côté Blade si besoin.
  // Ici, on expose un hook pour lazy-loading: l'écouteur de 'tree:lazy' remplace le placeholder.
  fireEvent(root, 'tree:setData', { data });
}

// Réinitialise la sélection et l'état visuel de l'arbre
function reset(root) {
  $all(root, 'li[role="treeitem"]').forEach((li) => {
    li.setAttribute('aria-selected', 'false');
    li.classList.remove('bg-base-200');
    const cb = li.querySelector('input[type="checkbox"]');
    const rd = li.querySelector('input[type="radio"]');
    if (cb) cb.checked = false;
    if (rd) rd.checked = false;
  });
  persistStateIfNeeded(root);
}

// Génère l'API publique pour une instance d'arbre
function apiFor(root) {
  return {
    expand: (nodeId) => {
      const li = root.querySelector(`li[role="treeitem"][data-id="${CSS.escape(String(nodeId))}"]`);
      if (li) toggleNode(root, li, true);
    },
    collapse: (nodeId) => {
      const li = root.querySelector(`li[role="treeitem"][data-id="${CSS.escape(String(nodeId))}"]`);
      if (li) toggleNode(root, li, false);
    },
    toggle: (nodeId) => {
      const li = root.querySelector(`li[role="treeitem"][data-id="${CSS.escape(String(nodeId))}"]`);
      if (li) toggleNode(root, li);
    },
    getSelected: () => getSelected(root),
    setData: (data) => setData(root, data),
    reset: () => reset(root),
  };
}

// Initialise un arbre sur un élément racine
function init(root) {
  if (!root || root.__treeInit) return;
  root.__treeInit = true;
  attachInteractions(root);
  restoreState(root);
  // Détermine s'il faut autofocus au chargement
  function shouldAutofocusOnInit(r) {
    // Opt-in via data-autofocus="true" pour éviter de casser l'ancre URL et le focus existant
    if (r.dataset.autofocus !== 'true') return false;
    // Si une ancre est présente ou une cible :target existe, ne pas voler le focus
    if (document.querySelector(':target')) return false;
    if (location.hash && location.hash.length > 1) return false;
    // Si un autre élément a déjà le focus, on n'interfère pas
    const active = document.activeElement;
    if (active && active !== document.body) return false;
    return true;
  }
  // Prépare le premier élément focusable (tabIndex) sans déclencher un focus/scroll
  const first = root.querySelector('li[role="treeitem"]');
  if (first) setFocus(root, first, false);
  // Autofocus uniquement si explicitement demandé et sans ancre
  if (first && shouldAutofocusOnInit(root)) setFocus(root, first, true);
  // Expose l'API instance sur la racine
  root.__treeApi = apiFor(root);
}

// Initialise tous les arbres présents dans le DOM
function initAll() {
  document.querySelectorAll('[data-treeview="1"]').forEach(init);
}

// API publique globale
window.DaisyTreeView = {
  init,
  initAll,
  getSelected(root) { return getSelected(root); },
};

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
