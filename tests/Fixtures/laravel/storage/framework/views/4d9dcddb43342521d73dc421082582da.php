<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => null,
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'size' => null,  // xs|sm|md|lg|xl
    // Custom CSS variables (optional)
    'bg' => null,        // --range-bg
    'thumb' => null,     // --range-thumb
    'fill' => null,      // --range-fill (e.g., 0)
    'noFill' => false,   // convenience to set --range-fill: 0
    'vertical' => false,
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
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => null,
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'size' => null,  // xs|sm|md|lg|xl
    // Custom CSS variables (optional)
    'bg' => null,        // --range-bg
    'thumb' => null,     // --range-thumb
    'fill' => null,      // --range-fill (e.g., 0)
    'noFill' => false,   // convenience to set --range-fill: 0
    'vertical' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'range';
    if ($vertical) $classes .= ' range-vertical';
    if ($color) $classes .= ' range-'.$color;
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) $classes .= ' range-'.$size;

    $dynamicAttributes = [];
    if (is_numeric($fill)) {
        $fillToken = (int) round(max(0, min(100, (float) $fill)));
        $classes .= ' daisy-range-fill-'.$fillToken;
    } elseif ($noFill) {
        $classes .= ' daisy-range-no-fill';
    }
?>

<input type="range" min="<?php echo e($min); ?>" max="<?php echo e($max); ?>" step="<?php echo e($step); ?>" <?php if(!is_null($value)): ?> value="<?php echo e($value); ?>" <?php endif; ?> <?php echo e($attributes->merge($dynamicAttributes + ['class' => $classes])); ?> />
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/range.blade.php ENDPATH**/ ?>