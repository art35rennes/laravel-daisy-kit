<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'end' => false,
    'hover' => false,
    /** @var bool Add daisyUI dropdown-close. Ignored with hover because it forces the menu closed. */
    'forceClose' => false,
    // Classes du bouton trigger (par défaut adapté à la navbar)
    'buttonClass' => 'btn btn-ghost',
    // Afficher le trigger en bouton circulaire (utile en navbar pour avatar/icone)
    'buttonCircle' => false,
    // Type de contenu: 'menu' (UL/menu) ou 'card' (helper dropdown)
    'type' => 'menu', // menu | card
    // Classes du contenu (prioritaire). Si null, on déduit selon type
    'contentClass' => null,
    // Compat: classes du menu (héritage v1)
    'menuClass' => 'menu menu-sm dropdown-content bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm',
    // Classes pour le helper dropdown (carte)
    'cardClass' => 'card card-sm dropdown-content bg-base-100 z-1 w-64 shadow-md',
    'cardBodyClass' => 'card-body',
    'id' => null,
    'triggerLabel' => null,
    'contentRole' => null,
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
    'label' => null,
    'end' => false,
    'hover' => false,
    /** @var bool Add daisyUI dropdown-close. Ignored with hover because it forces the menu closed. */
    'forceClose' => false,
    // Classes du bouton trigger (par défaut adapté à la navbar)
    'buttonClass' => 'btn btn-ghost',
    // Afficher le trigger en bouton circulaire (utile en navbar pour avatar/icone)
    'buttonCircle' => false,
    // Type de contenu: 'menu' (UL/menu) ou 'card' (helper dropdown)
    'type' => 'menu', // menu | card
    // Classes du contenu (prioritaire). Si null, on déduit selon type
    'contentClass' => null,
    // Compat: classes du menu (héritage v1)
    'menuClass' => 'menu menu-sm dropdown-content bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm',
    // Classes pour le helper dropdown (carte)
    'cardClass' => 'card card-sm dropdown-content bg-base-100 z-1 w-64 shadow-md',
    'cardBodyClass' => 'card-body',
    'id' => null,
    'triggerLabel' => null,
    'contentRole' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon les options (placement, hover).
    $root = 'dropdown';
    // Placement : dropdown-end pour aligner à droite (défaut : gauche).
    if ($end) $root .= ' dropdown-end';
    // Mode hover : ouverture au survol au lieu du clic (dropdown-hover).
    if ($hover) {
        $root .= ' dropdown-hover';
    }
    if ($forceClose && ! $hover) {
        $root .= ' dropdown-close';
    }

    // Déduction des classes de contenu selon le type si non fourni explicitement.
    $resolvedContentClass = $contentClass ?? ($type === 'card' ? $cardClass : $menuClass);
    $dropdownId = $id ?: 'dropdown-'.\Illuminate\Support\Str::uuid();
    $contentId = $dropdownId.'-content';
    $resolvedTriggerLabel = $triggerLabel ?: (is_string($label) ? $label : __('daisy::components.dropdown_open'));
    $resolvedContentRole = $contentRole ?: ($type === 'card' ? 'dialog' : 'menu');
?>


<div id="<?php echo e($dropdownId); ?>" <?php echo e($attributes->merge(['class' => $root])); ?>>
    
    <div tabindex="0" role="button" class="<?php echo e($buttonClass); ?><?php echo e($buttonCircle ? ' btn-circle' : ''); ?>" aria-label="<?php echo e($resolvedTriggerLabel); ?>" aria-controls="<?php echo e($contentId); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($trigger)): ?>
            <?php echo e($trigger); ?>

        <?php else: ?>
            <?php echo e($label ?? $resolvedTriggerLabel); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'card'): ?>
        
        <div id="<?php echo e($contentId); ?>" tabindex="0" role="<?php echo e($resolvedContentRole); ?>" class="<?php echo e($resolvedContentClass); ?>">
            <div class="<?php echo e($cardBodyClass); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($content)): ?>
                    <?php echo e($content); ?>

                <?php elseif(isset($slot)): ?>
                    <?php echo e($slot); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        
        <ul id="<?php echo e($contentId); ?>" tabindex="0" role="<?php echo e($resolvedContentRole); ?>" class="<?php echo e($resolvedContentClass); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($content)): ?>
                <?php echo e($content); ?>

            <?php elseif(isset($slot)): ?>
                <?php echo e($slot); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 </div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/overlay/dropdown.blade.php ENDPATH**/ ?>