<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'as' => 'div',
    'variant' => 'default',
    'size' => 'md',
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
    'as' => 'div',
    'variant' => 'default',
    'size' => 'md',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $tag = in_array($as, ['div', 'span', 'section', 'article', 'aside', 'figure'], true) ? $as : 'div';
    $classes = ['aura'];

    if (in_array($variant, ['dual', 'rainbow', 'holo', 'gold', 'silver', 'glow'], true)) {
        $classes[] = "aura-{$variant}";
    }

    if (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true)) {
        $classes[] = "aura-{$size}";
    }
?>

<<?php echo e($tag); ?> <?php echo e($attributes->class($classes)); ?>>
    <?php echo e($slot); ?>

</<?php echo e($tag); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/aura.blade.php ENDPATH**/ ?>