<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'text' => null,
    // Direction: by défaut vertical. Utiliser horizontal=true ou horizontalAt (ex: 'lg') pour horizontal
    'horizontal' => false,
    'horizontalAt' => null, // ex: 'lg' → lg:divider-horizontal
    // Couleur: neutral|primary|secondary|accent|success|warning|info|error
    'color' => null,
    // Placement du texte: start|end|null
    'position' => null,
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
    'text' => null,
    // Direction: by défaut vertical. Utiliser horizontal=true ou horizontalAt (ex: 'lg') pour horizontal
    'horizontal' => false,
    'horizontalAt' => null, // ex: 'lg' → lg:divider-horizontal
    // Couleur: neutral|primary|secondary|accent|success|warning|info|error
    'color' => null,
    // Placement du texte: start|end|null
    'position' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'divider';
    if ($horizontal) {
        $classes .= ' divider-horizontal';
    } elseif ($horizontalAt) {
        $classes .= ' '.$horizontalAt.':divider-horizontal';
    }
    if ($color) $classes .= ' divider-'.$color;
    if (in_array($position, ['start','end'], true)) $classes .= ' divider-'.$position;
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($text ?? $slot); ?>

</div>


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/divider.blade.php ENDPATH**/ ?>