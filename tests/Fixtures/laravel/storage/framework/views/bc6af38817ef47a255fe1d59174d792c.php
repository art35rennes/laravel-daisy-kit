<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Mode d'affichage: inline (ancré au déclencheur) ou modal (centre de l'écran)
    'mode' => 'inline', // inline | modal
    // Position pour le mode inline
    // pris en charge: top | right | bottom | left
    'position' => 'bottom',
    // Contenu du message
    'message' => 'Êtes-vous sûr ?'
        ,
    // Texte des boutons
    'okText' => 'OK',
    'cancelText' => 'Annuler',
    // Classes DaisyUI/Tailwind des boutons
    'okClass' => 'btn-primary',
    'cancelClass' => 'btn-secondary',
    // Largeur du panneau inline
    'panelClass' => 'w-72',
    // Id facultatif (utilisé surtout pour modal)
    'id' => null,
    // Titre de modal (mode modal)
    'title' => null,
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
    // Mode d'affichage: inline (ancré au déclencheur) ou modal (centre de l'écran)
    'mode' => 'inline', // inline | modal
    // Position pour le mode inline
    // pris en charge: top | right | bottom | left
    'position' => 'bottom',
    // Contenu du message
    'message' => 'Êtes-vous sûr ?'
        ,
    // Texte des boutons
    'okText' => 'OK',
    'cancelText' => 'Annuler',
    // Classes DaisyUI/Tailwind des boutons
    'okClass' => 'btn-primary',
    'cancelClass' => 'btn-secondary',
    // Largeur du panneau inline
    'panelClass' => 'w-72',
    // Id facultatif (utilisé surtout pour modal)
    'id' => null,
    // Titre de modal (mode modal)
    'title' => null,
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
    $isModal = ($mode === 'modal');
    $rootAttrs = $attributes->class('relative inline-block');

    // Mapping des positions inline vers classes utilitaires
    $posMap = [
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    ];
    $panelPos = $posMap[$position] ?? $posMap['bottom'];

    // Id auto si manquant en mode modal
    if ($isModal && empty($id)) {
        $id = 'popconfirm-modal-'.\Illuminate\Support\Str::uuid();
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isModal): ?>
    <span <?php echo e($rootAttrs->merge(['data-module' => ($module ?? 'popconfirm')])); ?>>
        <span class="popconfirm-trigger" data-popconfirm-modal="<?php echo e($id); ?>">
            <?php echo e($trigger ?? $slot); ?>

        </span>

        <?php if (isset($component)) { $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.modal','data' => ['id' => $id,'title' => $title,'backdrop' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($id),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'backdrop' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="flex items-start gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($icon)): ?>
                    <span class="mt-1 shrink-0"><?php echo e($icon); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="grow"><?php echo e($message); ?></div>
            </div>
             <?php $__env->slot('actions', null, []); ?> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cancelText !== ''): ?>
                    <button type="button"
                        class="btn <?php echo e($cancelClass); ?>"
                        data-popconfirm-action="cancel"
                        data-popconfirm-modal-target="<?php echo e($id); ?>">
                        <?php echo e($cancelText); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($okText !== ''): ?>
                    <button type="button"
                        class="btn <?php echo e($okClass); ?>"
                        data-popconfirm-action="confirm"
                        data-popconfirm-modal-target="<?php echo e($id); ?>">
                        <?php echo e($okText); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $attributes = $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $component = $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
    </span>
<?php else: ?>
    <span <?php echo e($rootAttrs->merge(['data-module' => ($module ?? 'popconfirm'), 'data-popconfirm' => true, 'data-position' => $position])); ?>>
        <span class="popconfirm-trigger cursor-pointer select-none inline-flex items-center" tabindex="0">
            <?php echo e($trigger ?? $slot); ?>

        </span>
        <div class="popconfirm-panel <?php echo e($panelClass); ?> absolute z-50 <?php echo e($panelPos); ?> hidden">
            <div class="rounded-box bg-base-100 shadow card-border p-4">
                <div class="flex items-start gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($icon)): ?>
                        <span class="mt-1 shrink-0"><?php echo e($icon); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="grow text-sm"><?php echo e($message); ?></div>
                </div>
                <div class="mt-3 flex justify-end gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cancelText !== ''): ?>
                        <button type="button" class="btn btn-sm <?php echo e($cancelClass); ?>" data-popconfirm-action="cancel"><?php echo e($cancelText); ?></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($okText !== ''): ?>
                        <button type="button" class="btn btn-sm <?php echo e($okClass); ?>" data-popconfirm-action="confirm"><?php echo e($okText); ?></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/overlay/popconfirm.blade.php ENDPATH**/ ?>