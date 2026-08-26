<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'vertical' => true,
    'size' => null, // xs|sm|md|lg|xl
    // Rendre horizontal à partir d'un breakpoint: sm|md|lg|xl (ajoute "sm:menu-horizontal" ...)
    'horizontalAt' => null,
    // Styles de conteneur
    'bg' => true,
    'rounded' => true,
    // Titre optionnel en tête
    'title' => null,
    // Filtrage
    'filterable' => false,
    'filterPlaceholder' => 'Rechercher...',
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
    'vertical' => true,
    'size' => null, // xs|sm|md|lg|xl
    // Rendre horizontal à partir d'un breakpoint: sm|md|lg|xl (ajoute "sm:menu-horizontal" ...)
    'horizontalAt' => null,
    // Styles de conteneur
    'bg' => true,
    'rounded' => true,
    // Titre optionnel en tête
    'title' => null,
    // Filtrage
    'filterable' => false,
    'filterPlaceholder' => 'Rechercher...',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon les options (background, rounded, orientation, taille).
    $classes = 'menu';
    if ($bg) $classes .= ' bg-base-100';
    if ($rounded) $classes .= ' rounded-box';
    // Orientation : vertical par défaut, horizontal si explicitement désactivé ou via breakpoint.
    if (!$vertical) $classes .= ' menu-horizontal';
    // Orientation responsive : devient horizontal à partir d'un breakpoint (ex: md:menu-horizontal).
    if ($horizontalAt && in_array($horizontalAt, ['sm','md','lg','xl'], true)) {
        $classes .= ' '.$horizontalAt.':menu-horizontal';
    }
    if ($size) $classes .= ' menu-'.$size;

    // Générer un ID unique pour le filtre si activé
    $filterId = $filterable ? 'menu-filter-'.uniqid() : null;
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterable): ?>
    <div data-module="menu-filter" class="mb-4">
        <input 
            type="text" 
            data-menu-filter-input
            placeholder="<?php echo e($filterPlaceholder); ?>"
            class="input input-sm w-full"
        />
        <ul <?php echo e($attributes->merge(['class' => $classes, 'data-menu-filter-target' => true])); ?>>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                <li class="menu-title"><?php echo e($title); ?></li>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php echo e($slot); ?>

        </ul>
    </div>
<?php else: ?>
    <ul <?php echo e($attributes->merge(['class' => $classes])); ?>>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
            <li class="menu-title"><?php echo e($title); ?></li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php echo e($slot); ?>

    </ul>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/menu.blade.php ENDPATH**/ ?>