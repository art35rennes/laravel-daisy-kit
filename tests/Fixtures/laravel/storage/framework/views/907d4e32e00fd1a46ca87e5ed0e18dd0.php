<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'width' => null, // e.g. w-32
    'height' => null, // e.g. h-6
    'rounded' => null, // none|sm|md|lg|xl|full
    /** @var string default|text Use skeleton-text for animated gradient line placeholders (daisyUI 5.5+). */
    'variant' => 'default',
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
    'width' => null, // e.g. w-32
    'height' => null, // e.g. h-6
    'rounded' => null, // none|sm|md|lg|xl|full
    /** @var string default|text Use skeleton-text for animated gradient line placeholders (daisyUI 5.5+). */
    'variant' => 'default',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = $variant === 'text' ? 'skeleton skeleton-text' : 'skeleton';
    if ($width) $classes .= ' '.$width;
    if ($height) $classes .= ' '.$height;
    if ($rounded) {
        $map = [
            'none' => 'rounded-none',
            'sm' => 'rounded',
            'md' => 'rounded-md',
            'lg' => 'rounded-lg',
            'xl' => 'rounded-xl',
            'full' => 'rounded-full',
        ];
        $classes .= ' '.($map[$rounded] ?? '');
    }
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>></div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/feedback/skeleton.blade.php ENDPATH**/ ?>