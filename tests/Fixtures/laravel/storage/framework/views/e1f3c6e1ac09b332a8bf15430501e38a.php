<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Compat: position = horizontal (start|center|end)
    'position' => 'end',
    'horizontal' => null, // start|center|end
    'vertical' => 'bottom', // top|middle|bottom
    'triggerable' => false,
    'limit' => 4,
    'module' => null,
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
    // Compat: position = horizontal (start|center|end)
    'position' => 'end',
    'horizontal' => null, // start|center|end
    'vertical' => 'bottom', // top|middle|bottom
    'triggerable' => false,
    'limit' => 4,
    'module' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $h = $horizontal ?? $position;
    $horizontalClass = [
        'start' => 'toast-start',
        'center' => 'toast-center',
        'end' => 'toast-end',
    ][$h] ?? 'toast-end';

    $verticalClass = [
        'top' => 'toast-top',
        'middle' => 'toast-middle',
        'bottom' => 'toast-bottom',
    ][$vertical] ?? 'toast-bottom';
?>

<?php
    $dataAttributes = [];

    if ($triggerable) {
        $dataAttributes = [
            'data-module' => $module ?? 'notify',
            'data-daisy-notify-container' => 'true',
            'data-notify-limit' => $limit,
            'data-notify-horizontal' => $h,
            'data-notify-vertical' => $vertical,
        ];
    }
?>

<div <?php echo e($attributes->merge(['class' => 'toast '.$horizontalClass.' '.$verticalClass])->merge($dataAttributes)); ?>>
    <?php echo e($slot ?? ''); ?>

</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/feedback/toast.blade.php ENDPATH**/ ?>