const collapseQueries = {
    sm: '(min-width: 640px)',
    md: '(min-width: 768px)',
    lg: '(min-width: 1024px)',
    xl: '(min-width: 1280px)',
    '2xl': '(min-width: 1536px)',
};

function classListFrom(value) {
    return (value || '').split(/\s+/).filter(Boolean);
}

function normalizeText(value) {
    return (value || '')
        .toLocaleLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function directChildItems(item) {
    const submenu = item.querySelector(':scope > details > [data-sidebar-submenu]');

    if (!submenu) {
        return [];
    }

    return Array.from(submenu.children).filter(child => child.matches('[data-sidebar-item]'));
}

function restoreSearchState(root) {
    root.querySelectorAll('[data-sidebar-item], [data-sidebar-section]').forEach(element => {
        element.hidden = false;
    });

    root.querySelectorAll('[data-sidebar-details]').forEach(details => {
        if (details.dataset.sidebarSearchRestore === undefined) {
            return;
        }

        details.open = details.dataset.sidebarSearchRestore === '1';
        delete details.dataset.sidebarSearchRestore;
    });
}

function filterSidebar(root, query) {
    const term = normalizeText(query.trim());
    const status = root.querySelector('[data-sidebar-search-status]');
    const empty = root.querySelector('[data-sidebar-search-empty]');
    const sections = Array.from(root.querySelectorAll('[data-sidebar-section]'));

    if (term === '') {
        restoreSearchState(root);

        if (status) {
            status.textContent = '';
        }

        if (empty) {
            empty.hidden = true;
        }

        return 0;
    }

    root.querySelectorAll('[data-sidebar-details]').forEach(details => {
        if (details.dataset.sidebarSearchRestore === undefined) {
            details.dataset.sidebarSearchRestore = details.open ? '1' : '0';
        }
    });

    let exactMatches = 0;

    function filterItem(item) {
        const selfMatches = normalizeText(item.dataset.sidebarLabel).includes(term);
        const children = directChildItems(item);
        const childMatches = children.reduce((matches, child) => filterItem(child) || matches, false);
        const visible = selfMatches || childMatches;

        item.hidden = !visible;

        if (selfMatches) {
            exactMatches += 1;
        }

        if (childMatches) {
            const details = item.querySelector(':scope > details');

            if (details) {
                details.open = true;
            }
        }

        return visible;
    }

    sections.forEach(section => {
        const items = Array.from(section.querySelectorAll(':scope > ul > [data-sidebar-item]'));
        const visible = items.reduce((matches, item) => filterItem(item) || matches, false);

        section.hidden = !visible;
    });

    if (status) {
        const label = status.dataset.resultsLabel || ':count result(s)';
        status.textContent = label.replace(':count', String(exactMatches));
    }

    if (empty) {
        empty.hidden = exactMatches > 0;
    }

    return exactMatches;
}

function initializeSearch(root) {
    const search = root.querySelector('[data-sidebar-search]');

    if (!search) {
        return;
    }

    search.addEventListener('input', () => filterSidebar(root, search.value));
    search.addEventListener('keydown', event => {
        if (event.key !== 'Escape' || search.value === '') {
            return;
        }

        search.value = '';
        filterSidebar(root, '');
        search.focus();
    });
}

function initializeCollapsedSubmenus(root, expandSidebar) {
    root.querySelectorAll('[data-sidebar-details], [data-sidebar-menu] details').forEach(details => {
        const submenu = details.querySelector(':scope > [data-sidebar-submenu]');

        if (!submenu || details.dataset.sidebarFlyoutInitialized === '1') {
            return;
        }

        details.dataset.sidebarFlyoutInitialized = '1';

        function isCollapsedMenu() {
            const item = details.closest('[data-sidebar-item]');
            const isTopLevel = !item || item.dataset.sidebarDepth === '0';

            return root.dataset.collapsed === '1' && isTopLevel;
        }

        details.querySelector(':scope > summary')?.addEventListener('click', event => {
            if (!isCollapsedMenu()) {
                return;
            }

            event.preventDefault();
            expandSidebar();
            details.open = true;
        });
    });
}

function initSidebar(root) {
    if (!root || root.dataset.sidebarInitialized === '1') {
        return null;
    }

    root.dataset.sidebarInitialized = '1';

    const toggle = root.querySelector('[data-sidebar-toggle], .sidebar-toggle');
    const storageKey = root.dataset.storageKey || null;
    const wideClasses = classListFrom(root.dataset.wideClass);
    const collapsedClasses = classListFrom(root.dataset.collapsedClass || 'w-20');
    const expandOnHover = root.dataset.expandOnHover === '1';
    const forcedState = root.dataset.forceCollapsed;
    const initialCollapsed = root.dataset.collapsed === '1';
    const collapseAt = root.dataset.collapseAt;
    const mediaQuery = collapseQueries[collapseAt] && window.matchMedia
        ? window.matchMedia(collapseQueries[collapseAt])
        : null;
    let preferredCollapsed = forcedState === undefined ? initialCollapsed : forcedState === '1';
    let hoverCloseTimeout = null;

    if (storageKey && forcedState === undefined) {
        try {
            const persisted = window.localStorage.getItem(storageKey);

            if (persisted === '1' || persisted === '0') {
                preferredCollapsed = persisted === '1';
            }
        } catch (_) {
            // Storage may be unavailable in private or sandboxed contexts.
        }
    }

    function isDesktop() {
        return mediaQuery ? mediaQuery.matches : true;
    }

    function updateToggle(collapsed) {
        if (!toggle) {
            return;
        }

        const label = collapsed
            ? (root.dataset.collapsedLabel || 'Expand')
            : (root.dataset.expandedLabel || 'Collapse');

        toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        toggle.setAttribute('aria-label', label);
        toggle.setAttribute('title', label);

        const labelElement = toggle.querySelector('[data-sidebar-toggle-label]');

        if (labelElement) {
            labelElement.textContent = label;
        }

        const collapsedIcon = toggle.querySelector('[data-sidebar-icon-collapsed]');
        const expandedIcon = toggle.querySelector('[data-sidebar-icon-expanded]');

        if (collapsedIcon) {
            collapsedIcon.hidden = !collapsed;
        }

        if (expandedIcon) {
            expandedIcon.hidden = collapsed;
        }
    }

    function renderState(collapsed) {
        root.classList.remove(...wideClasses, ...collapsedClasses);
        root.classList.add(...(collapsed ? collapsedClasses : wideClasses));
        root.dataset.collapsed = collapsed ? '1' : '0';
        updateToggle(collapsed);
    }

    function persistState() {
        if (!storageKey || forcedState !== undefined) {
            return;
        }

        try {
            window.localStorage.setItem(storageKey, preferredCollapsed ? '1' : '0');
        } catch (_) {
            // Storage may be unavailable in private or sandboxed contexts.
        }
    }

    function applyPreferredState() {
        const collapsed = forcedState !== undefined
            ? forcedState === '1'
            : isDesktop() && preferredCollapsed;

        renderState(collapsed);
    }

    toggle?.addEventListener('click', () => {
        preferredCollapsed = !preferredCollapsed;
        applyPreferredState();
        persistState();
    });

    function expandSidebar() {
        preferredCollapsed = false;
        applyPreferredState();
        persistState();
    }

    if (expandOnHover) {
        root.addEventListener('pointerenter', () => {
            if (!isDesktop()) {
                return;
            }

            window.clearTimeout(hoverCloseTimeout);
            renderState(false);
        });
        root.addEventListener('pointerleave', () => {
            if (!isDesktop()) {
                return;
            }

            hoverCloseTimeout = window.setTimeout(() => renderState(true), 160);
        });
        root.addEventListener('focusin', () => {
            if (isDesktop()) {
                renderState(false);
            }
        });
        root.addEventListener('focusout', () => {
            window.setTimeout(() => {
                if (isDesktop() && !root.contains(document.activeElement)) {
                    renderState(true);
                }
            }, 0);
        });
    }

    mediaQuery?.addEventListener('change', applyPreferredState);
    initializeCollapsedSubmenus(root, expandSidebar);
    initializeSearch(root);
    applyPreferredState();

    return {
        collapse() {
            preferredCollapsed = true;
            applyPreferredState();
            persistState();
        },
        expand() {
            preferredCollapsed = false;
            applyPreferredState();
            persistState();
        },
        filter(query) {
            return filterSidebar(root, query);
        },
    };
}

export default initSidebar;

export { filterSidebar, initSidebar, initializeCollapsedSubmenus, normalizeText };
