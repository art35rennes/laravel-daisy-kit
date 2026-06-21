/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it } from 'vitest';

import initPopover from '../../../resources/js/modules/popover.js';

describe('popover module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('opens and closes click popovers without duplicate initialization', () => {
        document.body.innerHTML = `
            <span data-popover data-trigger="click">
                <button class="popover-trigger" type="button">Click to toggle</button>
                <div class="popover-panel hidden">Click content</div>
            </span>
        `;

        const root = document.querySelector('[data-popover]');
        const trigger = root.querySelector('.popover-trigger');
        const panel = root.querySelector('.popover-panel');

        initPopover(root);
        initPopover(root);

        trigger.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(panel.classList.contains('hidden')).toBe(false);

        trigger.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(panel.classList.contains('hidden')).toBe(true);
        expect(root.classList.contains('relative')).toBe(true);
        expect(root.classList.contains('w-fit')).toBe(true);
    });

    it('opens focus popovers when a nested focusable trigger receives focus', () => {
        document.body.innerHTML = `
            <span data-popover data-trigger="focus">
                <span class="popover-trigger" tabindex="0">
                    <button type="button">Focus me</button>
                </span>
                <div class="popover-panel hidden">Focused content</div>
            </span>
        `;

        const root = document.querySelector('[data-popover]');
        const button = root.querySelector('button');
        const panel = root.querySelector('.popover-panel');

        initPopover(root);

        button.dispatchEvent(new FocusEvent('focusin', { bubbles: true }));

        expect(panel.classList.contains('hidden')).toBe(false);
    });

    it('updates arrow alignment classes when placement flips', () => {
        document.body.innerHTML = `
            <span data-popover data-trigger="click" data-position="bottom">
                <button class="popover-trigger" type="button">Toggle</button>
                <div class="popover-panel hidden">
                    <span class="popover-arrow absolute w-3 h-3 rotate-45 bg-base-100 border"></span>
                    Content
                </div>
            </span>
        `;

        const root = document.querySelector('[data-popover]');
        const panel = root.querySelector('.popover-panel');
        const arrow = root.querySelector('.popover-arrow');

        initPopover(root);

        expect(panel.dataset.currentPosition).toBe('bottom');
        expect(panel.classList.contains('top-full')).toBe(true);
        expect(arrow.classList.contains('-top-1')).toBe(true);
        expect(arrow.classList.contains('left-1/2')).toBe(true);
    });
});
