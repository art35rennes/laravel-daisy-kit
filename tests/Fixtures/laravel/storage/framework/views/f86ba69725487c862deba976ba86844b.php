<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [], // [['title' => '...', 'content' => '...', 'checked' => false, 'open' => false, 'close' => false]]
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'name' => 'accordion',
    'openIndex' => null, // 0-based index
    // Style/utilitaires
    'bgClass' => 'bg-base-100',
    'bordered' => true,
    'itemClass' => '',
    'titleClass' => 'text-lg font-medium',
    'contentClass' => 'text-sm',
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
    'items' => [], // [['title' => '...', 'content' => '...', 'checked' => false, 'open' => false, 'close' => false]]
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'name' => 'accordion',
    'openIndex' => null, // 0-based index
    // Style/utilitaires
    'bgClass' => 'bg-base-100',
    'bordered' => true,
    'itemClass' => '',
    'titleClass' => 'text-lg font-medium',
    'contentClass' => 'text-sm',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Sélection du modificateur d'icône : flèche (arrow) ou plus (plus).
    $collapseModifier = $arrow ? ' collapse-arrow' : ' collapse-plus';
?>


<div class="join join-vertical w-full <?php echo e($bgClass); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            // Gestion de l'état forcé : open/close a priorité sur checked (pour contrôle visuel).
            $forcedState = '';
            if (!empty($item['open'])) $forcedState = ' collapse-open';
            if (!empty($item['close'])) $forcedState = ' collapse-close';
            // Classes de bordure optionnelles.
            $borderClasses = $bordered ? ' card-border' : '';
        ?>
        
        <div class="collapse<?php echo e($collapseModifier); ?><?php echo e($forcedState); ?> join-item<?php echo e($borderClasses); ?> <?php echo e($itemClass); ?>">
            
            <input type="radio" name="<?php echo e($name); ?>" <?php if(($openIndex === $index) || (!is_null($item['checked'] ?? null) && $item['checked'])): echo 'checked'; endif; ?> />
            <div class="collapse-title <?php echo e($titleClass); ?>"><?php echo e($item['title'] ?? ''); ?></div>
            <div class="collapse-content <?php echo e($contentClass); ?>"><?php echo e($item['content'] ?? ''); ?></div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/accordion.blade.php ENDPATH**/ ?>