<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Local container (default) ou global
    'global' => false,
    // Couleur de la barre
    'color' => '#3b82f6',
    // Hauteur (ex: 6px, 0.5rem)
    'height' => '10px',
    // Décalage top (px) pour sticky/fixed
    'offset' => 0,
    // Container CSS selector (optionnel). Si null: détection auto du conteneur scrollable, ou window si global
    'container' => null,
    // Modal trigger
    'scroll' => null,          // pourcentage (0..100) à partir duquel on ouvre la modal
    'target' => null,          // sélecteur (ex: #myModal) d'un <dialog>
    'openOnce' => true,        // n'ouvrir qu'une fois (true) ou à chaque dépassement (false)
    // Classes supplémentaires
    'class' => '',
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
    // Local container (default) ou global
    'global' => false,
    // Couleur de la barre
    'color' => '#3b82f6',
    // Hauteur (ex: 6px, 0.5rem)
    'height' => '10px',
    // Décalage top (px) pour sticky/fixed
    'offset' => 0,
    // Container CSS selector (optionnel). Si null: détection auto du conteneur scrollable, ou window si global
    'container' => null,
    // Modal trigger
    'scroll' => null,          // pourcentage (0..100) à partir duquel on ouvre la modal
    'target' => null,          // sélecteur (ex: #myModal) d'un <dialog>
    'openOnce' => true,        // n'ouvrir qu'une fois (true) ou à chaque dépassement (false)
    // Classes supplémentaires
    'class' => '',
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
    $attrs = [
        'data-scrollstatus' => '1',
        'data-global' => $global ? 'true' : 'false',
        'data-color' => $color,
        'data-height' => $height,
        'data-offset' => (string)($offset ?? 0),
    ];
    if ($container) $attrs['data-container'] = $container;
    if ($scroll !== null) $attrs['data-scroll'] = (string)$scroll;
    if ($target) $attrs['data-target'] = $target;
    if ($openOnce === false) $attrs['data-open-once'] = 'false';
?>

<div <?php echo e($attributes->merge(['class' => trim('daisy-scroll-status w-full '.$class), 'data-module' => ($module ?? 'scroll-status')])->merge($attrs)); ?>>
    <progress class="daisy-scroll-status-progress" data-scrollstatus-progress max="100" value="0"></progress>
</div>


<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/scroll-status.blade.php ENDPATH**/ ?>