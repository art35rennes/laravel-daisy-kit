<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'gap' => 4,
    'align' => 'start',   // start|center|end
    'class' => '',
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
    'gap' => 4,
    'align' => 'start',   // start|center|end
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $gapValue = is_numeric($gap) ? (string) ((int) $gap) : (string) $gap;
    $alignMap = [
        'start' => 'items-start',
        'center' => 'items-center',
        'end' => 'items-end',
    ];
    $alignClass = $alignMap[$align] ?? $alignMap['start'];

    $rootClasses = 'daisy-grid grid grid-cols-12 gap-'.$gapValue.' '.$alignClass;
    if (! empty($class)) {
        $rootClasses .= ' '.$class;
    }
?>

<div <?php echo e($attributes->merge(['class' => trim($rootClasses)])); ?>>
    <?php echo e($slot); ?>

    
    
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/grid-layout.blade.php ENDPATH**/ ?>