<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'description' => null,
    'id' => null,
    // Breakpoint pour passer en 2 colonnes: sm|md|lg|xl|2xl
    'breakpoint' => 'lg',
    // Ratio des colonnes (desktop)
    'categoryWidth' => '1/3', // 1/4|1/3|1/2
    'contentWidth' => '2/3',  // 3/4|2/3|1/2
    // Espacement interne entre colonnes/éléments
    'gap' => 8,
    // Bordure top optionnelle pour séparer visuellement
    'borderTop' => false,
    'stickyAside' => false,
    'actionsAlignment' => 'end', // start | center | end | between
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
    'description' => null,
    'id' => null,
    // Breakpoint pour passer en 2 colonnes: sm|md|lg|xl|2xl
    'breakpoint' => 'lg',
    // Ratio des colonnes (desktop)
    'categoryWidth' => '1/3', // 1/4|1/3|1/2
    'contentWidth' => '2/3',  // 3/4|2/3|1/2
    // Espacement interne entre colonnes/éléments
    'gap' => 8,
    // Bordure top optionnelle pour séparer visuellement
    'borderTop' => false,
    'stickyAside' => false,
    'actionsAlignment' => 'end', // start | center | end | between
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>



<?php
    // Map des breakpoints vers classes grid-cols-12 (évite les classes entièrement dynamiques).
    $bpMap = [
        'sm' => 'sm:grid-cols-12',
        'md' => 'md:grid-cols-12',
        'lg' => 'lg:grid-cols-12',
        'xl' => 'xl:grid-cols-12',
        '2xl' => '2xl:grid-cols-12',
    ];
    $bp = $bpMap[$breakpoint] ?? $bpMap['lg'];

    // Map des spans pour la colonne "catégorie" (titre/description) selon le ratio et le breakpoint.
    $categoryMap = [
        '1/4' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-3',
            'lg' => 'lg:col-span-3',
            'xl' => 'xl:col-span-3',
            '2xl' => '2xl:col-span-3',
        ],
        '1/3' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-4',
            'lg' => 'lg:col-span-4',
            'xl' => 'xl:col-span-4',
            '2xl' => '2xl:col-span-4',
        ],
        '1/2' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-6',
            'lg' => 'lg:col-span-6',
            'xl' => 'xl:col-span-6',
            '2xl' => '2xl:col-span-6',
        ],
    ];

    // Map des spans pour la colonne "contenu" (slot principal) selon le ratio et le breakpoint.
    $contentMap = [
        '3/4' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-9',
            'lg' => 'lg:col-span-9',
            'xl' => 'xl:col-span-9',
            '2xl' => '2xl:col-span-9',
        ],
        '2/3' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-8',
            'lg' => 'lg:col-span-8',
            'xl' => 'xl:col-span-8',
            '2xl' => '2xl:col-span-8',
        ],
        '1/2' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-6',
            'lg' => 'lg:col-span-6',
            'xl' => 'xl:col-span-6',
            '2xl' => '2xl:col-span-6',
        ],
    ];

    $gapValue = is_numeric($gap) ? (int) $gap : 8;
    // Construction de la grille : 1 colonne sur mobile, 12 colonnes à partir du breakpoint.
    $root = 'grid grid-cols-1 '.$bp.' gap-'.$gapValue.($borderTop ? ' pt-8 mt-8 border-t' : '');

    // Sélection des classes selon le breakpoint demandé (fallback : lg).
    $bpKey = in_array($breakpoint, ['sm','md','lg','xl','2xl']) ? $breakpoint : 'lg';
    $categorySpans = $categoryMap[$categoryWidth] ?? $categoryMap['1/3'];
    $contentSpans = $contentMap[$contentWidth] ?? $contentMap['2/3'];

    // Construction des classes pour chaque colonne : espacement vertical + spans responsive.
    $categoryClass = 'space-y-1';
    $contentClass = 'space-y-4';
    $categoryClass .= ' '.($categorySpans[$bpKey] ?? $categoryMap['1/3'][$bpKey]);
    $contentClass .= ' '.($contentSpans[$bpKey] ?? $contentMap['2/3'][$bpKey]);
    $stickyMap = [
        'sm' => 'sm:sticky sm:top-6',
        'md' => 'md:sticky md:top-6',
        'lg' => 'lg:sticky lg:top-6',
        'xl' => 'xl:sticky xl:top-6',
        '2xl' => '2xl:sticky 2xl:top-6',
    ];
    $asideInnerClass = trim(($stickyAside ? ($stickyMap[$bpKey] ?? $stickyMap['lg']).' ' : '').'space-y-1');
    $actionsAlignmentClass = match ($actionsAlignment) {
        'start' => 'justify-start',
        'center' => 'justify-center',
        'between' => 'justify-between',
        default => 'justify-end',
    };
?>

<section <?php echo e($attributes->merge(['id' => $id, 'class' => $root])); ?>>
    <div class="<?php echo e($categoryClass); ?>">
        <div class="<?php echo e($asideInnerClass); ?>">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                        <h2 class="text-base font-medium"><?php echo e($title); ?></h2>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
                        <p class="text-sm text-base-content/70">
                            <?php echo e($description); ?>

                        </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($headerActions)): ?>
                    <div class="shrink-0">
                        <?php echo e($headerActions); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($aside)): ?>
                <div>
                    <?php echo e($aside); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="<?php echo e($contentClass); ?>">
        <?php echo e($slot); ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
            <div class="mt-4 flex items-center <?php echo e($actionsAlignmentClass); ?> gap-3">
                <?php echo e($actions); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/crud-section.blade.php ENDPATH**/ ?>