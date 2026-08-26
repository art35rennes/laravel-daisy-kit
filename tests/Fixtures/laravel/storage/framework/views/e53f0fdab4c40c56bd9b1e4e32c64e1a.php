<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'types' => [],
    'currentFilter' => 'all', // all, unread, or specific type
    'onFilterChange' => null, // Callback JS (optionnel)
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
    'types' => [],
    'currentFilter' => 'all', // all, unread, or specific type
    'onFilterChange' => null, // Callback JS (optionnel)
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $filterName = 'notification-filter-' . uniqid();
    $items = [
        ['label' => __('daisy::notifications.all'), 'value' => 'all', 'checked' => $currentFilter === 'all'],
        ['label' => __('daisy::notifications.unread'), 'value' => 'unread', 'checked' => $currentFilter === 'unread'],
    ];

    foreach ($types as $type) {
        $typeLabel = is_array($type) ? ($type['label'] ?? $type['value'] ?? $type) : $type;
        $typeValue = is_array($type) ? ($type['value'] ?? $typeLabel) : $type;
        $items[] = [
            'label' => $typeLabel,
            'value' => $typeValue,
            'checked' => $currentFilter === $typeValue,
        ];
    }
?>

<div <?php echo e($attributes->merge(['class' => 'notification-filters'])); ?>>
    <x-daisy::ui.advanced.filter
        :name="$filterName"
        :items="$items"
        :use-form="false"
        :all-label="__('daisy::notifications.all')"
        data-filter-name="<?php echo e($filterName); ?>"
        <?php if($onFilterChange): ?> data-on-filter-change="<?php echo e($onFilterChange); ?>" <?php endif; ?>
    />
</div>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/notification-filters.blade.php ENDPATH**/ ?>