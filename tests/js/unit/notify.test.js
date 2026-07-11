/** @vitest-environment jsdom */

import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

import initNotify, {
    clearNotifications,
    dismissNotification,
    installNotifyGlobals,
    notify,
} from '../../../resources/js/modules/notify.js';

describe('notify module', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
        vi.useFakeTimers();
        vi.setSystemTime(new Date('2026-01-01T00:00:00Z'));
    });

    afterEach(() => {
        clearNotifications();
        vi.useRealTimers();
    });

    it('creates a unique toast container and exposes the global API', () => {
        installNotifyGlobals();

        const notification = window.DaisyKit.notify({
            type: 'success',
            title: 'Saved',
            message: 'Changes persisted',
            autoDismiss: false,
            position: 'top-end',
        });

        const container = document.querySelector('[data-daisy-notify-container]');

        expect(container).not.toBeNull();
        expect(container.classList.contains('toast-top')).toBe(true);
        expect(container.classList.contains('toast-end')).toBe(true);
        expect(notification.textContent).toContain('Saved');
        expect(notification.textContent).toContain('Changes persisted');
        expect(notification.classList.contains('alert-success')).toBe(true);
    });

    it('responds to daisy notify events', () => {
        document.dispatchEvent(new CustomEvent('daisy:notify', {
            detail: {
                type: 'warning',
                title: 'Queued',
                message: 'Export started',
                autoDismiss: false,
            },
        }));

        const notification = document.querySelector('[data-notify-id]');

        expect(notification).not.toBeNull();
        expect(notification.role).toBe('alert');
        expect(notification.textContent).toContain('Queued');
    });

    it('renders a loading spinner for long-running actions', () => {
        const notification = notify({
            title: 'Generating',
            message: 'Please wait',
            loading: true,
            autoDismiss: false,
        });

        expect(notification.querySelector('.loading.loading-spinner')).not.toBeNull();
    });

    it('limits visible notifications and dismisses the oldest entry', () => {
        const first = notify({ title: 'First', autoDismiss: false, limit: 2 });

        notify({ title: 'Second', autoDismiss: false, limit: 2 });
        notify({ title: 'Third', autoDismiss: false, limit: 2 });

        const notifications = [...document.querySelectorAll('[data-notify-id]')];

        expect(first.isConnected).toBe(false);
        expect(notifications).toHaveLength(2);
        expect(notifications.map(notification => notification.textContent)).toEqual([
            expect.stringContaining('Second'),
            expect.stringContaining('Third'),
        ]);
    });

    it('runs action callbacks and custom action events', () => {
        const callback = vi.fn();
        const eventListener = vi.fn();

        document.addEventListener('notify:action', eventListener);

        const callbackNotification = notify({
            title: 'Undoable',
            autoDismiss: false,
            actions: [{ label: 'Undo', name: 'undo', callback }],
        });
        callbackNotification.querySelector('button').click();

        const eventNotification = notify({
            title: 'Event',
            autoDismiss: false,
            actions: [{ label: 'Open', name: 'open' }],
        });
        eventNotification.querySelector('button').click();

        expect(callback).toHaveBeenCalledWith(expect.objectContaining({ action: 'undo' }));
        expect(eventListener).toHaveBeenCalledOnce();
        expect(eventListener.mock.calls[0][0].detail.action).toBe('open');
    });

    it('auto dismisses notifications with progress and pauses on hover', () => {
        const notification = notify({
            title: 'Timed',
            autoDismissMs: 2000,
        });
        const progress = notification.querySelector('[data-alert-progress]');

        vi.advanceTimersByTime(1000);
        notification.dispatchEvent(new PointerEvent('pointerenter', { bubbles: true }));
        vi.advanceTimersByTime(2000);

        expect(notification.isConnected).toBe(true);
        expect(progress.value).toBe(50);

        notification.dispatchEvent(new PointerEvent('pointerleave', { bubbles: true }));
        vi.advanceTimersByTime(1000);

        expect(notification.isConnected).toBe(false);
    });

    it('initializes a server-rendered container with configured position and limit', () => {
        document.body.innerHTML = `
            <div class="toast toast-bottom toast-end" data-module="notify" data-notify-limit="2" data-notify-position="top-center"></div>
        `;

        const container = document.querySelector('[data-module="notify"]');

        initNotify(container, {});

        expect(container.dataset.daisyNotifyContainer).toBe('true');
        expect(container.dataset.notifyLimit).toBe('2');
        expect(container.classList.contains('toast-top')).toBe(true);
        expect(container.classList.contains('toast-center')).toBe(true);
        expect(container.classList.contains('toast-bottom')).toBe(false);
    });

    it('can dismiss a notification by id', () => {
        notify({ id: 'saved', title: 'Saved', autoDismiss: false });

        expect(dismissNotification('saved')).toBe(true);
        expect(document.querySelector('[data-notify-id="saved"]')).toBeNull();
    });
});
