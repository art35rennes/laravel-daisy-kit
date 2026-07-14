/**
 * @vitest-environment jsdom
 */

import { describe, expect, it, vi } from 'vitest';
import { mount } from '../../../resources/js/calendar-full/core.js';
import { renderDay, renderMonth } from '../../../resources/js/calendar-full/renderers.js';

const context = {
    currentDate: new Date(2026, 6, 13),
    events: [],
    firstDay: 1,
    hourStart: 8,
    hourEnd: 18,
    onEventClick: vi.fn(),
    onMore: vi.fn(),
};

describe('full calendar DaisyUI rendering', () => {
    it('renders weekday headings and marks days outside the current month', () => {
        const container = document.createElement('div');

        renderMonth(container, context);

        expect(container.querySelectorAll('.cf-weekday')).toHaveLength(7);
        expect(container.querySelectorAll('.cf-cell.is-outside-month').length).toBeGreaterThan(0);
        expect(container.querySelector('.cf-month').getAttribute('role')).toBe('grid');
    });

    it('renders the day view with one day column', () => {
        const container = document.createElement('div');

        renderDay(container, context);

        expect(container.querySelectorAll('.cf-day-col')).toHaveLength(1);
        expect(container.querySelector('.cf-day')).not.toBeNull();
    });

    it('renders untrusted event titles as text', () => {
        document.body.innerHTML = `
            <template data-calendar-event="chip">
                <span><span>{{title}}</span></span>
            </template>
        `;
        const container = document.createElement('div');
        const title = '<img src=x onerror="window.calendarXss = true">';

        renderMonth(container, {
            ...context,
            events: [{
                title,
                start: new Date(2026, 6, 13, 9),
                end: new Date(2026, 6, 13, 10),
                allDay: false,
            }],
        });

        expect(container.querySelector('.cf-event').textContent).toContain(title);
        expect(container.querySelector('.cf-event img')).toBeNull();
        expect(window.calendarXss).toBeUndefined();
    });

    it('blocks executable event URLs', () => {
        const container = document.createElement('div');

        renderMonth(container, {
            ...context,
            events: [{
                title: 'Unsafe event',
                start: new Date(2026, 6, 13, 9),
                end: new Date(2026, 6, 13, 10),
                allDay: false,
                url: 'javascript:window.calendarXss=true',
            }],
        });

        expect(container.querySelector('.cf-event').href).not.toMatch(/^javascript:/);
    });

    it('builds accessible DaisyUI joins and tabs in the toolbar', async () => {
        window.requestAnimationFrame = callback => {
            callback();

            return 1;
        };
        document.body.innerHTML = '<div data-calendar-full="1" data-options=\'{"initialDate":"2026-07-13"}\'></div>';
        const root = document.querySelector('[data-calendar-full]');

        const calendar = mount(root);
        await calendar.render();

        expect(root.querySelector('.cf-toolbar .join')).not.toBeNull();
        expect(root.querySelector('[role="tablist"].tabs')).not.toBeNull();
        expect(root.querySelector('[role="tab"][aria-selected="true"].tab-active')).not.toBeNull();
        expect([...root.querySelectorAll('button')].every(button => button.type === 'button')).toBe(true);
    });
});
