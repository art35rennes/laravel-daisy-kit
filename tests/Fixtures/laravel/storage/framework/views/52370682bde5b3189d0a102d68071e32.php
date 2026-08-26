<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'length' => 6,
    'value' => null,
    'numeric' => true,
    'joined' => false,
    'size' => 'md',
    'color' => null,
    'required' => false,
    'label' => null,
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
    'name',
    'length' => 6,
    'value' => null,
    'numeric' => true,
    'joined' => false,
    'size' => 'md',
    'color' => null,
    'required' => false,
    'label' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $length = max(1, min(8, (int) $length));
    $classes = ['otp'];

    if ($joined) {
        $classes[] = 'otp-joined';
    }

    if (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true)) {
        $classes[] = "otp-{$size}";
    }

    if (in_array($color, ['neutral', 'primary', 'secondary', 'accent', 'success', 'info', 'warning', 'error'], true)) {
        $classes[] = "otp-{$color}";
    }

    $inputAttributes = [
        'type' => 'text',
        'name' => $name,
        'value' => old($name, $value),
        'maxlength' => $length,
        'autocomplete' => 'one-time-code',
        'aria-label' => $label ?? \Illuminate\Support\Str::headline($name),
    ];

    if ($numeric) {
        $inputAttributes['inputmode'] = 'numeric';
        $inputAttributes['pattern'] = "[0-9]{{$length}}";
    }
?>

<label <?php echo e($attributes->class($classes)); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($index = 0; $index < $length; $index++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <span></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <input <?php $__currentLoopData = $inputAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $attributeValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($attribute); ?>="<?php echo e($attributeValue); ?>" <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php if($required): echo 'required'; endif; ?> />
</label>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/otp.blade.php ENDPATH**/ ?>