<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Breakpoint sur lequel on bascule en 2 colonnes.
    // Valeurs supportées: sm|md|lg|xl|2xl
    'breakpoint' => 'lg',
    // Conteneur visuel (permet de limiter à une colonne centrale).
    // Ex.: 'max-w-4xl mx-auto px-4 sm:px-6'
    'container' => 'max-w-4xl mx-auto px-4 sm:px-6',
    // Espacement vertical entre sections.
    'gap' => 12,
    // Props présentes pour compatibilité/évolutions (ratio appliqué par crud-section).
    'categoryWidth' => '1/3',
    'contentWidth' => '2/3',
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
    // Breakpoint sur lequel on bascule en 2 colonnes.
    // Valeurs supportées: sm|md|lg|xl|2xl
    'breakpoint' => 'lg',
    // Conteneur visuel (permet de limiter à une colonne centrale).
    // Ex.: 'max-w-4xl mx-auto px-4 sm:px-6'
    'container' => 'max-w-4xl mx-auto px-4 sm:px-6',
    // Espacement vertical entre sections.
    'gap' => 12,
    // Props présentes pour compatibilité/évolutions (ratio appliqué par crud-section).
    'categoryWidth' => '1/3',
    'contentWidth' => '2/3',
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
    // Normalisation du gap vers une classe Tailwind space-y-*.
    $gapValue = is_numeric($gap) ? (int) $gap : 12;
    $stackClasses = 'space-y-'.$gapValue;
    $actionsAlignmentClass = match ($actionsAlignment) {
        'start' => 'justify-start',
        'center' => 'justify-center',
        'between' => 'justify-between',
        default => 'justify-end',
    };
?>


<div <?php echo e($attributes->merge(['class' => trim($container.' '.$stackClasses)])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($header)): ?>
        <div class="mb-8">
            <?php echo e($header); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo e($slot); ?>


    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
        <div class="mt-8 flex items-center <?php echo e($actionsAlignmentClass); ?> gap-3">
            <?php echo e($actions); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/crud-layout.blade.php ENDPATH**/ ?>