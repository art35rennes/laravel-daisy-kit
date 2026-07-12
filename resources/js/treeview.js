const instances = new WeakMap();
const storagePrefix = 'treeview:';

function treeItems(root) {
    return Array.from(root.querySelectorAll('[role="treeitem"]'));
}

function directGroup(item) {
    return Array.from(item.children).find((child) => child.matches?.('[role="group"]')) || null;
}

function directHeader(item) {
    return Array.from(item.children).find((child) => child.matches?.('[data-node-header]')) || null;
}

function directControl(item) {
    return directHeader(item)?.querySelector('[data-tree-control]') || null;
}

function directChildren(item) {
    const group = directGroup(item);
    return group ? Array.from(group.children).filter((child) => child.matches?.('[role="treeitem"]')) : [];
}

function parentItem(item) {
    return item.parentElement?.closest('[role="treeitem"]') || null;
}

function findItem(root, id) {
    return treeItems(root).find((item) => item.dataset.id === String(id)) || null;
}

function isVisible(item) {
    if (item.hidden || item.classList.contains('hidden') || item.getAttribute('aria-hidden') === 'true') return false;

    let parent = parentItem(item);
    while (parent) {
        const group = directGroup(parent);
        if (parent.getAttribute('aria-expanded') !== 'true' || group?.classList.contains('hidden')) return false;
        parent = parentItem(parent);
    }

    return true;
}

function validateNode(node) {
    if (!node || typeof node !== 'object' || Array.isArray(node)) return false;
    if (!['string', 'number'].includes(typeof node.id)) return false;
    if (!['string', 'number'].includes(typeof node.label)) return false;
    if (node.children !== undefined && !Array.isArray(node.children)) return false;
    if (node.lazy === true && (node.children || []).length > 0) return false;
    return (node.children || []).every(validateNode);
}

function buildUrl(baseUrl, parameter, value) {
    const url = new URL(baseUrl, window.location.origin);
    url.searchParams.set(parameter, String(value));
    return url.toString();
}

function parseJsonAttribute(root, attribute, fallback) {
    try {
        return JSON.parse(root.dataset[attribute] || '');
    } catch {
        return fallback;
    }
}

class TreeView {
    constructor(root) {
        this.root = root;
        this.tree = root.querySelector('[role="tree"]');
        this.selection = root.dataset.selection === 'single' ? 'single' : 'multiple';
        this.initialValue = this.readInitialValue();
        this.initialExpandPaths = this.readInitialExpandPaths();
        this.hydrating = false;
        this.expansionBeforeSearch = null;
        this.searchAbortController = null;
        this.loadControllers = new Map();
        this.typeAhead = '';
        this.typeAheadTimer = null;
        this.handleKeyDown = this.onKeyDown.bind(this);
        this.handleClick = this.onClick.bind(this);
        this.handleChange = this.onChange.bind(this);
        this.handleSearchInput = this.onSearchInput.bind(this);
        this.handleSearchKeyDown = this.onSearchKeyDown.bind(this);
        this.handleSearchButton = () => this.runSearch(this.searchInput?.value || '');

        this.tree?.addEventListener('keydown', this.handleKeyDown);
        this.tree?.addEventListener('click', this.handleClick);
        this.tree?.addEventListener('change', this.handleChange);

        this.searchInput = root.querySelector('[data-tree-search]');
        this.searchButton = root.querySelector('[data-tree-search-button]');
        this.searchInput?.addEventListener('input', this.handleSearchInput);
        this.searchInput?.addEventListener('keydown', this.handleSearchKeyDown);
        this.searchButton?.addEventListener('click', this.handleSearchButton);

        this.normalizeState();
        this.setFocusItem(this.firstSelectedItem() || this.visibleItems()[0], false);
        const requiresHydration = this.initialExpandPaths.length > 0 || this.root.dataset.persist === 'true';

        if (requiresHydration) {
            this.ready = this.hydrateInitialState();
        } else {
            this.setValue(this.initialValue, { silent: true });
            this.initialValue = this.getValue();
            this.ready = this.markReady();
        }
    }

    visibleItems() {
        return treeItems(this.root).filter(isVisible);
    }

