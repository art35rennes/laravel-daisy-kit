<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'themes' => null,
    'value' => null,
    'name' => 'theme',
    // Variant d'affichage: buttons (join) | dropdown
    'variant' => 'buttons',
    // Taille des boutons: sm | md | lg
    'size' => 'sm',
    // Style ghost sur les items
    'ghost' => true,
    // Texte du déclencheur dropdown
    'label' => 'Theme',
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
    'themes' => null,
    'value' => null,
    'name' => 'theme',
    // Variant d'affichage: buttons (join) | dropdown
    'variant' => 'buttons',
    // Taille des boutons: sm | md | lg
    'size' => 'sm',
    // Style ghost sur les items
    'ghost' => true,
    // Texte du déclencheur dropdown
    'label' => 'Theme',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $themes = $themes ?? \Art35rennes\DaisyKit\Helpers\ThemeHelper::getAllThemes();
    $value = $value ?? \Art35rennes\DaisyKit\Helpers\ThemeHelper::getDefaultTheme();

    $sizeMap = [
        'sm' => 'btn-sm',
        'md' => 'btn-md',
        'lg' => 'btn-lg',
    ];
    $btnSize = $sizeMap[$size] ?? 'btn-sm';
    $itemBase = 'btn theme-controller ' . $btnSize;
    if ($ghost) {
        $itemBase .= ' btn-ghost';
    }

    $controllerAttributes = ['data-module' => 'theme-controller'];

    if (is_string($value) && trim($value) !== '') {
        $controllerAttributes['data-default-theme'] = trim($value);
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'dropdown'): ?>
    <div <?php echo e($attributes->merge(array_merge(['class' => 'dropdown'], $controllerAttributes))); ?>>
        <div tabindex="0" role="button" class="btn m-1">
            <?php echo e($label); ?>

            <svg width="12" height="12" class="inline-block h-2 w-2 fill-current opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2048 2048"><path d="M1799 349l242 241-1017 1017L7 590l242-241 775 775 775-775z"></path></svg>
        </div>
        <ul tabindex="0" class="dropdown-content bg-base-300 rounded-box z-1 w-52 p-2 shadow">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <li>
                    <input type="radio" name="<?php echo e($name); ?>" value="<?php echo e($t); ?>" class="w-full <?php echo e($itemBase); ?> btn-block justify-start" aria-label="<?php echo e(ucfirst($t)); ?>" <?php if($value === $t): echo 'checked'; endif; ?> />
                </li>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </ul>
    </div>
<?php else: ?>
    <div <?php echo e($attributes->merge(array_merge(['class' => 'join'], $controllerAttributes))); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <input type="radio" name="<?php echo e($name); ?>" value="<?php echo e($t); ?>" class="join-item <?php echo e($itemBase); ?>" aria-label="<?php echo e(ucfirst($t)); ?>" <?php if($value === $t): echo 'checked'; endif; ?> />
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/theme-controller.blade.php ENDPATH**/ ?>