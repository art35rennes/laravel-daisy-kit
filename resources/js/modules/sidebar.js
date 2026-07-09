function initSidebar(root, options = {}) {
    const searchInput = root.querySelector('[data-sidebar-search]');

    if (searchInput) {
        console.warn('[DaisyKit] sidebar.js: Le filtrage est maintenant géré par menu-filter. Utilisez data-module="menu-filter" sur le conteneur du filtre.');
    }

    initializeCollapsedSubmenus(root);
}

function initializeCollapsedSubmenus(root) {
    root.querySelectorAll('[data-sidebar-menu] details').forEach(details => {
        const submenu = details.querySelector('[data-sidebar-submenu]');

        if (!submenu || details.dataset.collapsedSubmenuInitialized === 'true') {
            return;
        }

        details.dataset.collapsedSubmenuInitialized = 'true';
        details.dataset.collapsedSubmenuDefaultOpen = details.open ? '1' : '0';

        const isCollapsed = () => root.dataset.collapsed === '1';

        const openSubmenu = () => {
            if (!isCollapsed()) {
                return;
            }

            details.open = true;
            details.dataset.collapsedSubmenuOpen = '1';
            submenu.setAttribute('aria-hidden', 'false');
        };

        const closeSubmenu = () => {
            if (!isCollapsed()) {
                return;
            }

            if (details.matches(':hover') || details.contains(document.activeElement)) {
                return;
            }

            delete details.dataset.collapsedSubmenuOpen;
            submenu.setAttribute('aria-hidden', 'true');

            if (details.dataset.collapsedSubmenuDefaultOpen !== '1') {
                details.open = false;
            }
        };

        details.addEventListener('mouseenter', openSubmenu);
        details.addEventListener('mouseover', openSubmenu);
        details.addEventListener('pointerenter', openSubmenu);
        details.querySelector('summary')?.addEventListener('focus', openSubmenu);
        details.querySelector('summary')?.addEventListener('click', () => window.setTimeout(openSubmenu, 0));
        details.addEventListener('focusin', openSubmenu);
        details.addEventListener('mouseleave', closeSubmenu);
        details.addEventListener('focusout', () => window.setTimeout(closeSubmenu, 0));
    });
}

export default initSidebar;

export { initSidebar, initializeCollapsedSubmenus };
