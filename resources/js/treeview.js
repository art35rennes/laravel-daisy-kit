/**
 * Daisy Kit - TreeView (agnostique backend)
 * 
 * Ce module fournit un composant d'arbre interactif, accessible et agnostique du backend.
 * Compatible DaisyUI v5 avec prise en charge complète de l'accessibilité ARIA.
 * 
 * ARCHITECTURE :
 * - Composant autonome sans dépendances externes
 * - Indexation DOM optimisée pour les grands arbres
 * - Gestion d'état avec persistance sessionStorage
 * - Support lazy-loading via événements personnalisés
 * - Navigation clavier complète (WAI-ARIA Tree Pattern)
 * 
 * API PUBLIQUE (window.DaisyTreeView) :
 *   - init(root) : initialise un arbre sur un élément racine
 *   - initAll() : initialise tous les arbres [data-treeview="1"] du DOM
 *   - expand(root, nodeId) : ouvre un nœud spécifique
 *   - collapse(root, nodeId) : ferme un nœud spécifique  
 *   - toggle(root, nodeId) : bascule l'état d'un nœud
 *   - getSelected(root) => array : retourne les ids sélectionnés
 *   - setData(root, data) : hook pour lazy-loading (événement personnalisé)
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
      if (li.classList.contains('hidden') || li.getAttribute('aria-hidden') === 'true') continue;
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

// Debounce utilitaire
function debounce(fn, wait) {
  let t = null;
  return function debounced(...args) {
    window.clearTimeout(t);
    t = window.setTimeout(() => fn.apply(this, args), wait);
  };
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

// Synchronise l'icône des chevrons avec l'état aria-expanded
function syncToggleIcons(root) {
  $all(root, 'li[role="treeitem"]').forEach((li) => {
    const btn = li.querySelector(':scope > div [data-toggle]');
    if (!btn) return;
    const isOpen = li.getAttribute('aria-expanded') === 'true';
    const c = btn.querySelector('[data-icon-collapsed]');
    const e = btn.querySelector('[data-icon-expanded]');
    if (c) c.classList.toggle('hidden', isOpen);
    if (e) e.classList.toggle('hidden', !isOpen);
  });
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
    // On ne traite que les nœuds ayant des enfants ou un placeholder lazy DIRECT
    const hasChildren = !!li.querySelector(':scope > ul[role="group"]') || !!li.querySelector(':scope > ul[role="group"] > [data-lazy-placeholder]');
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
      // Ne PAS déclencher de lazy auto ici: l'ouverture automatique issue de la persistance
      // ne doit pas charger les enfants tant que l'utilisateur n'a pas explicitement
      // ouvert les nœuds concernés.
    }
  });
  // Harmonise les icônes avec l'état restauré
  syncToggleIcons(root);
}

// --- Recherche ---
function clearSearch(root) {
  // Retire surbrillance et rend tous les nœuds visibles
  $all(root, 'li[role="treeitem"]').forEach((li) => {
    li.removeAttribute('data-search-match');
    li.removeAttribute('data-has-match-in-subtree');
    li.classList.remove('hidden');
    li.removeAttribute('aria-hidden');
    const header = li.querySelector(':scope > div[data-node-header]');
    if (header) header.classList.remove('bg-warning/20');
    const label = li.querySelector(':scope > div [data-label]');
    if (label && label.dataset.origLabel) {
      label.textContent = label.dataset.origLabel;
      delete label.dataset.origLabel;
    }
  });
  // Supprime les ellipses ajoutées par la recherche
  $all(root, 'li[data-search-ellipsis]')
    .forEach((el) => el.remove());
}

function markLabelHighlight(labelEl, term) {
  const text = labelEl.textContent || '';
  if (!labelEl.dataset.origLabel) labelEl.dataset.origLabel = text;
  const idx = text.toLowerCase().indexOf(term.toLowerCase());
  if (idx === -1) return;
  const before = text.slice(0, idx);
  const match = text.slice(idx, idx + term.length);
  const after = text.slice(idx + term.length);
  labelEl.innerHTML = `${before}<mark>${match}</mark>${after}`;
}

function computeSearchVisibility(root, term) {
  ensureIndex(root);
  // Post-ordre: on calcule si un nœud a un match direct ou dans ses descendants
  const visit = (li) => {
    const children = li.__childrenLis || [];
    let hasMatchInSubtree = false;
    for (const ch of children) {
      hasMatchInSubtree = visit(ch) || hasMatchInSubtree;
    }
    const label = li.querySelector(':scope > div [data-label]');
    const text = (label?.textContent || '').toLowerCase();
    const isDirectMatch = term && text.includes(term.toLowerCase());
    if (isDirectMatch && label) {
      const header = li.querySelector(':scope > div[data-node-header]');
      if (header) header.classList.add('bg-warning/20');
      markLabelHighlight(label, term);
      li.setAttribute('data-search-match', '1');
    }
    if (isDirectMatch || hasMatchInSubtree) {
      li.removeAttribute('aria-hidden');
      li.classList.remove('hidden');
      li.setAttribute('data-has-match-in-subtree', '1');
      // Ouvre la branche pour rendre visibles les descendants pertinents (hors lazy)
      const group = li.__group || li.querySelector(':scope > ul[role="group"]');
      const isLazyNode = li.hasAttribute('data-lazy-node');
      if (group && !isLazyNode) {
        li.setAttribute('aria-expanded', 'true');
        group.classList.remove('hidden');
      }
      return true;
    }
    // Masque les nœuds qui ne matchent pas et n'ont pas de descendant qui matche
    li.setAttribute('aria-hidden', 'true');
    li.classList.add('hidden');
    return false;
  };
  const tree = $(root, 'ul[role="tree"]');
  if (!tree) return;
  const topLis = Array.from(tree.querySelectorAll(':scope > li[role="treeitem"]'));
  topLis.forEach((li) => visit(li));
  // Met à jour les indicateurs d'élision "…" par niveau
  updateEllipsisMarkers(root);
}

function updateEllipsisMarkers(root) {
  // Retire d'abord les ellipses existantes
  $all(root, 'li[data-search-ellipsis]')
    .forEach((el) => el.remove());
  // Pour chaque niveau (ul) visible, si certains enfants directs sont masqués, ajoute "…" en fin
  const lists = $all(root, 'ul[role="tree"], ul[role="group"]');
  lists.forEach((ul) => {
    // Si la liste est masquée, ignorer
    if (ul.classList.contains('hidden')) return;
    const children = Array.from(ul.querySelectorAll(':scope > li[role="treeitem"]'));
    if (!children.length) return;
    const hiddenCount = children.filter((li) => li.classList.contains('hidden') || li.getAttribute('aria-hidden') === 'true').length;
    if (hiddenCount <= 0) return;
    // Calcule le niveau et l'indentation
    const parentLi = ul.closest('li[role="treeitem"]');
    const parentLevel = parentLi ? (parseInt(parentLi.getAttribute('aria-level') || '0', 10) || 0) : 0;
    const level = parentLi ? (parentLevel + 1) : 1;
    const indentPx = Math.max(0, (level - 1)) * 16;
    // Construit l'élément d'ellipses
    const li = document.createElement('li');
    li.setAttribute('data-search-ellipsis', '1');
    li.setAttribute('role', 'presentation');
    li.innerHTML = `
      <div class="flex items-center gap-2 px-2 py-1 rounded opacity-60" data-node-header="1" style="padding-left: ${indentPx}px">
        <span class="inline-block w-6"></span>
        <span class="flex-1 select-none">…</span>
      </div>
    `;
    ul.appendChild(li);
  });
}

async function expandPath(root, path, token) {
  if (!Array.isArray(path) || path.length === 0) return false;
  const getLi = (id) => root.querySelector(`li[role="treeitem"][data-id="${CSS.escape(String(id))}"]`);
  // Assure l'existence du premier nœud
  let li = getLi(path[0]);
  if (!li) return false;
  const waitForChild = (parentLi, childId, timeoutMs = 3000) => new Promise((resolve) => {
    const start = Date.now();
    const tick = () => {
      if (token && root.__searchToken !== token) return resolve(null);
      const found = getLi(childId);
      if (found) return resolve(found);
      if (Date.now() - start >= timeoutMs) return resolve(null);
      setTimeout(tick, 30);
    };
    tick();
  });
  for (let i = 0; i < path.length - 1; i++) {
    if (token && root.__searchToken !== token) return false;
    const parentId = path[i];
    const childId = path[i + 1];
    const parentLi = getLi(parentId);
    if (!parentLi) return false;
    // Ouvre le parent (et force le lazy si déjà ouvert mais non chargé)
    if (parentLi.getAttribute('aria-expanded') !== 'true') {
      toggleNode(root, parentLi, true);
    } else if (parentLi.hasAttribute('data-lazy-node')) {
      const group = parentLi.querySelector(':scope > ul[role="group"]');
      const hasChildItems = !!(group && group.querySelector(':scope > li[role="treeitem"]'));
      const placeholder = group && group.querySelector(':scope > [data-lazy-placeholder]');
      if (!hasChildItems) {
        // Branches lazy ouvertes mais vides (restauration), on relance le chargement
        toggleNode(root, parentLi, true);
      } else if (placeholder && !hasChildItems) {
        toggleNode(root, parentLi, true);
      }
    }
    // Attends que l'enfant soit présent
    const child = await waitForChild(parentLi, childId);
    if (!child) return false;
  }
  return true;
}

async function performRemoteSearch(root, term, token) {
  const baseUrl = root.dataset.searchUrl;
  const param = root.dataset.searchParam || 'q';
  if (!baseUrl) return [];
  try {
    const url = (() => {
      try {
        const u = new URL(baseUrl, window.location.origin);
        u.searchParams.set(param, term);
        return u.toString();
      } catch (_) {
        const sep = baseUrl.includes('?') ? '&' : '?';
        return `${baseUrl}${sep}${encodeURIComponent(param)}=${encodeURIComponent(term)}`;
      }
    })();
    if (root.__searchAbort) root.__searchAbort.abort();
    const ctrl = new AbortController();
    root.__searchAbort = ctrl;
    const res = await fetch(url, { signal: ctrl.signal });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    const paths = Array.isArray(data?.paths) ? data.paths : (Array.isArray(data) && Array.isArray(data[0]) ? data : []);
    // Réduit le nombre de chemins pour éviter l'explosion
    const capped = paths.slice(0, 50);
    for (const p of capped) {
      if (token && root.__searchToken !== token) return;
      // Déroule le chemin (ouvre uniquement les parents nécessaires à l'affichage du nœud matché)
      // N'ouvre pas les descendants du nœud trouvé
      const parentPath = p.slice(0, Math.max(1, p.length - 0));
      await expandPath(root, parentPath, token);
    }
    return capped;
  } catch (e) {
    // eslint-disable-next-line no-console
    console.error('Tree search remote error', e);
    return [];
  }
}

async function runSearch(root, term) {
  const minLen = parseInt(root.dataset.searchMin || '2', 10) || 2;
  const debounceMs = parseInt(root.dataset.searchDebounce || '300', 10) || 300;
  if (!term || term.length < minLen) {
    clearSearch(root);
    return;
  }
  // Token pour annuler les recherches concurrentes
  root.__searchToken = (root.__searchToken || 0) + 1;
  const token = root.__searchToken;
  clearSearch(root);
  // Si une URL de recherche est fournie, on déroule les chemins pour inclure les branches lazy
  let paths = [];
  if (root.dataset.searchUrl) {
    paths = await performRemoteSearch(root, term, token) || [];
    if (token && root.__searchToken !== token) return; // annulé
  }
  // Applique un filtrage local basé sur le terme (et/ou les chemins ouverts via remote)
  computeSearchVisibility(root, term);
  syncToggleIcons(root);
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
  // Simplification: si le nœud est lazy, à chaque ouverture on supprime la branche et on recharge
  if (willOpen && li.hasAttribute('data-lazy-node')) {
    const grp = li.querySelector(':scope > ul[role="group"]');
    if (grp) grp.innerHTML = '<li class="px-2 py-1 text-sm opacity-60" data-lazy-placeholder>Chargement…</li>';
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

// Met à jour les surlignages de sélection sur tous les nœuds
function refreshSelectionHighlight(root) {
  const mode = root.dataset.selection || 'single';
  $all(root, 'li[role="treeitem"]').forEach((li) => {
    const header = li.querySelector(':scope > div[data-node-header]');
    if (!header) return;
    let active = false;
    if (mode === 'multiple') {
      const cb = li.querySelector(':scope input[type="checkbox"]');
      active = !!(cb && (cb.checked || cb.indeterminate));
    } else {
      const rd = li.querySelector(':scope input[type="radio"]');
      active = !!(rd && rd.checked) || li.getAttribute('aria-selected') === 'true';
    }
    header.classList.toggle('bg-base-200', active);
  });
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
  // Met à jour les surlignages globaux (sélection et mixed)
  refreshSelectionHighlight(root);
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

  // Lazy-loading automatique (optionnel) si data-lazy-url est défini sur la racine
  if (root.dataset.lazyUrl) {
    // Hérite des classes et du HTML du toggle/inputs existants pour garantir le même rendu
    const selectionMode = root.dataset.selection || 'single';
    const radioName = $(root, 'ul[role="tree"]').dataset.radioName || (root.id ? root.id + '-group' : 'tree-group');
    const sampleRadioClass = ($(root, 'input[type="radio"]')?.className || 'radio radio-sm') + ' shrink-0';
    const sampleCheckboxClass = ($(root, 'input[type="checkbox"]')?.className || 'checkbox checkbox-sm') + ' shrink-0';
    const sampleToggleInner = ($(root, '[data-toggle]')?.innerHTML) || (
      '<span data-icon-collapsed>▸</span><span data-icon-expanded class="hidden">▾</span>'
    );
    const normalizedToggleInner = (() => {
      try {
        const tmp = document.createElement('div');
        tmp.innerHTML = sampleToggleInner;
        const coll = tmp.querySelector('[data-icon-collapsed]');
        const exp = tmp.querySelector('[data-icon-expanded]');
        if (coll) coll.classList.remove('hidden');
        if (exp) exp.classList.add('hidden');
        return tmp.innerHTML;
      } catch (_) {
        return '<span data-icon-collapsed>▸</span><span data-icon-expanded class="hidden">▾</span>';
      }
    })();
    // Rendu d'un nœud (réutilisé par lazy et reload)
    const renderNode = (item, level, parentDisabled = false) => {
      const id = item.id;
      const label = item.label ?? String(item.id);
      const isDisabled = !!item.disabled || !!parentDisabled;
      const disabledAttr = isDisabled ? ' disabled' : '';
      const isLazy = !!item.lazy;
      const children = Array.isArray(item.children) ? item.children : [];
      const hasChildren = isLazy || children.length > 0;
      const indentPx = Math.max(0, (level - 1)) * 16;
      const inputHtml = selectionMode === 'multiple'
        ? `<input type="checkbox" class="${sampleCheckboxClass}" tabindex="-1"${disabledAttr} />`
        : `<input type="radio" name="${radioName}" class="${sampleRadioClass}" tabindex="-1"${disabledAttr} />`;
      const toggleHtml = hasChildren
        ? (`<button type="button" class="btn btn-ghost btn-xs btn-square" aria-label="Toggle" data-toggle="1" tabindex="-1">${normalizedToggleInner}</button>`)
        : `<span class="inline-block w-6"></span>`;
      let html = '';
      html += `<li role="treeitem" aria-level="${level}" aria-expanded="false" aria-selected="false" data-id="${String(id)}" class="outline-none"${isLazy ? ' data-lazy-node="1"' : ''}>`;
      html += `  <div class="flex items-center gap-2 px-2 py-1 rounded hover:bg-base-200 focus:bg-base-200" data-node-header="1" style="padding-left: ${indentPx}px">`;
      html += `    ${toggleHtml}`;
      html += `    ${inputHtml}`;
      const labelClasses = `flex-1 cursor-default select-none${isDisabled ? ' opacity-50' : ''}`;
      html += `    <span class="${labelClasses}" data-label="1">${label}</span>`;
      html += `  </div>`;
      if (hasChildren) {
        html += `  <ul role="group" class="pl-2 ml-4 border-l border-base-300 hidden" data-children="1">`;
        if (isLazy) {
          html += `    <li class="px-2 py-1 text-sm opacity-60 hidden" data-lazy-placeholder="1">Loading…</li>`;
        } else {
          children.forEach((ch) => {
            html += renderNode(ch, level + 1, isDisabled);
          });
        }
        html += `  </ul>`;
      }
      html += `</li>`;
      return html;
    };
    const buildUrl = (baseUrl, paramName, nodeId) => {
      try {
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set(paramName, String(nodeId));
        return url.toString();
      } catch (_) {
        const sep = baseUrl.includes('?') ? '&' : '?';
        return `${baseUrl}${sep}${encodeURIComponent(paramName)}=${encodeURIComponent(String(nodeId))}`;
      }
    };
    root.addEventListener('tree:lazy', async (e) => {
      const { li, nodeId } = e.detail || {};
      if (!li) return;
      const group = li.querySelector(':scope > ul[role="group"]');
      if (!group) return;
      // Placeholder chargement (déjà mis lors du toggle, mais on assure)
      // Toujours placer un placeholder visible avant chargement pour états persistés
      if (!group.querySelector('[data-lazy-placeholder]')) {
        group.innerHTML = '<li class="px-2 py-1 text-sm opacity-60" data-lazy-placeholder>Chargement…</li>';
      }
      const param = root.dataset.lazyParam || 'node';
      const url = buildUrl(root.dataset.lazyUrl, param, nodeId);
      try {
        const res = await fetch(url);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const items = await res.json();
        const parentLevel = parseInt(li.getAttribute('aria-level') || '0', 10) || 0;
        const baseLevel = parentLevel + 1;
        // Remplace uniquement le placeholder pour éviter d'effacer d'autres nœuds
        const placeholder = group.querySelector(':scope > [data-lazy-placeholder]');
        const parentDisabled = !!li.querySelector(':scope > div input[disabled]');
        const html = (items || []).map((it) => renderNode(it, baseLevel, parentDisabled)).join('');
        group.innerHTML = '';
        group.insertAdjacentHTML('afterbegin', html);
        // Marque le parent comme ouvert et synchronise l'icône
        li.setAttribute('aria-expanded', 'true');
        const btn = li.querySelector('[data-toggle]');
        if (btn) {
          const c = btn.querySelector('[data-icon-collapsed]');
          const e2 = btn.querySelector('[data-icon-expanded]');
          if (c) c.classList.add('hidden');
          if (e2) e2.classList.remove('hidden');
        }
      } catch (err) {
        group.innerHTML = '<li class="px-2 py-1 text-error">Erreur de chargement</li>';
        // eslint-disable-next-line no-console
        console.error('Tree lazy auto-load error', err);
      }
      // Recalcule l'index et les surlignages
      root.__indexStale = true;
      refreshSelectionHighlight(root);
      syncToggleIcons(root);
    });

    // Plus de bouton reload: chaque ouverture d'un nœud lazy recharge la branche
  }

  // Recherche (champ en-tête)
  if (root.dataset.searchEnabled === 'true') {
    const input = $(root, '[data-tree-search]');
    const btn = $(root, '[data-tree-search-btn]');
    const debounceMs = parseInt(root.dataset.searchDebounce || '300', 10) || 300;
    const debounced = debounce(() => {
      if (!input) return;
      runSearch(root, input.value.trim());
    }, debounceMs);
    if (input) {
      input.addEventListener('input', () => {
        const minLen = parseInt(root.dataset.searchMin || '2', 10) || 2;
        if (!input.value || input.value.trim().length < minLen) {
          // Efface immédiatement si en-dessous du seuil
          clearSearch(root);
        }
        if (root.dataset.searchAuto === 'true') debounced();
      });
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          runSearch(root, input.value.trim());
        }
      });
    }
    if (btn && input) {
      btn.addEventListener('click', () => {
        runSearch(root, input.value.trim());
      });
    }
  }
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
  // Après restauration, normalise le surlignage (inclut mixed pré-existants)
  refreshSelectionHighlight(root);
  syncToggleIcons(root);
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
  document.querySelectorAll('[data-treeview="1"]').forEach((el) => {
    // Si déjà initialisé, on resynchronise les icônes et on sort
    if (el.__treeInit) {
      syncToggleIcons(el);
      return;
    }
    init(el);
  });
}

// API publique globale
window.DaisyTreeView = {
  init,
  initAll,
  getSelected(root) { return getSelected(root); },
};

// Export pour le système data-module (kit/index.js)
export default init;
export { init, initAll };

// Initialisation automatique (compatible import tardif)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
