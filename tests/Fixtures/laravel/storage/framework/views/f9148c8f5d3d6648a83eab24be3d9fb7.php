<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'legend' => null,
    // Helpers pour fond/bordure arrondi, afin de refléter la doc plus facilement
    'bg' => null,        // ex: base-200
    'bordered' => false, // ajoute border + border-base-300
    'rounded' => true,   // rounded-box
    'padding' => 'p-4',
    'width' => null,     // ex: w-xs
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
    'legend' => null,
    // Helpers pour fond/bordure arrondi, afin de refléter la doc plus facilement
    'bg' => null,        // ex: base-200
    'bordered' => false, // ajoute border + border-base-300
    'rounded' => true,   // rounded-box
    'padding' => 'p-4',
    'width' => null,     // ex: w-xs
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'fieldset';
    if ($bg) $classes .= ' bg-'.$bg;
    if ($bordered) $classes .= ' card-border';
    if ($rounded) $classes .= ' rounded-box';
    if ($width) $classes .= ' '.$width;
    if ($padding) $classes .= ' '.$padding;
?>

<fieldset <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($legend): ?>
        <legend class="fieldset-legend"><?php echo e($legend); ?></legend>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($slot); ?>

</fieldset>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/fieldset.blade.php ENDPATH**/ ?>