<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => 'solid', // solid|outline|dash|soft|ghost
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
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => 'solid', // solid|outline|dash|soft|ghost
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
        'xs' => 'badge-xs',
        'sm' => 'badge-sm',
        'md' => 'badge-md',
        'lg' => 'badge-lg',
        'xl' => 'badge-xl',
    ];
    $classes = 'badge';
    $classes .= ' badge-'.$color;
    if (isset($sizeMap[$size])) $classes .= ' '.$sizeMap[$size];
    if ($variant === 'outline') $classes .= ' badge-outline';
    if ($variant === 'dash') $classes .= ' badge-dash';
    if ($variant === 'soft') $classes .= ' badge-soft';
    if ($variant === 'ghost') $classes .= ' badge-ghost';
?>

<span <?php echo e($attributes->merge(['class' => $classes])); ?>><?php echo e($slot); ?></span>


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/badge.blade.php ENDPATH**/ ?>