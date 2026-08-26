<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'imageUrl' => null,
    'bordered' => false,
    'compact' => false,
    'side' => false,
    'imageFull' => false,
    'color' => null, // base-100 (default) or any bg-* utility
    // Styles DaisyUI supplémentaires
    'dash' => false, // card-dash
    'size' => 'md',  // xs|sm|md|lg|xl
    // Accessibilité image
    'imageAlt' => '',
    // Résilience média
    'imageClass' => null,   // classes appliquées à l'image si fournies (prioritaires)
    'figureClass' => null,  // classes appliquées au <figure>
    'selectable' => false,
    'checked' => null,
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
    'title' => null,
    'imageUrl' => null,
    'bordered' => false,
    'compact' => false,
    'side' => false,
    'imageFull' => false,
    'color' => null, // base-100 (default) or any bg-* utility
    // Styles DaisyUI supplémentaires
    'dash' => false, // card-dash
    'size' => 'md',  // xs|sm|md|lg|xl
    // Accessibilité image
    'imageAlt' => '',
    // Résilience média
    'imageClass' => null,   // classes appliquées à l'image si fournies (prioritaires)
    'figureClass' => null,  // classes appliquées au <figure>
    'selectable' => false,
    'checked' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon les options (compact, side, imageFull, etc.).
    $root = 'card';
    if ($side) {
        $root .= ' card-side';
    }
    if ($imageFull) {
        $root .= ' image-full';
    }
    if ($bordered) {
        $root .= ' card-border';
    }
    if ($dash) {
        $root .= ' card-dash';
    }

    // Mapping des tailles vers les classes daisyUI (daisyUI 5: card-compact removed; use card-sm).
    $sizeMap = [
        'xs' => 'card-xs',
        'sm' => 'card-sm',
        'md' => 'card-md',
        'lg' => 'card-lg',
        'xl' => 'card-xl',
    ];
    $effectiveSize = $compact ? 'sm' : $size;
    if (isset($sizeMap[$effectiveSize])) {
        $root .= ' '.$sizeMap[$effectiveSize];
    }

    // Couleur de fond : personnalisée ou base-100 par défaut.
    $bgClass = $color ? ' bg-'.$color : ' bg-base-100';
    $root .= $bgClass.' shadow';
    // Colonne pour empiler figure + corps ; éviter flex-col avec card-side (disposition horizontale).
    if (! $side) {
        $root .= ' flex flex-col';
    }
    if ($selectable) {
        $root .= ' cursor-pointer';
    }

    $cardAttributes = $attributes->merge(['class' => $root]);
    if (! is_null($checked)) {
        $cardAttributes = $cardAttributes->merge(['aria-checked' => $checked ? 'true' : 'false']);
    }
?>

<div <?php echo e($cardAttributes); ?>>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl || isset($figure)): ?>
        <?php
            // Classes par défaut pour rendre le média plus résilient selon le layout.
            $defaultImageClass = $imageFull
                ? 'w-full h-full object-cover' // image-full : image en overlay sur la carte.
                : ($side ? 'w-48 sm:w-64 object-cover' : 'w-full h-auto object-contain'); // side : image à côté, sinon image en haut.

            $finalImageClass = trim(($imageClass ?? '') ?: $defaultImageClass);

            // Classes pour le figure : overflow-hidden pour image-full et side (évite les débordements).
            $defaultFigureClass = $imageFull
                ? 'overflow-hidden'
                : ($side ? 'overflow-hidden' : '');

            $finalFigureClass = trim(($figureClass ?? '') ?: $defaultFigureClass);
        ?>
        <figure class="<?php echo e($finalFigureClass); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl): ?>
                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($imageAlt); ?>" class="<?php echo e($finalImageClass); ?>" loading="lazy" />
            <?php else: ?>
                <?php echo e($figure); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </figure>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'card-body flex flex-col flex-1 min-h-0',
        'gap-2' => $title || isset($actions),
    ]); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
            <h2 class="card-title"><?php echo e($title); ?></h2>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['flex-1' => isset($actions)]); ?>"><?php echo e($slot); ?></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
            <div class="card-actions mt-auto justify-end">
                <?php echo e($actions); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/card.blade.php ENDPATH**/ ?>