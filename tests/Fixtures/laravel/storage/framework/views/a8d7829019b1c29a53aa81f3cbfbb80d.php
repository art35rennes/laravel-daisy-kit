<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
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
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
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
        'xs' => 'toggle-xs',
        'sm' => 'toggle-sm',
        'md' => 'toggle-md',
        'lg' => 'toggle-lg',
        'xl' => 'toggle-xl',
    ];

    $classes = 'toggle';

    if ($color) {
        $classes .= ' toggle-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
?>

<input type="checkbox" role="switch" <?php if($checked): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> <?php if($indeterminate): ?> aria-checked="mixed" data-indeterminate="true" <?php endif; ?> <?php echo e($attributes->merge(['class' => $classes])); ?> />


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/toggle.blade.php ENDPATH**/ ?>