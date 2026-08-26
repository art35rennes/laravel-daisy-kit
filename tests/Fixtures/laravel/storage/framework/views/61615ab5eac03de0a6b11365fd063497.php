<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'rating',
    'count' => 5,
    'value' => 0,
    'half' => false,
    'shape' => 'star-2', // star|star-2|heart|circle etc (mask-*)
    'color' => null, // bg-primary etc without bg- prefix
    'readOnly' => false,
    'size' => null, // xs|sm|md|lg|xl
    'clearable' => false, // ajoute un input rating-hidden pour pouvoir clear
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
    'name' => 'rating',
    'count' => 5,
    'value' => 0,
    'half' => false,
    'shape' => 'star-2', // star|star-2|heart|circle etc (mask-*)
    'color' => null, // bg-primary etc without bg- prefix
    'readOnly' => false,
    'size' => null, // xs|sm|md|lg|xl
    'clearable' => false, // ajoute un input rating-hidden pour pouvoir clear
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon les options (half, size).
    $wrapper = 'rating';
    if ($half) $wrapper .= ' rating-half';
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) $wrapper .= ' rating-'.$size;
    // Base de la classe mask : définit la forme (star, heart, circle, etc.).
    $maskBase = 'mask mask-'.$shape;
    // Couleur : personnalisée ou warning par défaut (jaune/orange pour les étoiles).
    $colorClass = $color ? ' bg-'.$color : ' bg-warning';
?>

<div <?php echo e($attributes->merge(['class' => $wrapper])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($readOnly): ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= $count; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php 
                // Détection de l'item actuel : correspond à la valeur affichée.
                $isCurrent = (!$half && (int)$value === $i) || ($half && ($value === $i || $value === ($i - 0.5))); 
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$half): ?>
                
                <div class="<?php echo e($maskBase); ?><?php echo e($colorClass ? ' '.$colorClass : ''); ?>" aria-label="<?php echo e($i); ?> star" <?php if($isCurrent): ?> aria-current="true" <?php endif; ?>></div>
            <?php else: ?>
                
                <div class="mask mask-half-1 <?php echo e($maskBase); ?><?php echo e($colorClass ? ' '.$colorClass : ''); ?>" aria-label="<?php echo e(($i - 0.5)); ?> star" <?php if($value === ($i - 0.5)): ?> aria-current="true" <?php endif; ?>></div>
                <div class="mask mask-half-2 <?php echo e($maskBase); ?><?php echo e($colorClass ? ' '.$colorClass : ''); ?>" aria-label="<?php echo e($i); ?> star" <?php if($value === $i): ?> aria-current="true" <?php endif; ?>></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php else: ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($clearable): ?>
            
            <input type="radio" name="<?php echo e($name); ?>" value="0" class="rating-hidden" aria-label="Clear rating" />
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$half): ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= $count; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <input type="radio" name="<?php echo e($name); ?>" class="<?php echo e($maskBase); ?><?php echo e($colorClass ? ' '.$colorClass : ''); ?>" aria-label="<?php echo e($i); ?> star" value="<?php echo e($i); ?>" <?php if($value == $i): echo 'checked'; endif; ?> />
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php else: ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= $count; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <input type="radio" name="<?php echo e($name); ?>" class="mask mask-half-1 <?php echo e($maskBase); ?><?php echo e($colorClass ? ' '.$colorClass : ''); ?>" aria-label="<?php echo e(($i - 0.5)); ?> star" value="<?php echo e(($i - 0.5)); ?>" <?php if($value == ($i - 0.5)): echo 'checked'; endif; ?> />
                <input type="radio" name="<?php echo e($name); ?>" class="mask mask-half-2 <?php echo e($maskBase); ?><?php echo e($colorClass ? ' '.$colorClass : ''); ?>" aria-label="<?php echo e($i); ?> star" value="<?php echo e($i); ?>" <?php if($value == $i): echo 'checked'; endif; ?> />
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/rating.blade.php ENDPATH**/ ?>