/**
 * Daisy Kit - Menu Filter
 *
 * Module générique pour filtrer les items d'un menu.
 * Supporte les menus avec sous-menus collapsibles (<details>) et filtrage récursif.
 *
 * Structure HTML requise :
 * <div data-module="menu-filter">
 *   <input data-menu-filter-input placeholder="Rechercher..." />
 *   <ul data-menu-filter-target>
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
 * </div>
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
    
    // Récupère le texte du label (peut avoir une classe spécifique ou texte direct)
    const label = link.querySelector('.sidebar-label, .menu-label');
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
    
    const label = summary.querySelector('.sidebar-label, .menu-label');
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
    // Chercher le target dans le root ou ses enfants
    let menuTarget = root.querySelector('[data-menu-filter-target]');
    
    // Si pas trouvé, chercher dans le document (fallback)
    if (!menuTarget) {
        menuTarget = document.querySelector('[data-menu-filter-target]');
    }
    
    if (!menuTarget) {
        console.warn('[menu-filter] Target not found in filterMenu', root);
        return;
    }

    const term = searchTerm.trim();
    const hasSearch = term.length > 0;

    // Le target peut être un ul directement ou un conteneur
    const menu = menuTarget.tagName === 'UL' ? menuTarget : menuTarget;
    
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
        menu.querySelectorAll('.menu-title').forEach((title) => {
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
 * Initialise le module de filtrage pour un menu
 */
function initMenuFilter(root, options = {}) {
    // Le root peut être le conteneur avec data-module ou un parent
    // Chercher l'input dans le root ou ses enfants
    let searchInput = root.querySelector('[data-menu-filter-input]');
    
    // Si pas trouvé, chercher dans le parent (cas de la sidebar où l'input est dans un div séparé)
    if (!searchInput) {
        const moduleContainer = root.closest('[data-module="menu-filter"]');
        if (moduleContainer) {
            searchInput = moduleContainer.querySelector('[data-menu-filter-input]');
        }
    }
    
    if (!searchInput) return;
    
    // Trouver le conteneur parent pour chercher le target
    // Pour sidebar-navigation, le target est dans le même parent (nav) que le module
    // Le root est le div avec data-module, donc on cherche dans son parent
    let container = root.parentElement;
    
    // Vérifier si le target est dans le parent direct
    if (container && container.querySelector('[data-menu-filter-target]')) {
        // Target trouvé dans le parent direct
    } else {
        // Chercher dans les parents suivants (nav, aside, etc.)
        container = root.closest('nav, aside, div, section') || root.parentElement;
        
        // Si toujours pas trouvé, chercher dans le document entier (fallback)
        if (!container || !container.querySelector('[data-menu-filter-target]')) {
            container = document;
        }
    }
    
    // Vérifier que le target existe
    const target = container.querySelector('[data-menu-filter-target]');
    if (!target) {
        console.warn('[menu-filter] Target not found for menu filter', root, container);
        return;
    }

    // Debounce pour limiter les recherches pendant la saisie
    const debounceMs = parseInt(root.dataset.filterDebounce || options.debounce || '300', 10) || 300;
    const debouncedFilter = debounce((term) => {
        filterMenu(container, term);
    }, debounceMs);

    // Écoute les changements dans le champ de recherche
    searchInput.addEventListener('input', (e) => {
        const term = e.target.value;
        debouncedFilter(term);
    });
    
    // Initialiser le filtre avec une chaîne vide pour s'assurer que tout est visible au départ
    filterMenu(container, '');

    // Réinitialise le filtre si le champ est vidé ou si Escape est pressé
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterMenu(container, '');
            searchInput.blur();
        }
    });
}

// Export par défaut pour le système data-module
export default initMenuFilter;

// Export nommé pour compatibilité
export { initMenuFilter, filterMenu };