    firstSelectedItem() {
        return treeItems(this.root).find((item) => {
            if (this.selection === 'single') return item.getAttribute('aria-selected') === 'true';
            return item.getAttribute('aria-checked') === 'true';
        });
    }

    setFocusItem(item, focus = true) {
        if (!item) return;
        treeItems(this.root).forEach((candidate) => candidate.tabIndex = candidate === item ? 0 : -1);
        if (focus) item.focus();
    }

    readValue() {
        if (this.selection === 'single') {
            const selected = treeItems(this.root).find((item) => directControl(item)?.checked || item.getAttribute('aria-selected') === 'true');
            return selected?.dataset.id || null;
        }

        const selectedItems = treeItems(this.root)
            .filter((item) => directControl(item)?.checked);

        if (this.root.dataset.valueMode === 'selected-roots') {
            return selectedItems
                .filter((item) => !this.hasSelectedAncestor(item))
                .map((item) => item.dataset.id);
        }

        return selectedItems
            .filter((item) => directChildren(item).length === 0 && !item.dataset.lazy)
            .map((item) => item.dataset.id);
    }

    readInitialValue() {
        const fallback = this.readRenderedValue();
        const value = Object.hasOwn(this.root.dataset, 'initialValue')
            ? parseJsonAttribute(this.root, 'initialValue', fallback)
            : fallback;

        if (this.selection === 'single') {
            return typeof value === 'string' || typeof value === 'number' ? String(value) : null;
        }

        return Array.isArray(value)
            ? value.filter((id) => typeof id === 'string' || typeof id === 'number').map(String)
            : fallback;
    }

    readRenderedValue() {
        const selectedIds = treeItems(this.root)
            .filter((item) => directControl(item)?.checked)
            .map((item) => item.dataset.id);

        return this.selection === 'single' ? (selectedIds[0] || null) : selectedIds;
    }

    readInitialExpandPaths() {
        const paths = parseJsonAttribute(this.root, 'initialExpandPaths', []);

        if (!Array.isArray(paths)) return [];

        return paths
            .filter(Array.isArray)
            .map((path) => path.filter((id) => typeof id === 'string' || typeof id === 'number').map(String))
            .filter((path) => path.length > 0);
    }

    hasSelectedAncestor(item) {
        let ancestor = parentItem(item);

        while (ancestor) {
            if (directControl(ancestor)?.checked) return true;
            ancestor = parentItem(ancestor);
        }

        return false;
    }

    getValue() {
        const value = this.readValue();
        return Array.isArray(value) ? [...value] : value;
    }

    setValue(value, options = {}) {
        if (this.selection === 'single') {
            const selectedId = value === null || value === undefined ? null : String(value);
            treeItems(this.root).forEach((item) => {
                const selected = item.dataset.id === selectedId;
                const control = directControl(item);
                if (control) control.checked = selected;
                item.setAttribute('aria-selected', selected ? 'true' : 'false');
                this.syncHighlight(item, selected, false);
            });
        } else {
            const selectedIds = new Set((Array.isArray(value) ? value : [value]).filter((id) => id !== null && id !== undefined).map(String));
            treeItems(this.root).forEach((item) => {
                const control = directControl(item);
                if (!control) return;
                const selected = selectedIds.has(item.dataset.id);
                control.checked = selected;
                control.indeterminate = false;
            });
            treeItems(this.root).filter((item) => selectedIds.has(item.dataset.id) && directChildren(item).length > 0).forEach((item) => {
                this.setDescendants(item, true);
            });
            this.recalculateAll();
        }

        if (!options.silent) this.emitChange(options.source || 'api', null);
        return this.getValue();
    }

    reset() {
        this.setValue(this.initialValue, { source: 'reset' });
        this.clearSearch();
        return this.getValue();
    }

    normalizeState() {
        if (this.selection === 'multiple') {
            treeItems(this.root).forEach((item) => {
                const control = directControl(item);
                if (!control) return;
                const mixed = item.getAttribute('aria-checked') === 'mixed' || control.dataset.indeterminate === 'true';
                control.indeterminate = mixed;
                control.setAttribute('aria-checked', mixed ? 'mixed' : (control.checked ? 'true' : 'false'));
            });
            this.recalculateAll();
        } else {
            this.setValue(this.initialValue, { silent: true });
        }
        this.syncExpansionIcons();
    }

