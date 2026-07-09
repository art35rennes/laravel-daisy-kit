/**
 * @vitest-environment jsdom
 */

import { beforeEach, describe, expect, it, vi } from 'vitest';
import { initSidebar } from '../../../resources/js/modules/sidebar.js';

describe('sidebar module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        vi.useRealTimers();
    });

    it('opens collapsed submenus on pointer interaction', () => {
        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="1">
                <ul data-sidebar-menu>
                    <li>
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

        details.dispatchEvent(new Event('pointerenter'));

        expect(details.open).toBe(true);
        expect(details.dataset.collapsedSubmenuOpen).toBe('1');
        expect(submenu.getAttribute('aria-hidden')).toBe('false');
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

    it('closes collapsed submenus after hover and focus leave', () => {
        vi.useFakeTimers();

        document.body.innerHTML = `
            <aside data-sidebar-root data-collapsed="1">
                <ul data-sidebar-menu>
                    <li>
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

        details.dispatchEvent(new Event('pointerenter'));
        details.dispatchEvent(new Event('mouseleave'));
        vi.runAllTimers();

        expect(details.open).toBe(false);
        expect(details.dataset.collapsedSubmenuOpen).toBeUndefined();
        expect(submenu.getAttribute('aria-hidden')).toBe('true');
    });
});
