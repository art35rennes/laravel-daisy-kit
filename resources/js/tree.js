import '../css/tree.css';
import { createMountable } from './core/mountable.js';

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:tree:${name}`, { bubbles: true, detail }));
}

function normalizeItems(items, usedIds = new Set(), trail = []) {
    if (!Array.isArray(items)) {
        return [];
    }

    return items.flatMap((item, index) => {
        if (!item || Array.isArray(item) || typeof item !== 'object') {
            return [];
        }

        const candidate = typeof item.id === 'string' && item.id !== '' ? item.id : `node-${[...trail, index].join('-')}`;
        let id = candidate;
        let suffix = 1;

        while (usedIds.has(id)) {
            id = `${candidate}-${suffix}`;
            suffix += 1;
        }

        usedIds.add(id);

        let source = null;
        if (typeof item.source === 'string' && item.source !== '') {
            try {
                const url = new URL(item.source, window.location.href);
                if (['http:', 'https:'].includes(url.protocol)) source = url;
            } catch {
                // Invalid lazy endpoints are treated as absent configuration.
            }
        }

        return [{
            children: normalizeItems(item.children, usedIds, [...trail, index]),
            expanded: item.expanded === true,
            id,
            label: typeof item.label === 'string' && item.label !== '' ? item.label : id,
            source,
        }];
    });
}

function updateStatus(root, message = null) {
    const status = root.querySelector('[data-daisy-kit-status]');

    if (!status) {
        return;
    }

    status.hidden = message === null;
    status.textContent = message ?? '';
}

function initialize(root, configuration) {
    const treeRoot = root.querySelector('[data-daisy-kit-tree-root]');
    const searchInput = root.querySelector('[data-daisy-kit-tree-search]');

    if (!treeRoot) {
        updateStatus(root, 'This tree is missing its required markup.');
        root.dataset.daisyKitState = 'error';
        emit(root, 'error', { reason: 'missing-content' });

        return;
    }

    const initialMarkup = treeRoot.innerHTML;
    const items = normalizeItems(configuration.items);
    const multiple = configuration.multiple === true;
    const valueInput = root.querySelector('[data-daisy-kit-tree-value]');
    const persistenceKey = typeof configuration.persistenceKey === 'string' && configuration.persistenceKey !== ''
        ? `daisy-kit:tree:${configuration.persistenceKey}`
        : null;
    const itemsById = new Map();
    const buttonsById = new Map();
    const expandedIds = new Set();
    const loadingIds = new Set();
    const selectedIds = new Set();
    let selectedId = null;
    let searchQuery = '';

    function persistedState() {
        if (!persistenceKey) return null;

        try {
            const value = JSON.parse(localStorage.getItem(persistenceKey) ?? 'null');

            return value && typeof value === 'object' ? value : null;
        } catch {
            return null;
        }
    }

    function persist() {
        if (!persistenceKey) return;

        try {
            localStorage.setItem(persistenceKey, JSON.stringify({
                expandedIds: [...expandedIds],
                selectedId,
                selectedIds: [...selectedIds],
            }));
        } catch {
            // Storage can be unavailable in a privacy-restricted host.
        }
    }

    function index(itemsToIndex, parentId = null) {
        itemsToIndex.forEach((item) => {
            item.parentId = parentId;
            itemsById.set(item.id, item);

            if (item.expanded) {
                expandedIds.add(item.id);
            }

            index(item.children, item.id);
        });
    }

    function renderItems(itemsToRender, level = 1) {
        const fragment = document.createDocumentFragment();

        itemsToRender.forEach((item) => {
            const listItem = document.createElement('li');
            const button = document.createElement('button');
            const hasChildren = item.children.length > 0 || item.source !== null;

            listItem.role = 'none';
            button.dataset.daisyKitTreeNode = item.id;
            button.role = 'treeitem';
            button.tabIndex = -1;
            button.type = 'button';
            button.textContent = item.label;
            button.setAttribute('aria-level', String(level));
            button.setAttribute('aria-selected', 'false');

            if (multiple) {
                button.setAttribute('aria-checked', 'false');
            }

            if (hasChildren) {
                button.setAttribute('aria-expanded', String(expandedIds.has(item.id)));
            }

            buttonsById.set(item.id, button);
            listItem.append(button);
            fragment.append(listItem);

            if (hasChildren) {
                const group = document.createElement('ul');

                group.role = 'group';
                group.append(renderItems(item.children, level + 1));
                listItem.append(group);
            }
        });

        return fragment;
    }

    function applyVisibility(itemsToApply, ancestorsVisible = true) {
        itemsToApply.forEach((item) => {
            const button = buttonsById.get(item.id);
            const expanded = expandedIds.has(item.id);

            if (button) {
                button.hidden = !ancestorsVisible || (searchQuery !== '' && !matchingIds(items).has(item.id));
                button.setAttribute('aria-selected', String(selectedId === item.id));

                if (multiple) {
                    const selection = selectionState(item);
                    button.setAttribute('aria-checked', selection);
                }

                if (item.children.length > 0) {
                    button.setAttribute('aria-expanded', String(expanded));
                }
            }

            applyVisibility(item.children, ancestorsVisible && (expanded || searchQuery !== ''));
        });
    }

    function matchingIds(itemsToMatch) {
        const matches = new Set();
        const query = searchQuery.toLocaleLowerCase();

        function visit(item) {
            const selfMatches = query === '' || item.label.toLocaleLowerCase().includes(query);
            const childMatches = item.children.map(visit).some(Boolean);

            if (selfMatches || childMatches) matches.add(item.id);

            return selfMatches || childMatches;
        }

        itemsToMatch.forEach(visit);

        return matches;
    }

    function expandMatchingPaths(itemsToSearch) {
        const query = searchQuery.toLocaleLowerCase();

        function visit(item) {
            const selfMatches = item.label.toLocaleLowerCase().includes(query);
            const childMatches = item.children.map(visit).some(Boolean);

            if (childMatches) expandedIds.add(item.id);

            return selfMatches || childMatches;
        }

        itemsToSearch.forEach(visit);
    }

    function visibleButtons() {
        return [...buttonsById.values()].filter((button) => !button.hidden);
    }

    function focusButton(button) {
        if (!button) {
            return;
        }

        buttonsById.forEach((node) => {
            node.tabIndex = -1;
        });
        button.tabIndex = 0;
        button.focus();
    }

    function descendantIds(item) {
        if (item.children.length === 0) {
            return [item.id];
        }

        return item.children.flatMap(descendantIds);
    }

    function selectionState(item) {
        const ids = descendantIds(item);
        const selected = ids.filter((id) => selectedIds.has(id)).length;

        if (selected === 0) {
            return 'false';
        }

        return selected === ids.length ? 'true' : 'mixed';
    }

    function syncValue() {
        if (valueInput instanceof HTMLInputElement) {
            valueInput.value = JSON.stringify([...selectedIds]);
        }
    }

    function setSelected(id) {
        const item = itemsById.get(id);

        if (!item) {
            return;
        }

        if (multiple) {
            const ids = descendantIds(item);
            const shouldSelect = ids.some((candidate) => !selectedIds.has(candidate));

            ids.forEach((candidate) => {
                if (shouldSelect) {
                    selectedIds.add(candidate);
                } else {
                    selectedIds.delete(candidate);
                }
            });
            syncValue();
            applyVisibility(items);
            persist();
            emit(root, 'selection-changed', { ids: [...selectedIds] });

            return;
        }

        selectedId = id;
        applyVisibility(items);
        persist();
        emit(root, 'selected', { id: item.id, label: item.label });
    }

    function setExpanded(id, expanded) {
        const item = itemsById.get(id);

        if (!item || (item.children.length === 0 && item.source === null) || expandedIds.has(id) === expanded) {
            return;
        }

        if (expanded) {
            expandedIds.add(id);
        } else {
            expandedIds.delete(id);
        }

        applyVisibility(items);
        persist();
        emit(root, expanded ? 'expanded' : 'collapsed', { id: item.id, label: item.label });
    }

    async function loadChildren(id) {
        const item = itemsById.get(id);

        if (!item?.source || loadingIds.has(id) || item.children.length > 0) return;

        loadingIds.add(id);
        root.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(item.source, { credentials: 'same-origin' });
            const payload = await response.json();
            if (!response.ok || !payload || !Array.isArray(payload.items)) throw new Error('Invalid lazy tree response.');

            item.children = normalizeItems(payload.items, new Set(itemsById.keys()), [item.id]);
            index(item.children, item.id);
            expandedIds.add(id);
            treeRoot.replaceChildren(renderItems(items));
            applyVisibility(items);
            persist();
        } catch {
            updateStatus(root, 'The tree branch could not be loaded.');
            emit(root, 'error', { reason: 'lazy-source-unavailable' });
        } finally {
            loadingIds.delete(id);
            root.setAttribute('aria-busy', 'false');
        }
    }

    function onClick(event) {
        const button = event.target.closest('[data-daisy-kit-tree-node]');

        if (!(button instanceof HTMLButtonElement) || !treeRoot.contains(button)) {
            return;
        }

        setSelected(button.dataset.daisyKitTreeNode);
        focusButton(button);
    }

    function onKeyDown(event) {
        const button = event.target.closest('[data-daisy-kit-tree-node]');
        const current = button instanceof HTMLButtonElement && treeRoot.contains(button) ? button : visibleButtons()[0];

        if (!current) {
            return;
        }

        const id = current.dataset.daisyKitTreeNode;
        const item = itemsById.get(id);
        const visible = visibleButtons();
        const position = visible.indexOf(current);

        if (event.key === 'ArrowDown' && position < visible.length - 1) {
            event.preventDefault();
            focusButton(visible[position + 1]);
        }

        if (event.key === 'ArrowUp' && position > 0) {
            event.preventDefault();
            focusButton(visible[position - 1]);
        }

        if (event.key === 'ArrowRight' && item) {
            event.preventDefault();

            if (item.children.length === 0 && item.source !== null) {
                void loadChildren(item.id);

                return;
            }

            if (item.children.length > 0 && !expandedIds.has(item.id)) {
                setExpanded(item.id, true);

                return;
            }

            if (item.children.length > 0) {
                focusButton(buttonsById.get(item.children[0].id));
            }
        }

        if (event.key === 'ArrowLeft' && item) {
            event.preventDefault();

            if (item.children.length > 0 && expandedIds.has(item.id)) {
                setExpanded(item.id, false);

                return;
            }

            focusButton(buttonsById.get(item.parentId));
        }

        if (event.key === 'Home') {
            event.preventDefault();
            focusButton(visible[0]);
        }

        if (event.key === 'End') {
            event.preventDefault();
            focusButton(visible.at(-1));
        }

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            setSelected(id);
        }
    }

    index(items);
    const stored = persistedState();
    if (stored) {
        (Array.isArray(stored.expandedIds) ? stored.expandedIds : []).forEach((id) => {
            if (itemsById.has(id)) expandedIds.add(id);
        });
        selectedId = typeof stored.selectedId === 'string' && itemsById.has(stored.selectedId) ? stored.selectedId : null;
        (Array.isArray(stored.selectedIds) ? stored.selectedIds : []).forEach((id) => {
            if (typeof id === 'string' && itemsById.has(id)) selectedIds.add(id);
        });
    }
    treeRoot.replaceChildren(renderItems(items));
    applyVisibility(items);

    if (items.length === 0) {
        updateStatus(root, 'No tree items are available.');
        root.dataset.daisyKitState = 'empty';
    } else {
        visibleButtons()[0].tabIndex = 0;
        root.dataset.daisyKitState = 'ready';
    }

    treeRoot.addEventListener('click', onClick);
    treeRoot.addEventListener('keydown', onKeyDown);
    const onSearch = (event) => {
        searchQuery = event.currentTarget.value.trim();
        if (searchQuery !== '') expandMatchingPaths(items);
        applyVisibility(items);
        persist();
    };
    searchInput?.addEventListener('input', onSearch);

    return () => {
        treeRoot.removeEventListener('click', onClick);
        treeRoot.removeEventListener('keydown', onKeyDown);
        searchInput?.removeEventListener('input', onSearch);
        treeRoot.innerHTML = initialMarkup;
    };
}

const module = createMountable('tree', initialize);

export const { mount, mountAll, unmount } = module;
