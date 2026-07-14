/**
 * @vitest-environment jsdom
 */

import { beforeEach, describe, expect, it, vi } from 'vitest';
import { initSidebar } from '../../../resources/js/modules/sidebar.js';

describe('sidebar module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        vi.useRealTimers();
        vi.unstubAllGlobals();
    });

    it('expands the sidebar and opens a collapsed submenu on click', () => {
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="1" data-wide-class="w-64" data-collapsed-class="w-16">
                <ul data-sidebar-menu>
                    <li data-sidebar-item data-sidebar-depth="0">
                        <details>
                            <summary data-sidebar-row>Settings</summary>
                            <ul data-sidebar-submenu aria-hidden="true">
                                <li><a href="/settings" data-sidebar-row><span class="sidebar-label hidden">General</span></a></li>
                            </ul>
                        </details>
                    </li>
                </ul>
            </aside>
        `;

        const sidebar = document.querySelector('[data-sidebar-root]');
        const details = sidebar.querySelector('details');
        const submenu = sidebar.querySelector('[data-sidebar-submenu]');

        initSidebar(sidebar);

        details.querySelector('summary').dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(details.open).toBe(true);
        expect(sidebar.dataset.collapsed).toBe('0');
        expect(submenu.hidden).toBe(false);
    });

    it('keeps expanded sidebars using native details behavior', () => {
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="0">
                <ul data-sidebar-menu>
                    <li>
                        <details>
                            <summary data-sidebar-row>Settings</summary>
                            <ul data-sidebar-submenu aria-hidden="false">
                                <li><a href="/settings" data-sidebar-row><span class="sidebar-label">General</span></a></li>
                            </ul>
                        </details>
                    </li>
                </ul>
            </aside>
        `;

        const sidebar = document.querySelector('[data-sidebar-root]');
        const details = sidebar.querySelector('details');

        initSidebar(sidebar);

        details.dispatchEvent(new Event('pointerenter'));

        expect(details.open).toBe(false);
        expect(details.dataset.collapsedSubmenuOpen).toBeUndefined();
    });

    it('owns the collapsed state and only persists when configured', () => {
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="0" data-wide-class="w-64" data-collapsed-class="w-16"
                data-expanded-label="Collapse" data-collapsed-label="Expand">
                <button type="button" data-sidebar-toggle aria-expanded="true">
                    <span data-sidebar-toggle-label>Collapse</span>
                    <span data-sidebar-icon-collapsed hidden></span>
                    <span data-sidebar-icon-expanded></span>
                </button>
                <ul data-sidebar-menu></ul>
            </aside>
        `;

        const sidebar = document.querySelector('[data-sidebar-root]');
        const toggle = sidebar.querySelector('[data-sidebar-toggle]');
        const storageSpy = vi.spyOn(Storage.prototype, 'setItem');

        initSidebar(sidebar);
        toggle.click();

        expect(sidebar.dataset.collapsed).toBe('1');
        expect(sidebar.classList.contains('w-16')).toBe(true);
        expect(toggle.getAttribute('aria-expanded')).toBe('false');
        expect(storageSpy).not.toHaveBeenCalled();

        storageSpy.mockRestore();
    });

    it('filters nested items and opens matching ancestors', () => {
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="0">
                <input type="search" data-sidebar-search>
                <p data-sidebar-search-status></p>
                <p data-sidebar-search-empty hidden></p>
                <ul data-sidebar-menu data-menu-filter-target>
                    <li data-sidebar-section>
                        <span data-sidebar-section-title>Settings</span>
                        <ul>
                            <li data-sidebar-item data-sidebar-label="Access">
                                <details data-sidebar-details>
                                    <summary>Access</summary>
                                    <ul data-sidebar-submenu>
                                        <li data-sidebar-item data-sidebar-label="Roles"><a href="/roles">Roles</a></li>
                                    </ul>
                                </details>
                            </li>
                            <li data-sidebar-item data-sidebar-label="Billing"><a href="/billing">Billing</a></li>
                        </ul>
                    </li>
                </ul>
            </aside>
        `;

        const sidebar = document.querySelector('[data-sidebar-root]');
        const search = sidebar.querySelector('[data-sidebar-search]');
        const access = sidebar.querySelector('[data-sidebar-label="Access"]');
        const billing = sidebar.querySelector('[data-sidebar-label="Billing"]');

        initSidebar(sidebar);
        search.value = 'roles';
        search.dispatchEvent(new Event('input', { bubbles: true }));

        expect(access.hidden).toBe(false);
        expect(access.querySelector('details').open).toBe(true);
        expect(billing.hidden).toBe(true);
        expect(sidebar.querySelector('[data-sidebar-search-status]').textContent).toContain('1');
        expect(sidebar.querySelector('[data-sidebar-search-empty]').hidden).toBe(true);
    });

    it('shows the empty search state when no nested item matches', () => {
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="0">
                <input type="search" data-sidebar-search>
                <p data-sidebar-search-status></p>
                <p data-sidebar-search-empty hidden>No result</p>
                <ul data-sidebar-menu data-menu-filter-target>
                    <li data-sidebar-section>
                        <ul><li data-sidebar-item data-sidebar-label="Home"><a href="/">Home</a></li></ul>
                    </li>
                </ul>
            </aside>
        `;

        const sidebar = document.querySelector('[data-sidebar-root]');
        const search = sidebar.querySelector('[data-sidebar-search]');

        initSidebar(sidebar);
        search.value = 'missing';
        search.dispatchEvent(new Event('input', { bubbles: true }));

        expect(sidebar.querySelector('[data-sidebar-search-empty]').hidden).toBe(false);
        expect(sidebar.querySelector('[data-sidebar-section]').hidden).toBe(true);
    });

    it('keeps the drawer expanded below its responsive breakpoint', () => {
        let mediaListener;
        const mediaQuery = {
            matches: false,
            addEventListener: (_event, listener) => {
                mediaListener = listener;
            },
        };
        vi.stubGlobal('matchMedia', vi.fn(() => mediaQuery));
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="1" data-collapse-at="lg"
                data-wide-class="w-64" data-collapsed-class="w-20">
                <ul data-sidebar-menu></ul>
            </aside>
        `;

        const sidebar = document.querySelector('[data-sidebar-root]');

        initSidebar(sidebar);

        expect(sidebar.dataset.collapsed).toBe('0');

        mediaQuery.matches = true;
        mediaListener();

        expect(sidebar.dataset.collapsed).toBe('1');
    });
});
