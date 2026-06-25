function debounce(fn, wait) {
    let timeout = null;

    function debounced(...args) {
        clearTimeout(timeout);
        timeout = window.setTimeout(() => fn.apply(this, args), wait);
    }

    debounced.cancel = () => {
        clearTimeout(timeout);
        timeout = null;
    };

    return debounced;
}

function normalizeText(text) {
    if (!text) return '';

    return String(text)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function parseArrayOption(value, fallback = []) {
    if (Array.isArray(value)) {
        return value;
    }

    if (typeof value === 'string' && value.trim() !== '') {
        try {
            const parsed = JSON.parse(value);

            return Array.isArray(parsed) ? parsed : fallback;
        } catch (_) {
            return fallback;
        }
    }

    return fallback;
}

function normalizeOption(item) {
    if (typeof item === 'string') {
        return { value: item, label: item, subtitle: '', avatar: '', disabled: false };
    }

    if (!item || typeof item !== 'object') {
        return null;
    }

    const value = String(item.value ?? item.id ?? item.label ?? '').trim();
    const label = String(item.label ?? item.name ?? item.value ?? item.id ?? value).trim();

    if (!value) {
        return null;
    }

    return {
        value,
        label: label || value,
        subtitle: item.subtitle ? String(item.subtitle) : '',
        avatar: item.avatar ? String(item.avatar) : '',
        disabled: item.disabled === true,
    };
}

export default function initMultiSelect(root, options = {}) {
    if (root.dataset.multiSelectInitialized === 'true') {
        return;
    }

    const shell = root.querySelector('[data-role="shell"]');
    const input = root.querySelector('[data-role="input"]');
    const selectedWrap = root.querySelector('[data-role="selected"]');
    const hiddenInputsWrap = root.querySelector('[data-role="hidden-inputs"]');
    const list = root.querySelector('[data-role="list"]');
    const nativeSelect = root.querySelector('[data-role="native"]');
    const message = root.querySelector('[data-role="message"]');

    if (!shell || !input || !selectedWrap || !hiddenInputsWrap || !list || !nativeSelect) {
        return;
    }

    root.dataset.multiSelectInitialized = 'true';

    const endpoint = String(options.endpoint ?? root.dataset.endpoint ?? '').trim();
    const param = String(options.param ?? root.dataset.param ?? 'q');
    const submitName = String(options.submitName ?? root.dataset.submitName ?? '');
    const parsedDebounceMs = Number.parseInt(options.debounce ?? root.dataset.debounce ?? '500', 10);
    const parsedMinChars = Number.parseInt(options.minChars ?? root.dataset.minChars ?? '3', 10);
    const parsedMaxItems = Number.parseInt(options.maxItems ?? root.dataset.maxItems ?? '', 10);
    const debounceMs = Number.isInteger(parsedDebounceMs) ? Math.max(0, parsedDebounceMs) : 500;
    const minChars = Number.isInteger(parsedMinChars) ? Math.max(0, parsedMinChars) : 3;
    const maxItems = Number.isInteger(parsedMaxItems) && parsedMaxItems > 0 ? parsedMaxItems : null;
    const fetchOnEmpty = String(options.fetchOnEmpty ?? root.dataset.fetchOnEmpty ?? 'true') === 'true';
    const defaultItems = parseArrayOption(options.default ?? root.dataset.default, []).map(normalizeOption).filter(Boolean);
    const noResultsText = String(options.noResultsText ?? root.dataset.noResultsText ?? 'No results found.');
    const loadingText = String(options.loadingText ?? root.dataset.loadingText ?? 'Loading...');
    const errorText = String(options.errorText ?? root.dataset.errorText ?? 'Unable to load results.');
    const selectedText = String(options.selectedText ?? root.dataset.selectedText ?? 'selected');
    const placeholder = String(options.placeholder ?? root.dataset.placeholder ?? input.getAttribute('placeholder') ?? '');
    const tokenClass = String(options.tokenClass ?? root.dataset.tokenClass ?? 'badge badge-soft badge-neutral');
    const tokenRemoveClass = String(options.tokenRemoveClass ?? root.dataset.tokenRemoveClass ?? 'btn btn-ghost btn-xs btn-circle');
    const disabled = String(options.disabled ?? root.dataset.disabled ?? 'false') === 'true';
    const readonly = String(options.readonly ?? root.dataset.readonly ?? 'false') === 'true';
    const inert = disabled || readonly;

    let selectedItems = Array.from(selectedWrap.querySelectorAll('[data-multi-select-item]')).map((item) => ({
        value: String(item.dataset.value ?? ''),
        label: String(item.dataset.label ?? item.dataset.value ?? ''),
    })).filter((item) => item.value !== '');
    let currentItems = [];
    let activeIndex = -1;
    let aborter = null;

    function localOptions() {
        return Array.from(nativeSelect.options)
            .map((option) => normalizeOption({
                value: option.value,
                label: option.textContent?.trim() ?? option.value,
                subtitle: option.dataset.subtitle ?? '',
                avatar: option.dataset.avatar ?? '',
                disabled: option.disabled,
            }))
            .filter(Boolean);
    }

    function isSelected(value) {
        return selectedItems.some((item) => item.value === value);
    }

    function hasReachedLimit() {
        return maxItems !== null && selectedItems.length >= maxItems;
    }

    function setOpen(open) {
        root.classList.toggle('dropdown-open', open);
        list.classList.toggle('hidden', !open);
        input.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function showMessage(text, isError = false) {
        if (!message) return;

        message.textContent = text;
        message.classList.toggle('hidden', text === '');
        shell.classList.toggle('select-error', isError);
    }

    function syncNativeSelect() {
        Array.from(nativeSelect.options).forEach((option) => {
            option.selected = isSelected(option.value);
        });

        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function syncHiddenInputs() {
        hiddenInputsWrap.replaceChildren();

        if (!submitName) {
            return;
        }

        selectedItems.forEach((item) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = submitName;
            hidden.value = item.value;
            hidden.setAttribute('data-multi-select-hidden', '1');
            hiddenInputsWrap.appendChild(hidden);
        });
    }

    function createSelectedElement(item, index) {
        const badge = document.createElement('span');
        badge.className = tokenClass;
        badge.setAttribute('data-multi-select-item', '');
        badge.setAttribute('data-value', item.value);
        badge.setAttribute('data-label', item.label);

        const label = document.createElement('span');
        label.className = 'truncate';
        label.textContent = item.label;
        badge.appendChild(label);

        if (!readonly) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = tokenRemoveClass;
            button.setAttribute('data-multi-select-remove', '');
            button.setAttribute('aria-label', `Remove ${item.label}`);
            const removeIcon = document.createElement('span');
            removeIcon.setAttribute('aria-hidden', 'true');
            removeIcon.textContent = '\u00d7';
            button.appendChild(removeIcon);
            button.disabled = disabled;
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                removeSelectedAt(index);
            });

            badge.appendChild(button);
        }

        return badge;
    }

    function renderSelected() {
        selectedWrap.querySelectorAll('[data-multi-select-item]').forEach((item) => item.remove());

        selectedItems.forEach((item, index) => {
            selectedWrap.insertBefore(createSelectedElement(item, index), input);
        });

        root.setAttribute('data-selected-count', String(selectedItems.length));
        input.setAttribute('aria-label', selectedItems.length > 0 ? `${selectedItems.length} ${selectedText}` : '');
        input.setAttribute('placeholder', selectedItems.length > 0 ? '' : placeholder);
        syncNativeSelect();
        syncHiddenInputs();
    }

    function ensureNativeOption(item) {
        if (Array.from(nativeSelect.options).some((option) => option.value === item.value)) {
            return;
        }

        const option = document.createElement('option');
        option.value = item.value;
        option.textContent = item.label;
        if (item.subtitle) option.dataset.subtitle = item.subtitle;
        if (item.avatar) option.dataset.avatar = item.avatar;
        nativeSelect.appendChild(option);
    }

    function addSelected(item) {
        if (inert || item.disabled || isSelected(item.value) || hasReachedLimit()) {
            return false;
        }

        ensureNativeOption(item);
        selectedItems.push({ value: item.value, label: item.label });
        renderSelected();
        showMessage('');

        return true;
    }

    function focusInput() {
        if (inert) {
            return;
        }

        input.focus({ preventScroll: true });
    }

    function removeSelectedAt(index, { reopen = false } = {}) {
        selectedItems.splice(index, 1);
        renderSelected();

        if (reopen) {
            void refreshSuggestions(input.value);
            return;
        }

        setOpen(false);
    }

    function renderList(items, emptyMessage = '') {
        list.replaceChildren();
        currentItems = items;
        activeIndex = items.findIndex((item) => !item.disabled);

        if (items.length === 0) {
            if (!emptyMessage) {
                setOpen(false);
                return;
            }

            const li = document.createElement('li');
            const span = document.createElement('span');
            span.className = 'text-sm text-base-content/70';
            span.textContent = emptyMessage;
            li.appendChild(span);
            list.appendChild(li);
            setOpen(true);
            return;
        }

        items.forEach((item, index) => {
            const li = document.createElement('li');
            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('role', 'option');
            button.setAttribute('aria-selected', isSelected(item.value) ? 'true' : 'false');
            button.className = index === activeIndex ? 'active' : '';

            if (item.avatar) {
                const avatar = document.createElement('div');
                avatar.className = 'avatar';
                const inner = document.createElement('div');
                inner.className = 'w-6 rounded-full';
                const img = document.createElement('img');
                img.src = item.avatar;
                img.alt = '';
                inner.appendChild(img);
                avatar.appendChild(inner);
                button.appendChild(avatar);
            }

            const textWrap = document.createElement('div');
            textWrap.className = 'flex flex-col text-left';
            const title = document.createElement('span');
            title.textContent = item.label;
            textWrap.appendChild(title);

            if (item.subtitle) {
                const subtitle = document.createElement('span');
                subtitle.className = 'text-xs opacity-70';
                subtitle.textContent = item.subtitle;
                textWrap.appendChild(subtitle);
            }

            button.appendChild(textWrap);

            if (item.disabled) {
                button.classList.add('disabled');
                button.setAttribute('aria-disabled', 'true');
            } else {
                button.addEventListener('mousedown', (event) => event.preventDefault());
                button.addEventListener('click', () => {
                    if (addSelected(item)) {
                        input.value = '';
                        focusInput();
                        void refreshSuggestions('');
                    }
                });
            }

            li.appendChild(button);
            list.appendChild(li);
        });

        setOpen(true);
    }

    function moveActiveIndex(direction) {
        if (currentItems.length === 0) {
            return;
        }

        let nextIndex = activeIndex;

        do {
            nextIndex = (nextIndex + direction + currentItems.length) % currentItems.length;
        } while (currentItems[nextIndex]?.disabled && nextIndex !== activeIndex);

        activeIndex = nextIndex;
        list.querySelectorAll('button[role="option"]').forEach((button, index) => {
            button.classList.toggle('active', index === activeIndex);
        });
    }

    function filterLocal(query) {
        const normalizedQuery = normalizeText(query);

        return localOptions()
            .filter((item) => !isSelected(item.value))
            .filter((item) => {
                if (!normalizedQuery) return true;

                return [item.label, item.value, item.subtitle]
                    .filter(Boolean)
                    .some((candidate) => normalizeText(candidate).includes(normalizedQuery));
            });
    }

    function normalizePayload(payload) {
        if (Array.isArray(payload)) {
            return payload;
        }

        if (payload && typeof payload === 'object' && Array.isArray(payload.items)) {
            return payload.items;
        }

        return [];
    }

    async function fetchRemote(query) {
        if (aborter) {
            aborter.abort();
        }

        aborter = new AbortController();
        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set(param, query);

        const response = await fetch(url.toString(), {
            signal: aborter.signal,
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        const payload = await response.json();

        return normalizePayload(payload)
            .map(normalizeOption)
            .filter(Boolean)
            .filter((item) => !isSelected(item.value));
    }

    async function refreshSuggestions(query) {
        const trimmed = String(query ?? '').trim();

        if (inert || hasReachedLimit()) {
            renderList([]);
            return;
        }

        if (!endpoint) {
            const items = filterLocal(trimmed);
            renderList(items, trimmed && items.length === 0 ? noResultsText : '');
            return;
        }

        if (!trimmed) {
            if (defaultItems.length > 0) {
                renderList(defaultItems.filter((item) => !isSelected(item.value)));
                return;
            }

            if (!fetchOnEmpty) {
                renderList([]);
                return;
            }
        }

        if (trimmed.length < minChars && trimmed.length > 0) {
            renderList([]);
            return;
        }

        renderList([], loadingText);

        try {
            const items = await fetchRemote(trimmed);

            if (input.value.trim() !== trimmed) {
                return;
            }

            renderList(items, noResultsText);
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }

            renderList([], errorText);
        }
    }

    const debouncedRefresh = debounce((value) => {
        void refreshSuggestions(value);
    }, debounceMs);

    root.addEventListener('click', (event) => {
        if (inert || event.target.closest('[data-role="list"], [data-multi-select-remove]')) {
            return;
        }

        if (!event.target.closest('[data-role="shell"], [data-role="selected"], [data-role="input"]')) {
            return;
        }

        const wasFocused = document.activeElement === input;
        focusInput();

        if (wasFocused) {
            void refreshSuggestions(input.value);
        }
    });

    input.addEventListener('focus', () => {
        if (inert) {
            return;
        }

        void refreshSuggestions(input.value);
    });

    input.addEventListener('input', () => {
        if (inert) {
            return;
        }

        debouncedRefresh(input.value);
    });

    input.addEventListener('keydown', (event) => {
        if (inert) {
            return;
        }

        if ((event.key === 'ArrowDown' || event.key === 'ArrowUp') && currentItems.length > 0) {
            event.preventDefault();
            moveActiveIndex(event.key === 'ArrowDown' ? 1 : -1);
            setOpen(true);
            return;
        }

        if (event.key === 'Escape') {
            setOpen(false);
            return;
        }

        if (event.key === 'Backspace' && input.value === '' && selectedItems.length > 0) {
            event.preventDefault();
            removeSelectedAt(selectedItems.length - 1, { reopen: true });
            return;
        }

        if (event.key !== 'Enter') {
            return;
        }

        if (currentItems.length > 0 && activeIndex >= 0) {
            event.preventDefault();
            const activeItem = currentItems[activeIndex];

            if (activeItem && addSelected(activeItem)) {
                input.value = '';
                debouncedRefresh.cancel();
                void refreshSuggestions('');
            }
        }
    });

    document.addEventListener('click', (event) => {
        const path = typeof event.composedPath === 'function' ? event.composedPath() : [];
        const clickedInside = path.length > 0 ? path.includes(root) : root.contains(event.target);

        if (!clickedInside) {
            setOpen(false);
        }
    });

    renderSelected();
}