    async hydrateInitialState() {
        this.hydrating = true;
        this.root.setAttribute('aria-busy', 'true');
        this.setStatus(this.root.dataset.loadingLabel || 'Loading…');

        try {
            await this.restoreExpansion();

            for (const path of this.initialExpandPaths) {
                for (const nodeId of path) {
                    await this.expand(nodeId);
                }
            }

            this.setValue(this.initialValue, { silent: true });
            this.initialValue = this.getValue();
            this.setFocusItem(this.firstSelectedItem() || this.visibleItems()[0], false);
            this.markReady();
        } finally {
            this.hydrating = false;
            this.root.removeAttribute('aria-busy');
            this.setStatus('');
        }
    }

    markReady() {
        this.root.dispatchEvent(new CustomEvent('daisy:tree-ready', {
            bubbles: true,
            detail: { value: this.getValue() },
        }));

        return Promise.resolve(this.getValue());
    }

    setDescendants(item, checked) {
        directChildren(item).forEach((child) => {
            const control = directControl(child);
            if (control && !control.disabled && child.getAttribute('aria-disabled') !== 'true') {
                control.checked = checked;
                control.indeterminate = false;
            }
            this.setDescendants(child, checked);
        });
    }

    recalculateAll() {
        const visit = (item) => {
            const children = directChildren(item);
            children.forEach(visit);
            const control = directControl(item);
            if (!control) return;

            if (children.length > 0) {
                const states = children.map((child) => {
                    const childControl = directControl(child);
                    if (childControl?.indeterminate) return 'mixed';
                    return childControl?.checked ? 'checked' : 'unchecked';
                });
                control.checked = states.every((state) => state === 'checked');
                control.indeterminate = !control.checked && states.some((state) => state !== 'unchecked');
            }

            const state = control.indeterminate ? 'mixed' : (control.checked ? 'true' : 'false');
            control.setAttribute('aria-checked', state);
            item.setAttribute('aria-checked', state);
            this.syncHighlight(item, control.checked, control.indeterminate);
        };

        Array.from(this.tree?.children || []).filter((item) => item.matches?.('[role="treeitem"]')).forEach(visit);
    }

    syncHighlight(item, selected, mixed) {
        directHeader(item)?.classList.toggle('bg-base-200', selected || mixed);
    }

    select(item, source) {
        if (this.hydrating || !item || this.root.dataset.disabled === 'true' || item.getAttribute('aria-disabled') === 'true') return;
        const control = directControl(item);
        if (!control || control.disabled) return;

        if (this.selection === 'single') {
            this.setValue(item.dataset.id, { silent: true });
        } else {
            const checked = !(control.checked || control.indeterminate);
            control.checked = checked;
            control.indeterminate = false;
            this.setDescendants(item, checked);
            this.recalculateAll();
        }

        this.emitChange(source, item.dataset.id);
    }

    emitChange(source, nodeId) {
        this.root.dispatchEvent(new CustomEvent('daisy:tree-change', {
            bubbles: true,
            detail: { value: this.getValue(), nodeId, source },
        }));
    }

    async expand(nodeId) {
        return this.setExpanded(findItem(this.root, nodeId), true, 'api');
    }

    collapse(nodeId) {
        return this.setExpanded(findItem(this.root, nodeId), false, 'api');
    }

    async toggle(nodeId) {
        const item = findItem(this.root, nodeId);
        if (!item) return false;
        return this.setExpanded(item, item.getAttribute('aria-expanded') !== 'true', 'api');
    }

    async setExpanded(item, expanded, source) {
        if (!item || !item.hasAttribute('aria-expanded')) return false;
        item.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        directGroup(item)?.classList.toggle('hidden', !expanded);
        this.syncExpansionIcons(item);
        if (!['restore', 'search', 'search-reset'].includes(source)) this.persistExpansion();

        if (expanded && item.dataset.lazy === '1' && item.dataset.loaded !== 'true') {
            await this.load(item, source);
        }

        return true;
    }

