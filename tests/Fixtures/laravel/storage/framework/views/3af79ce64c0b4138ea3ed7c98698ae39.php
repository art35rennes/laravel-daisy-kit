<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'disabled' => false,
    'inputId' => null,
    'toolbar' => true, // true|false
    'height' => null,  // ex: '20rem'
    'attachments' => false, // pièces jointes Trix
    // Lazy init options
    // false => init auto; 'button'|true => bouton pour init à la demande
    'lazy' => false,
    'lazyButtonLabel' => 'Activer l\'éditeur',
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
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'disabled' => false,
    'inputId' => null,
    'toolbar' => true, // true|false
    'height' => null,  // ex: '20rem'
    'attachments' => false, // pièces jointes Trix
    // Lazy init options
    // false => init auto; 'button'|true => bouton pour init à la demande
    'lazy' => false,
    'lazyButtonLabel' => 'Activer l\'éditeur',
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
    $heightClass = null;
    if ($height) {
        $heightValue = trim((string) $height);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $heightValue, $matches) === 1) {
            $token = (int) round((float) $matches[1]);
            $heightClass = $token >= 1 && $token <= 1200 ? 'daisy-wysiwyg-min-height-px-'.$token : null;
        } elseif (preg_match('/^(\d+(?:\.\d+)?)rem$/', $heightValue, $matches) === 1) {
            $token = (int) round(((float) $matches[1]) * 4);
            $heightClass = $token >= 1 && $token <= 400 ? 'daisy-wysiwyg-min-height-rem-'.$token : null;
        }
    }

    $inputId = $inputId ?: ($name ? 'trix-'.str_replace(['[',']','.'], '-', $name).'-'.uniqid() : 'trix-'.uniqid());
    $classes = 'trix-wrapper';
    $editorClasses = trim('trix-content block w-full '.($heightClass ?? ''));
    $attachmentsAttr = $attachments ? '1' : '0';
    $isDeferred = $lazy === true || $lazy === 1 || $lazy === '1' || $lazy === 'button';
?>

<div <?php echo e($attributes->merge(['class' => $classes, 'data-module' => ($module ?? 'lazy-editors')])); ?> data-trix-attachments="<?php echo e($attachmentsAttr); ?>" <?php if($isDeferred): ?> data-trix-deferred="1" <?php endif; ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDeferred): ?>
        <div class="mb-2">
            <button type="button" class="btn btn-primary btn-sm" data-trix-init-button><?php echo e($lazyButtonLabel); ?></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div data-trix-container <?php if($isDeferred): ?> class="hidden" <?php endif; ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($toolbar): ?>
            <trix-toolbar id="<?php echo e($inputId); ?>-toolbar"></trix-toolbar>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
            <input id="<?php echo e($inputId); ?>-input" type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" />
            <trix-editor input="<?php echo e($inputId); ?>-input" placeholder="<?php echo e($placeholder); ?>" <?php if($disabled): echo 'disabled'; endif; ?>
                class="<?php echo e($editorClasses); ?>"
                <?php if($toolbar): ?> toolbar="<?php echo e($inputId); ?>-toolbar" <?php endif; ?>></trix-editor>
        <?php else: ?>
            <trix-editor placeholder="<?php echo e($placeholder); ?>" <?php if($disabled): echo 'disabled'; endif; ?>
                class="<?php echo e($editorClasses); ?>"
                <?php if($toolbar): ?> toolbar="<?php echo e($inputId); ?>-toolbar" <?php endif; ?>><?php echo $value ?? $slot; ?></trix-editor>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/wysiwyg.blade.php ENDPATH**/ ?>