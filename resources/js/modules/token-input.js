function debounce(fn, wait) {
    let timeout = null;

    return function debounced(...args) {
        clearTimeout(timeout);
        timeout = window.setTimeout(() => fn.apply(this, args), wait);
    };
}

export function normalizeText(text) {
    if (!text) return '';

    return String(text)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

export function normalizeTokenValue(value, preset = 'text') {
    const normalized = String(value ?? '').trim();

    if (preset === 'email') {
        return normalized.toLowerCase();
    }

    return normalized;
}

export function isValidTokenValue(value, preset = 'text') {
    const normalized = normalizeTokenValue(value, preset);

    if (!normalized) {
        return false;
    }

    if (preset === 'email') {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalized);
    }

    return true;
}

function escapeForRegex(value) {
    return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

export function splitTokenEntries(raw, separators = [',', ';', '\n']) {
    const text = String(raw ?? '');

    if (!text.trim()) {
        return [];
    }

    const cleanedSeparators = Array.from(new Set((Array.isArray(separators) ? separators : [separators])
        .map((item) => String(item ?? ''))
        .filter(Boolean)));

    if (cleanedSeparators.length === 0) {
        return [text.trim()];
    }

    const pattern = new RegExp(cleanedSeparators.map(escapeForRegex).join('|'), 'g');

    return text
        .split(pattern)
        .map((part) => part.trim())
        .filter(Boolean);
}

function parseArrayOption(value, fallback) {
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

function normalizeSuggestion(item) {
    if (typeof item === 'string') {
        return { value: item, label: item };
    }

    if (!item || typeof item !== 'object') {
        return null;
    }

    return {
        value: String(item.value ?? item.label ?? ''),
        label: String(item.label ?? item.value ?? ''),
        subtitle: item.subtitle ? String(item.subtitle) : '',
        avatar: item.avatar ? String(item.avatar) : '',
        disabled: item.disabled === true,
    };
}

export default function initTokenInput(root, options = {}) {
    const shell = root.querySelector('[data-role="shell"]');
    const input = root.querySelector('[data-role="input"]');
    const tokensWrap = root.querySelector('[data-role="tokens"]');
    const hiddenInputsWrap = root.querySelector('[data-role="hidden-inputs"]');
    const list = root.querySelector('[data-role="list"]');
    const message = root.querySelector('[data-role="message"]');

    if (!shell || !input || !tokensWrap || !hiddenInputsWrap || !list) {
        return;
    }

    const preset = String(options.preset ?? root.dataset.preset ?? 'text');
    const submitName = String(options.submitName ?? root.dataset.submitName ?? '');
    const allowDuplicates = String(options.allowDuplicates ?? root.dataset.allowDuplicates ?? 'false') === 'true';
    const maxItems = Number.parseInt(options.maxItems ?? root.dataset.maxItems ?? '', 10);
    const minChars = Number.parseInt(options.minChars ?? root.dataset.minChars ?? '2', 10) || 2;
    const debounceMs = Number.parseInt(options.debounce ?? root.dataset.debounce ?? '300', 10) || 300;
    const endpoint = String(options.endpoint ?? root.dataset.endpoint ?? '').trim();
    const param = String(options.param ?? root.dataset.param ?? 'q');
    const delimiters = parseArrayOption(options.delimiters ?? root.dataset.delimiters, ['Enter', 'Tab', ',']);
    const pasteSeparators = parseArrayOption(options.pasteSeparators ?? root.dataset.pasteSeparators, [',', ';', '\n']);
    const suggestions = parseArrayOption(options.suggestions ?? root.dataset.suggestions, [])
        .map(normalizeSuggestion)
        .filter(Boolean);
    const noResultsText = String(options.noResultsText ?? root.dataset.noResultsText ?? 'No results found.');
    const invalidText = String(options.invalidText ?? root.dataset.invalidText ?? 'The value is invalid.');
    const duplicateText = String(options.duplicateText ?? root.dataset.duplicateText ?? 'This value is already added.');
    const maxItemsText = String(options.maxItemsText ?? root.dataset.maxItemsText ?? 'You reached the maximum number of items.');
    const tokenClass = String(options.tokenClass ?? root.dataset.tokenClass ?? 'badge badge-soft badge-neutral');
    const tokenRemoveClass = String(options.tokenRemoveClass ?? root.dataset.tokenRemoveClass ?? 'btn btn-ghost btn-xs btn-circle');

    let tokens = Array.from(tokensWrap.querySelectorAll('[data-token-item]')).map((item) => ({
        value: normalizeTokenValue(item.dataset.value ?? item.dataset.label ?? '', preset),
        label: String(item.dataset.label ?? item.dataset.value ?? '').trim(),
    }));
    let activeIndex = -1;
    let currentItems = [];
    let aborter = null;
    let blurTimeout = null;

    function hasReachedLimit() {
        return Number.isFinite(maxItems) && maxItems > 0 && tokens.length >= maxItems;
    }

    function clearError() {
        shell.classList.remove('input-error');
        if (message) {
            message.textContent = '';
            message.classList.add('hidden');
        }
    }

    function setError(text) {
        shell.classList.add('input-error');
        if (message) {
            message.textContent = text;
            message.classList.remove('hidden');
        }
    }

    function setOpen(open) {
        root.classList.toggle('dropdown-open', open);
        list.classList.toggle('hidden', !open);
        input.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function syncHiddenInputs() {
        hiddenInputsWrap.innerHTML = '';

        if (!submitName) {
            return;
        }

        tokens.forEach((token) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = submitName;
            hidden.value = token.value;
            hidden.setAttribute('data-token-hidden', '1');
            hiddenInputsWrap.appendChild(hidden);
        });
    }

    function removeTokenAt(index) {
        if (index < 0 || index >= tokens.length) {
            return;
        }

        tokens.splice(index, 1);
        renderTokens();
        clearError();
        void refreshSuggestions(input.value);
    }

    function createTokenElement(token, index) {
        const badge = document.createElement('span');
        badge.className = tokenClass;
        badge.setAttribute('data-token-item', '');
        badge.setAttribute('data-value', token.value);
        badge.setAttribute('data-label', token.label);

        const label = document.createElement('span');
        label.className = 'truncate';
        label.textContent = token.label;
        badge.appendChild(label);

        const button = document.createElement('button');
        button.type = 'button';
        button.className = tokenRemoveClass;
        button.setAttribute('data-token-remove', '');
        button.setAttribute('aria-label', `Remove ${token.label}`);
        button.innerHTML = '<span aria-hidden="true">&times;</span>';
        button.addEventListener('click', () => removeTokenAt(index));

        badge.appendChild(button);

        return badge;
    }

    function renderTokens() {
        tokensWrap.querySelectorAll('[data-token-item]').forEach((item) => item.remove());

        tokens.forEach((token, index) => {
            tokensWrap.insertBefore(createTokenElement(token, index), input);
        });

        syncHiddenInputs();
    }

    function tokenExists(value) {
        return tokens.some((token) => token.value === value);
    }

    function addToken(entry) {
        const source = typeof entry === 'object' && entry !== null
            ? {
                value: String(entry.value ?? entry.label ?? ''),
                label: String(entry.label ?? entry.value ?? ''),
            }
            : {
                value: String(entry ?? ''),
                label: String(entry ?? ''),
            };

        const value = normalizeTokenValue(source.value, preset);
        const label = String(source.label || source.value).trim() || value;

        if (!isValidTokenValue(value, preset)) {
            setError(invalidText);
            return false;
        }

        if (!allowDuplicates && tokenExists(value)) {
            setError(duplicateText);
            return false;
        }

        if (hasReachedLimit()) {
            setError(maxItemsText);
            return false;
        }

        tokens.push({ value, label: preset === 'email' && source.label === source.value ? value : label });
        renderTokens();
        clearError();
        return true;
    }

    function commitInputValue(raw) {
        const entries = splitTokenEntries(raw, pasteSeparators);

        if (entries.length === 0) {
            input.value = '';
            clearError();
            return true;
        }

        const failures = [];
        let addedAny = false;

        entries.forEach((entry) => {
            if (addToken(entry)) {
                addedAny = true;
            } else {
                failures.push(entry);
            }
        });

        input.value = failures.join(', ');

        if (addedAny && failures.length === 0) {
            clearError();
        }

        return failures.length === 0;
    }

    function normalizeQueryItem(item) {
        const normalized = normalizeSuggestion(item);
        if (!normalized) return null;

        normalized.value = normalizeTokenValue(normalized.value, preset);
        normalized.label = normalized.label.trim() || normalized.value;

        return normalized;
    }

    function renderList(items, emptyMessage = '') {
        list.innerHTML = '';
        currentItems = items;
        activeIndex = items.length > 0 ? 0 : -1;

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
                    if (addToken(item)) {
                        input.value = '';
                        renderList([]);
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

    function filterLocalSuggestions(query) {
        const normalizedQuery = normalizeText(query);

        return suggestions
            .map(normalizeQueryItem)
            .filter(Boolean)
            .filter((item) => !tokenExists(item.value))
            .filter((item) => {
                if (!normalizedQuery) return true;

                return [item.label, item.value, item.subtitle]
                    .filter(Boolean)
                    .some((candidate) => normalizeText(candidate).includes(normalizedQuery));
            });
    }

    async function fetchRemoteSuggestions(query) {
        if (!endpoint) {
            return [];
        }

        if (aborter) {
            aborter.abort();
        }

        aborter = new AbortController();

        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set(param, query);

        const response = await fetch(url.toString(), { signal: aborter.signal });
        const payload = await response.json();

        const items = Array.isArray(payload) ? payload : [];

        return items
            .map(normalizeQueryItem)
            .filter(Boolean)
            .filter((item) => !tokenExists(item.value));
    }

    async function refreshSuggestions(query) {
        const trimmed = String(query ?? '').trim();

        if (suggestions.length > 0) {
            const items = filterLocalSuggestions(trimmed);
            renderList(items, trimmed ? noResultsText : '');
            return;
        }

        if (!endpoint) {
            renderList([]);
            return;
        }

        if (trimmed.length < minChars) {
            renderList([]);
            return;
        }

        try {
            const items = await fetchRemoteSuggestions(trimmed);
            renderList(items, noResultsText);
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }

            renderList([], noResultsText);
        }
    }

    const debouncedRefresh = debounce((value) => {
        void refreshSuggestions(value);
    }, debounceMs);

    root.addEventListener('click', () => {
        input.focus();
    });

    input.addEventListener('focus', () => {
        if (blurTimeout) {
            clearTimeout(blurTimeout);
            blurTimeout = null;
        }

        if (suggestions.length > 0) {
            void refreshSuggestions(input.value);
        }
    });

    input.addEventListener('input', () => {
        clearError();
        debouncedRefresh(input.value);
    });

    input.addEventListener('keydown', (event) => {
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

        if (event.key === 'Backspace' && input.value === '' && tokens.length > 0) {
            event.preventDefault();
            removeTokenAt(tokens.length - 1);
            return;
        }

        if (!delimiters.includes(event.key)) {
            return;
        }

        event.preventDefault();

        if (currentItems.length > 0 && activeIndex >= 0 && ['Enter', 'Tab'].includes(event.key)) {
            const activeItem = currentItems[activeIndex];

            if (activeItem && !activeItem.disabled && addToken(activeItem)) {
                input.value = '';
                renderList([]);
            }

            return;
        }

        commitInputValue(input.value);
        void refreshSuggestions('');
    });

    input.addEventListener('blur', () => {
        blurTimeout = window.setTimeout(() => {
            commitInputValue(input.value);
            setOpen(false);
        }, 120);
    });

    input.addEventListener('paste', (event) => {
        const text = event.clipboardData?.getData('text') ?? '';
        const entries = splitTokenEntries(text, pasteSeparators);

        if (entries.length <= 1) {
            return;
        }

        event.preventDefault();
        commitInputValue(text);
        void refreshSuggestions('');
    });

    renderTokens();
}
