<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'inputId' => null,
    'value' => null,
    'placeholder' => null,
    'min' => null,
    'max' => null,
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
    'inputId' => null,
    'value' => null,
    'placeholder' => null,
    'min' => null,
    'max' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<input
    type="date"
    <?php if(!is_null($inputId)): ?> id="<?php echo e($inputId); ?>" <?php endif; ?>
    <?php if(!is_null($value)): ?> value="<?php echo e($value); ?>" <?php endif; ?>
    <?php if(!is_null($placeholder)): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
    <?php if(!is_null($min)): ?> min="<?php echo e($min); ?>" <?php endif; ?>
    <?php if(!is_null($max)): ?> max="<?php echo e($max); ?>" <?php endif; ?>
    <?php echo e($attributes->merge(['class' => 'input daisy-native-picker-date'])); ?>

/>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/calendar-native.blade.php ENDPATH**/ ?>