    syncExpansionIcons(scope = this.root) {
        const items = scope.matches?.('[role="treeitem"]') ? [scope] : treeItems(scope);
        items.forEach((item) => {
            if (!item.hasAttribute('aria-expanded')) return;
            const expanded = item.getAttribute('aria-expanded') === 'true';
            const header = directHeader(item);
            header?.querySelector('[data-tree-collapsed-icon]')?.classList.toggle('hidden', expanded);
            header?.querySelector('[data-tree-expanded-icon]')?.classList.toggle('hidden', !expanded);
            const button = header?.querySelector('[data-tree-toggle]');
            if (button) {
                const label = header.querySelector('[data-tree-label]')?.textContent || item.dataset.id;
                const template = expanded
                    ? (this.root.dataset.collapseLabel || 'Collapse :label')
                    : (this.root.dataset.expandLabel || 'Expand :label');
                button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                button.setAttribute('aria-label', template.replace(':label', label));
            }
        });
    }

    createNode(node, level, parentDisabled = false, inheritedSelection = false) {
        if (!validateNode(node)) throw new TypeError('Invalid tree node');
        const id = String(node.id);
        const children = node.children || [];
        const hasChildren = node.lazy === true || children.length > 0;
        const disabled = parentDisabled || node.disabled === true;
        const item = document.createElement('li');
        item.id = `${this.root.id}-item-${this.safeDomToken(id, level)}`;
        item.setAttribute('role', 'treeitem');
        item.setAttribute('aria-level', String(level));
        item.dataset.id = id;
        item.dataset.level = String(level);
        item.tabIndex = -1;
        item.className = 'outline-none focus-visible:ring-2 focus-visible:ring-primary';
        if (hasChildren) item.setAttribute('aria-expanded', node.expanded === true && node.lazy !== true ? 'true' : 'false');
        if (node.lazy === true) item.dataset.lazy = '1';
        if (disabled) item.setAttribute('aria-disabled', 'true');

        const header = document.createElement('div');
        header.dataset.nodeHeader = '1';
        header.className = `flex items-center gap-2 rounded px-2 py-1 hover:bg-base-200 daisy-tree-indent-${Math.min(64, Math.max(0, level - 1))}`;
        header.append(this.createToggle(hasChildren));

        const control = document.createElement('input');
        control.type = this.selection === 'single' ? 'radio' : 'checkbox';
        control.dataset.treeControl = '1';
        control.tabIndex = -1;
        control.disabled = disabled;
        control.className = `${this.selection === 'single' ? 'radio' : 'checkbox'} ${this.selection === 'single' ? 'radio' : 'checkbox'}-${this.root.dataset.controlSize || 'sm'} shrink-0`;
        if (this.selection === 'single') {
            item.setAttribute('aria-selected', 'false');
            if (this.root.dataset.name) control.name = this.root.dataset.name;
            control.value = id;
        } else {
            item.setAttribute('aria-checked', inheritedSelection ? 'true' : 'false');
            control.checked = inheritedSelection;
            if (!hasChildren && this.root.dataset.name) control.name = `${this.root.dataset.name}[]`;
            control.value = id;
        }
        header.append(control);

        const label = document.createElement('span');
        label.dataset.treeLabel = '1';
        label.className = `min-w-0 flex-1 select-none break-words${disabled ? ' opacity-50' : ' cursor-default'}`;
        label.textContent = String(node.label);
        header.append(label);
        item.append(header);

        if (hasChildren) {
            const group = document.createElement('ul');
            group.setAttribute('role', 'group');
            group.dataset.treeGroup = '1';
            group.className = `ml-4 border-l pl-2${node.expanded === true && node.lazy !== true ? '' : ' hidden'}`;
            children.forEach((child) => group.append(this.createNode(child, level + 1, disabled, inheritedSelection)));
            item.append(group);
        }

        return item;
    }

