/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it } from 'vitest';

import initTooltip, { computeTooltipPosition, initAllTooltips } from '../../../resources/js/modules/tooltip.js';

describe('tooltip module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        document.documentElement.style.removeProperty('--daisy-z-tooltip-content');
    });

    it('keeps a tooltip inside the viewport and flips it when the preferred side does not fit', () => {
        const result = computeTooltipPosition({
            anchorRect: { top: 4, right: 48, bottom: 36, left: 16, width: 32, height: 32 },
            tooltipRect: { width: 240, height: 40 },
            preferredPlacement: 'top',
            viewport: { width: 320, height: 240 },
        });

        expect(result.placement).toBe('right');
        expect(result.left).toBeGreaterThanOrEqual(8);
        expect(result.left + 240).toBeLessThanOrEqual(312);
    });

    it('renders the active tooltip in a document-level portal', () => {
        document.body.innerHTML = `
            <div style="overflow-x: auto; width: 200px">
                <span class="tooltip tooltip-top" data-tip="Ouvrir le menu d'action rapide">
                    <button type="button" aria-label="Action rapide">Action</button>
                </span>
            </div>
        `;

        const root = document.querySelector('.tooltip');
        const trigger = root.querySelector('button');

        root.getBoundingClientRect = () => ({
            top: 80,
            right: 32,
            bottom: 112,
            left: 0,
            width: 32,
            height: 32,
        });

        initTooltip(root);
        root.dispatchEvent(new Event('pointerenter'));

        const portal = document.querySelector('[data-daisy-tooltip-portal]');

        expect(root.dataset.tooltipReady).toBe('true');
        expect(portal.parentElement).toBe(document.body);
        expect(portal.hidden).toBe(false);
        expect(portal.textContent).toBe("Ouvrir le menu d'action rapide");
        expect(Number.parseFloat(portal.style.left)).toBeGreaterThanOrEqual(8);
        expect(trigger.getAttribute('aria-describedby')).toBe(portal.id);
    });

    it('keeps a modal tooltip inside its active top layer', () => {
        document.body.innerHTML = `
            <dialog open>
                <span class="tooltip" data-tip="Fermer">
                    <button type="button">Close</button>
                </span>
            </dialog>
        `;

        const root = document.querySelector('.tooltip');

        initTooltip(root);
        root.dispatchEvent(new Event('pointerenter'));

        expect(document.querySelector('[data-daisy-tooltip-portal]').parentElement).toBe(document.querySelector('dialog'));
    });

    it('dismisses the active tooltip when its trigger opens a modal', () => {
        document.body.innerHTML = `
            <span class="tooltip" data-tip="Preview">
                <button type="button">Open</button>
            </span>
            <dialog></dialog>
        `;

        const root = document.querySelector('.tooltip');
        const trigger = root.querySelector('button');

        initTooltip(root);
        root.dispatchEvent(new Event('pointerenter'));
        trigger.dispatchEvent(new PointerEvent('pointerdown', { bubbles: true }));

        expect(document.querySelector('[data-daisy-tooltip-portal]').hidden).toBe(true);
        expect(trigger.hasAttribute('aria-describedby')).toBe(false);
    });

    it('initializes tooltips added to a table after the initial render', () => {
        const container = document.createElement('div');
        container.innerHTML = `
            <span class="tooltip tooltip-left">
                <span class="tooltip-content">Télécharger</span>
                <button type="button">Download</button>
            </span>
        `;
        document.body.append(container);

        initAllTooltips(container);

        expect(container.querySelector('.tooltip').dataset.tooltipReady).toBe('true');
    });
});
