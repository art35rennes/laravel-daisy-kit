import '../css/combobox.css';
import { compareItems, rankItem } from '@tanstack/match-sorter-utils';
import { createMountable } from './core/mountable.js';
import { createInstanceIdentifier } from './core/identifiers.js';

const facades = new WeakMap();

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:combobox:${name}`, { bubbles: true, detail }));
}

function normalizeOptions(options) {
    if (!Array.isArray(options)) return [];

    return options.flatMap((option) => {
        if (!option || typeof option !== 'object' || Array.isArray(option) || typeof option.value !== 'string') return [];

        return [{
            value: option.value,
            label: typeof option.label === 'string' && option.label !== '' ? option.label : option.value,
            description: typeof option.description === 'string' ? option.description : '',
            disabled: option.disabled === true,
        }];
    });
}

function asValues(value, multiple) {
    const values = Array.isArray(value) ? value : (value === null || value === undefined || value === '' ? [] : [value]);
    const normalized = values.filter((item) => typeof item === 'string');

    return multiple ? [...new Set(normalized)] : normalized.slice(0, 1);
}

function initialize(root, configuration) {
    const input = root.querySelector('[data-daisy-kit-combobox-input]');
    const listbox = root.querySelector('[data-daisy-kit-combobox-listbox]');
    const tokens = root.querySelector('[data-daisy-kit-combobox-tokens]');
    const valuesNode = root.querySelector('[data-daisy-kit-combobox-values]');
    const requiredInput = root.querySelector('[data-daisy-kit-combobox-required]');

    if (!(input instanceof HTMLInputElement) || !(listbox instanceof HTMLElement) || !(tokens instanceof HTMLElement) || !(valuesNode instanceof HTMLElement)) {
        throw new Error('Combobox markup is incomplete.');
    }

    const initialDom = {
        activeDescendant: input.getAttribute('aria-activedescendant'),
        controls: input.getAttribute('aria-controls'),
        expanded: input.getAttribute('aria-expanded'),
        inputValue: input.value,
        listboxHidden: listbox.hidden,
        listboxId: listbox.getAttribute('id'),
        listboxMarkup: listbox.innerHTML,
        requiredValue: requiredInput instanceof HTMLInputElement ? requiredInput.value : null,
        tokensMarkup: tokens.innerHTML,
        valuesMarkup: valuesNode.innerHTML,
    };

    const multiple = configuration.multiple === true;
    const allowCustom = configuration.allowCustom === true;
    const maxItems = Number.isInteger(configuration.maxItems) && configuration.maxItems > 0 ? configuration.maxItems : null;
    const minChars = Number.isInteger(configuration.minChars) && configuration.minChars > 0 ? configuration.minChars : 0;
    const debounce = Number.isInteger(configuration.debounce) && configuration.debounce >= 0 ? configuration.debounce : 200;
    const queryParam = typeof configuration.queryParam === 'string' && configuration.queryParam !== '' ? configuration.queryParam : 'query';
    const name = typeof configuration.name === 'string' && configuration.name !== '' ? configuration.name : null;
    const separators = Array.isArray(configuration.tokenSeparators) ? configuration.tokenSeparators.filter((separator) => typeof separator === 'string' && separator !== '') : [','];
    const source = typeof configuration.source === 'string' && configuration.source !== '' ? configuration.source : null;
    const listboxId = listbox.id || createInstanceIdentifier('daisy-kit-combobox-listbox');
    listbox.id = listboxId;
    input.setAttribute('aria-controls', listboxId);
    let options = normalizeOptions(configuration.options);
    let values = asValues(configuration.value, multiple);
    let activeIndex = -1;
    let controller = null;
    let timer = null;
    let destroyed = false;
    let query = '';

    const optionFor = (value) => options.find((option) => option.value === value) ?? { value, label: value, description: '', disabled: false };
    const canAdd = (value) => value !== '' && !values.includes(value) && (!maxItems || values.length < maxItems);
    const filtered = () => {
        if (query.length < minChars) return [];
        if (query === '') return options;

        return options
            .map((option) => ({ option, rank: rankItem(`${option.label} ${option.description} ${option.value}`, query) }))
            .filter(({ rank }) => rank.passed)
            .sort((left, right) => compareItems(left.rank, right.rank))
            .map(({ option }) => option);
    };

    function render() {
        const matches = filtered();
        listbox.replaceChildren();
        activeIndex = matches.length === 0 ? -1 : Math.min(activeIndex, matches.length - 1);
        matches.forEach((option, index) => {
            const item = document.createElement('li');
            item.role = 'option';
            item.id = `${listboxId}-option-${index}`;
            item.dataset.value = option.value;
            item.setAttribute('aria-disabled', String(option.disabled));
            item.setAttribute('aria-selected', String(values.includes(option.value)));
            item.classList.toggle('active', index === activeIndex);
            item.textContent = option.description === '' ? option.label : `${option.label} — ${option.description}`;
            listbox.append(item);
        });
        listbox.hidden = matches.length === 0;
        input.setAttribute('aria-expanded', String(!listbox.hidden));
        const activeOption = matches[activeIndex];
        if (activeOption && !activeOption.disabled) input.setAttribute('aria-activedescendant', `${listboxId}-option-${activeIndex}`);
        else input.removeAttribute('aria-activedescendant');
        tokens.replaceChildren();
        valuesNode.replaceChildren();
        values.forEach((value) => {
            const option = optionFor(value);
            if (multiple) {
                const token = document.createElement('button');
                token.className = 'badge badge-outline gap-1';
                token.dataset.value = value;
                token.disabled = configuration.disabled === true || configuration.readonly === true;
                token.type = 'button';
                token.textContent = `${option.label} ×`;
                tokens.append(token);
            }
            if (name) {
                const hidden = document.createElement('input');
                hidden.name = multiple ? `${name}[]` : name;
                hidden.type = 'hidden';
                hidden.value = value;
                valuesNode.append(hidden);
            }
        });
        input.value = multiple ? query : (values[0] ? optionFor(values[0]).label : query);
        if (requiredInput instanceof HTMLInputElement) requiredInput.value = values.join(',');
    }

    function setValues(next, notify = true) {
        const normalized = asValues(next, multiple);
        if (maxItems && normalized.length > maxItems) {
            emit(root, 'error', {
                code: 'max-items',
                maxItems,
                message: `The combobox cannot contain more than ${maxItems} item${maxItems === 1 ? '' : 's'}.`,
                values: [...values],
            });

            return false;
        }

        values = normalized;
        render();
        if (notify) {
            emit(root, 'change', {
                value: multiple ? [...values] : (values[0] ?? null),
                values: [...values],
            });
            root.dispatchEvent(new Event('change', { bubbles: true }));
        }

        return true;
    }

    function select(value) {
        if (optionFor(value).disabled || !canAdd(value)) return false;
        setValues(multiple ? [...values, value] : [value]);
        query = '';
        render();
        close();

        return true;
    }

    function close() {
        const wasOpen = input.getAttribute('aria-expanded') === 'true';
        activeIndex = -1;
        listbox.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');

        return wasOpen;
    }

    function open() {
        if (input.disabled || input.readOnly) {
            return false;
        }

        render();
        if (!listbox.hidden) input.setAttribute('aria-expanded', 'true');

        return true;
    }

    async function refresh() {
        if (!source || query.length < minChars) return false;
        controller?.abort();
        controller = new AbortController();
        const request = controller;
        const requestedQuery = query;
        emit(root, 'loading', { loading: true, query: requestedQuery });
        root.setAttribute('aria-busy', 'true');
        try {
            const url = new URL(source, window.location.href);
            url.searchParams.set(queryParam, requestedQuery);
            const response = await fetch(url, { credentials: 'same-origin', signal: request.signal });
            if (destroyed || controller !== request) return false;
            if (!response.ok) {
                emit(root, 'error', {
                    code: 'source-unavailable',
                    message: 'The combobox source request failed.',
                    query: requestedQuery,
                    status: response.status,
                });

                return false;
            }
            const payload = await response.json();
            if (destroyed || controller !== request) return false;
            if (!payload || !Array.isArray(payload.items)) {
                emit(root, 'error', {
                    code: 'invalid-response',
                    message: 'The combobox source returned an invalid response.',
                    query: requestedQuery,
                });

                return false;
            }
            options = normalizeOptions(payload.items);
            activeIndex = -1;
            render();

            return true;
        } catch (error) {
            if (destroyed || controller !== request || (error instanceof DOMException && error.name === 'AbortError')) {
                return false;
            }

            emit(root, 'error', {
                code: 'source-unavailable',
                message: error instanceof Error && error.message !== ''
                    ? error.message
                    : 'The combobox source is unavailable.',
                query: requestedQuery,
            });

            return false;
        } finally {
            if (controller === request) {
                controller = null;
                emit(root, 'loading', { loading: false, query: requestedQuery });
                root.removeAttribute('aria-busy');
            }
        }
    }

    const onInput = () => {
        query = input.value;
        emit(root, 'query', { query });
        if (source) {
            window.clearTimeout(timer);
            timer = window.setTimeout(refresh, debounce);
        }
        activeIndex = -1;
        render();
    };
    const onKeydown = (event) => {
        const matches = filtered();
        if (event.key === 'ArrowDown') { event.preventDefault(); activeIndex = Math.min(activeIndex + 1, matches.length - 1); open(); }
        if (event.key === 'ArrowUp') { event.preventDefault(); activeIndex = Math.max(activeIndex - 1, 0); open(); }
        if (event.key === 'Escape') close();
        if (event.key === 'Enter') {
            event.preventDefault();
            const match = matches[activeIndex];
            if (match) select(match.value);
            else if (allowCustom && query !== '') select(query);
        }
        if (allowCustom && multiple && separators.some((separator) => input.value.endsWith(separator))) {
            const token = input.value.slice(0, -1).trim();
            if (token) select(token);
        }
        render();
    };
    const onListClick = (event) => {
        const target = event.target.closest('[data-value]');
        if (target instanceof HTMLElement && target.getAttribute('aria-disabled') !== 'true') select(target.dataset.value ?? '');
    };
    const onTokenClick = (event) => {
        const target = event.target.closest('[data-value]');
        if (target instanceof HTMLElement) setValues(values.filter((value) => value !== target.dataset.value));
    };
    const onPaste = (event) => {
        if (!allowCustom || !multiple) return;
        const pasted = event.clipboardData?.getData('text') ?? '';
        const pattern = separators.length > 0
            ? new RegExp(separators.map((separator) => separator.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'))
            : null;
        const candidates = (pattern ? pasted.split(pattern) : [pasted]).map((value) => value.trim()).filter(Boolean);
        if (candidates.length === 0) return;
        event.preventDefault();
        setValues([...values, ...candidates]);
        query = '';
        render();
    };
    const onDocumentPointerDown = (event) => { if (!root.contains(event.target)) close(); };
    input.addEventListener('input', onInput);
    input.addEventListener('focus', open);
    input.addEventListener('keydown', onKeydown);
    input.addEventListener('paste', onPaste);
    listbox.addEventListener('click', onListClick);
    tokens.addEventListener('click', onTokenClick);
    document.addEventListener('pointerdown', onDocumentPointerDown);
    render();

    facades.set(root, {
        getValue: () => multiple ? [...values] : (values[0] ?? null),
        setValue: (value) => setValues(value),
        clear: () => setValues([]),
        open,
        close,
        refresh,
    });
    return () => {
        destroyed = true;
        controller?.abort();
        window.clearTimeout(timer);
        document.removeEventListener('pointerdown', onDocumentPointerDown);
        input.removeEventListener('input', onInput);
        input.removeEventListener('focus', open);
        input.removeEventListener('keydown', onKeydown);
        input.removeEventListener('paste', onPaste);
        listbox.removeEventListener('click', onListClick);
        tokens.removeEventListener('click', onTokenClick);
        input.value = initialDom.inputValue;
        listbox.innerHTML = initialDom.listboxMarkup;
        listbox.hidden = initialDom.listboxHidden;
        tokens.innerHTML = initialDom.tokensMarkup;
        valuesNode.innerHTML = initialDom.valuesMarkup;
        if (requiredInput instanceof HTMLInputElement && initialDom.requiredValue !== null) requiredInput.value = initialDom.requiredValue;
        for (const [attribute, value] of [['aria-activedescendant', initialDom.activeDescendant], ['aria-controls', initialDom.controls], ['aria-expanded', initialDom.expanded], ['id', initialDom.listboxId]]) {
            const element = attribute === 'id' ? listbox : input;
            if (value === null) element.removeAttribute(attribute); else element.setAttribute(attribute, value);
        }
        facades.delete(root);
    };
}

const module = createMountable('combobox', initialize);
export function getInstance(root) { return facades.get(root) ?? null; }
export function mount(root) { module.mount(root); return getInstance(root); }
export function mountAll(scope = document) { return [...scope.querySelectorAll('[data-daisy-kit-module="combobox"]')].map(mount); }
export function unmount(root) { return module.unmount(root); }
