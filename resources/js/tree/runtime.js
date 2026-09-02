import { createModel, safeSource } from './model.js';
import { createRenderer } from './render.js';
import { matchingNodes, nodeMatches } from './search.js';
import { createTransport } from './transport.js';
import { createPersistence } from './persistence.js';
import { translator } from './labels.js';

export function initialize(root, configuration) {
    const translate = translator(configuration.labels);
    const tree = root.querySelector('[data-daisy-kit-tree-root]');
    const status = root.querySelector('[data-daisy-kit-status]');
    function emit(name, detail) {
        root.dispatchEvent(new CustomEvent(`daisy-kit:tree:${name}`, { bubbles: true, detail }));
    }
    function showStatus(message = '') {
        if (!status) return;
        status.textContent = message;
        status.hidden = !message;
    }
    if (!tree) {
        showStatus(translate('missingContent'));
        emit('error', { code: 'missing-content', message: translate('missingContent') });
        root.dataset.daisyKitState = 'error';
        return null;
    }
    const originalMarkup = tree.innerHTML;
    const originalBusy = root.getAttribute('aria-busy');
    const originalMultiple = tree.getAttribute('aria-multiselectable');
    const model = createModel(configuration);
    const renderer = createRenderer(root, model, translate);
    const persistence = createPersistence(configuration.persistenceKey);
    const source = safeSource(configuration.searchSource);
    const input = root.querySelector('[data-daisy-kit-tree-search]');
    const field = root.querySelector('[data-daisy-kit-tree-value]');
    const originalFieldValue = field?.value;
    const originalFieldDisabled = field?.disabled;
    const loadingIds = new Set();
    const errors = new Map();
    let active = true;
    let searching = false;
    let query = '';
    let draft = '';
    let remoteMatches = null;
    let expansionBeforeSearch = null;
    let timer = null;
    let typeBuffer = '';
    let typeTime = 0;
    let revision = 0;
    let searchError = false;
    let customFilter = null;
    let expansionBeforeFilter = null;
    let filterErrorReported = false;
    const branchTasks = new Map();

    function nodeSnapshot(node) {
        return Object.freeze({
            id: node.id, parentId: node.parentId, label: node.label, description: node.description,
            badge: node.badge, disabled: node.disabled, loaded: node.loaded, hasMore: node.nextCursor !== null,
        });
    }
    function matches() {
        const searched = source && query ? remoteMatches : matchingNodes(model, query, configuration.searchMatch);
        if (!customFilter) return searched;
        const filtered = new Set();
        for (const node of model.nodes.values()) {
            const matchesSearch = source && query
                ? remoteMatches?.has(node.id)
                : nodeMatches(node, query, configuration.searchMatch);
            if (!matchesSearch) continue;
            try {
                if (!customFilter(nodeSnapshot(node))) continue;
            } catch {
                if (!filterErrorReported) {
                    filterErrorReported = true;
                    emit('error', { code: 'filter-failed', message: translate('filterError') });
                }
                continue;
            }
            filtered.add(node.id);
            model.ancestors(node.id).forEach((id) => filtered.add(id));
        }
        return filtered;
    }
    function visibleIds() { return model.visible(matches()); }
    function valueArray() {
        const value = model.getValue();
        return Array.isArray(value) ? value : value === null ? [] : [value];
    }
    function getState() {
        const visible = visibleIds();
        const values = valueArray();
        const visibleSelected = values.filter((id) => visible.includes(id)).length;
        return {
            value: model.getValue(), values, expandedIds: [...model.expanded], visibleIds: visible,
            query, searchDraft: draft, loadingIds: [...loadingIds], searching,
            filter: Object.freeze({ active: customFilter !== null }),
            pagination: Object.fromEntries([...model.nodes.values()]
                .filter((node) => node.nextCursor !== null)
                .map((node) => [node.id, { hasMore: true, nextCursor: node.nextCursor }])),
            selection: { total: values.length, visible: visibleSelected, hidden: values.length - visibleSelected },
        };
    }
    function persist() {
        const ids = [...new Set([...valueArray(), ...model.expanded])];
        persistence.write({ value: model.getValue(), expandedIds: [...(expansionBeforeSearch ?? model.expanded)],
            paths: ids.map((id) => [...model.ancestors(id).reverse(), id]) });
    }
    function render() {
        if (!active) return;
        const state = getState();
        renderer.render({
            visibleIds: state.visibleIds, loadingIds, errors,
            highlight: configuration.highlightMatches && query ? { query, mode: configuration.searchMatch } : null,
        });
        root.setAttribute('aria-busy', String(searching || loadingIds.size > 0));
        const loading = root.querySelector('[data-daisy-kit-tree-loading]');
        if (loading) loading.textContent = searching || loadingIds.size > 0 ? translate('loading') : '';
        root.dataset.daisyKitState = model.nodes.size ? 'ready' : 'empty';
        if (field) {
            field.value = JSON.stringify(state.values);
            field.disabled = model.disabled;
        }
        const summary = root.querySelector('[data-daisy-kit-tree-summary]');
        if (summary) summary.textContent = translate('summary', state.selection);
        const results = root.querySelector('[data-daisy-kit-tree-results]');
        if (results) results.textContent = translate('results', { count: state.visibleIds.length });
        const empty = root.querySelector('[data-daisy-kit-tree-empty]');
        if (empty) {
            empty.hidden = state.visibleIds.length > 0 || searching;
            empty.textContent = translate(query ? 'noResults' : 'empty');
        }
        root.querySelectorAll('[data-tree-command]').forEach((button) => {
            const action = button.dataset.treeCommand;
            if (action === 'applySearch') {
                button.hidden = configuration.searchMode !== 'manual' && !searchError;
                button.textContent = translate(searchError ? 'retry' : 'applySearch');
            }
            button.disabled = (['clear', 'selectVisible'].includes(action) && model.disabled)
                || (action === 'clear' && state.values.length === 0)
                || (action === 'applySearch' && draft === query && !searchError)
                || (action === 'clearSearch' && !draft && !query);
        });
    }
    const transport = createTransport((loading, key) => {
        if (key === 'search') searching = loading;
        else loading ? loadingIds.add(key.slice(7)) : loadingIds.delete(key.slice(7));
        emit('loading', key === 'search' ? { loading, query: draft } : { loading, id: key.slice(7) });
        render();
    });

    function publish() {
        revision += 1;
        render();
        persist();
        emit('change', { value: model.getValue(), values: valueArray() });
        field?.dispatchEvent(new Event('input', { bubbles: true }));
        field?.dispatchEvent(new Event('change', { bubbles: true }));
    }
    function setValue(value) {
        if (!active || !model.setValue(value)) return false;
        publish();
        return true;
    }
    function clear() { return setValue(model.multiple ? [] : null); }
    function setExpanded(id, expanded) {
        if (!active || !model.branch(id) || model.expanded.has(id) === expanded) return false;
        expanded ? model.expanded.add(id) : model.expanded.delete(id);
        render();
        persist();
        emit(expanded ? 'expanded' : 'collapsed', { id, label: model.nodes.get(id).label });
        return true;
    }

    function load(id, reload = false, append = false) {
        if (branchTasks.has(id)) return branchTasks.get(id);
        const node = model.nodes.get(id);
        if (!active || !node?.source || (!reload && (node.loaded || node.children.length > 0))) return Promise.resolve(false);
        const task = (async () => {
            errors.delete(id);
            const before = JSON.stringify(model.getValue());
            try {
                const url = new URL(node.source);
                if (append && node.nextCursor !== null) url.searchParams.set('cursor', node.nextCursor);
                const result = await transport.request(`branch:${id}`, url);
                if (!active || result === null || !model.nodes.has(id)) return false;
                model.merge(result.items, id, result.nextCursor === null, !append);
                model.nodes.get(id).nextCursor = result.nextCursor;
                errors.delete(id);
                render();
                if (before !== JSON.stringify(model.getValue())) publish();
                return true;
            } catch {
                if (!active) return false;
                errors.set(id, true);
                render();
                emit('error', { code: 'lazy-source-unavailable', message: translate('loadError'), id });
                return false;
            } finally { branchTasks.delete(id); }
        })();
        branchTasks.set(id, task);
        return task;
    }
    function loadMore(id) {
        const node = model.nodes.get(id);
        if (!active || !node?.source || node.nextCursor === null) return Promise.resolve(false);
        return load(id, true, true);
    }
    async function expand(id) {
        const node = model.nodes.get(id);
        if (!active || !node || !model.branch(id)) return false;
        if (!node.loaded && node.children.length === 0 && !(await load(id))) return false;
        if (!active) return false;
        if (!model.branch(id)) { render(); return true; }
        return model.expanded.has(id) || setExpanded(id, true);
    }
    function collapse(id) { return setExpanded(id, false); }
    async function expandPath(ids) {
        if (!active || !Array.isArray(ids) || !ids.length) return false;
        for (let index = 0; index < ids.length; index += 1) {
            const node = model.nodes.get(ids[index]);
            if (!node || (index > 0 && node.parentId !== ids[index - 1])) return false;
            if (index < ids.length - 1 && !(await expand(node.id))) return false;
        }
        return true;
    }
    function expandAll() {
        if (!active) return false;
        const ids = model.walk().filter((id) => model.nodes.get(id).children.length && !model.expanded.has(id));
        ids.forEach((id) => model.expanded.add(id));
        render();
        persist();
        ids.forEach((id) => emit('expanded', { id, label: model.nodes.get(id).label }));
        return ids.length > 0;
    }
    function collapseAll() {
        if (!active) return false;
        const ids = [...model.expanded];
        model.expanded.clear();
        render();
        persist();
        ids.forEach((id) => emit('collapsed', { id, label: model.nodes.get(id).label }));
        return ids.length > 0;
    }
    function selectVisible() {
        if (!active || !model.multiple || model.disabled) return false;
        let changed = false;
        const visible = visibleIds();
        const visibleSet = new Set(visible);
        for (const id of visible) {
            const node = model.nodes.get(id);
            const hasVisibleDescendant = node.children.some((child) => visibleSet.has(child));
            const selectableResult = model.rootMode
                ? node.loaded && !hasVisibleDescendant
                : node.loaded && node.children.length === 0;
            if (selectableResult) changed = model.select(id, true) || changed;
        }
        if (changed) publish();
        return changed;
    }

    function setFilter(predicate) {
        if (!active || typeof predicate !== 'function') return false;
        const accepted = [];
        try {
            for (const node of model.nodes.values()) {
                if (predicate(nodeSnapshot(node))) accepted.push(node.id);
            }
        } catch {
            emit('error', { code: 'filter-failed', message: translate('filterError') });
            return false;
        }
        customFilter = predicate;
        filterErrorReported = false;
        expansionBeforeFilter ??= new Set(model.expanded);
        accepted.forEach((id) => model.ancestors(id).forEach((ancestor) => model.expanded.add(ancestor)));
        render();
        emit('filtered', { active: true, visibleIds: visibleIds() });
        return true;
    }
    function clearFilter() {
        if (!active || customFilter === null) return false;
        customFilter = null;
        filterErrorReported = false;
        if (expansionBeforeFilter) {
            model.expanded.clear();
            expansionBeforeFilter.forEach((id) => model.expanded.add(id));
            expansionBeforeFilter = null;
        }
        render();
        emit('filtered', { active: false, visibleIds: visibleIds() });
        return true;
    }

    function clearSearch() {
        if (!active) return false;
        const changed = Boolean(draft || query || searching || searchError);
        if (timer !== null) clearTimeout(timer);
        timer = null;
        transport.cancel('search');
        draft = '';
        query = '';
        remoteMatches = null;
        searchError = false;
        showStatus();
        if (input) input.value = '';
        if (expansionBeforeSearch) {
            model.expanded.clear();
            expansionBeforeSearch.forEach((id) => model.expanded.add(id));
            expansionBeforeSearch = null;
        }
        render();
        if (changed) emit('search', { query });
        return changed;
    }
    async function applySearch() {
        if (!active) return false;
        if (timer !== null) clearTimeout(timer);
        timer = null;
        const term = draft.trim();
        if (!term || term.length < (configuration.searchMin ?? 0)) {
            const savedDraft = draft;
            const changed = clearSearch();
            draft = savedDraft;
            if (input) input.value = draft;
            return changed;
        }
        expansionBeforeSearch ??= new Set(model.expanded);
        searchError = false;
        showStatus();
        try {
            if (source) {
                const url = new URL(source);
                url.searchParams.set(configuration.searchParam ?? 'query', term);
                const result = await transport.request('search', url);
                if (!active || result === null || term !== draft.trim()) return false;
                model.merge(result.items);
                remoteMatches = new Set();
                function index(items) {
                    items.forEach((item) => { remoteMatches.add(String(item.id)); index(item.children ?? []); });
                }
                index(result.items);
            }
            query = term;
            const found = matches();
            found?.forEach((id) => model.ancestors(id).forEach((parent) => model.expanded.add(parent)));
            render();
            emit('search', { query });
            return true;
        } catch {
            if (!active || term !== draft.trim()) return false;
            searchError = true;
            showStatus(translate('searchError'));
            render();
            emit('error', { code: 'search-source-unavailable', message: translate('searchError'), query: term });
            return false;
        }
    }
    function setSearch(value) {
        if (!active || typeof value !== 'string' || value === draft) return false;
        draft = value;
        if (input) input.value = value;
        if (timer !== null) clearTimeout(timer);
        transport.cancel('search');
        if (configuration.searchMode !== 'manual') {
            if (!value.trim()) return clearSearch();
            timer = setTimeout(() => { void applySearch(); }, configuration.searchDebounce ?? 200);
        }
        render();
        return true;
    }

    function select(id) {
        const node = model.nodes.get(id);
        if (!node || (model.multiple && !model.rootMode && !node.loaded)) return false;
        if (!model.select(id, model.multiple ? model.state(id) !== 'true' : true)) return false;
        publish();
        return true;
    }
    const commands = { clear, expandAll, collapseAll, selectVisible, clearSearch, applySearch };
    function onClick(event) {
        const target = event.target instanceof Element ? event.target : null;
        const command = target?.closest('[data-tree-command]');
        if (command && root.contains(command)) { void commands[command.dataset.treeCommand]?.(); return; }
        const row = target?.closest('[data-daisy-kit-tree-node]');
        if (!row || !tree.contains(row)) return;
        const id = row.dataset.daisyKitTreeNode;
        const action = target.closest('[data-tree-action]')?.dataset.treeAction;
        if (action === 'toggle') void (model.expanded.has(id) ? collapse(id) : expand(id));
        else if (action === 'load-more') {
            const restoreFocus = document.activeElement === target;
            void loadMore(id).then(() => {
                if (!active || !restoreFocus) return;
                const branch = [...root.querySelectorAll('[data-daisy-kit-tree-node]')]
                    .find((element) => element.dataset.daisyKitTreeNode === id);
                const next = branch?.querySelector('[data-tree-action="load-more"]');
                if (next instanceof HTMLElement) next.focus();
                else renderer.focus(id);
            });
            return;
        }
        else if (action === 'retry') void expand(id);
        else select(id);
        renderer.focus(id);
    }
    function onKeyDown(event) {
        if (event.target === input) {
            if (event.key === 'Enter') { event.preventDefault(); void applySearch(); }
            if (event.key === 'Escape') { event.preventDefault(); clearSearch(); }
            return;
        }
        if (event.target.closest?.('[data-tree-action="load-more"], [data-tree-action="retry"]')) return;
        const row = event.target.closest?.('[data-daisy-kit-tree-node]');
        if (!row || !tree.contains(row)) return;
        const id = row.dataset.daisyKitTreeNode;
        const node = model.nodes.get(id);
        const visible = visibleIds();
        const index = visible.indexOf(id);
        const keys = ['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Enter', ' '];
        if (keys.includes(event.key)) event.preventDefault();
        if (event.key === 'ArrowDown') renderer.focus(visible[index + 1]);
        if (event.key === 'ArrowUp') renderer.focus(visible[index - 1]);
        if (event.key === 'Home') renderer.focus(visible[0]);
        if (event.key === 'End') renderer.focus(visible.at(-1));
        if (event.key === 'ArrowLeft') model.expanded.has(id) ? collapse(id) : renderer.focus(node.parentId);
        if (event.key === 'ArrowRight') {
            if (!model.expanded.has(id) || !node.loaded) void expand(id);
            else renderer.focus(node.children.find((child) => visible.includes(child)));
        }
        if (event.key === 'Enter' || event.key === ' ') select(id);
        if (event.key.length === 1 && event.key !== ' ' && !event.ctrlKey && !event.metaKey && !event.altKey) {
            typeBuffer = Date.now() - typeTime > 700 ? event.key : typeBuffer + event.key;
            typeTime = Date.now();
            const ordered = [...visible.slice(index + 1), ...visible.slice(0, index + 1)];
            renderer.focus(ordered.find((key) => model.nodes.get(key).label.toLocaleLowerCase().startsWith(typeBuffer.toLocaleLowerCase())));
        }
    }
    function onInput(event) { setSearch(event.currentTarget.value); }
    root.addEventListener('click', onClick);
    root.addEventListener('keydown', onKeyDown);
    input?.addEventListener('input', onInput);

    const stored = persistence.read();
    const storedExpanded = Array.isArray(stored?.expandedIds) ? stored.expandedIds : [];
    if (Array.isArray(stored?.expandedIds)) model.expanded.clear();
    storedExpanded.forEach((id) => { if (model.nodes.has(id)) model.expanded.add(id); });
    const restoredValue = stored?.value ?? (model.multiple ? stored?.selectedIds : stored?.selectedId);
    const explicitValue = configuration.hasInitialValue ?? Object.hasOwn(configuration, 'value');
    const wantedValue = explicitValue ? configuration.value : restoredValue;
    if (wantedValue !== undefined) model.setValue(wantedValue, true);
    render();
    const initialRevision = revision;
    const paths = configuration.initialExpandPaths ?? [];
    const storedPaths = Array.isArray(stored?.paths) ? stored.paths : [];
    const initialExpanded = [...model.expanded];
    async function restorePaths() {
        for (const id of initialExpanded) {
            if (!active) return;
            if (!model.nodes.get(id)?.loaded) await expand(id);
        }
        for (const path of paths) {
            if (!active) return;
            await expandPath(path);
        }
        // Hydrate saved selections without opening branches that the user left collapsed.
        for (const path of storedPaths) {
            if (!Array.isArray(path)) continue;
            for (let index = 0; index < path.length - 1; index += 1) {
                if (!active) return;
                const node = model.nodes.get(path[index]);
                if (!node || (index > 0 && node.parentId !== path[index - 1])) break;
                if (!node.loaded && !(await load(node.id))) break;
            }
        }
        if (!active) return;
        for (const id of storedExpanded) if (model.nodes.has(id) && model.branch(id)) model.expanded.add(id);
        if (revision === initialRevision && wantedValue !== undefined) model.setValue(wantedValue, true);
        render();
    }
    void restorePaths();

    return {
        getValue: () => model.getValue(), getState, setValue, clear, expand, collapse,
        focus: (id) => active && renderer.focus(id), setSearch, applySearch, clearSearch, setFilter, clearFilter,
        expandPath, expandAll, collapseAll, selectVisible,
        loadMore, reloadBranch: (id) => load(id, true),
        destroy() {
            active = false;
            if (timer !== null) clearTimeout(timer);
            transport.destroy();
            root.removeEventListener('click', onClick);
            root.removeEventListener('keydown', onKeyDown);
            input?.removeEventListener('input', onInput);
            tree.innerHTML = originalMarkup;
            if (field) {
                field.value = originalFieldValue;
                field.disabled = originalFieldDisabled;
            }
            if (originalMultiple === null) tree.removeAttribute('aria-multiselectable');
            else tree.setAttribute('aria-multiselectable', originalMultiple);
            if (originalBusy === null) root.removeAttribute('aria-busy');
            else root.setAttribute('aria-busy', originalBusy);
            showStatus();
        },
    };
}
