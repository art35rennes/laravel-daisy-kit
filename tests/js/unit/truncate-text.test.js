/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it } from 'vitest';

import initTruncateText from '../../../resources/js/modules/truncate-text.js';

function setElementSize(element, { clientWidth = 100, scrollWidth = 220, clientHeight = 20, scrollHeight = 20 } = {}) {
    Object.defineProperties(element, {
        clientWidth: { configurable: true, value: clientWidth },
        scrollWidth: { configurable: true, value: scrollWidth },
        clientHeight: { configurable: true, value: clientHeight },
        scrollHeight: { configurable: true, value: scrollHeight },
    });
}

describe('truncate text module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('opens a copyable popover on click when the text is truncated', () => {
        document.body.innerHTML = `
            <span class="tooltip" data-tip="Full text">
                <span
                    data-module="truncate-text"
                    data-truncate-text-title="Full text"
                    data-truncate-text-reveal="both"
                    data-truncate-text-only-when-truncated="true"
                >Full text</span>
                <span class="daisy-truncate-popover hidden" aria-hidden="true">Full text</span>
            </span>
        `;

        const root = document.querySelector('[data-module="truncate-text"]');
        const popover = document.querySelector('.daisy-truncate-popover');

        setElementSize(root);
        initTruncateText(root);

        expect(root.getAttribute('role')).toBe('button');
        expect(root.getAttribute('tabindex')).toBe('0');
        expect(popover.classList.contains('hidden')).toBe(true);

        root.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(popover.classList.contains('hidden')).toBe(false);
        expect(popover.getAttribute('aria-hidden')).toBe('false');
    });

    it('keeps measured tooltip and popover disabled when the text fits', () => {
        document.body.innerHTML = `
            <span class="tooltip" data-tip="Short text">
                <span
                    data-module="truncate-text"
                    data-truncate-text-title="Short text"
                    data-truncate-text-reveal="both"
                    data-truncate-text-only-when-truncated="true"
                >Short text</span>
                <span class="daisy-truncate-popover hidden" aria-hidden="true">Short text</span>
            </span>
        `;

        const root = document.querySelector('[data-module="truncate-text"]');
        const wrapper = document.querySelector('.tooltip');
        const popover = document.querySelector('.daisy-truncate-popover');

        setElementSize(root, { clientWidth: 220, scrollWidth: 100 });
        initTruncateText(root);

        expect(wrapper.dataset.tip).toBeUndefined();
        expect(root.hasAttribute('role')).toBe(false);
        expect(root.hasAttribute('tabindex')).toBe(false);

        root.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(popover.classList.contains('hidden')).toBe(true);
        expect(popover.getAttribute('aria-hidden')).toBe('true');
    });

    it('keeps tooltip and popover available when overflow-only behavior is disabled', () => {
        document.body.innerHTML = `
            <span class="tooltip" data-tip="Short text">
                <span
                    data-module="truncate-text"
                    data-truncate-text-title="Short text"
                    data-truncate-text-reveal="both"
                    data-truncate-text-only-when-truncated="false"
                >Short text</span>
                <span class="daisy-truncate-popover hidden" aria-hidden="true">Short text</span>
            </span>
        `;

        const root = document.querySelector('[data-module="truncate-text"]');
        const wrapper = document.querySelector('.tooltip');
        const popover = document.querySelector('.daisy-truncate-popover');

        setElementSize(root, { clientWidth: 220, scrollWidth: 100 });
        initTruncateText(root);

        expect(wrapper.dataset.tip).toBe('Short text');
        expect(root.getAttribute('role')).toBe('button');

        root.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(popover.classList.contains('hidden')).toBe(false);
    });
});
