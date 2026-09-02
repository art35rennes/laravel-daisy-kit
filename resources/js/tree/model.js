export function safeSource(value) {
    if (value === undefined || value === null || value === '') return null;
    if (typeof value !== 'string') throw new TypeError('Invalid tree source.');
    const url = new URL(value, typeof window === 'undefined' ? 'http://localhost/' : window.location.href);
    if (!['http:', 'https:'].includes(url.protocol) || url.username || url.password) {
        throw new TypeError('Invalid tree source.');
    }
    return url.href;
}

export function createModel(configuration = {}) {
    const nodes = new Map();
    const roots = [];
    const expanded = new Set();
    const selected = new Set();
    const multiple = configuration.multiple === true;
    const rootMode = configuration.valueMode === 'selected-roots';
    const disabled = configuration.disabled === true;

    function ancestors(id) {
        const result = [];
        let current = nodes.get(id);
        while (current?.parentId !== null && current?.parentId !== undefined) {
            current = nodes.get(current.parentId);
            if (current) result.push(current.id);
        }
        return result;
    }

    // Validate an entire payload before mutating the canonical tree.
    function merge(items, parentId = null, complete = false, replace = complete) {
        if (!Array.isArray(items)) throw new TypeError('Tree items must be an array.');
        const staged = new Map();
        function visit(values, parent, inheritedDisabled, depth = 0) {
            if (depth > 64) throw new TypeError('Tree depth exceeds 64 levels.');
            return values.map((item) => {
                if (!item || typeof item !== 'object' || Array.isArray(item)
                    || !['string', 'number'].includes(typeof item.id) || String(item.id) === ''
                    || typeof item.label !== 'string' || !item.label.trim()
                    || (item.children !== undefined && !Array.isArray(item.children))) {
                    throw new TypeError('Every tree item requires a unique id and a label.');
                }
                const id = String(item.id);
                if (staged.has(id) || (nodes.has(id) && nodes.get(id).parentId !== parent)) {
                    throw new TypeError('Duplicate or reparented tree id.');
                }
                const old = nodes.get(id);
                const source = item.source === undefined ? old?.source ?? null : safeSource(item.source);
                const node = {
                    id, parentId: parent, label: item.label,
                    description: typeof item.description === 'string' ? item.description : old?.description ?? '',
                    badge: typeof item.badge === 'string' ? item.badge : old?.badge ?? '',
                    disabled: inheritedDisabled || item.disabled === true || old?.disabled === true,
                    source, loaded: old?.loaded ?? source === null, nextCursor: old?.nextCursor ?? null,
                    children: [...(old?.children ?? [])],
                };
                staged.set(id, node);
                const children = visit(item.children ?? [], id, node.disabled, depth + 1);
                node.children = [...new Set([...node.children, ...children])];
                return id;
            });
        }
        const ids = visit(items, parentId, nodes.get(parentId)?.disabled === true);
        staged.forEach((node, id) => nodes.set(id, node));
        const siblings = parentId === null ? roots : nodes.get(parentId).children;
        if (replace && parentId !== null) {
            function remove(id) {
                nodes.get(id)?.children.forEach(remove);
                nodes.delete(id);
                selected.delete(id);
                expanded.delete(id);
            }
            siblings.filter((id) => !ids.includes(id)).forEach(remove);
            siblings.splice(0, siblings.length, ...ids);
        }
        siblings.push(...ids.filter((id) => !siblings.includes(id)));
        if (parentId !== null) nodes.get(parentId).loaded = complete;
        return ids;
    }

    function branch(id) {
        const node = nodes.get(id);
        return Boolean(node && (node.children.length || !node.loaded));
    }

    function leaves(id) {
        const node = nodes.get(id);
        if (!node || node.disabled) return [];
        if (!node.children.length) return node.loaded ? [id] : [];
        return node.children.flatMap(leaves);
    }

    function state(id) {
        const node = nodes.get(id);
        if (!node || node.disabled) return 'false';
        if (!multiple) return selected.has(id) ? 'true' : 'false';
        if (rootMode && [id, ...ancestors(id)].some((key) => selected.has(key))) return 'true';
        if (!node.children.length) return selected.has(id) ? 'true' : 'false';
        const children = node.children.filter((key) => !nodes.get(key).disabled).map(state);
        if (children.length && node.loaded && children.every((value) => value === 'true')) return 'true';
        return children.some((value) => value !== 'false') ? 'mixed' : 'false';
    }

    function getValue() {
        if (!multiple) return [...selected][0] ?? null;
        if (!rootMode) return [...selected];
        const values = new Set();
        for (const id of selected) {
            const highest = ancestors(id).filter((key) => state(key) === 'true').at(-1);
            values.add(highest ?? id);
        }
        return [...values];
    }

    function select(id, checked = state(id) !== 'true') {
        const node = nodes.get(id);
        if (disabled || !node || node.disabled) return false;
        const before = JSON.stringify(getValue());
        if (!multiple) {
            selected.clear();
            if (checked) selected.add(id);
        } else if (!rootMode) {
            for (const key of leaves(id)) checked ? selected.add(key) : selected.delete(key);
        } else if (checked) {
            if (![id, ...ancestors(id)].some((key) => selected.has(key))) {
                for (const key of selected) if (ancestors(key).includes(id)) selected.delete(key);
                selected.add(id);
            }
        } else {
            // Split a selected ancestor into its remaining sibling subtrees.
            const path = [id, ...ancestors(id)];
            const anchorIndex = path.findIndex((key) => selected.has(key));
            if (anchorIndex > 0) {
                selected.delete(path[anchorIndex]);
                for (let index = anchorIndex; index > 0; index -= 1) {
                    for (const child of nodes.get(path[index]).children) {
                        if (child !== path[index - 1] && !nodes.get(child).disabled) selected.add(child);
                    }
                }
            }
            for (const key of selected) if (key === id || ancestors(key).includes(id)) selected.delete(key);
        }
        return before !== JSON.stringify(getValue());
    }

    function setValue(value, force = false) {
        const values = multiple ? value : value === null ? [] : [value];
        if ((!force && disabled) || !Array.isArray(values) || values.some((id) =>
            typeof id !== 'string' || !nodes.has(id) || nodes.get(id).disabled
            || (multiple && !rootMode && leaves(id).length === 0))) return false;
        const before = JSON.stringify(getValue());
        selected.clear();
        for (const id of values) {
            if (!multiple || rootMode) selected.add(id);
            else leaves(id).forEach((key) => selected.add(key));
        }
        return before !== JSON.stringify(getValue());
    }

    function walk() {
        const result = [];
        function visit(id) { result.push(id); nodes.get(id).children.forEach(visit); }
        roots.forEach(visit);
        return result;
    }

    function visible(matches = null) {
        const result = [];
        function visit(id) {
            if (matches && !matches.has(id)) return;
            result.push(id);
            if (expanded.has(id)) nodes.get(id).children.forEach(visit);
        }
        roots.forEach(visit);
        return result;
    }

    merge(configuration.items ?? []);
    function initialExpansion(items) {
        items.forEach((item) => {
            if (item.expanded === true) expanded.add(String(item.id));
            initialExpansion(item.children ?? []);
        });
    }
    initialExpansion(configuration.items ?? []);
    if (configuration.value !== undefined && configuration.value !== null) setValue(configuration.value, true);
    return { nodes, roots, expanded, selected, multiple, rootMode, disabled, ancestors, branch,
        merge, leaves, state, getValue, setValue, select, walk, visible };
}
