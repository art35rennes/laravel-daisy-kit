function element(tag, className, text) {
    const node = document.createElement(tag);
    node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
}

function highlightedText(text, query, mode) {
    const fragment = document.createDocumentFragment();
    if (!query) {
        fragment.append(document.createTextNode(text));
        return fragment;
    }
    const normalizedText = text.toLocaleLowerCase();
    const normalizedQuery = query.toLocaleLowerCase();
    const indexes = [];
    if (mode === 'includes') {
        const start = normalizedText.indexOf(normalizedQuery);
        if (start >= 0) indexes.push(...Array.from({ length: normalizedQuery.length }, (_, index) => start + index));
    } else {
        let cursor = 0;
        for (const character of normalizedQuery) {
            const index = normalizedText.indexOf(character, cursor);
            if (index < 0) break;
            indexes.push(index);
            cursor = index + 1;
        }
        if (indexes.length !== [...normalizedQuery].length) indexes.splice(0);
    }
    const matched = new Set(indexes);
    let buffer = '';
    let marked = false;
    function flush() {
        if (!buffer) return;
        if (marked) {
            const mark = document.createElement('mark');
            mark.textContent = buffer;
            fragment.append(mark);
        } else fragment.append(document.createTextNode(buffer));
        buffer = '';
    }
    [...text].forEach((character, index) => {
        const nextMarked = matched.has(index);
        if (buffer && marked !== nextMarked) flush();
        marked = nextMarked;
        buffer += character;
    });
    flush();
    return fragment;
}

export function createRenderer(root, model, translate) {
    const tree = root.querySelector('[data-daisy-kit-tree-root]');
    const templates = new Map([...root.querySelectorAll('template[data-daisy-kit-tree-template]')]
        .map((template) => [template.dataset.daisyKitTreeTemplate, template]));
    let controls = new Map();
    let focusedId = null;

    function focus(id, scroll = true) {
        const node = controls.get(id);
        if (!node || node.hidden) return false;
        controls.forEach((control) => { control.tabIndex = -1; });
        node.tabIndex = 0;
        focusedId = id;
        node.focus({ preventScroll: true });
        if (scroll) node.firstElementChild?.scrollIntoView?.({ block: 'nearest', inline: 'nearest' });
        return true;
    }

    function render({ visibleIds, loadingIds, errors, highlight }) {
        const active = document.activeElement;
        const hadFocus = tree.contains(active);
        const activeId = active?.closest('[data-daisy-kit-tree-node]')?.dataset.daisyKitTreeNode;
        const previous = activeId ?? focusedId;
        const visible = new Set(visibleIds);
        controls = new Map();

        function branch(ids, level) {
            const fragment = document.createDocumentFragment();
            const visibleSiblings = ids.filter((id) => visible.has(id));
            ids.forEach((id) => {
                const item = model.nodes.get(id);
                const hasChildren = model.branch(id);
                const opened = model.expanded.has(id);
                const selection = model.state(id);
                const disabled = model.disabled || item.disabled;
                const pendingLeaf = model.multiple && !model.rootMode && !item.loaded;
                const listItem = element('li', 'daisy-kit-tree__node');
                listItem.role = 'treeitem';
                listItem.dataset.daisyKitTreeNode = id;
                listItem.tabIndex = -1;
                listItem.hidden = !visible.has(id);
                listItem.setAttribute('aria-label', item.label);
                listItem.setAttribute('aria-level', String(level));
                listItem.setAttribute('aria-posinset', String(Math.max(1, visibleSiblings.indexOf(id) + 1)));
                listItem.setAttribute('aria-setsize', String(visibleSiblings.length || ids.length));
                listItem.setAttribute(model.multiple ? 'aria-checked' : 'aria-selected', selection);
                if (disabled) listItem.setAttribute('aria-disabled', 'true');
                if (hasChildren) listItem.setAttribute('aria-expanded', String(opened));
                if (loadingIds.has(id)) listItem.setAttribute('aria-busy', 'true');
                const row = element('div', 'daisy-kit-tree__row');
                row.dataset.selection = selection;
                const disclosure = element(hasChildren ? 'button' : 'span', 'daisy-kit-tree__disclosure');
                if (hasChildren) {
                    disclosure.classList.add('btn', 'btn-ghost', 'btn-sm', 'btn-square');
                    disclosure.type = 'button';
                    disclosure.tabIndex = -1;
                    disclosure.dataset.treeAction = 'toggle';
                    disclosure.setAttribute('aria-label', translate(opened ? 'collapse' : 'expand', { label: item.label }));
                    disclosure.append(element('span', loadingIds.has(id) ? 'loading loading-spinner loading-xs' : 'daisy-kit-tree__chevron'));
                }
                row.append(disclosure);
                const check = element('input', model.multiple ? 'checkbox checkbox-sm' : 'radio radio-sm');
                check.type = model.multiple ? 'checkbox' : 'radio';
                check.tabIndex = -1;
                check.setAttribute('aria-hidden', 'true');
                check.dataset.treeAction = 'select';
                check.checked = selection === 'true';
                check.indeterminate = selection === 'mixed';
                check.disabled = disabled || pendingLeaf;
                const content = element('span', 'daisy-kit-tree__label');
                if (templates.has(id)) content.append(templates.get(id).content.cloneNode(true));
                else {
                    const title = element('span', 'daisy-kit-tree__title');
                    title.append(highlightedText(item.label, highlight?.query, highlight?.mode));
                    content.append(title);
                    if (item.description) {
                        const description = element('span', 'daisy-kit-tree__description');
                        description.append(highlightedText(item.description, highlight?.query, highlight?.mode));
                        content.append(description);
                    }
                }
                row.append(check, content);
                if (item.badge && !templates.has(id)) row.append(element('span', 'badge badge-ghost badge-sm', item.badge));
                if (pendingLeaf) row.title = translate('loadBeforeSelect');
                listItem.append(row);
                controls.set(id, listItem);
                if (errors.has(id)) {
                    const feedback = element('div', 'daisy-kit-tree__branch-error text-error');
                    feedback.setAttribute('role', 'status');
                    feedback.append(element('span', '', translate('loadError')));
                    const retry = element('button', 'btn btn-ghost btn-sm', translate('retry'));
                    retry.type = 'button';
                    retry.tabIndex = -1;
                    retry.dataset.treeAction = 'retry';
                    feedback.append(retry);
                    listItem.append(feedback);
                }
                if (item.children.length) {
                    const group = element('ul', 'daisy-kit-tree__group');
                    group.role = 'group';
                    group.hidden = !opened;
                    group.append(branch(item.children, level + 1));
                    listItem.append(group);
                }
                if (opened && item.nextCursor !== null) {
                    const more = element('button', 'btn btn-ghost btn-sm daisy-kit-tree__load-more', translate('loadMore'));
                    more.type = 'button';
                    more.dataset.treeAction = 'load-more';
                    more.disabled = loadingIds.has(id);
                    listItem.append(more);
                }
                fragment.append(listItem);
            });
            return fragment;
        }

        tree.setAttribute('aria-multiselectable', String(model.multiple));
        tree.replaceChildren(branch(model.roots, 1));
        const fallback = previous && model.ancestors(previous).find((id) => visible.has(id));
        focusedId = visible.has(previous) ? previous : fallback ?? visibleIds[0] ?? null;
        if (controls.has(focusedId)) controls.get(focusedId).tabIndex = 0;
        if (hadFocus && focusedId) focus(focusedId, false);
    }

    return { render, focus };
}
