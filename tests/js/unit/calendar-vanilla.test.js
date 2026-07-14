/**
 * @vitest-environment jsdom
 */

import { beforeEach, describe, expect, it, vi } from 'vitest';

const { Calendar } = vi.hoisted(() => ({
    Calendar: vi.fn(function Calendar(element, options) {
        Object.assign(this, options);
        this.context = { selectedDates: options.selectedDates ?? [] };
        this.init = vi.fn();
        this.element = element;
        this.options = options;
    }),
}));

vi.mock('vanilla-calendar-pro', () => ({ Calendar }));

import initCalendarVanilla from '../../../resources/js/modules/calendar-vanilla.js';

describe('Vanilla Calendar Pro adapter', () => {
    beforeEach(() => {
        Calendar.mockClear();
        document.body.innerHTML = '';
    });

    it('initializes each calendar once with supported options', () => {
        document.body.innerHTML = `
            <input type="hidden" data-calendar-vanilla-input value="2026-07-13">
            <div class="vc w-full" data-calendar-vanilla="1" aria-label="Dates du séjour"></div>
        `;
        const element = document.querySelector('[data-calendar-vanilla]');
        const data = {
            options: {
                locale: 'fr-FR',
                selectionDatesMode: 'single',
                selectedDates: ['2026-07-13'],
                layouts: { default: '<script>alert(1)</script>' },
            },
        };

        const first = initCalendarVanilla(element, data);
        const second = initCalendarVanilla(element, data);

        expect(first).toBe(second);
        expect(Calendar).toHaveBeenCalledTimes(1);
        expect(first.init).toHaveBeenCalledOnce();
        expect(first.options.locale).toBe('fr-FR');
        expect(first.options.layouts).toBeUndefined();
        expect(first.options.styles.calendar).toContain('vc');
        expect(first.options.labels.application).toBe('Dates du séjour');
    });

    it('synchronizes the hidden field and dispatches a change event', () => {
        document.body.innerHTML = `
            <input type="hidden" data-calendar-vanilla-input>
            <div class="vc" data-calendar-vanilla="1"></div>
        `;
        const element = document.querySelector('[data-calendar-vanilla]');
        const input = document.querySelector('[data-calendar-vanilla-input]');
        const changed = vi.fn();
        element.addEventListener('calendar:change', changed);

        const instance = initCalendarVanilla(element, {
            options: { selectionDatesMode: 'multiple-ranged' },
            valueSeparator: ',',
        });
        instance.context.selectedDates = ['2026-07-13', '2026-07-14', '2026-07-15'];
        instance.options.onClickDate(instance);

        expect(input.value).toBe('2026-07-13,2026-07-15');
        expect(changed).toHaveBeenCalledOnce();
        expect(changed.mock.calls[0][0].detail.selectedDates).toEqual(instance.context.selectedDates);
    });
});
