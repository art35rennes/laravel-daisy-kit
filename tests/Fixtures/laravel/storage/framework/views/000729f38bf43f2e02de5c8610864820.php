<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
    'name' => null,
    'id' => null,
    'value' => '1',
    'uncheckedValue' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
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
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
    'name' => null,
    'id' => null,
    'value' => '1',
    'uncheckedValue' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
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
        'xs' => 'checkbox-xs',
        'sm' => 'checkbox-sm',
        'md' => 'checkbox-md',
        'lg' => 'checkbox-lg',
        'xl' => 'checkbox-xl',
    ];

    $classes = 'checkbox';

    if ($color) {
        $classes .= ' checkbox-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first($name) : null);
    $hasError = filled($errorMessage);

    if ($hasError) {
        $classes .= ' checkbox-error';
    }

    $checkboxId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $oldInputExists = $name && data_get(session()->get('_old_input', []), $name, '__missing__') !== '__missing__';
    $oldInputValue = $name ? data_get(session()->get('_old_input', []), $name) : null;
    $resolvedChecked = $bindOld && $oldInputExists
        ? in_array((string) $oldInputValue, ['1', 'true', 'on', (string) $value], true)
        : (bool) $checked;
    $resolvedDescribedBy = $describedBy ?: ($hasError && $checkboxId ? $checkboxId.'-error' : null);
    $checkboxAttributes = $attributes
        ->merge(['class' => $classes])
        ->merge(array_filter([
            'id' => $checkboxId,
            'name' => $name,
            'value' => $value,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $resolvedDescribedBy,
        ], static fn ($attributeValue) => ! is_null($attributeValue)));
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name && ! is_null($uncheckedValue)): ?>
    <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($uncheckedValue); ?>">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<input type="checkbox" <?php if($resolvedChecked && !$indeterminate): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> <?php if($indeterminate): ?> aria-checked="mixed" data-indeterminate="true" <?php endif; ?> <?php echo e($checkboxAttributes); ?> />

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($indeterminate): ?>
	<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/checkbox.blade.php ENDPATH**/ ?>