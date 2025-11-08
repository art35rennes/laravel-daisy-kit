/**
 * Daisy Kit - Select (Unified: Local Search + Remote Autocomplete)
 *
 * Fonctionnement unifié:
 * - Toujours un seul champ de saisie (le <select> original est masqué et sert à la soumission).
 * - Si "endpoint" est défini → suggestions distantes (format: [{ value, label, disabled? }]).
 *   - Optionnel: "default" (array) pour suggestions par défaut à l'ouverture/sans saisie.
 * - Sinon → suggestions locales construites depuis les <option> du <select>.
 *
 * Accessibilité & Qualité :
 * - Debounce sur la saisie (options.debounce, default 300ms)
 * - minChars avant requête distante (options.minChars, default 2)
 * - Annulation des requêtes en cours (AbortController)
 * - Navigation clavier ↑/↓/Enter/Escape
 * - Ouverture de la liste uniquement si propositions; sinon affiche "Aucun résultat" uniquement si saisie non vide
 */

function debounce(fn, wait) {
    let timeout = null;
    return function debounced(...args) {
        window.clearTimeout(timeout);
        timeout = window.setTimeout(() => fn.apply(this, args), wait);
    };
}

function normalizeText(text) {
    if (!text) return '';
    return String(text)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function matches(text, query) {
    if (!query) return true;
    return normalizeText(text).includes(normalizeText(query));
}

export default function initSelect(rootEl, options = {}) {
    // Déterminer l'élément <select> (rootEl peut être un wrapper SSR ou le select)
    const selectEl = rootEl.tagName === 'SELECT' ? rootEl : rootEl.querySelector('select');
    if (!selectEl) return;

    const endpoint = String(options.endpoint || '').trim();
    const defaultData = Array.isArray(options.default) ? options.default : [];
    const paramName = String(options.param || 'q');
    const debounceMs = Number.parseInt(options.debounce ?? 300, 10) || 300;
    const minChars = Number.parseInt(options.minChars ?? 2, 10) || 2;
    const fetchOnEmpty = String(options.fetchOnEmpty ?? (selectEl.dataset.fetchOnEmpty ?? rootEl.dataset?.fetchOnEmpty) ?? 'true') === 'true';
    const userPlaceholder = (rootEl.dataset?.placeholder || selectEl.dataset?.placeholder) || selectEl.getAttribute('placeholder') || '';

    // Cacher le select original (il sert de champ de formulaire)
    selectEl.setAttribute('hidden', 'hidden');

    // Valeur initiale si déjà sélectionnée
    const initialOption = selectEl.querySelector('option:checked');
    const initialLabel = initialOption?.textContent || '';
    const hasRealInitial = initialOption && String(initialOption.value || '') !== '';

    // UI: input + dropdown
    let wrapper = null;
    let input = null;
    let list = null;
    let createdWrapper = false;

    // Si SSR a déjà rendu un wrapper
    if (rootEl.tagName !== 'SELECT') {
        wrapper = rootEl;
        input = wrapper.querySelector('[data-role="input"]') || wrapper.querySelector('input[data-select-input]');
        list = wrapper.querySelector('[data-role="list"]') || wrapper.querySelector('ul[role="listbox"]');
        // Normaliser classes et attributs
        wrapper.classList.add('dropdown', 'w-full');
        if (!input) {
            const inputWrap = document.createElement('label');
            inputWrap.className = 'input input-bordered flex items-center gap-2 w-full';
            input = document.createElement('input');
            input.type = 'text';
            input.className = 'grow';
            inputWrap.appendChild(input);
            wrapper.insertBefore(inputWrap, selectEl);
        }
        if (!list) {
            list = document.createElement('ul');
            wrapper.appendChild(list);
        }
        list.classList.add('dropdown-content', 'z-10', 'menu', 'bg-base-100', 'rounded-box', 'w-full', 'shadow', 'hidden');
        list.setAttribute('role', 'listbox');
    } else {
        // Créer wrapper côté client si pas SSR
        wrapper = document.createElement('div');
        wrapper.className = 'dropdown w-full';
        const inputWrap = document.createElement('label');
        inputWrap.className = 'input input-bordered flex items-center gap-2 w-full';
        input = document.createElement('input');
        input.type = 'text';
        input.className = 'grow';
        inputWrap.appendChild(input);
        list = document.createElement('ul');
        list.className = 'dropdown-content z-10 menu bg-base-100 rounded-box w-full shadow hidden';
        list.setAttribute('role', 'listbox');
        selectEl.parentNode?.insertBefore(wrapper, selectEl.nextSibling);
        wrapper.appendChild(inputWrap);
        wrapper.appendChild(list);
        createdWrapper = true;
    }

    // Placeholder: priorité au data-placeholder/placeholder; sinon, si option sélectionnée vide => utiliser son label
    const placeholderText = userPlaceholder || (!hasRealInitial && initialLabel ? initialLabel : 'Tapez pour rechercher...');
    input.placeholder = placeholderText;
    input.setAttribute('autocomplete', 'off');
    input.setAttribute('aria-expanded', 'false');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('data-select-input', '1');
    // Back-compat ancres pour tests existants
    input.setAttribute('data-select-autocomplete-input', '1');
    input.setAttribute('data-select-search-input', '1');
    // Ne préremplit pas si la valeur sélectionnée est vide (placeholder)
    input.value = hasRealInitial ? initialLabel : '';

    // Local data snapshot
    const localData = Array.from(selectEl.querySelectorAll('option')).map((opt) => ({
        value: String(opt.value ?? ''),
        label: String(opt.textContent ?? ''),
        disabled: opt.disabled === true,
    }));

    // State & helpers
    let aborter = null;
    let lastQuery = '';
    let activeIndex = -1;

    function setOpen(open) {
        wrapper.classList.toggle('dropdown-open', open);
        input.setAttribute('aria-expanded', open ? 'true' : 'false');
        list.classList.toggle('hidden', !open);
    }

    function clearList(message = '', openIfMessage = false) {
        list.innerHTML = '';
        activeIndex = -1;
        if (message) {
            const li = document.createElement('li');
            const msg = document.createElement('span');
            msg.className = 'disabled';
            msg.textContent = message;
            li.appendChild(msg);
            list.appendChild(li);
            if (openIfMessage) setOpen(true);
        } else {
            setOpen(false);
        }
    }

    function selectValue(value, label) {
        const opt = Array.from(selectEl.options).find((o) => o.value === value);
        if (opt) {
            opt.selected = true;
        } else {
            const newOpt = document.createElement('option');
            newOpt.value = value;
            newOpt.textContent = label;
            selectEl.appendChild(newOpt);
            newOpt.selected = true;
        }
        input.value = label;
        setOpen(false);
    }

    function renderList(items) {
        list.innerHTML = '';
        activeIndex = -1;
        items.forEach((it, idx) => {
            const value = String(it?.value ?? '');
            const label = String(it?.label ?? value);
            const disabled = it?.disabled === true;
            const subtitle = it?.subtitle ? String(it.subtitle) : '';
            const avatar = it?.avatar ? String(it.avatar) : '';

            const li = document.createElement('li');
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('role', 'option');

            // Contenu bouton: avatar (optionnel) + label + sous-titre
            if (avatar) {
                const avatarWrap = document.createElement('div');
                avatarWrap.className = 'avatar';
                const inner = document.createElement('div');
                inner.className = 'w-6 rounded-full';
                const img = document.createElement('img');
                img.alt = '';
                img.src = avatar;
                inner.appendChild(img);
                avatarWrap.appendChild(inner);
                btn.appendChild(avatarWrap);
            }
            const textWrap = document.createElement('div');
            textWrap.className = 'flex flex-col text-left';
            const titleEl = document.createElement('span');
            titleEl.textContent = label;
            textWrap.appendChild(titleEl);
            if (subtitle) {
                const subEl = document.createElement('span');
                subEl.className = 'text-xs opacity-70';
                subEl.textContent = subtitle;
                textWrap.appendChild(subEl);
            }
            btn.appendChild(textWrap);

            if (disabled) {
                btn.classList.add('disabled');
                btn.setAttribute('aria-disabled', 'true');
            }
            btn.addEventListener('click', () => {
                if (!disabled) selectValue(value, label);
            });
            li.appendChild(btn);
            list.appendChild(li);
            if (idx === activeIndex) btn.classList.add('active');
        });
        setOpen(items.length > 0);
    }

    function renderGroups(groups) {
        list.innerHTML = '';
        activeIndex = -1;
        groups.forEach((group) => {
            const title = String(group?.title ?? '');
            const items = Array.isArray(group?.items) ? group.items : [];
            if (title) {
                const titleLi = document.createElement('li');
                const titleDiv = document.createElement('div');
                titleDiv.className = 'menu-title';
                titleDiv.textContent = title;
                titleLi.appendChild(titleDiv);
                list.appendChild(titleLi);
            }
            items.forEach((it, idx) => {
                const value = String(it?.value ?? '');
                const label = String(it?.label ?? value);
                const disabled = it?.disabled === true;
                const subtitle = it?.subtitle ? String(it.subtitle) : '';
                const avatar = it?.avatar ? String(it.avatar) : '';
                const li = document.createElement('li');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.setAttribute('role', 'option');
                if (avatar) {
                    const avatarWrap = document.createElement('div');
                    avatarWrap.className = 'avatar';
                    const inner = document.createElement('div');
                    inner.className = 'w-6 rounded-full';
                    const img = document.createElement('img');
                    img.alt = '';
                    img.src = avatar;
                    inner.appendChild(img);
                    avatarWrap.appendChild(inner);
                    btn.appendChild(avatarWrap);
                }
                const textWrap = document.createElement('div');
                textWrap.className = 'flex flex-col text-left';
                const titleEl = document.createElement('span');
                titleEl.textContent = label;
                textWrap.appendChild(titleEl);
                if (subtitle) {
                    const subEl = document.createElement('span');
                    subEl.className = 'text-xs opacity-70';
                    subEl.textContent = subtitle;
                    textWrap.appendChild(subEl);
                }
                btn.appendChild(textWrap);
                if (disabled) {
                    btn.classList.add('disabled');
                    btn.setAttribute('aria-disabled', 'true');
                }
                btn.addEventListener('click', () => {
                    if (!disabled) selectValue(value, label);
                });
                li.appendChild(btn);
                list.appendChild(li);
                if (idx === activeIndex) btn.classList.add('active');
            });
        });
        const hasAny = list.querySelector('button[role="option"]') !== null;
        setOpen(hasAny);
    }

    function filterLocal(q) {
        const term = String(q || '').trim();
        const result = term ? localData.filter((it) => matches(it.label, term)) : localData;
        if (result.length > 0) {
            renderList(result);
        } else {
            // Afficher "Aucun résultat" si saisie non vide
            clearList(term ? 'Aucun résultat' : '', !!term);
        }
    }

    function showDefaultIfAny() {
        if (endpoint && Array.isArray(defaultData) && defaultData.length > 0) {
            renderList(defaultData);
        }
    }

    function buildUrl(q) {
        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set(paramName, q);
        return url.toString();
    }

    async function fetchRemote(q) {
        if (aborter) {
            try { aborter.abort(); } catch (_) {}
        }
        aborter = new AbortController();
        const signal = aborter.signal;
        const url = buildUrl(q);
        try {
            const res = await fetch(url, { method: 'GET', signal, headers: { 'Accept': 'application/json' } });
            if (!res.ok) {
                clearList('Erreur serveur', true);
                return;
            }
            const data = await res.json();
            let items = [];
            let groups = null;
            let meta = null;

            if (Array.isArray(data)) {
                items = data;
            } else if (data && typeof data === 'object') {
                if (Array.isArray(data.items)) items = data.items;
                if (Array.isArray(data.groups)) groups = data.groups;
                if (data.meta && typeof data.meta === 'object') meta = data.meta;
            }

            if (groups && groups.length > 0) {
                renderGroups(groups);
            } else if (items.length > 0) {
                renderList(items);
            } else {
                clearList('Aucun résultat', true);
            }

            // Indicateur "x résultats supplémentaires"
            const more = meta && Number.isFinite(meta.more) ? Number(meta.more) : 0;
            if (more > 0) {
                const li = document.createElement('li');
                const badge = document.createElement('div');
                badge.className = 'badge badge-ghost';
                badge.textContent = `${more} résultat(s) supplémentaires`;
                li.appendChild(badge);
                list.appendChild(li);
                setOpen(true);
            }
        } catch (err) {
            if (err?.name === 'AbortError') return;
            clearList('Erreur réseau', true);
        }
    }

    const run = debounce((q) => {
        const term = String(q || '');
        if (endpoint) {
            if (!term) {
                // Champ vide → montrer defaultData si fourni, sinon fetch('') si fetchOnEmpty, sinon rien
                if (defaultData.length > 0) {
                    renderList(defaultData);
                } else if (fetchOnEmpty) {
                    fetchRemote('');
                } else {
                    clearList('');
                }
                return;
            }
            if (term.length < minChars) {
                // ne pas ouvrir si pas assez de caractères, mais afficher aide si déjà saisi
                clearList('Saisissez davantage de caractères', true);
                return;
            }
            if (term === lastQuery) return;
            lastQuery = term;
            fetchRemote(term);
        } else {
            filterLocal(term);
        }
    }, debounceMs);

    input.addEventListener('input', (e) => {
        run(e.target.value || '');
    });

    input.addEventListener('focus', () => {
        if (!endpoint) {
            // local: ouvrir avec toutes les options (ou filtre courant) au focus
            filterLocal(input.value || '');
        } else {
            // remote: afficher defaultData si vide et dispo
            if (!(input.value || '').trim()) {
                if (defaultData.length > 0) {
                    renderList(defaultData);
                } else if (fetchOnEmpty) {
                    fetchRemote('');
                } else {
                    setOpen(false);
                }
            }
        }
    });

    input.addEventListener('keydown', (e) => {
        const items = Array.from(list.querySelectorAll('button[role="option"]:not(.disabled)'));
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (items.length) {
                activeIndex = (activeIndex + 1) % items.length;
                items[activeIndex].focus();
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (items.length) {
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                items[activeIndex].focus();
            }
        } else if (e.key === 'Enter') {
            if (document.activeElement && document.activeElement.getAttribute('role') === 'option') {
                e.preventDefault();
                document.activeElement.click();
            }
        } else if (e.key === 'Escape') {
            setOpen(false);
            input.blur();
        }
    });

    document.addEventListener('click', (ev) => {
        if (!wrapper.contains(ev.target)) {
            setOpen(false);
        }
    });
}


