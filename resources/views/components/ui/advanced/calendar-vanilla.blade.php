@props([
    'inputId' => null,
    'mode' => 'date',
    'months' => 1,
    'showPrevNext' => true,
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'firstDay' => 1,
    'type' => null,
    'options' => [],
    'valueSeparator' => ',',
    'label' => null,
])

@php
    $allowedOptions = [
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
        'enableDates',
        'enableDateToggle',
        'enableEdgeDatesOnly',
        'enableJumpToSelectedDate',
        'enableMonthChangeOnDayClick',
        'enableWeekNumbers',
        'monthsToSwitch',
        'selectedHolidays',
        'selectedMonth',
        'selectedWeekends',
        'selectedYear',
        'selectionMonthsMode',
        'selectionYearsMode',
    ];
    $calendarOptions = Illuminate\Support\Arr::only(is_array($options) ? $options : [], $allowedOptions);
    $separator = filled($valueSeparator) ? (string) $valueSeparator : ',';
    $monthsCount = max(1, min(12, (int) $months));
    $resolvedType = $type ?? ($monthsCount > 1 ? 'multiple' : 'default');
    $resolvedType = in_array($resolvedType, ['default', 'multiple', 'month', 'year'], true) ? $resolvedType : 'default';
    $selectionMode = match ($mode) {
        'range' => 'multiple-ranged',
        'multi' => 'multiple',
        default => 'single',
    };
    $selectedValues = match (true) {
        is_array($value) => array_values($value),
        is_string($value) && str_contains($value, $separator) => array_values(array_filter(array_map('trim', explode($separator, $value)))),
        filled($value) => [(string) $value],
        default => [],
    };
    $selectedDates = $mode === 'range' && count($selectedValues) === 2
        ? [implode(':', $selectedValues)]
        : $selectedValues;

    $calendarOptions['type'] = $resolvedType;
    $calendarOptions['firstWeekday'] = max(0, min(6, (int) $firstDay));
    $calendarOptions['selectionDatesMode'] = $selectionMode;
    $calendarOptions['selectedDates'] = $selectedDates;
    $calendarOptions['enableJumpToSelectedDate'] = $calendarOptions['enableJumpToSelectedDate'] ?? filled($value);

    if ($resolvedType === 'multiple') {
        $calendarOptions['displayMonthsCount'] = max(2, $monthsCount);
    }

    if (filled($min)) {
        $calendarOptions['dateMin'] = (string) $min;
        $calendarOptions['displayDateMin'] = (string) $min;
    }

    if (filled($max)) {
        $calendarOptions['dateMax'] = (string) $max;
        $calendarOptions['displayDateMax'] = (string) $max;
    }

    if (filled($locale)) {
        $calendarOptions['locale'] = (string) $locale;
    }

    if ($attributes->has('disabled')) {
        $calendarOptions['selectionDatesMode'] = false;
    }

    $inputValue = is_array($value) ? implode($separator, $value) : ($value ?? '');
    $inputAttributes = $attributes->only(['name', 'form', 'disabled', 'required']);
    $calendarAttributes = $attributes->except(['name', 'form', 'disabled', 'required']);
@endphp

<input
    type="hidden"
    data-calendar-vanilla-input
    @if(filled($inputId)) id="{{ $inputId }}" @endif
    value="{{ $inputValue }}"
    {{ $inputAttributes }}
>

<div
    data-module="calendar-vanilla"
    data-calendar-vanilla="1"
    data-options='@json($calendarOptions)'
    data-value-separator="{{ $separator }}"
    {{ $calendarAttributes->merge([
        'class' => trim('vc '.($showPrevNext ? '' : '[&_.vc-arrow]:hidden')),
        'role' => 'group',
        'aria-label' => $label ?? __('daisy::calendar.calendar'),
    ]) }}
></div>

@include('daisy::components.partials.assets')
