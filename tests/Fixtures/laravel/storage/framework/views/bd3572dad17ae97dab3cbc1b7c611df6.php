<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Déclencheur: click | hover | focus
    'trigger' => 'click',
    // Position: top | right | bottom | left
    'position' => 'top',
    // Ouverture forcée par défaut (sera fermé par JS si interactions)
    'open' => false,
    // Titre simple si pas de slot header
    'title' => null,
    // Classe du panneau (taille)
    'panelClass' => 'w-64',
    // Afficher une flèche directionnelle
    'arrow' => false,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
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
    // Déclencheur: click | hover | focus
    'trigger' => 'click',
    // Position: top | right | bottom | left
    'position' => 'top',
    // Ouverture forcée par défaut (sera fermé par JS si interactions)
    'open' => false,
    // Titre simple si pas de slot header
    'title' => null,
    // Classe du panneau (taille)
    'panelClass' => 'w-64',
    // Afficher une flèche directionnelle
    'arrow' => false,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rootAttrs = $attributes->class('popover-root relative inline-flex w-fit align-middle');
    $posMap = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    ];
    $panelPos = $posMap[$position] ?? $posMap['top'];
    $panelHidden = $open ? '' : 'hidden';
?>

<span <?php echo e($rootAttrs->merge([
    'data-module' => ($module ?? 'popover'),
    'data-popover' => true,
    'data-trigger' => $trigger,
    'data-position' => $position,
])); ?>>
    <span class="popover-trigger inline-flex items-center" tabindex="0">
        <?php echo e($triggerSlot ?? ($triggerContent ?? $slot)); ?>

    </span>
    <div class="popover-panel absolute z-50 <?php echo e($panelPos); ?> <?php echo e($panelHidden); ?>">
        <div class="relative rounded-box bg-base-100 shadow card-border p-4 <?php echo e($panelClass); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($arrow): ?>
                <?php
                    $arrowBase = 'absolute w-3 h-3 rotate-45 bg-base-100 border';
                    $arrowPos = [
                        // Panel au-dessus du trigger → décale d'1px pour cacher la jonction
                        'top' => 'left-1/2 -translate-x-1/2 -bottom-1 border-t-0 border-l-0',
                        // Panel à droite du trigger
                        'right' => '-left-1 top-1/2 -translate-y-1/2 border-t-0 border-r-0',
                        // Panel en-dessous du trigger
                        'bottom' => 'left-1/2 -translate-x-1/2 -top-1 border-b-0 border-r-0',
                        // Panel à gauche du trigger
                        'left' => '-right-1 top-1/2 -translate-y-1/2 border-b-0 border-l-0',
                    ][$position] ?? 'left-1/2 -translate-x-1/2 -bottom-1 border-t-0 border-l-0';
                ?>
                <span class="popover-arrow <?php echo e($arrowBase); ?> <?php echo e($arrowPos); ?>"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($title) || isset($header)): ?>
                <div class="mb-2 font-medium text-base-content/90">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($header)): ?>
                        <?php echo e($header); ?>

                    <?php else: ?>
                        <?php echo e($title); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="text-sm leading-relaxed">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($content)): ?>
                    <?php echo e($content); ?>

                <?php else: ?>
                    <?php echo e($slot); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer)): ?>
                <div class="mt-3 pt-3 border-t">
                    <?php echo e($footer); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</span>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/overlay/popover.blade.php ENDPATH**/ ?>