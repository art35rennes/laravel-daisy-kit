<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'shape' => 'spinner', // spinner | dots | ring | ball | bars | infinity
    'size' => 'md',       // xs | sm | md | lg | xl
    'color' => null,      // primary | secondary | accent | info | success | warning | error | neutral
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
    'shape' => 'spinner', // spinner | dots | ring | ball | bars | infinity
    'size' => 'md',       // xs | sm | md | lg | xl
    'color' => null,      // primary | secondary | accent | info | success | warning | error | neutral
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizeMap = [
        'xs' => 'loading-xs',
        'sm' => 'loading-sm',
        'md' => 'loading-md',
        'lg' => 'loading-lg',
        'xl' => 'loading-xl',
    ];

    $classes = 'loading';
    $classes .= ' loading-'.$shape;
    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
    if (!empty($color)) {
        $classes .= ' text-'.$color;
    }
?>

<span aria-live="polite" aria-busy="true" <?php echo e($attributes->merge(['class' => $classes])); ?>></span>


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/feedback/loading.blade.php ENDPATH**/ ?>