    createToggle(hasChildren) {
        if (!hasChildren) {
            const spacer = document.createElement('span');
            spacer.className = 'inline-block w-6 shrink-0';
            spacer.setAttribute('aria-hidden', 'true');
            return spacer;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.tabIndex = -1;
        button.dataset.treeToggle = '1';
        button.className = 'btn btn-ghost btn-xs btn-square shrink-0';
        button.setAttribute('aria-label', this.root.dataset.expandLabel || 'Expand');
        const collapsed = document.createElement('span');
        collapsed.dataset.treeCollapsedIcon = '1';
        collapsed.textContent = '›';
        const expanded = document.createElement('span');
        expanded.dataset.treeExpandedIcon = '1';
        expanded.className = 'hidden';
        expanded.textContent = '⌄';
        button.append(collapsed, expanded);
        return button;
    }

    safeDomToken(id, level) {
        let hash = 2166136261;
        for (const character of `${id}-${level}`) {
            hash ^= character.charCodeAt(0);
            hash = Math.imul(hash, 16777619);
        }
        return Math.abs(hash).toString(36);
    }

    async load(item, source = 'api') {
        if (!this.root.dataset.lazyUrl) return false;
        this.loadControllers.get(item)?.abort();
        const controller = new AbortController();
        this.loadControllers.set(item, controller);
        item.dataset.loading = 'true';
        this.setStatus(this.root.dataset.loadingLabel || 'Loading…');

        try {
            const response = await fetch(buildUrl(this.root.dataset.lazyUrl, this.root.dataset.lazyParam || 'node', item.dataset.id), { signal: controller.signal });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const payload = await response.json();
            if (!payload || !Array.isArray(payload.items) || !payload.items.every(validateNode)) throw new TypeError('Invalid lazy tree response');
            const group = directGroup(item);
            const inheritSelection = this.selection === 'multiple' && directControl(item)?.checked === true;
            const level = Number.parseInt(item.dataset.level || '1', 10) + 1;
            const fragment = document.createDocumentFragment();
            payload.items.forEach((node) => fragment.append(this.createNode(node, level, item.getAttribute('aria-disabled') === 'true', inheritSelection)));
            group.replaceChildren(fragment);
            item.dataset.loaded = 'true';
            delete item.dataset.loadError;
            this.recalculateAll();
            this.syncExpansionIcons(group);
            this.setStatus('');
            this.root.dispatchEvent(new CustomEvent('daisy:tree-load', {
                bubbles: true,
                detail: { value: this.getValue(), nodeId: item.dataset.id, source },
            }));
            return true;
        } catch (error) {
            if (error.name === 'AbortError') return false;
            item.dataset.loadError = 'true';
            this.setStatus(this.root.dataset.loadErrorLabel || 'Unable to load items. Activate to retry.');
            this.root.dispatchEvent(new CustomEvent('daisy:tree-error', {
                bubbles: true,
                detail: { value: this.getValue(), nodeId: item.dataset.id, source, error },
            }));
            return false;
        } finally {
            delete item.dataset.loading;
            this.loadControllers.delete(item);
        }
    }

    async reload(nodeId = null) {
        const items = nodeId === null ? treeItems(this.root).filter((item) => item.dataset.lazy === '1') : [findItem(this.root, nodeId)].filter(Boolean);
        const results = [];
        for (const item of items) {
            delete item.dataset.loaded;
            results.push(await this.load(item, 'reload'));
        }
        return results.every(Boolean);
    }

    search(term) {
        return this.runSearch(String(term || '').trim());
    }

    async runSearch(term) {
        const minimum = Number.parseInt(this.root.dataset.searchMin || '2', 10);
        if (term.length < minimum) {
            this.clearSearch();
            return [];
        }

        if (!this.expansionBeforeSearch) {
            this.expansionBeforeSearch = new Map(treeItems(this.root).filter((item) => item.hasAttribute('aria-expanded')).map((item) => [item.dataset.id, item.getAttribute('aria-expanded') === 'true']));
        }

        if (this.root.dataset.searchUrl) await this.remoteSearch(term);
        this.clearSearchPresentation();
        const normalizedTerm = term.toLocaleLowerCase();
        const matches = [];
        const visit = (item) => {
            let childMatch = false;
            directChildren(item).forEach((child) => {
                childMatch = visit(child) || childMatch;
            });
            const label = directHeader(item)?.querySelector('[data-tree-label]');
            const directMatch = (label?.textContent || '').toLocaleLowerCase().includes(normalizedTerm);
            const visible = directMatch || childMatch;
            item.classList.toggle('hidden', !visible);
            item.setAttribute('aria-hidden', visible ? 'false' : 'true');
            if (visible && directGroup(item)) this.setExpanded(item, true, 'search');
            if (directMatch && label) {
                matches.push(item.dataset.id);
                this.highlight(label, term);
            }
            return visible;
        };
        Array.from(this.tree.children).filter((item) => item.matches?.('[role="treeitem"]')).forEach(visit);
        this.setStatus(matches.length ? '' : (this.root.dataset.noResultsLabel || 'No results'));
        return matches;
    }

    highlight(label, term) {
        const text = label.textContent || '';
        label.dataset.originalLabel = text;
        const index = text.toLocaleLowerCase().indexOf(term.toLocaleLowerCase());
        if (index < 0) return;
        const mark = document.createElement('mark');
        mark.textContent = text.slice(index, index + term.length);
        label.replaceChildren(document.createTextNode(text.slice(0, index)), mark, document.createTextNode(text.slice(index + term.length)));
    }

    clearSearchPresentation() {
        treeItems(this.root).forEach((item) => {
            item.classList.remove('hidden');
            item.removeAttribute('aria-hidden');
            const label = directHeader(item)?.querySelector('[data-tree-label]');
            if (label?.dataset.originalLabel !== undefined) {
                label.textContent = label.dataset.originalLabel;
                delete label.dataset.originalLabel;
            }
        });
    }

    clearSearch() {
        this.searchAbortController?.abort();
        this.clearSearchPresentation();
        if (this.expansionBeforeSearch) {
            this.expansionBeforeSearch.forEach((expanded, id) => this.setExpanded(findItem(this.root, id), expanded, 'search-reset'));
            this.expansionBeforeSearch = null;
        }
        this.setStatus('');
    }

    async remoteSearch(term) {
        this.searchAbortController?.abort();
        this.searchAbortController = new AbortController();
        try {
            const response = await fetch(buildUrl(this.root.dataset.searchUrl, this.root.dataset.searchParam || 'q', term), { signal: this.searchAbortController.signal });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const payload = await response.json();
            if (!payload || !Array.isArray(payload.paths) || !payload.paths.every((path) => Array.isArray(path))) throw new TypeError('Invalid tree search response');
            for (const path of payload.paths.slice(0, 50)) await this.expandPath(path);
        } catch (error) {
            if (error.name === 'AbortError') return;
            this.root.dispatchEvent(new CustomEvent('daisy:tree-error', {
                bubbles: true,
                detail: { value: this.getValue(), nodeId: null, source: 'search', error },
            }));
        }
    }

    async expandPath(path) {
        for (const id of path.slice(0, -1)) await this.expand(id);
    }

    onKeyDown(event) {
        const current = event.target.closest?.('[role="treeitem"]');
        if (!current) return;
        const visible = this.visibleItems();
        const index = visible.indexOf(current);
        const focus = (item) => {
            event.preventDefault();
            this.setFocusItem(item);
        };

        if (event.key === 'ArrowDown') focus(visible[Math.min(visible.length - 1, index + 1)]);
        else if (event.key === 'ArrowUp') focus(visible[Math.max(0, index - 1)]);
        else if (event.key === 'Home') focus(visible[0]);
        else if (event.key === 'End') focus(visible[visible.length - 1]);
        else if (event.key === 'ArrowRight') {
            event.preventDefault();
            if (current.getAttribute('aria-expanded') === 'false') this.setExpanded(current, true, 'keyboard');
            else this.setFocusItem(directChildren(current)[0]);
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            if (current.getAttribute('aria-expanded') === 'true') this.setExpanded(current, false, 'keyboard');
            else this.setFocusItem(parentItem(current));
        } else if (event.key === ' ' || event.key === 'Enter') {
            event.preventDefault();
            this.select(current, 'keyboard');
        } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
            this.handleTypeAhead(event.key, current);
        }
    }

