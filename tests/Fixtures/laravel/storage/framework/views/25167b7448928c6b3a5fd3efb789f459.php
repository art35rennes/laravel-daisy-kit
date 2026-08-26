<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Taille du canvas (largeur)
    'width' => 400,
    // Hauteur du canvas
    'height' => 200,
    // Couleur de l'encre
    'penColor' => '#000000',
    // Épaisseur du trait
    'minWidth' => 0.5,
    'maxWidth' => 2.5,
    // Vitesse de dessin (0-1)
    'velocityFilterWeight' => 0.7,
    // Mode responsive (ajuste automatiquement la taille)
    'responsive' => true,
    // Désactivé
    'disabled' => false,
    // Afficher les boutons d'action (effacer, télécharger)
    'showActions' => true,
    // Label pour le bouton effacer
    'clearLabel' => null,
    // Label pour le bouton télécharger
    'downloadLabel' => null,
    // Format de téléchargement (png, jpg, svg)
    'downloadFormat' => 'png',
    // Nom du fichier de téléchargement
    'downloadFilename' => 'signature',
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
    // Valeur initiale du champ caché
    'value' => null,
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
    // Taille du canvas (largeur)
    'width' => 400,
    // Hauteur du canvas
    'height' => 200,
    // Couleur de l'encre
    'penColor' => '#000000',
    // Épaisseur du trait
    'minWidth' => 0.5,
    'maxWidth' => 2.5,
    // Vitesse de dessin (0-1)
    'velocityFilterWeight' => 0.7,
    // Mode responsive (ajuste automatiquement la taille)
    'responsive' => true,
    // Désactivé
    'disabled' => false,
    // Afficher les boutons d'action (effacer, télécharger)
    'showActions' => true,
    // Label pour le bouton effacer
    'clearLabel' => null,
    // Label pour le bouton télécharger
    'downloadLabel' => null,
    // Format de téléchargement (png, jpg, svg)
    'downloadFormat' => 'png',
    // Nom du fichier de téléchargement
    'downloadFilename' => 'signature',
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
    // Valeur initiale du champ caché
    'value' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $id = $attributes->get('id') ?? 'sign-'.uniqid();
    $canvasWidth = max(1, (int) $width);
    $canvasHeight = max(1, (int) $height);
    $clearLabel = $clearLabel ?? __('daisy::common.clear');
    $downloadLabel = $downloadLabel ?? __('daisy::common.download');
?>

<div id="<?php echo e($id); ?>" 
     data-module="<?php echo e($module ?? 'sign'); ?>" 
     data-sign="1"
     data-width="<?php echo e($canvasWidth); ?>"
     data-height="<?php echo e($canvasHeight); ?>"
     data-pen-color="<?php echo e($penColor); ?>"
     data-min-width="<?php echo e((float)$minWidth); ?>"
     data-max-width="<?php echo e((float)$maxWidth); ?>"
     data-velocity-filter-weight="<?php echo e((float)$velocityFilterWeight); ?>"
     data-responsive="<?php echo e($responsive ? 'true' : 'false'); ?>"
     data-disabled="<?php echo e($disabled ? 'true' : 'false'); ?>"
     data-show-actions="<?php echo e($showActions ? 'true' : 'false'); ?>"
     data-clear-label="<?php echo e($clearLabel); ?>"
     data-download-label="<?php echo e($downloadLabel); ?>"
     data-download-format="<?php echo e($downloadFormat); ?>"
     data-download-filename="<?php echo e($downloadFilename); ?>"
     <?php echo e($attributes->merge(['class' => 'sign-container daisy-sign-container'])); ?>>
    
    <div class="card card-border bg-base-100 daisy-sign-card">
        <div class="card-body p-4">
            <div class="daisy-sign-canvas-wrapper relative overflow-hidden rounded-box card-border bg-base-200"
                 data-sign-canvas-wrapper>
                <canvas data-sign-canvas
                        width="<?php echo e($canvasWidth); ?>"
                        height="<?php echo e($canvasHeight); ?>"
                        class="daisy-sign-canvas"></canvas>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
                <div class="card-actions justify-end mt-4 gap-2 flex-wrap">
                    <button type="button" 
                            class="btn btn-sm btn-ghost" 
                            data-sign-clear
                            <?php if($disabled): echo 'disabled'; endif; ?>>
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-eraser'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
                        <?php echo e($clearLabel); ?>

                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-primary" 
                            data-sign-download
                            <?php if($disabled): echo 'disabled'; endif; ?>>
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-download'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
                        <?php echo e($downloadLabel); ?>

                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <input type="hidden" 
                   name="<?php echo e($attributes->get('name', 'signature')); ?>" 
                   data-sign-input
                   value="<?php echo e(is_scalar($value) ? $value : ''); ?>" />
        </div>
    </div>
</div>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/sign.blade.php ENDPATH**/ ?>