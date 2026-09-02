import '../css/transfer-list.css';
import { compareItems, rankItem } from '@tanstack/match-sorter-utils';
import Sortable from 'sortablejs';
import { createMountable } from './core/mountable.js';

const sides = ['source', 'target'];

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:transfer-list:${name}`, { bubbles: true, detail }));
}

function normalizeItems(items) {
    if (!Array.isArray(items)) return [];

    const known = new Set();

    return items.flatMap((item) => {
        if (!item || typeof item !== 'object' || Array.isArray(item) || typeof item.value !== 'string' || known.has(item.value)) return [];

        known.add(item.value);

        return [{
            avatar: typeof item.avatar === 'string' ? item.avatar : '',
            description: typeof item.description === 'string' ? item.description : '',
            disabled: item.disabled === true,
            initials: typeof item.initials === 'string' ? item.initials : '',
            label: typeof item.label === 'string' && item.label !== '' ? item.label : item.value,
            meta: typeof item.meta === 'string' ? item.meta : '',
            value: item.value,
        }];
    });
}

function initialsFor(item) {
    if (item.initials !== '') return item.initials.slice(0, 3);

    return item.label
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

function initialize(root, configuration) {
    const lists = {
        source: root.querySelector('[data-daisy-kit-transfer-source]'),
        target: root.querySelector('[data-daisy-kit-transfer-target]'),
    };
    const valuesNode = root.querySelector('[data-daisy-kit-transfer-values]');
    const requiredInput = root.querySelector('[data-daisy-kit-transfer-required]');

    if (!(lists.source instanceof HTMLElement) || !(lists.target instanceof HTMLElement) || !(valuesNode instanceof HTMLElement)) {
        throw new Error('Transfer list markup is incomplete.');
    }

    const searchInputs = [...root.querySelectorAll('[data-daisy-kit-transfer-search]')]
        .filter((input) => input instanceof HTMLInputElement);
    const mutableNodes = [...root.querySelectorAll([
        '[data-daisy-kit-transfer-count]',
        '[data-daisy-kit-transfer-empty]',
        '[data-daisy-kit-transfer-move]',
        '[data-daisy-kit-transfer-page]',
        '[data-daisy-kit-transfer-page-status]',
        '[data-daisy-kit-transfer-pagination]',
        '[data-daisy-kit-transfer-reorder]',
        '[data-daisy-kit-transfer-select-all]',
    ].join(','))].map((element) => ({
        checked: element instanceof HTMLInputElement ? element.checked : null,
        disabled: element instanceof HTMLButtonElement || element instanceof HTMLInputElement
            ? element.disabled
            : null,
        element,
        hidden: element.hidden,
        indeterminate: element instanceof HTMLInputElement ? element.indeterminate : null,
        text: element.matches('[data-daisy-kit-transfer-count], [data-daisy-kit-transfer-empty], [data-daisy-kit-transfer-page-status]')
            ? element.textContent
            : null,
    }));
    const initialDom = {
        requiredValue: requiredInput instanceof HTMLInputElement ? requiredInput.value : null,
        searchValues: searchInputs.map((input) => input.value),
        sourceMarkup: lists.source.innerHTML,
        targetMarkup: lists.target.innerHTML,
        valuesMarkup: valuesNode.innerHTML,
    };
    const items = normalizeItems(configuration.items);
    const itemByValue = new Map(items.map((item) => [item.value, item]));
    const maxItems = Number.isInteger(configuration.maxItems) && configuration.maxItems > 0
        ? configuration.maxItems
        : null;
    const name = typeof configuration.name === 'string' && configuration.name !== '' ? configuration.name : null;
    const pageSize = Number.isInteger(configuration.pageSize) && configuration.pageSize > 0
        ? configuration.pageSize
        : 10;
    const pagination = configuration.pagination === true;
    const selectAllScope = ['page', 'filtered'].includes(configuration.selectAllScope)
        ? configuration.selectAllScope
        : 'page';
    let target = Array.isArray(configuration.value)
        ? configuration.value.filter((value) => typeof value === 'string' && itemByValue.has(value))
        : [];
    target = [...new Set(target)].slice(0, maxItems ?? undefined);

    const selected = { source: new Set(), target: new Set() };
    const searches = { source: '', target: '' };
    const pages = { source: 1, target: 1 };
    const sortables = [];
    let destroyed = false;

    function sideItems(side) {
        const targetValues = new Set(target);

        return side === 'target'
            ? target.map((value) => itemByValue.get(value)).filter(Boolean)
            : items.filter((item) => !targetValues.has(item.value));
    }

    function filteredItems(side) {
        const search = searches[side];
        const available = sideItems(side);

        if (search === '') return available;

        return available
            .map((item) => ({
                item,
                rank: rankItem(`${item.label} ${item.description} ${item.meta} ${item.value}`, search),
            }))
            .filter(({ rank }) => rank.passed)
            .sort((left, right) => compareItems(left.rank, right.rank))
            .map(({ item }) => item);
    }

    function pageState(side) {
        const filtered = filteredItems(side);
        const totalPages = pagination ? Math.max(Math.ceil(filtered.length / pageSize), 1) : 1;
        pages[side] = Math.min(Math.max(pages[side], 1), totalPages);
        const start = pagination ? (pages[side] - 1) * pageSize : 0;
        const visible = pagination ? filtered.slice(start, start + pageSize) : filtered;

        return { filtered, total: sideItems(side).length, totalPages, visible };
    }

    function orderedSelection(side) {
        return sideItems(side)
            .filter((item) => selected[side].has(item.value))
            .map((item) => item.value);
    }

    function selectionScope(side, scope = selectAllScope) {
        const state = pageState(side);
        const candidates = scope === 'filtered' ? state.filtered : state.visible;

        return candidates.filter((item) => !item.disabled).map((item) => item.value);
    }

    function createAvatar(item) {
        const avatar = document.createElement('span');
        avatar.className = 'avatar placeholder daisy-kit-transfer-list__avatar';
        const frame = document.createElement('span');

        if (item.avatar !== '') {
            const image = document.createElement('img');
            image.alt = '';
            image.loading = 'lazy';
            image.src = item.avatar;
            frame.append(image);
        } else {
            frame.textContent = initialsFor(item);
        }

        avatar.append(frame);

        return avatar;
    }

    function createItem(side, item) {
        const option = document.createElement('li');
        const isDisabled = configuration.disabled === true || item.disabled;
        const isSelected = selected[side].has(item.value);
        option.className = 'daisy-kit-transfer-list__item';
        option.dataset.value = item.value;
        option.role = 'option';
        option.tabIndex = isDisabled ? -1 : 0;
        option.setAttribute('aria-disabled', String(isDisabled));
        option.setAttribute('aria-selected', String(isSelected));

        const selectionIndicator = document.createElement('span');
        selectionIndicator.className = 'daisy-kit-transfer-list__item-check';
        selectionIndicator.setAttribute('aria-hidden', 'true');
        option.append(selectionIndicator, createAvatar(item));

        const content = document.createElement('span');
        content.className = 'daisy-kit-transfer-list__item-content';
        const label = document.createElement('span');
        label.className = 'daisy-kit-transfer-list__item-label';
        label.textContent = item.label;
        content.append(label);

        if (item.description !== '') {
            const description = document.createElement('span');
            description.className = 'daisy-kit-transfer-list__item-description';
            description.textContent = item.description;
            content.append(description);
        }

        option.append(content);

        if (item.meta !== '') {
            const meta = document.createElement('span');
            meta.className = 'badge badge-ghost badge-sm daisy-kit-transfer-list__item-meta';
            meta.textContent = item.meta;
            option.append(meta);
        }

        if (side === 'target' && configuration.sortable === true && !isDisabled) {
            const handle = document.createElement('span');
            handle.className = 'daisy-kit-transfer-list__drag-handle';
            handle.setAttribute('aria-hidden', 'true');
            handle.textContent = '⋮⋮';
            option.append(handle);
        }

        return option;
    }

    function renderPanel(side) {
        const list = lists[side];
        const state = pageState(side);
        list.replaceChildren(...state.visible.map((item) => createItem(side, item)));

        const count = root.querySelector(`[data-daisy-kit-transfer-count="${side}"]`);
        if (count instanceof HTMLElement) {
            count.textContent = `${selected[side].size} selected · ${state.total} total`;
        }

        const empty = root.querySelector(`[data-daisy-kit-transfer-empty="${side}"]`);
        if (empty instanceof HTMLElement) {
            empty.hidden = state.filtered.length !== 0;
            empty.textContent = state.total === 0
                ? (side === 'source' ? 'No available items' : 'No selected items')
                : 'No matching items';
        }

        const selectAll = root.querySelector(`[data-daisy-kit-transfer-select-all="${side}"]`);
        if (selectAll instanceof HTMLInputElement) {
            const candidates = selectAllScope === 'filtered' ? state.filtered : state.visible;
            const scope = candidates.filter((item) => !item.disabled).map((item) => item.value);
            const selectedCount = scope.filter((value) => selected[side].has(value)).length;
            selectAll.checked = scope.length > 0 && selectedCount === scope.length;
            selectAll.indeterminate = selectedCount > 0 && selectedCount < scope.length;
            selectAll.disabled = configuration.disabled === true || scope.length === 0;
        }

        const paginationNode = root.querySelector(`[data-daisy-kit-transfer-pagination="${side}"]`);
        if (paginationNode instanceof HTMLElement) paginationNode.hidden = !pagination || state.totalPages <= 1;

        const status = root.querySelector(`[data-daisy-kit-transfer-page-status="${side}"]`);
        if (status instanceof HTMLElement) status.textContent = `Page ${pages[side]} of ${state.totalPages}`;

        for (const direction of ['previous', 'next']) {
            const button = root.querySelector(`[data-daisy-kit-transfer-page="${side}:${direction}"]`);
            if (button instanceof HTMLButtonElement) {
                const unavailable = direction === 'previous' ? pages[side] <= 1 : pages[side] >= state.totalPages;
                button.disabled = configuration.disabled === true || unavailable;
            }
        }
    }

    function renderActions() {
        const add = root.querySelector('[data-daisy-kit-transfer-move="to-target"]');
        const remove = root.querySelector('[data-daisy-kit-transfer-move="to-source"]');

        if (add instanceof HTMLButtonElement) {
            add.disabled = configuration.disabled === true || selected.source.size === 0;
        }

        if (remove instanceof HTMLButtonElement) {
            remove.hidden = configuration.oneWay === true;
            remove.disabled = configuration.disabled === true || configuration.oneWay === true || selected.target.size === 0;
        }

        for (const direction of ['up', 'down']) {
            const button = root.querySelector(`[data-daisy-kit-transfer-reorder="${direction}"]`);
            if (button instanceof HTMLButtonElement) {
                button.disabled = configuration.disabled === true || selected.target.size === 0;
            }
        }
    }

    function renderValues() {
        valuesNode.replaceChildren();

        if (name) {
            target.forEach((value) => {
                const input = document.createElement('input');
                input.name = `${name}[]`;
                input.type = 'hidden';
                input.value = value;
                input.disabled = configuration.disabled === true;
                valuesNode.append(input);
            });
        }

        if (requiredInput instanceof HTMLInputElement) requiredInput.value = target.join(',');
    }

    function render() {
        renderPanel('source');
        renderPanel('target');
        renderActions();
        renderValues();
    }

    function reportError(code, message, context = {}) {
        emit(root, 'error', { code, message, ...context });
    }

    function publishSelection(side) {
        render();
        emit(root, 'selection-change', { side, values: orderedSelection(side) });
    }

    function publishChange(direction, movedValues) {
        render();
        emit(root, 'change', { direction, movedValues: [...movedValues], values: [...target] });
        root.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function setSelection(side, values) {
        if (!sides.includes(side)) {
            reportError('invalid-side', 'The transfer list side is invalid.', { side, values: [...target] });

            return false;
        }

        if (!Array.isArray(values)) {
            reportError('invalid-selection', 'Transfer list selection values must be an array.', { side, values: [...target] });

            return false;
        }

        const eligible = new Set(sideItems(side).filter((item) => !item.disabled).map((item) => item.value));
        selected[side] = new Set(values.filter((value) => typeof value === 'string' && eligible.has(value)));
        publishSelection(side);

        return true;
    }

    function selectAll(side, scope = selectAllScope) {
        if (!sides.includes(side) || !['page', 'filtered'].includes(scope)) {
            reportError('invalid-selection-scope', 'The transfer list selection scope is invalid.', {
                scope,
                side,
                values: [...target],
            });

            return false;
        }

        const values = selectionScope(side, scope);
        if (values.length === 0) return false;
        const shouldClear = values.every((value) => selected[side].has(value));
        values.forEach((value) => shouldClear ? selected[side].delete(value) : selected[side].add(value));
        publishSelection(side);

        return true;
    }

    function setPage(side, page) {
        if (!sides.includes(side) || !Number.isInteger(page) || page < 1) {
            reportError('invalid-page', 'The transfer list page is invalid.', { page, side, values: [...target] });

            return false;
        }

        const totalPages = pagination ? Math.max(Math.ceil(filteredItems(side).length / pageSize), 1) : 1;
        const nextPage = Math.min(page, totalPages);
        if (pages[side] === nextPage) return false;
        pages[side] = nextPage;
        renderPanel(side);
        renderActions();
        emit(root, 'page-change', { page: nextPage, pageSize, side, totalPages });

        return true;
    }

    function setTargetValues(values, notify = true, change = { direction: null, movedValues: [] }) {
        if (!Array.isArray(values)) {
            reportError('invalid-value', 'Transfer list values must be an array.', { values: [...target] });

            return false;
        }

        const next = [...new Set(values.filter((value) => typeof value === 'string' && itemByValue.has(value)))];
        const disabledValues = items
            .filter((item) => item.disabled === true)
            .filter((item) => {
                const currentIndex = target.indexOf(item.value);
                const nextIndex = next.indexOf(item.value);

                return currentIndex === -1 ? nextIndex !== -1 : nextIndex !== currentIndex;
            })
            .map((item) => item.value);

        if (disabledValues.length > 0) {
            reportError('disabled-item', 'Disabled transfer list items cannot be moved.', {
                disabledValues,
                values: [...target],
            });

            return false;
        }

        if (maxItems && next.length > maxItems) {
            reportError('max-items', `The transfer list cannot contain more than ${maxItems} item${maxItems === 1 ? '' : 's'}.`, {
                maxItems,
                values: [...target],
            });

            return false;
        }

        target = next;
        selected.source.clear();
        selected.target.clear();
        pages.source = 1;
        pages.target = 1;
        if (notify) publishChange(change.direction, change.movedValues); else render();

        return true;
    }

    function move(direction, values = [...selected[direction === 'to-target' ? 'source' : 'target']]) {
        if (configuration.disabled === true) {
            reportError('disabled', 'The transfer list is disabled.', { values: [...target] });

            return false;
        }

        if (!['to-target', 'to-source'].includes(direction)) {
            reportError('invalid-direction', 'The transfer direction is invalid.', { direction, values: [...target] });

            return false;
        }

        if (configuration.oneWay === true && direction === 'to-source') {
            reportError('one-way', 'This transfer list only allows transfers to the target list.', {
                direction,
                values: [...target],
            });

            return false;
        }

        if (!Array.isArray(values)) {
            reportError('invalid-value', 'Transfer list values must be an array.', { values: [...target] });

            return false;
        }

        if (values.length === 0) return false;
        const disabledValues = [...new Set(values.filter((value) => itemByValue.get(value)?.disabled === true))];

        if (disabledValues.length > 0) {
            reportError('disabled-item', 'Disabled transfer list items cannot be moved.', {
                disabledValues,
                values: [...target],
            });

            return false;
        }

        const movedValues = [...new Set(direction === 'to-target'
            ? values.filter((value) => itemByValue.has(value) && !target.includes(value))
            : target.filter((value) => values.includes(value)))];

        if (movedValues.length === 0) return false;

        return direction === 'to-target'
            ? setTargetValues([...target, ...movedValues], true, { direction, movedValues })
            : setTargetValues(target.filter((value) => !movedValues.includes(value)), true, { direction, movedValues });
    }

    function reorder(values) {
        const completePermutation = Array.isArray(values)
            && values.length === target.length
            && new Set(values).size === target.length
            && values.every((value) => target.includes(value));

        if (!completePermutation) {
            reportError('invalid-reorder', 'Reordering requires a complete permutation of the target values.', {
                values: [...target],
            });
            render();

            return false;
        }

        const movedDisabledValues = target.filter((value, index) => {
            return itemByValue.get(value)?.disabled === true && values[index] !== value;
        });

        if (movedDisabledValues.length > 0) {
            reportError('disabled-item', 'Disabled transfer list items cannot be moved.', {
                disabledValues: movedDisabledValues,
                values: [...target],
            });
            render();

            return false;
        }

        target = [...values];
        selected.source.clear();
        selected.target.clear();
        render();
        emit(root, 'reorder', { values: [...target] });
        root.dispatchEvent(new Event('change', { bubbles: true }));

        return true;
    }

    function reorderVisible(values) {
        const visible = new Set(pageState('target').visible.map((item) => item.value));
        const ordered = values.filter((value) => visible.has(value));
        let visibleIndex = 0;

        reorder(target.map((value) => visible.has(value) ? ordered[visibleIndex++] : value));
    }

    function shiftTarget(direction, values = [...selected.target]) {
        if (configuration.disabled === true || values.length === 0) return false;

        const moving = new Set(values);
        const order = filteredItems('target').map((item) => item.value);
        const visible = new Set(order);
        const step = direction === 'up' ? -1 : 1;
        const indexes = order.map((_, index) => index);
        if (step === 1) indexes.reverse();

        for (const index of indexes) {
            const neighbor = index + step;
            if (moving.has(order[index]) && order[neighbor] !== undefined && !moving.has(order[neighbor])) {
                [order[index], order[neighbor]] = [order[neighbor], order[index]];
            }
        }

        let visibleIndex = 0;
        const next = target.map((value) => visible.has(value) ? order[visibleIndex++] : value);
        if (next.every((value, position) => value === target[position])) return false;
        if (!reorder(next)) return false;
        values.forEach((value) => selected.target.add(value));
        render();

        return true;
    }

    function toggleItem(side, option) {
        if (!(option instanceof HTMLElement) || option.getAttribute('aria-disabled') === 'true') return;
        const value = option.dataset.value;
        if (!value) return;
        if (selected[side].has(value)) selected[side].delete(value); else selected[side].add(value);
        publishSelection(side);
    }

    function onListKeydown(side, event) {
        const option = event.target.closest('[role="option"]');
        if (!(option instanceof HTMLElement) || option.getAttribute('aria-disabled') === 'true') return;

        if (side === 'target' && event.altKey && ['ArrowUp', 'ArrowDown'].includes(event.key)) {
            event.preventDefault();
            const value = option.dataset.value;
            shiftTarget(event.key === 'ArrowUp' ? 'up' : 'down', [value]);
            [...lists.target.children].find((item) => item.dataset.value === value)?.focus();

            return;
        }

        const options = [...lists[side].querySelectorAll('[role="option"]')];
        const position = options.indexOf(option);
        if (event.key === 'ArrowDown' && options[position + 1]) {
            event.preventDefault();
            options[position + 1].focus();
        }
        if (event.key === 'ArrowUp' && options[position - 1]) {
            event.preventDefault();
            options[position - 1].focus();
        }
        if (event.key === ' ' || event.key === 'Enter') {
            event.preventDefault();
            toggleItem(side, option);
        }
        if (event.key === 'ArrowRight' && side === 'source') {
            event.preventDefault();
            move('to-target', [option.dataset.value]);
        }
        if (event.key === 'ArrowLeft' && side === 'target' && configuration.oneWay !== true) {
            event.preventDefault();
            move('to-source', [option.dataset.value]);
        }
    }

    const sourceClick = (event) => toggleItem('source', event.target.closest('[role="option"]'));
    const targetClick = (event) => toggleItem('target', event.target.closest('[role="option"]'));
    const sourceKeydown = (event) => onListKeydown('source', event);
    const targetKeydown = (event) => onListKeydown('target', event);
    const onActions = (event) => {
        const orderButton = event.target.closest('[data-daisy-kit-transfer-reorder]');
        if (orderButton instanceof HTMLElement) shiftTarget(orderButton.dataset.daisyKitTransferReorder);

        const moveButton = event.target.closest('[data-daisy-kit-transfer-move]');
        if (moveButton instanceof HTMLElement) move(moveButton.dataset.daisyKitTransferMove);

        const pageButton = event.target.closest('[data-daisy-kit-transfer-page]');
        if (pageButton instanceof HTMLElement) {
            const [side, direction] = pageButton.dataset.daisyKitTransferPage.split(':');
            setPage(side, pages[side] + (direction === 'next' ? 1 : -1));
        }
    };
    const onSearch = (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !sides.includes(input.dataset.daisyKitTransferSearch)) return;
        const side = input.dataset.daisyKitTransferSearch;
        searches[side] = input.value;
        pages[side] = 1;
        renderPanel(side);
        renderActions();
        emit(root, 'search', { query: input.value, side });
    };
    const onSelectAll = (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !sides.includes(input.dataset.daisyKitTransferSelectAll)) return;
        selectAll(input.dataset.daisyKitTransferSelectAll);
    };

    lists.source.addEventListener('click', sourceClick);
    lists.target.addEventListener('click', targetClick);
    lists.source.addEventListener('keydown', sourceKeydown);
    lists.target.addEventListener('keydown', targetKeydown);
    root.addEventListener('click', onActions);
    root.addEventListener('input', onSearch);
    root.addEventListener('change', onSelectAll);

    if (configuration.sortable === true && configuration.disabled !== true) {
        sortables.push(Sortable.create(lists.target, {
            animation: 150,
            draggable: ':scope > li',
            filter: '[aria-disabled="true"]',
            handle: '.daisy-kit-transfer-list__drag-handle',
            preventOnFilter: true,
            onEnd: () => {
                if (!destroyed) {
                    reorderVisible([...lists.target.querySelectorAll(':scope > li')]
                        .map((item) => item.dataset.value)
                        .filter(Boolean));
                }
            },
        }));
    }

    render();

    const facade = {
        clearSelection(side = null) {
            if (side !== null && !sides.includes(side)) {
                reportError('invalid-side', 'The transfer list side is invalid.', { side, values: [...target] });

                return false;
            }

            const changedSides = side === null ? sides : [side];
            changedSides.forEach((currentSide) => selected[currentSide].clear());
            render();
            changedSides.forEach((currentSide) => emit(root, 'selection-change', {
                side: currentSide,
                values: [],
            }));

            return true;
        },
        getSelection() {
            return { source: orderedSelection('source'), target: orderedSelection('target') };
        },
        getTargetValues: () => [...target],
        move,
        reorder,
        selectAll,
        setPage,
        setSelection,
        setTargetValues,
    };
    return {
        ...facade,
        destroy() {
            destroyed = true;
            sortables.forEach((sortable) => sortable.destroy());
            lists.source.removeEventListener('click', sourceClick);
            lists.target.removeEventListener('click', targetClick);
            lists.source.removeEventListener('keydown', sourceKeydown);
            lists.target.removeEventListener('keydown', targetKeydown);
            root.removeEventListener('click', onActions);
            root.removeEventListener('input', onSearch);
            root.removeEventListener('change', onSelectAll);
            lists.source.innerHTML = initialDom.sourceMarkup;
            lists.target.innerHTML = initialDom.targetMarkup;
            valuesNode.innerHTML = initialDom.valuesMarkup;
            searchInputs.forEach((input, index) => { input.value = initialDom.searchValues[index] ?? ''; });
            mutableNodes.forEach((snapshot) => {
                snapshot.element.hidden = snapshot.hidden;
                if (snapshot.disabled !== null) snapshot.element.disabled = snapshot.disabled;
                if (snapshot.checked !== null) snapshot.element.checked = snapshot.checked;
                if (snapshot.indeterminate !== null) snapshot.element.indeterminate = snapshot.indeterminate;
                if (snapshot.text !== null) snapshot.element.textContent = snapshot.text;
            });
            if (requiredInput instanceof HTMLInputElement && initialDom.requiredValue !== null) {
                requiredInput.value = initialDom.requiredValue;
            }
        },
    };
}

const module = createMountable('transfer-list', initialize);

export function getInstance(root) {
    return module.getInstance(root);
}

export function mount(root) {
    return module.mount(root);
}

export function mountAll(scope = document) {
    return module.mountAll(scope);
}

export function unmount(root) {
    return module.unmount(root);
}
