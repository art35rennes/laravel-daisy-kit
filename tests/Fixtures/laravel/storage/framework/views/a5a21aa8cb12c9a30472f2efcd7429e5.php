<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'color' => 'success', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg|xl
    // Accessibilité et balise
    'label' => null, // aria-label
    'as' => 'span', // span|div
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
    'color' => 'success', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg|xl
    // Accessibilité et balise
    'label' => null, // aria-label
    'as' => 'span', // span|div
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'status';
    $classes .= ' status-'.$color;
    $classes .= ' status-'.$size;
    $tag = in_array($as, ['div','span'], true) ? $as : 'span';
?>

<<?php echo e($tag); ?> <?php echo $label ? 'aria-label="'.e($label).'"' : ''; ?> <?php echo e($attributes->merge(['class' => $classes])); ?>></<?php echo e($tag); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/status.blade.php ENDPATH**/ ?>