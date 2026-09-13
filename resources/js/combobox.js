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
        if (!option || typeof option !== 'object' || Array.isArray(option) || typeof option.value !== 'string') {
            return [];
        }

        const label = typeof option.label === 'string' && option.label !== '' ? option.label : option.value;

        return [{
            avatar: typeof option.avatar === 'string' && option.avatar !== '' ? option.avatar : '',
            description: typeof option.description === 'string' ? option.description : '',
            disabled: option.disabled === true,
            initials: typeof option.initials === 'string' ? option.initials : '',
            label,
            meta: typeof option.meta === 'string' ? option.meta : '',
            value: option.value,
        }];
    });
}

function validOptionInput(options) {
    return Array.isArray(options) && options.every(option => option
        && typeof option === 'object'
        && !Array.isArray(option)
        && typeof option.value === 'string');
}

function asValues(value, multiple) {
    const values = Array.isArray(value) ? value : (value === null || value === undefined || value === '' ? [] : [value]);
    const normalized = values.filter((item) => typeof item === 'string');

    return multiple ? [...new Set(normalized)] : normalized.slice(0, 1);
}

function initialsFor(option) {
    if (option.initials !== '') return option.initials.slice(0, 3).toLocaleUpperCase();

    return option.label
        .split(/\s+/u)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toLocaleUpperCase();
}

function appendText(parent, className, text) {
    if (text === '') return;

    const element = document.createElement('span');
    element.className = className;
    element.textContent = text;
    parent.append(element);
}

function createDefaultOptionContent(option, selected) {
    const content = document.createElement('span');
    content.className = 'daisy-kit-combobox__option';
    content.classList.toggle('daisy-kit-combobox__option--plain', option.avatar === '' && option.initials === '');

    if (option.avatar !== '' || option.initials !== '') {
        const avatar = document.createElement('span');
        avatar.className = 'daisy-kit-combobox__avatar';
        avatar.setAttribute('aria-hidden', 'true');
        const fallback = document.createElement('span');
        fallback.dataset.daisyKitComboboxAvatarFallback = '';
        fallback.textContent = initialsFor(option);

        if (option.avatar !== '') {
            const image = document.createElement('img');
            image.alt = '';
            image.src = option.avatar;
            fallback.hidden = true;
            image.addEventListener('error', () => {
                image.hidden = true;
                fallback.hidden = false;
            }, { once: true });
            avatar.append(image, fallback);
        } else {
            avatar.append(fallback);
        }

        content.append(avatar);
    }

    const body = document.createElement('span');
    body.className = 'daisy-kit-combobox__option-body';
    appendText(body, 'daisy-kit-combobox__option-label', option.label);
    appendText(body, 'daisy-kit-combobox__option-description', option.description);
    appendText(body, 'daisy-kit-combobox__option-meta', option.meta);
    content.append(body);

    const check = document.createElement('span');
    check.className = 'daisy-kit-combobox__option-check';
    check.hidden = !selected;
    check.setAttribute('aria-hidden', 'true');
    check.textContent = '✓';
    content.append(check);

    return content;
}

