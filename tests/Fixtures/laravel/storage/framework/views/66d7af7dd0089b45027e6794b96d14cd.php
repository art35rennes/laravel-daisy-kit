<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => 0,
    'size' => null, // e.g. 6rem, 80px
    'thickness' => null, // e.g. 4px
    'color' => null, // text-primary etc without text- prefix
    'showValue' => true,
    // Accessibilité / échelle
    'min' => 0,
    'max' => 100,
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
    'value' => 0,
    'size' => null, // e.g. 6rem, 80px
    'thickness' => null, // e.g. 4px
    'color' => null, // text-primary etc without text- prefix
    'showValue' => true,
    // Accessibilité / échelle
    'min' => 0,
    'max' => 100,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $lengthToken = function ($value, string $prefix, int $remMultiplier, int $maxRemToken, int $maxPxToken) {
        if (! is_string($value) && ! $value instanceof \Stringable && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d+(?:\.\d+)?)rem$/', $value, $matches) === 1) {
            $token = (int) round(((float) $matches[1]) * $remMultiplier);

            return $token >= 1 && $token <= $maxRemToken ? "{$prefix}-rem-{$token}" : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= $maxPxToken ? "{$prefix}-px-{$token}" : null;
        }

        return null;
    };

    $min = (int) $min;
    $max = max($min + 1, (int) $max);
    $rawValue = is_numeric($value) ? (float) $value : 0.0;
    $percent = (int) round(max(0, min(100, (($rawValue - $min) / ($max - $min)) * 100)));

    $classes = 'radial-progress daisy-radial-value-'.$percent;
    if ($color) $classes .= ' text-'.$color;

    if ($sizeClass = $lengthToken($size, 'daisy-radial-size', 4, 128, 512)) {
        $classes .= ' '.$sizeClass;
    }

    if ($thicknessClass = $lengthToken($thickness, 'daisy-radial-thickness', 100, 200, 64)) {
        $classes .= ' '.$thicknessClass;
    }
?>

<div <?php echo e($attributes->merge(['class' => $classes, 'role' => 'progressbar', 'aria-valuemin' => $min, 'aria-valuemax' => $max, 'aria-valuenow' => $rawValue])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showValue): ?>
        <?php echo e($slot->isNotEmpty() ? $slot : $percent.'%'); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/radial-progress.blade.php ENDPATH**/ ?>