<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Mode: native (input type=color) | advanced (palette + sliders)
    'mode' => 'advanced',
    // Valeur initiale (hex|rgb|hsl) - stock interne en HSLA
    'value' => '#563d7c',
    'name' => null,
    // Readonly/disabled
    'disabled' => false,
    // Afficher comme dropdown attaché à un bouton/trigger
    'dropdown' => false,
    // Palette d'échantillons (swatches): tableau de lignes de couleurs
    'swatches' => [],
    // Hauteur max des swatches
    'swatchesHeight' => 0,
    // Désactiver sections
    'showPalette' => true,
    'showInputs' => true,
    'showFormatToggle' => true,
    'showAlpha' => true,
    'showHue' => true,
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
    // Mode: native (input type=color) | advanced (palette + sliders)
    'mode' => 'advanced',
    // Valeur initiale (hex|rgb|hsl) - stock interne en HSLA
    'value' => '#563d7c',
    'name' => null,
    // Readonly/disabled
    'disabled' => false,
    // Afficher comme dropdown attaché à un bouton/trigger
    'dropdown' => false,
    // Palette d'échantillons (swatches): tableau de lignes de couleurs
    'swatches' => [],
    // Hauteur max des swatches
    'swatchesHeight' => 0,
    // Désactiver sections
    'showPalette' => true,
    'showInputs' => true,
    'showFormatToggle' => true,
    'showAlpha' => true,
    'showHue' => true,
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
    $id = $attributes->get('id') ?? 'colorpicker-'.uniqid();
    $isNative = ($mode === 'native');
    $chipValue = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) === 1 ? (string) $value : '#563d7c';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNative): ?>
    <input type="color" id="<?php echo e($id); ?>" <?php if($name): ?> name="<?php echo e($name); ?>" <?php endif; ?> value="<?php echo e($value); ?>" <?php echo e($attributes->merge(['class' => 'input w-32'])); ?> <?php if($disabled): echo 'disabled'; endif; ?> />
<?php else: ?>
    <div id="<?php echo e($id); ?>" data-module="<?php echo e($module ?? 'color-picker'); ?>" data-colorpicker="1"
         data-value="<?php echo e($value); ?>"
         data-disabled="<?php echo e($disabled ? 'true' : 'false'); ?>"
         data-dropdown="<?php echo e($dropdown ? 'true' : 'false'); ?>"
         data-swatches='<?php echo json_encode($swatches, 15, 512) ?>'
         data-swatches-height="<?php echo e((int)$swatchesHeight); ?>"
         data-show-palette="<?php echo e($showPalette ? 'true' : 'false'); ?>"
         data-show-inputs="<?php echo e($showInputs ? 'true' : 'false'); ?>"
         data-show-format-toggle="<?php echo e($showFormatToggle ? 'true' : 'false'); ?>"
         data-show-alpha="<?php echo e($showAlpha ? 'true' : 'false'); ?>"
         data-show-hue="<?php echo e($showHue ? 'true' : 'false'); ?>"
         <?php echo e($attributes->merge(['class' => 'inline-block'])); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
            <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" data-colorpicker-input>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dropdown): ?>
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-sm btn-ghost" data-colorpicker-trigger>
                    <input type="color" value="<?php echo e($chipValue); ?>" disabled class="daisy-color-chip-input daisy-color-chip-input-sm mr-2 align-middle" data-colorchip aria-hidden="true" tabindex="-1">
                    <span data-colortext class="align-middle text-sm"><?php echo e($value); ?></span>
                </div>
                <div tabindex="0" class="dropdown-content bg-base-100 rounded-box shadow p-3 z-[1] w-72 max-w-[calc(100vw-2rem)]" data-colorpicker-panel></div>
            </div>
        <?php else: ?>
            <div class="w-72 max-w-full rounded-box bg-base-100 shadow p-3" data-colorpicker-panel></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/color-picker.blade.php ENDPATH**/ ?>