    handleTypeAhead(character, current) {
        window.clearTimeout(this.typeAheadTimer);
        this.typeAhead += character.toLocaleLowerCase();
        this.typeAheadTimer = window.setTimeout(() => this.typeAhead = '', 500);
        const visible = this.visibleItems();
        const start = visible.indexOf(current);
        const ordered = [...visible.slice(start + 1), ...visible.slice(0, start + 1)];
        const match = ordered.find((item) => directHeader(item)?.querySelector('[data-tree-label]')?.textContent?.trim().toLocaleLowerCase().startsWith(this.typeAhead));
        if (match) this.setFocusItem(match);
    }

    onClick(event) {
        if (this.hydrating) return;
        const item = event.target.closest?.('[role="treeitem"]');
        if (!item) return;
        if (event.target.closest('[data-tree-toggle]')) {
            this.setExpanded(item, item.getAttribute('aria-expanded') !== 'true', 'pointer');
            this.setFocusItem(item);
        } else if (event.target.closest('[data-tree-label]')) {
            this.select(item, 'pointer');
            this.setFocusItem(item);
        }
    }

    onChange(event) {
        if (!event.target.matches?.('[data-tree-control]')) return;
        if (this.hydrating) return;
        const item = event.target.closest('[role="treeitem"]');
        if (this.selection === 'single') this.setValue(item.dataset.id, { silent: true });
        else {
            this.setDescendants(item, event.target.checked);
            this.recalculateAll();
        }
        this.emitChange('control', item.dataset.id);
    }

