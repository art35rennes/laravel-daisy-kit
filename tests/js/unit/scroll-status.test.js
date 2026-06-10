/** @vitest-environment jsdom */

import { beforeEach, describe, expect, it } from 'vitest';
import '../../../resources/js/scroll-status.js';

function defineMetric(element, property, value) {
    Object.defineProperty(element, property, {
        configurable: true,
        value,
    });
}

describe('scroll-status module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('updates a progress value without runtime width styles', () => {
        document.body.innerHTML = `
            <div id="scrollbox">
                <div data-scrollstatus="1" data-container="#scrollbox">
                    <progress data-scrollstatus-progress max="100" value="0"></progress>
                </div>
            </div>
        `;

        const scrollbox = document.getElementById('scrollbox');
        defineMetric(scrollbox, 'scrollHeight', 1000);
        defineMetric(scrollbox, 'clientHeight', 500);
        scrollbox.scrollTop = 250;

        const root = document.querySelector('[data-scrollstatus="1"]');
        window.DaisyScrollStatus.init(root);

        const progress = root.querySelector('[data-scrollstatus-progress]');

        expect(progress.value).toBe(50);
        expect(root.getAttribute('style')).toBeNull();
        expect(progress.getAttribute('style')).toBeNull();
    });
});