function initialize(root, configuration) {
    const input = root.querySelector('[data-daisy-kit-combobox-input]');
    const listbox = root.querySelector('[data-daisy-kit-combobox-listbox]');
    const tokens = root.querySelector('[data-daisy-kit-combobox-tokens]');
    const valuesNode = root.querySelector('[data-daisy-kit-combobox-values]');
    const popup = root.querySelector('[data-daisy-kit-combobox-popup]') ?? listbox;
    const popupStatus = root.querySelector('[data-daisy-kit-combobox-popup-status]');
    const control = root.querySelector('[data-daisy-kit-combobox-control]');
    const toggle = root.querySelector('[data-daisy-kit-combobox-toggle]');
    const requiredInput = root.querySelector('[data-daisy-kit-combobox-required]');

    if (!(input instanceof HTMLInputElement)
        || !(listbox instanceof HTMLElement)
        || !(tokens instanceof HTMLElement)
        || !(valuesNode instanceof HTMLElement)
        || !(popup instanceof HTMLElement)) {
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
        listboxMultiselectable: listbox.getAttribute('aria-multiselectable'),
        popupHidden: popup.hidden,
        popupPlacement: popup.getAttribute('data-placement'),
        popupStatusHidden: popupStatus instanceof HTMLElement ? popupStatus.hidden : null,
        popupStatusText: popupStatus instanceof HTMLElement ? popupStatus.textContent : null,
        requiredValue: requiredInput instanceof HTMLInputElement ? requiredInput.value : null,
        toggleControls: toggle?.getAttribute('aria-controls') ?? null,
        toggleExpanded: toggle?.getAttribute('aria-expanded') ?? null,
        tokensMarkup: tokens.innerHTML,
        valuesMarkup: valuesNode.innerHTML,
    };

    const multiple = configuration.multiple === true;
    const allowCustom = configuration.allowCustom === true;
    const maxItems = Number.isInteger(configuration.maxItems) && configuration.maxItems > 0
        ? configuration.maxItems
        : null;
    const maxSuggestions = Number.isInteger(configuration.maxSuggestions) && configuration.maxSuggestions > 0
        ? configuration.maxSuggestions
        : 50;
    const minChars = Number.isInteger(configuration.minChars) && configuration.minChars > 0
        ? configuration.minChars
        : 0;
    const debounce = Number.isInteger(configuration.debounce) && configuration.debounce >= 0
        ? configuration.debounce
        : 200;
    const queryParam = typeof configuration.queryParam === 'string' && configuration.queryParam !== ''
        ? configuration.queryParam
        : 'query';
    const name = typeof configuration.name === 'string' && configuration.name !== '' ? configuration.name : null;
    const separators = Array.isArray(configuration.tokenSeparators)
        ? configuration.tokenSeparators.filter((separator) => typeof separator === 'string' && separator !== '')
        : [','];
    const source = typeof configuration.source === 'string' && configuration.source !== '' ? configuration.source : null;
    const allowedSearchFields = ['label', 'description', 'meta', 'value'];
    const configuredSearchFields = Array.isArray(configuration.searchFields)
        ? [...new Set(configuration.searchFields.filter(field => allowedSearchFields.includes(field)))]
        : [];
    const searchFields = configuredSearchFields.length > 0 ? configuredSearchFields : allowedSearchFields;
    const labels = {
        loading: configuration.labels?.loading || 'Loading suggestions…',
        noResults: configuration.labels?.noResults || 'No matching suggestions.',
        remove: configuration.labels?.remove || 'Remove :label',
    };
    const listboxId = listbox.id || createInstanceIdentifier('daisy-kit-combobox-listbox');
    listbox.id = listboxId;
    listbox.setAttribute('aria-multiselectable', String(multiple));
    input.setAttribute('aria-controls', listboxId);
    toggle?.setAttribute('aria-controls', listboxId);

    let options = normalizeOptions(configuration.options);
    const knownOptions = new Map(options.map(option => [option.value, option]));
    let values = asValues(configuration.value, multiple);
    let activeIndex = -1;
    let controller = null;
    let timer = null;
    let destroyed = false;
    let query = '';
    let opened = false;
    let loading = false;
    let loadedQuery = null;
    let optionRenderer = null;
    let rendererFailures = new Set();

    const rememberOptions = nextOptions => nextOptions.forEach(option => knownOptions.set(option.value, option));
    const optionFor = (value) => knownOptions.get(value) ?? {
        avatar: '',
        description: '',
        disabled: false,
        initials: '',
        label: value,
        meta: '',
        value,
    };
    const canAdd = (value) => value !== '' && !values.includes(value) && (!maxItems || values.length < maxItems);
    const filtered = () => {
        if (query !== '' && query.length < minChars) return [];
        if (query === '') return options.slice(0, maxSuggestions);

        return options
            .map((option) => ({
                option,
                rank: rankItem(searchFields.map(field => option[field]).join(' '), query),
            }))
            .filter(({ rank }) => rank.passed)
            .sort((left, right) => compareItems(left.rank, right.rank))
            .slice(0, maxSuggestions)
            .map(({ option }) => option);
    };

    function renderOptionContent(option, selected, active) {
        if (optionRenderer) {
            try {
                const snapshot = Object.freeze({ ...option });
                const context = Object.freeze({ active, query, selected });
                const rendered = optionRenderer(snapshot, context);

                if (rendered instanceof Node) return rendered;
                if (typeof rendered === 'string') return document.createTextNode(rendered);
            } catch (error) {
                const message = error instanceof Error && error.message !== ''
                    ? error.message
                    : 'The option renderer failed.';
                const failureKey = `${option.value}:${message}`;
                if (!rendererFailures.has(failureKey)) {
                    rendererFailures.add(failureKey);
                    emit(root, 'error', {
                        code: 'option-render-failed',
                        message,
                        value: option.value,
                    });
                }
            }
        }

        return createDefaultOptionContent(option, selected);
    }

    function renderOptions() {
        const matches = filtered();
        listbox.replaceChildren();
        activeIndex = matches.length === 0 ? -1 : Math.min(activeIndex, matches.length - 1);

        matches.forEach((option, index) => {
            const selected = values.includes(option.value);
            const active = index === activeIndex;
            const item = document.createElement('li');
            item.role = 'option';
            item.id = `${listboxId}-option-${index}`;
            item.dataset.value = option.value;
            item.setAttribute('aria-disabled', String(option.disabled));
            item.setAttribute('aria-selected', String(selected));
            item.classList.toggle('active', active);
            item.append(renderOptionContent(option, selected, active));
            listbox.append(item);
        });

        const activeOption = matches[activeIndex];
        if (activeOption && !activeOption.disabled) {
            input.setAttribute('aria-activedescendant', `${listboxId}-option-${activeIndex}`);
        } else {
            input.removeAttribute('aria-activedescendant');
        }

        listbox.hidden = loading || matches.length === 0;
        if (popupStatus instanceof HTMLElement) {
            popupStatus.textContent = loading ? labels.loading : labels.noResults;
            popupStatus.hidden = !loading && matches.length > 0;
        }

        return matches;
    }

    function renderTokens() {
        tokens.replaceChildren();
        valuesNode.replaceChildren();

        values.forEach((value) => {
            const option = optionFor(value);
            if (multiple) {
                const token = document.createElement('button');
                token.className = 'badge badge-soft badge-primary gap-1 daisy-kit-combobox__token';
                token.dataset.daisyKitComboboxToken = '';
                token.dataset.value = value;
                token.disabled = configuration.disabled === true || configuration.readonly === true;
                token.type = 'button';
                token.setAttribute('aria-label', labels.remove.replace(':label', option.label));

                const tokenLabel = document.createElement('span');
                tokenLabel.className = 'daisy-kit-combobox__token-label';
                tokenLabel.dataset.daisyKitComboboxTokenLabel = '';
                tokenLabel.textContent = option.label;
                const remove = document.createElement('span');
                remove.setAttribute('aria-hidden', 'true');
                remove.textContent = '×';
                token.append(tokenLabel, remove);
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

        if (requiredInput instanceof HTMLInputElement) requiredInput.value = values.join(',');
    }

    function updatePlacement() {
        if (!opened) return;

        const shell = root.querySelector('[data-daisy-kit-combobox-shell]');
        if (!(shell instanceof HTMLElement)) return;

        const bounds = shell.getBoundingClientRect();
        const below = window.innerHeight - bounds.bottom;
        const above = bounds.top;
        popup.dataset.placement = below < Math.min(popup.scrollHeight || 288, 288) && above > below ? 'top' : 'bottom';
    }

    function render() {
        renderOptions();
        renderTokens();
        popup.hidden = !opened;
        input.setAttribute('aria-expanded', String(opened));
        toggle?.setAttribute('aria-expanded', String(opened));
        input.value = multiple ? query : (query !== '' ? query : (values[0] ? optionFor(values[0]).label : ''));
        updatePlacement();
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

    async function refresh() {
        if (!source || query.length < minChars) return false;

        controller?.abort();
        controller = new AbortController();
        const request = controller;
        const requestedQuery = query;
        loading = true;
        emit(root, 'loading', { loading: true, query: requestedQuery });
        root.setAttribute('aria-busy', 'true');
        render();

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
            rememberOptions(options);
            activeIndex = -1;
            loadedQuery = requestedQuery;

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
                loading = false;
                emit(root, 'loading', { loading: false, query: requestedQuery });
                root.removeAttribute('aria-busy');
                if (!destroyed) render();
            }
        }
    }

    function open() {
        if (input.disabled || input.readOnly) return false;

        opened = true;
        render();
        if (source && query.length >= minChars && loadedQuery !== query && controller === null) void refresh();

        return true;
    }

    function close() {
        const wasOpen = opened;
        opened = false;
        activeIndex = -1;
        render();

        return wasOpen;
    }

    function select(value) {
        if (optionFor(value).disabled || !canAdd(value)) return false;

        query = '';
        activeIndex = -1;
        const changed = setValues(multiple ? [...values, value] : [value]);
        if (!changed) return false;

        if (multiple) {
            opened = true;
            render();
            input.focus();
            if (source && minChars === 0 && loadedQuery !== '') void refresh();
        } else {
            close();
        }

        return true;
    }

    function moveActive(direction) {
        const matches = filtered();
        if (matches.length === 0) return;

        let candidate = activeIndex;
        for (let attempts = 0; attempts < matches.length; attempts += 1) {
            candidate = Math.max(0, Math.min(matches.length - 1, candidate + direction));
            if (!matches[candidate].disabled) break;
            if ((direction > 0 && candidate === matches.length - 1) || (direction < 0 && candidate === 0)) break;
        }
        activeIndex = candidate;
        opened = true;
        render();
        const active = document.getElementById(`${listboxId}-option-${activeIndex}`);
        if (typeof active?.scrollIntoView === 'function') active.scrollIntoView({ block: 'nearest' });
    }

    const onInput = () => {
        query = input.value;
        opened = true;
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
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (!opened) open();
            moveActive(1);

            return;
        }
        if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (!opened) open();
            if (activeIndex === -1) activeIndex = matches.length;
            moveActive(-1);

            return;
        }
        if (event.key === 'Escape' || event.key === 'Tab') {
            close();

            return;
        }
        if (event.key === 'Enter') {
            event.preventDefault();
            const match = matches[activeIndex];
            if (match) select(match.value);
            else if (allowCustom && query !== '') select(query);

            return;
        }
        if (event.key === 'Backspace' && multiple && input.value === '' && values.length > 0) {
            event.preventDefault();
            setValues(values.slice(0, -1));

            return;
        }
        if (allowCustom && multiple && separators.some((separator) => input.value.endsWith(separator))) {
            const token = input.value.slice(0, -1).trim();
            if (token) select(token);
        }
    };
    const onListClick = (event) => {
        const target = event.target.closest('[role="option"][data-value]');
        if (target instanceof HTMLElement && target.getAttribute('aria-disabled') !== 'true') {
            select(target.dataset.value ?? '');
        }
    };
    const onListPointerDown = (event) => event.preventDefault();
    const onTokenClick = (event) => {
        const target = event.target.closest('[data-daisy-kit-combobox-token]');
        if (target instanceof HTMLElement) setValues(values.filter((value) => value !== target.dataset.value));
    };
    const onPaste = (event) => {
        if (!allowCustom || !multiple) return;

        const pasted = event.clipboardData?.getData('text') ?? '';
        const pattern = separators.length > 0
            ? new RegExp(separators.map((separator) => separator.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'))
            : null;
        const candidates = (pattern ? pasted.split(pattern) : [pasted])
            .map((value) => value.trim())
            .filter(Boolean);
        if (candidates.length === 0) return;

        event.preventDefault();
        query = '';
        setValues([...values, ...candidates]);
    };
    const onDocumentPointerDown = (event) => {
        if (event.target instanceof Node && !root.contains(event.target)) close();
    };
    const onControlPointerDown = (event) => {
        if (event.target instanceof Element && event.target.closest('button')) return;
        if (event.target !== input) event.preventDefault();
        input.focus();
        open();
    };
    const onTogglePointerDown = (event) => event.preventDefault();
    const onToggleClick = () => {
        const shouldOpen = !opened;
        input.focus();
        if (shouldOpen) open(); else close();
    };
    const onFocus = () => open();
    const onViewportChange = () => updatePlacement();

    input.addEventListener('input', onInput);
    input.addEventListener('focus', onFocus);
    input.addEventListener('keydown', onKeydown);
    input.addEventListener('paste', onPaste);
    listbox.addEventListener('click', onListClick);
    listbox.addEventListener('pointerdown', onListPointerDown);
    tokens.addEventListener('click', onTokenClick);
    control?.addEventListener('pointerdown', onControlPointerDown);
    toggle?.addEventListener('pointerdown', onTogglePointerDown);
    toggle?.addEventListener('click', onToggleClick);
    document.addEventListener('pointerdown', onDocumentPointerDown);
    window.addEventListener('resize', onViewportChange);
    window.addEventListener('scroll', onViewportChange, true);
    render();

    facades.set(root, {
        clear: () => setValues([]),
        clearOptionRenderer() {
            optionRenderer = null;
            rendererFailures = new Set();
            render();

            return true;
        },
        close,
        getOptions: () => options.map(option => ({ ...option })),
        getValue: () => multiple ? [...values] : (values[0] ?? null),
        open,
        refresh,
        setOptionRenderer(renderer) {
            if (typeof renderer !== 'function') return false;

            optionRenderer = renderer;
            rendererFailures = new Set();
            render();

            return true;
        },
        setOptions(nextOptions) {
            if (!validOptionInput(nextOptions)) {
                emit(root, 'error', {
                    code: 'invalid-options',
                    message: 'Combobox options must be an array of canonical option objects.',
                });

                return false;
            }

            options = normalizeOptions(nextOptions);
            rememberOptions(options);
            activeIndex = -1;
            loadedQuery = source ? null : loadedQuery;
            render();

            return true;
        },
        setValue: (value) => setValues(value),
    });

    return () => {
        destroyed = true;
        const activeController = controller;
        controller = null;
        activeController?.abort();
        window.clearTimeout(timer);
        document.removeEventListener('pointerdown', onDocumentPointerDown);
        window.removeEventListener('resize', onViewportChange);
        window.removeEventListener('scroll', onViewportChange, true);
        input.removeEventListener('input', onInput);
        input.removeEventListener('focus', onFocus);
        input.removeEventListener('keydown', onKeydown);
        input.removeEventListener('paste', onPaste);
        listbox.removeEventListener('click', onListClick);
        listbox.removeEventListener('pointerdown', onListPointerDown);
        tokens.removeEventListener('click', onTokenClick);
        control?.removeEventListener('pointerdown', onControlPointerDown);
        toggle?.removeEventListener('pointerdown', onTogglePointerDown);
        toggle?.removeEventListener('click', onToggleClick);
        input.value = initialDom.inputValue;
        listbox.innerHTML = initialDom.listboxMarkup;
        listbox.hidden = initialDom.listboxHidden;
        popup.hidden = initialDom.popupHidden;
        if (initialDom.popupPlacement === null) delete popup.dataset.placement;
        else popup.dataset.placement = initialDom.popupPlacement;
        tokens.innerHTML = initialDom.tokensMarkup;
        valuesNode.innerHTML = initialDom.valuesMarkup;
        if (popupStatus instanceof HTMLElement && initialDom.popupStatusHidden !== null) {
            popupStatus.hidden = initialDom.popupStatusHidden;
            popupStatus.textContent = initialDom.popupStatusText;
        }
        if (requiredInput instanceof HTMLInputElement && initialDom.requiredValue !== null) {
            requiredInput.value = initialDom.requiredValue;
        }
        root.removeAttribute('aria-busy');
        for (const [attribute, value] of [
            ['aria-activedescendant', initialDom.activeDescendant],
            ['aria-controls', initialDom.controls],
            ['aria-expanded', initialDom.expanded],
            ['id', initialDom.listboxId],
        ]) {
            const element = attribute === 'id' ? listbox : input;
            if (value === null) element.removeAttribute(attribute); else element.setAttribute(attribute, value);
        }
        if (toggle instanceof HTMLElement) {
            if (initialDom.toggleControls === null) toggle.removeAttribute('aria-controls');
            else toggle.setAttribute('aria-controls', initialDom.toggleControls);
            if (initialDom.toggleExpanded === null) toggle.removeAttribute('aria-expanded');
            else toggle.setAttribute('aria-expanded', initialDom.toggleExpanded);
        }
        if (initialDom.listboxMultiselectable === null) listbox.removeAttribute('aria-multiselectable');
        else listbox.setAttribute('aria-multiselectable', initialDom.listboxMultiselectable);
        facades.delete(root);
    };
}

const module = createMountable('combobox', initialize);
export function getInstance(root) { return facades.get(root) ?? null; }
export function mount(root) { module.mount(root); return getInstance(root); }
export function mountAll(scope = document) {
    return [...scope.querySelectorAll('[data-daisy-kit-module="combobox"]')].map(mount);
}
export function unmount(root) { return module.unmount(root); }