    onSearchInput() {
        const term = this.searchInput?.value || '';
        window.clearTimeout(this.searchTimer);
        if (this.root.dataset.searchAuto === 'false') return;
        const delay = Number.parseInt(this.root.dataset.searchDebounce || '300', 10);
        this.searchTimer = window.setTimeout(() => this.runSearch(term), delay);
    }

    onSearchKeyDown(event) {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        this.runSearch(this.searchInput?.value || '');
    }

    setStatus(message) {
        const status = this.root.querySelector('[data-tree-status]');
        if (status) status.textContent = message;
    }

    persistExpansion() {
        if (this.root.dataset.persist !== 'true' || !this.root.id) return;
        const expanded = treeItems(this.root).filter((item) => item.getAttribute('aria-expanded') === 'true').map((item) => item.dataset.id);
        try {
            sessionStorage.setItem(`${storagePrefix}${this.root.id}`, JSON.stringify({ expanded }));
        } catch {}
    }

    async restoreExpansion() {
        if (this.root.dataset.persist !== 'true' || !this.root.id) return;
        try {
            const state = JSON.parse(sessionStorage.getItem(`${storagePrefix}${this.root.id}`));
            if (!Array.isArray(state?.expanded)) return;
            for (const id of state.expanded) {
                await this.setExpanded(findItem(this.root, id), true, 'restore');
            }
        } catch {}
    }

    destroy() {
        this.tree?.removeEventListener('keydown', this.handleKeyDown);
        this.tree?.removeEventListener('click', this.handleClick);
        this.tree?.removeEventListener('change', this.handleChange);
        this.searchInput?.removeEventListener('input', this.handleSearchInput);
        this.searchInput?.removeEventListener('keydown', this.handleSearchKeyDown);
        this.searchButton?.removeEventListener('click', this.handleSearchButton);
        this.searchAbortController?.abort();
        this.loadControllers.forEach((controller) => controller.abort());
        window.clearTimeout(this.searchTimer);
        window.clearTimeout(this.typeAheadTimer);
        instances.delete(this.root);
        delete this.root.__treeView;
    }
}

function init(root) {
    if (!root) return null;
    if (instances.has(root)) return instances.get(root);
    const instance = new TreeView(root);
    instances.set(root, instance);
    root.__treeView = instance;
    return instance;
}

function initAll(scope = document) {
    return Array.from(scope.querySelectorAll('[data-treeview="1"]')).map(init);
}

window.DaisyTreeView = {
    init,
    initAll,
    get(root) {
        return instances.get(root) || null;
    },
};

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => initAll());
else initAll();

export default init;
export { init, initAll };
