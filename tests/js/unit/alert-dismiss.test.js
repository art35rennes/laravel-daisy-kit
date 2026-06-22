/** @vitest-environment jsdom */

import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

import initAlertDismiss from '../../../resources/js/modules/alert-dismiss.js';

describe('alert dismiss module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        vi.useFakeTimers();
        vi.setSystemTime(new Date('2026-01-01T00:00:00Z'));
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('removes an alert when the dismiss control is clicked', () => {
        document.body.innerHTML = `
            <div data-module="alert-dismiss">
                <button type="button" data-alert-dismiss>Close</button>
            </div>
        `;

        const alert = document.querySelector('[data-module="alert-dismiss"]');
        const listener = vi.fn();

        alert.addEventListener('daisy:alert-dismiss', listener);
        initAlertDismiss(alert);
        alert.querySelector('[data-alert-dismiss]').click();

        expect(alert.isConnected).toBe(false);
        expect(listener).toHaveBeenCalledOnce();
    });

    it('removes an alert after its configured delay and unloads progress', () => {
        document.body.innerHTML = `
            <div data-module="alert-dismiss" data-alert-auto-dismiss="2000">
                <progress max="100" value="100" data-alert-progress></progress>
                <span data-alert-remaining>2s</span>
            </div>
        `;

        const alert = document.querySelector('[data-module="alert-dismiss"]');
        const progress = alert.querySelector('[data-alert-progress]');
        const remaining = alert.querySelector('[data-alert-remaining]');

        initAlertDismiss(alert);

        vi.advanceTimersByTime(1000);

        expect(progress.value).toBe(50);
        expect(remaining.textContent).toBe('1s');

        vi.advanceTimersByTime(1000);

        expect(alert.isConnected).toBe(false);
        expect(progress.value).toBe(0);
    });

    it('clears the auto dismiss timer when manually dismissed', () => {
        document.body.innerHTML = `
            <div data-module="alert-dismiss" data-alert-auto-dismiss="2000">
                <button type="button" data-alert-dismiss>Close</button>
            </div>
        `;

        const alert = document.querySelector('[data-module="alert-dismiss"]');

        initAlertDismiss(alert);
        alert.querySelector('[data-alert-dismiss]').click();

        vi.advanceTimersByTime(3000);

        expect(alert.isConnected).toBe(false);
    });
});
