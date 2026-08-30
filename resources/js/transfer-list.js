import '../css/transfer-list.css';
import { compareItems, rankItem } from '@tanstack/match-sorter-utils';
import Sortable from 'sortablejs';
import { createMountable } from './core/mountable.js';

const facades = new WeakMap();

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:transfer-list:${name}`, { bubbles: true, detail }));
}

function normalizeItems(items) {
    if (!Array.isArray(items)) return [];
    const known = new Set();
    return items.flatMap((item) => {
        if (!item || typeof item !== 'object' || Array.isArray(item) || typeof item.value !== 'string' || known.has(item.value)) return [];
        known.add(item.value);
        return [{ value: item.value, label: typeof item.label === 'string' && item.label !== '' ? item.label : item.value, description: typeof item.description === 'string' ? item.description : '', disabled: item.disabled === true }];
    });
}

function initialize(root, configuration) {
    const sourceList = root.querySelector('[data-daisy-kit-transfer-source]');
    const targetList = root.querySelector('[data-daisy-kit-transfer-target]');
    const valuesNode = root.querySelector('[data-daisy-kit-transfer-values]');
    const requiredInput = root.querySelector('[data-daisy-kit-transfer-required]');
    if (!(sourceList instanceof HTMLElement) || !(targetList instanceof HTMLElement) || !(valuesNode instanceof HTMLElement)) throw new Error('Transfer list markup is incomplete.');

    const searchInputs = [...root.querySelectorAll('[data-daisy-kit-transfer-search]')].filter((input) => input instanceof HTMLInputElement);
    const initialDom = {
        requiredValue: requiredInput instanceof HTMLInputElement ? requiredInput.value : null,
        searchValues: searchInputs.map((input) => input.value),
        sourceMarkup: sourceList.innerHTML,
        targetMarkup: targetList.innerHTML,
        valuesMarkup: valuesNode.innerHTML,
    };

    const items = normalizeItems(configuration.items);
    const itemByValue = new Map(items.map((item) => [item.value, item]));
    const maxItems = Number.isInteger(configuration.maxItems) && configuration.maxItems > 0 ? configuration.maxItems : null;
    const name = typeof configuration.name === 'string' && configuration.name !== '' ? configuration.name : null;
    let target = Array.isArray(configuration.value) ? configuration.value.filter((value) => typeof value === 'string' && itemByValue.has(value)) : [];
    target = [...new Set(target)].slice(0, maxItems ?? undefined);
    const selected = { source: new Set(), target: new Set() };
    let sourceSearch = '';
    let targetSearch = '';
    let destroyed = false;
    const sortables = [];

    function filtered(side) {
        const search = side === 'source' ? sourceSearch : targetSearch;
        const list = side === 'target' ? target.map((value) => itemByValue.get(value)) : items.filter((item) => !target.includes(item.value));
        if (search === '') return list;

        return list
            .map((item) => ({ item, rank: rankItem(`${item.label} ${item.description} ${item.value}`, search) }))
            .filter(({ rank }) => rank.passed)
            .sort((left, right) => compareItems(left.rank, right.rank))
            .map(({ item }) => item);
    }

    function renderList(side) {
        const list = side === 'target' ? targetList : sourceList;
        list.replaceChildren();
        filtered(side).forEach((item) => {
            const li = document.createElement('li');
            li.role = 'option';
            li.tabIndex = configuration.disabled === true || item.disabled ? -1 : 0;
            li.dataset.value = item.value;
            li.setAttribute('aria-disabled', String(configuration.disabled === true || item.disabled));
            li.setAttribute('aria-selected', String(selected[side].has(item.value)));
            li.textContent = item.description === '' ? item.label : `${item.label} — ${item.description}`;
            list.append(li);
        });
    }

    function render() {
        renderList('source');
        renderList('target');
        valuesNode.replaceChildren();
        if (name) target.forEach((value, index) => {
            const input = document.createElement('input');
            input.name = `${name}[]`;
            input.type = 'hidden';
            input.value = value;
            valuesNode.append(input);
        });
        if (requiredInput instanceof HTMLInputElement) requiredInput.value = target.join(',');
    }

    function publishChange() {
        render();
        emit(root, 'change', { values: [...target] });
        root.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function reportError(code, message, context = {}) {
        emit(root, 'error', { code, message, ...context });
    }

    function setTargetValues(values, notify = true) {
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
        selected.source.clear(); selected.target.clear();
        if (notify) publishChange(); else render();

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

        if (!Array.isArray(values)) {
            reportError('invalid-value', 'Transfer list values must be an array.', { values: [...target] });

            return false;
        }

        if (values.length === 0) {
            return false;
        }

        const disabledValues = [...new Set(values.filter((value) => itemByValue.get(value)?.disabled === true))];
        if (disabledValues.length > 0) {
            reportError('disabled-item', 'Disabled transfer list items cannot be moved.', {
                disabledValues,
                values: [...target],
            });

            return false;
        }

        if (direction === 'to-target') {
            const additions = values.filter((value) => itemByValue.has(value) && !target.includes(value));
            if (maxItems && target.length + additions.length > maxItems) {
                reportError('max-items', `The transfer list cannot contain more than ${maxItems} item${maxItems === 1 ? '' : 's'}.`, {
                    maxItems,
                    values: [...target],
                });

                return false;
            }

            return setTargetValues([...target, ...additions]);
        } else {
            return setTargetValues(target.filter((value) => !values.includes(value)));
        }
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
        const visible = new Set(filtered('target').map((item) => item.value));
        const ordered = values.filter((value) => visible.has(value));
        let index = 0;

        reorder(target.map((value) => visible.has(value) ? ordered[index++] : value));
    }

    function shiftTarget(direction, values = [...selected.target]) {
        if (configuration.disabled === true || values.length === 0) return false;

        const moving = new Set(values);
        const order = filtered('target').map((item) => item.value);
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

        let index = 0;
        const next = target.map((value) => visible.has(value) ? order[index++] : value);
        if (next.every((value, position) => value === target[position])) return false;
        if (!reorder(next)) return false;

        values.forEach((value) => selected.target.add(value));
        renderList('target');

        return true;
    }

    function onListClick(side, event) {
        const button = event.target.closest('[role="option"]');
        if (!(button instanceof HTMLElement) || button.getAttribute('aria-disabled') === 'true') return;
        const value = button.dataset.value;
        if (!value) return;
        if (selected[side].has(value)) selected[side].delete(value); else selected[side].add(value);
        renderList(side);
    }
    function onListKeydown(side, event) {
        const button = event.target.closest('[role="option"]');
        if (!(button instanceof HTMLElement) || button.getAttribute('aria-disabled') === 'true') return;
        if (side === 'target' && event.altKey && ['ArrowUp', 'ArrowDown'].includes(event.key)) {
            event.preventDefault();
            const value = button.dataset.value;
            shiftTarget(event.key === 'ArrowUp' ? 'up' : 'down', [value]);
            [...targetList.children].find((item) => item.dataset.value === value)?.focus();

            return;
        }
        const buttons = [...(side === 'target' ? targetList : sourceList).querySelectorAll('[role="option"]')];
        const position = buttons.indexOf(button);
        if (event.key === 'ArrowDown' && buttons[position + 1]) { event.preventDefault(); buttons[position + 1].focus(); }
        if (event.key === 'ArrowUp' && buttons[position - 1]) { event.preventDefault(); buttons[position - 1].focus(); }
        if (event.key === ' ' || event.key === 'Enter') { event.preventDefault(); onListClick(side, event); }
        if (event.key === 'ArrowRight' && side === 'source') { event.preventDefault(); move('to-target', [button.dataset.value]); }
        if (event.key === 'ArrowLeft' && side === 'target') { event.preventDefault(); move('to-source', [button.dataset.value]); }
    }
    const sourceClick = (event) => onListClick('source', event);
    const targetClick = (event) => onListClick('target', event);
    const sourceKeydown = (event) => onListKeydown('source', event);
    const targetKeydown = (event) => onListKeydown('target', event);
    const onActions = (event) => {
        const orderButton = event.target.closest('[data-daisy-kit-transfer-reorder]');
        if (orderButton instanceof HTMLElement) shiftTarget(orderButton.dataset.daisyKitTransferReorder);
        const button = event.target.closest('[data-daisy-kit-transfer-move]');
        if (button instanceof HTMLElement) move(button.dataset.daisyKitTransferMove);
    };
    const onSearch = (event) => { const input = event.target; if (!(input instanceof HTMLInputElement)) return; if (input.dataset.daisyKitTransferSearch === 'source') sourceSearch = input.value; else targetSearch = input.value; renderList(input.dataset.daisyKitTransferSearch); };
    sourceList.addEventListener('click', sourceClick); targetList.addEventListener('click', targetClick);
    sourceList.addEventListener('keydown', sourceKeydown); targetList.addEventListener('keydown', targetKeydown);
    root.addEventListener('click', onActions); root.addEventListener('input', onSearch);
    if (configuration.sortable === true && configuration.disabled !== true) {
        sortables.push(Sortable.create(targetList, {
            animation: 150,
            draggable: ':scope > li',
            filter: '[aria-disabled="true"]',
            preventOnFilter: true,
            onEnd: () => {
                if (!destroyed) {
                    reorderVisible([...targetList.querySelectorAll(':scope > li')]
                        .map((item) => item.dataset.value)
                        .filter(Boolean));
                }
            },
        }));
    }
    render();
    facades.set(root, {
        getTargetValues: () => [...target],
        setTargetValues,
        move,
        reorder,
        clearSelection() {
            selected.source.clear();
            selected.target.clear();
            render();

            return true;
        },
    });
    return () => {
        destroyed = true;
        sortables.forEach((sortable) => sortable.destroy());
        sourceList.removeEventListener('click', sourceClick);
        targetList.removeEventListener('click', targetClick);
        sourceList.removeEventListener('keydown', sourceKeydown);
        targetList.removeEventListener('keydown', targetKeydown);
        root.removeEventListener('click', onActions);
        root.removeEventListener('input', onSearch);
        sourceList.innerHTML = initialDom.sourceMarkup;
        targetList.innerHTML = initialDom.targetMarkup;
        valuesNode.innerHTML = initialDom.valuesMarkup;
        searchInputs.forEach((input, index) => { input.value = initialDom.searchValues[index] ?? ''; });
        if (requiredInput instanceof HTMLInputElement && initialDom.requiredValue !== null) requiredInput.value = initialDom.requiredValue;
        facades.delete(root);
    };
}

const module = createMountable('transfer-list', initialize);
export function getInstance(root) { return facades.get(root) ?? null; }
export function mount(root) { module.mount(root); return getInstance(root); }
export function mountAll(scope = document) { return [...scope.querySelectorAll('[data-daisy-kit-module="transfer-list"]')].map(mount); }
export function unmount(root) { return module.unmount(root); }
