<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,
    'value' => null,
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'uncheckable' => false, // permet de décocher un radio déjà coché
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
    'name' => null,
    'value' => null,
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'uncheckable' => false, // permet de décocher un radio déjà coché
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
        'xs' => 'radio-xs',
        'sm' => 'radio-sm',
        'md' => 'radio-md',
        'lg' => 'radio-lg',
        'xl' => 'radio-xl',
    ];

    $classes = 'radio';

    if ($color) {
        $classes .= ' radio-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
?>

<input type="radio" <?php if($name): ?> name="<?php echo e($name); ?>" <?php endif; ?> <?php if(!is_null($value)): ?> value="<?php echo e($value); ?>" <?php endif; ?> <?php if($checked): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($attributes->merge(['class' => $classes])); ?> <?php if($uncheckable): ?> data-uncheckable="1" <?php endif; ?> />


<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/radio.blade.php ENDPATH**/ ?>