<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'text' => null,
    'open' => false,
    'position' => 'top', // top|right|bottom|left
    'color' => null, // neutral|primary|secondary|accent|info|success|warning|error
    'alignment' => null, // start|center|end
    // Utiliser un contenu personnalisé au lieu de data-tip
    'content' => null,
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
    'text' => null,
    'open' => false,
    'position' => 'top', // top|right|bottom|left
    'color' => null, // neutral|primary|secondary|accent|info|success|warning|error
    'alignment' => null, // start|center|end
    // Utiliser un contenu personnalisé au lieu de data-tip
    'content' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'tooltip';
    
    // Position
    $validPositions = ['top','right','bottom','left'];
    if (in_array($position, $validPositions)) {
        $classes .= ' tooltip-'.$position;
    }
    
    // Open state
    if ($open) {
        $classes .= ' tooltip-open';
    }
    
    // Color
    $validColors = ['neutral','primary','secondary','accent','info','success','warning','error'];
    if ($color && in_array($color, $validColors)) {
        $classes .= ' tooltip-'.$color;
    }

    if (in_array($alignment, ['start', 'center', 'end'], true)) {
        $classes .= ' tooltip-'.$alignment;
    }
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!is_null($text) && empty($content) && !isset($contentSlot)): ?>
        <div class="tooltip-content">
            <?php echo e($text); ?>

        </div>
    <?php elseif(!empty($content) || isset($contentSlot)): ?>
        <div class="tooltip-content">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($contentSlot)): ?>
                <?php echo e($contentSlot); ?>

            <?php else: ?>
                <?php echo e($content); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($slot); ?>

</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/overlay/tooltip.blade.php ENDPATH**/ ?>