<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'filter',
    'items' => [], // array of labels or [['label' => 'Tab 1', 'checked' => false]]
    'useForm' => true,
    'resetLabel' => '×', // shown for form reset button
    'allLabel' => 'All', // aria-label for filter-reset when not using form
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
    'name' => 'filter',
    'items' => [], // array of labels or [['label' => 'Tab 1', 'checked' => false]]
    'useForm' => true,
    'resetLabel' => '×', // shown for form reset button
    'allLabel' => 'All', // aria-label for filter-reset when not using form
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Choix du tag wrapper : <form> si useForm (permet reset natif), sinon <div>.
    $WrapperTag = $useForm ? 'form' : 'div';
?>


<<?php echo e($WrapperTag); ?> <?php echo e($attributes->merge(['class' => 'filter'])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($useForm): ?>
        
        <input class="btn btn-square" type="reset" value="<?php echo e($resetLabel); ?>" />
    <?php else: ?>
        
        <input class="btn filter-reset" type="radio" name="<?php echo e($name); ?>" aria-label="<?php echo e($allLabel); ?>" />
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            // Extraction du label et de l'état checked (support array ou string simple).
            $label = is_array($item) ? ($item['label'] ?? 'Option '.($i+1)) : $item;
            $checked = is_array($item) ? ($item['checked'] ?? false) : false;
        ?>
        <input class="btn" type="radio" name="<?php echo e($name); ?>" aria-label="<?php echo e($label); ?>" <?php if($checked): echo 'checked'; endif; ?> />
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</<?php echo e($WrapperTag); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/filter.blade.php ENDPATH**/ ?>