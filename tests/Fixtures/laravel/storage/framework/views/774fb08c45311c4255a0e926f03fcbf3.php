<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<input
    type="hidden"
    data-calendar-vanilla-input
    <?php if(filled($inputId)): ?> id="<?php echo e($inputId); ?>" <?php endif; ?>
    value="<?php echo e($inputValue); ?>"
    <?php echo e($inputAttributes); ?>

>

<div
    data-module="calendar-vanilla"
    data-calendar-vanilla="1"
    data-options='<?php echo json_encode($calendarOptions, 15, 512) ?>'
    data-value-separator="<?php echo e($separator); ?>"
    <?php echo e($calendarAttributes->merge([
        'class' => trim('vc '.($showPrevNext ? '' : '[&_.vc-arrow]:hidden')),
        'role' => 'group',
        'aria-label' => $label ?? __('daisy::calendar.calendar'),
    ])); ?>

></div>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/calendar-vanilla.blade.php ENDPATH**/ ?>