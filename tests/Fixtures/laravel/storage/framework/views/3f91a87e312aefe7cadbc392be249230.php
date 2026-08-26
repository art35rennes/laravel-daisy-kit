<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'open' => false,
    'id' => null,
    'title' => null,
    // Positionnement DaisyUI
    // vertical: top | middle | bottom
    'vertical' => 'middle',
    // horizontal: start | end | null
    'horizontal' => null,
    // Afficher le backdrop cliquable pour fermer (méthode dialog)
    'backdrop' => true,
    // Afficher un bouton de fermeture (X) en haut à droite
    'closeButton' => true,
    // Classes supplémentaires sur .modal-box (ex: max-w-xl)
    'boxClass' => '',
    // Responsive & taille
    'responsive' => true,
    // xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl (mappe sur max-w-*)
    'size' => null,
    // Active un scroll interne si le contenu dépasse la hauteur de l'écran
    'scrollable' => true,
    // Déplace le <dialog> sous <body> pour éviter les problèmes de positionnement
    // quand un parent a transform/filter/perspective (fixe => relatif au parent)
    'teleport' => true,
    'closeLabel' => 'Close modal',
    'initialFocus' => null,
    'method' => 'dialog',
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
    'open' => false,
    'id' => null,
    'title' => null,
    // Positionnement DaisyUI
    // vertical: top | middle | bottom
    'vertical' => 'middle',
    // horizontal: start | end | null
    'horizontal' => null,
    // Afficher le backdrop cliquable pour fermer (méthode dialog)
    'backdrop' => true,
    // Afficher un bouton de fermeture (X) en haut à droite
    'closeButton' => true,
    // Classes supplémentaires sur .modal-box (ex: max-w-xl)
    'boxClass' => '',
    // Responsive & taille
    'responsive' => true,
    // xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl (mappe sur max-w-*)
    'size' => null,
    // Active un scroll interne si le contenu dépasse la hauteur de l'écran
    'scrollable' => true,
    // Déplace le <dialog> sous <body> pour éviter les problèmes de positionnement
    // quand un parent a transform/filter/perspective (fixe => relatif au parent)
    'teleport' => true,
    'closeLabel' => 'Close modal',
    'initialFocus' => null,
    'method' => 'dialog',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isPopover = $method === 'popover';
    // Construction des classes CSS pour le positionnement (vertical et horizontal).
    $modalClasses = 'modal';
    // Placement vertical : top (haut), middle (centre, défaut), bottom (bas).
    if (in_array($vertical, ['top','middle','bottom'], true)) {
        $modalClasses .= ' modal-' . $vertical;
    }
    // Placement horizontal : start (gauche), end (droite), null (centré).
    if (in_array($horizontal, ['start','end'], true)) {
        $modalClasses .= ' modal-' . $horizontal;
    }

    // Génération d'un ID unique si manquant (requis pour la téléportation et les triggers).
    if (empty($id)) {
        $id = 'modal-'.\Illuminate\Support\Str::uuid();
    }
    $titleId = $title ? $id.'-title' : null;

    // Préparation des attributs du dialog : classes + état open si spécifié.
    $rootAttrs = $attributes->merge([
        'class' => $modalClasses,
    ]);

    if (! $isPopover) {
        $rootAttrs = $rootAttrs->merge([
            'data-module' => 'modal',
            'data-teleport' => $teleport ? 'true' : 'false',
        ]);
    }
    if ($open && ! $isPopover) {
        $rootAttrs = $rootAttrs->merge(['open' => true]);
    }
    if ($isPopover) {
        $rootAttrs = $rootAttrs->merge(['popover' => true]);
    }
    if ($titleId) {
        $rootAttrs = $rootAttrs->merge(['aria-labelledby' => $titleId]);
    }
    if ($initialFocus) {
        $rootAttrs = $rootAttrs->merge(['data-initial-focus' => $initialFocus]);
    }

    // Mapping des tailles vers les classes max-width Tailwind.
    $sizeToMax = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
    ];
    $maxWidthClass = $sizeToMax[$size] ?? 'max-w-lg';
    $isSideModal = in_array($horizontal, ['start','end'], true);
    // Classes responsive : w-11/12 sur mobile + max-width sur desktop (si responsive activé).
    $boxResponsiveClasses = $responsive ? ('w-11/12 ' . $maxWidthClass) : $maxWidthClass;
    // Les placements start/end se comportent comme des panneaux latéraux pleine hauteur.
    $sideClasses = $isSideModal ? ' h-[100svh] max-h-[100svh] rounded-none' : '';
    // Classes de scroll : limite la hauteur des modales classiques, ou scrolle le panneau latéral.
    $scrollClasses = $scrollable
        ? ($isSideModal ? ' overflow-y-auto' : ' max-h-[calc(100svh-4rem)] overflow-y-auto')
        : '';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPopover): ?>
<div <?php echo e($rootAttrs); ?> <?php if($id): ?> id="<?php echo e($id); ?>" <?php endif; ?>>
<?php else: ?>
<dialog <?php echo e($rootAttrs); ?> <?php if($id): ?> id="<?php echo e($id); ?>" <?php endif; ?>>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <div class="modal-box <?php echo e($boxResponsiveClasses); ?><?php echo e($sideClasses); ?><?php echo e($scrollClasses); ?> <?php echo e($boxClass); ?>">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($header) || $title || $closeButton): ?>
            <div class="flex items-start justify-between gap-4 mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($header)): ?>
                    <div class="min-w-0 flex-1">
                        <?php echo e($header); ?>

                    </div>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                        <h3 id="<?php echo e($titleId); ?>" class="text-lg font-bold"><?php echo e($title); ?></h3>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($closeButton): ?>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-circle btn-ghost shrink-0" 
                        <?php if($isPopover): ?>
                            popovertarget="<?php echo e($id); ?>"
                            popovertargetaction="hide"
                        <?php else: ?>
                            data-modal-close
                        <?php endif; ?>
                        aria-label="<?php echo e($closeLabel); ?>"
                    >
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-x'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <div class="mb-4"><?php echo e($slot); ?></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer) || isset($actions)): ?>
            <div class="modal-action">
                <?php echo e($footer ?? $actions ?? ''); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($backdrop && ! $isPopover): ?>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPopover): ?>
</div>
<?php else: ?>
</dialog>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/overlay/modal.blade.php ENDPATH**/ ?>