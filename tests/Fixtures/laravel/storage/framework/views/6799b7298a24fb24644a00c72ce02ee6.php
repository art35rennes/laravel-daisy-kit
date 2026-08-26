<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'multiple' => false,
    'accept' => null,
    'disabled' => false,
    // Drag & Drop + Preview
    'dragdrop' => false,
    'preview' => false,
    // Taille max de la zone (classes)
    'dropZoneClass' => 'border border-dashed rounded-box p-4',
    'dropzoneText' => null,
    'helpText' => null,
    'browseText' => null,
    // Classes de mise en page des aperçus (conserve les valeurs actuelles par défaut)
    'previewContainerClass' => null,
    'previewItemClass' => null,
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
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'multiple' => false,
    'accept' => null,
    'disabled' => false,
    // Drag & Drop + Preview
    'dragdrop' => false,
    'preview' => false,
    // Taille max de la zone (classes)
    'dropZoneClass' => 'border border-dashed rounded-box p-4',
    'dropzoneText' => null,
    'helpText' => null,
    'browseText' => null,
    // Classes de mise en page des aperçus (conserve les valeurs actuelles par défaut)
    'previewContainerClass' => null,
    'previewItemClass' => null,
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
    $sizeMap = [
        'xs' => 'file-input-xs',
        'sm' => 'file-input-sm',
        'md' => 'file-input-md',
        'lg' => 'file-input-lg',
        'xl' => 'file-input-xl',
    ];

    $classes = 'file-input w-full';
    if ($variant === 'ghost') $classes .= ' file-input-ghost';
    if ($color) $classes .= ' file-input-'.$color;
    if (isset($sizeMap[$size])) $classes .= ' '.$sizeMap[$size];

    $isMultiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $dropzoneText ??= $isMultiple
        ? 'Glissez-déposez vos fichiers ici'
        : 'Glissez-déposez votre fichier ici';
    $helpText ??= $isMultiple
        ? 'Vous pouvez sélectionner plusieurs fichiers.'
        : 'Un seul fichier sera conservé.';
    $browseText ??= 'Parcourir';
    $previewContainerClass ??= $isMultiple
        ? 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4'
        : 'grid-cols-1 max-w-sm';
    $previewItemClass ??= 'aspect-video';

    $inputAttributes = $attributes;
    if ($accept !== null && ! $inputAttributes->has('accept')) {
        $inputAttributes = $inputAttributes->merge(['accept' => $accept]);
    }
?>
<?php
    $id = $attributes->get('id') ?? 'file-'.uniqid();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$dragdrop && !$preview): ?>
    <input type="file" id="<?php echo e($id); ?>" @multiple($isMultiple) <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($inputAttributes->merge(['class' => $classes])); ?> />
<?php else: ?>
    <div id="<?php echo e($id); ?>-wrap" data-module="<?php echo e($module ?? 'file-input'); ?>" data-fileinput="1" data-preview="<?php echo e($preview ? 'true' : 'false'); ?>" data-multiple="<?php echo e($isMultiple ? 'true' : 'false'); ?>" data-preview-item-class="<?php echo e($previewItemClass); ?>" class="space-y-2">
        <input type="file" id="<?php echo e($id); ?>" @multiple($isMultiple) <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($inputAttributes->merge(['class' => $classes.' hidden'])); ?> />
        <div class="<?php echo e($dropZoneClass); ?> bg-base-100 flex flex-col items-center justify-center gap-2 text-center text-sm" data-dropzone>
            <div class="flex items-center justify-center gap-2">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-cloud-arrow-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 opacity-70']); ?>
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
                <span class="opacity-70"><?php echo e($dropzoneText); ?></span>
            </div>
            <span class="btn btn-ghost btn-xs"><?php echo e($browseText); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($helpText): ?>
                <span class="text-xs text-base-content/60"><?php echo e($helpText); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($preview): ?>
            <div class="<?php echo e($previewContainerClass); ?> grid gap-2" data-previews></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/file-input.blade.php ENDPATH**/ ?>