<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'state' => null, // success|warning|error|info
    'message' => null,
    'as' => 'div', // wrapper tag
    'full' => true, // apply w-full on wrapper
    'hintHidden' => false, // add hidden on hint when not visible
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
    'state' => null, // success|warning|error|info
    'message' => null,
    'as' => 'div', // wrapper tag
    'full' => true, // apply w-full on wrapper
    'hintHidden' => false, // add hidden on hint when not visible
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $hintClass = match($state) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'error' => 'text-error',
        'info' => 'text-info',
        default => 'text-base-content/70',
    };
    $wrapperClasses = trim(($full ? 'w-full ' : '').'flex flex-col gap-1');
?>

<<?php echo e($as); ?> <?php echo e($attributes->merge(['class' => $wrapperClasses])); ?>>
    <?php echo e($slot); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message): ?>
        <p class="validator-hint <?php echo e($hintHidden ? 'hidden' : ''); ?> <?php echo e($hintClass); ?>"><?php echo e($message); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</<?php echo e($as); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/validator.blade.php ENDPATH**/ ?>