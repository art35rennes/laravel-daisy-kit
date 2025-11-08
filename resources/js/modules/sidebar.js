/**
 * Daisy Kit - Sidebar Search
 *
 * Ce module gère la recherche/filtre dans la sidebar.
 * Il filtre les items du menu en fonction du terme de recherche saisi.
 *
 * Structure HTML requise :
 * <aside data-module="sidebar" data-searchable="true">
 *   <input data-sidebar-search />
 *   <ul data-sidebar-menu>
 *     <li class="menu-title">Section</li>
 *     <li><a>Item</a></li>
 *     <li>
 *       <details>
 *         <summary>Parent</summary>
 *         <ul>
 *           <li><a>Child</a></li>
 *         </ul>
 *       </details>
 *     </li>
 *   </ul>
 * </aside>
 */

/**
 * Debounce utilitaire pour limiter les appels de recherche
 */
function debounce(fn, wait) {
    let timeout = null;
    return function debounced(...args) {
        window.clearTimeout(timeout);
        timeout = window.setTimeout(() => fn.apply(this, args), wait);
    };
}

/**
 * Normalise le texte pour la recherche (minuscules, suppression accents)
 */
function normalizeText(text) {
    if (!text) return '';
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

/**
 * Vérifie si un texte contient le terme de recherche
 */
function matchesSearch(text, searchTerm) {
    if (!searchTerm) return true;
    const normalizedText = normalizeText(text);
    const normalizedSearch = normalizeText(searchTerm);
    return normalizedText.includes(normalizedSearch);
}

/**
 * Récupère le texte d'un élément de menu (label)
 */
function getItemText(element) {
    const link = element.querySelector('a');
    if (!link) {
        // Si pas de lien, peut-être un span ou texte direct
        const span = element.querySelector('span');
        if (span) return span.textContent || '';
        return element.textContent || '';
    }
    
    // Récupère le texte du label (span.sidebar-label ou texte direct)
    const label = link.querySelector('.sidebar-label');
    if (label) {
        return label.textContent || '';
    }
    return link.textContent || '';
}

/**
 * Récupère le texte d'un summary (pour les details)
 */
function getSummaryText(element) {
    const summary = element.querySelector('summary');
    if (!summary) return '';
    
    const label = summary.querySelector('.sidebar-label');
    if (label) {
        return label.textContent || '';
    }
    return summary.textContent || '';
}

/**
 * Filtre récursivement un élément de menu et ses enfants
 * Retourne true si l'élément ou un de ses descendants matche
 */
function filterMenuItem(li, searchTerm, hasSearch) {
    if (!hasSearch) {
        // Pas de recherche : tout est visible
        li.classList.remove('hidden');
        li.removeAttribute('data-search-hidden');
        // Récursivement pour les enfants
        const details = li.querySelector('details');
        if (details) {
            const childList = details.querySelector('ul');
            if (childList) {
                Array.from(childList.querySelectorAll(':scope > li')).forEach((child) => {
                    filterMenuItem(child, searchTerm, hasSearch);
                });
            }
        }
        return true;
    }

    // Vérifie si c'est un item avec children (details)
    const details = li.querySelector('details');
    if (details) {
        const summaryText = getSummaryText(li);
        const summaryMatch = matchesSearch(summaryText, searchTerm);

        // Filtre récursivement les enfants
        const childList = details.querySelector('ul');
        let hasMatchingChild = false;
        
        if (childList) {
            Array.from(childList.querySelectorAll(':scope > li')).forEach((child) => {
                const childMatches = filterMenuItem(child, searchTerm, hasSearch);
                if (childMatches) {
                    hasMatchingChild = true;
                }
            });
        }

        // Affiche le parent si lui-même ou un enfant matche
        if (summaryMatch || hasMatchingChild) {
            li.classList.remove('hidden');
            li.removeAttribute('data-search-hidden');
            // Ouvre le details pour montrer les enfants qui matchent
            if (hasMatchingChild) {
                details.open = true;
            }
            return true;
        } else {
            li.classList.add('hidden');
            li.setAttribute('data-search-hidden', 'true');
            return false;
        }
    } else {
        // Item simple (lien direct ou span)
        const itemText = getItemText(li);
        const match = matchesSearch(itemText, searchTerm);

        if (match) {
            li.classList.remove('hidden');
            li.removeAttribute('data-search-hidden');
            return true;
        } else {
            li.classList.add('hidden');
            li.setAttribute('data-search-hidden', 'true');
            return false;
        }
    }
}

/**
 * Filtre les items du menu selon le terme de recherche
 */
function filterMenu(root, searchTerm) {
    const menuContainer = root.querySelector('[data-sidebar-menu]');
    if (!menuContainer) return;

    const term = searchTerm.trim();
    const hasSearch = term.length > 0;

    // Si le conteneur est un ul, on travaille directement dessus
    // Sinon, on cherche tous les ul à l'intérieur
    const menu = menuContainer.tagName === 'UL' ? menuContainer : menuContainer;
    
    // Récupère tous les li (directs ou dans les ul enfants)
    const allLis = menu.tagName === 'UL' 
        ? Array.from(menu.querySelectorAll(':scope > li'))
        : Array.from(menu.querySelectorAll('ul > li'));

    // Si pas de recherche, affiche tout
    if (!hasSearch) {
        allLis.forEach((li) => {
            filterMenuItem(li, term, hasSearch);
        });
        // Affiche aussi les sections (menu-title)
        menuContainer.querySelectorAll('.menu-title').forEach((title) => {
            title.classList.remove('hidden');
            title.removeAttribute('data-search-hidden');
        });
        return;
    }

    // Parcourt tous les éléments du menu dans l'ordre DOM
    const allElements = Array.from(menu.children);
    const sections = new Map(); // Pour tracker les sections et leurs items visibles

    let currentSection = null;

    allElements.forEach((element) => {
        // Si c'est un menu-title, on le track comme section courante
        if (element.classList.contains('menu-title')) {
            currentSection = element;
            if (!sections.has(currentSection)) {
                sections.set(currentSection, { visible: false, title: currentSection });
            }
            return;
        }

        // Si c'est un ul, on parcourt ses enfants li
        if (element.tagName === 'UL') {
            Array.from(element.querySelectorAll(':scope > li')).forEach((li) => {
                // Filtre récursivement l'item et ses enfants
                const isVisible = filterMenuItem(li, term, hasSearch);

                // Si l'item est visible et qu'on a une section courante, on marque la section comme visible
                if (isVisible && currentSection) {
                    const section = sections.get(currentSection);
                    if (section) {
                        section.visible = true;
                    }
                }
            });
        } else if (element.tagName === 'LI') {
            // Item direct (si le menu est un ul)
            const li = element;
            // Filtre récursivement l'item et ses enfants
            const isVisible = filterMenuItem(li, term, hasSearch);

            // Si l'item est visible et qu'on a une section courante, on marque la section comme visible
            if (isVisible && currentSection) {
                const section = sections.get(currentSection);
                if (section) {
                    section.visible = true;
                }
            }
        }
    });

    // Si pas de sections trackées, on filtre directement tous les li
    if (sections.size === 0) {
        allLis.forEach((li) => {
            filterMenuItem(li, term, hasSearch);
        });
    } else {
        // Masque les sections qui n'ont aucun item visible
        sections.forEach((section) => {
            if (section.visible) {
                section.title.classList.remove('hidden');
                section.title.removeAttribute('data-search-hidden');
            } else {
                section.title.classList.add('hidden');
                section.title.setAttribute('data-search-hidden', 'true');
            }
        });
    }
}

/**
 * Initialise le module de recherche pour la sidebar
 */
function initSidebar(root, options = {}) {
    const searchInput = root.querySelector('[data-sidebar-search]');
    if (!searchInput) return;

    const searchable = root.dataset.searchable === 'true' || options.searchable === true;
    if (!searchable) return;

    // Debounce pour limiter les recherches pendant la saisie
    const debounceMs = parseInt(root.dataset.searchDebounce || '300', 10) || 300;
    const debouncedFilter = debounce((term) => {
        filterMenu(root, term);
    }, debounceMs);

    // Écoute les changements dans le champ de recherche
    searchInput.addEventListener('input', (e) => {
        const term = e.target.value;
        debouncedFilter(term);
    });

    // Réinitialise le filtre si le champ est vidé
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterMenu(root, '');
            searchInput.blur();
        }
    });
}

// Export par défaut pour le système data-module
export default initSidebar;

// Export nommé pour compatibilité
export { initSidebar };

