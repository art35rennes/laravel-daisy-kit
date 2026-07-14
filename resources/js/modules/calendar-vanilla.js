import { Calendar } from 'vanilla-calendar-pro';

const instances = new WeakMap();
const supportedOptions = new Set([
    'dateMax',
    'dateMin',
    'dateToday',
    'disableAllDates',
    'disableDates',
    'disableDatesGaps',
    'disableDatesPast',
    'disableToday',
    'disableWeekdays',
    'displayDateMax',
    'displayDateMin',
    'displayDatesOutside',
    'displayDisabledDates',
    'displayMonthsCount',
    'enableDates',
    'enableDateToggle',
    'enableEdgeDatesOnly',
    'enableJumpToSelectedDate',
    'enableMonthChangeOnDayClick',
    'enableWeekNumbers',
    'firstWeekday',
    'locale',
    'monthsToSwitch',
    'selectedDates',
    'selectedHolidays',
    'selectedMonth',
    'selectedWeekends',
    'selectedYear',
    'selectionDatesMode',
    'selectionMonthsMode',
    'selectionYearsMode',
    'type',
]);

function parseOptions(element, data) {
    if (data.options && typeof data.options === 'object') {
        return data.options;
    }

    try {
        return JSON.parse(element.dataset.options ?? '{}');
    } catch {
        return {};
    }
}

function filterOptions(options) {
    return Object.fromEntries(
        Object.entries(options).filter(([key]) => supportedOptions.has(key)),
    );
}

function selectedValue(selectedDates, mode, separator) {
    if (mode === 'multiple-ranged' && selectedDates.length > 1) {
        return [selectedDates[0], selectedDates.at(-1)].join(separator);
    }

    if (mode === 'multiple') {
        return selectedDates.join(separator);
    }

    return selectedDates[0] ?? '';
}

function syncValue(element, input, calendar, separator) {
    const selectedDates = [...(calendar.context.selectedDates ?? [])];
    const value = selectedValue(selectedDates, calendar.selectionDatesMode, separator);

    if (input) {
        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    element.dispatchEvent(new CustomEvent('calendar:change', {
        bubbles: true,
        detail: { selectedDates, value },
    }));
}

export function initCalendarVanilla(element, data = {}) {
    const existing = instances.get(element);
    if (existing) {
        return existing;
    }

    const input = element.previousElementSibling?.matches('[data-calendar-vanilla-input]')
        ? element.previousElementSibling
        : null;
    const separator = String(data.valueSeparator ?? element.dataset.valueSeparator ?? ',');
    const options = filterOptions(parseOptions(element, data));
    const accessibleLabel = element.getAttribute('aria-label');
    const calendar = new Calendar(element, {
        ...options,
        ...(accessibleLabel ? { labels: { application: accessibleLabel } } : {}),
        styles: {
            calendar: element.className || 'vc',
        },
        onClickDate(self) {
            syncValue(element, input, self, separator);
        },
    });

    calendar.init();
    instances.set(element, calendar);

    return calendar;
}

export default initCalendarVanilla;
