<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'vertical' => true,
    // Styles utilitaires inspirés de la doc
    'bg' => false,          // ajoute bg-base-100
    'rounded' => false,     // ajoute rounded-box
    'shadow' => false,      // true -> shadow (classe daisyUI)
    'title' => null,        // texte en-tête (li spécifique)
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
    // Styles utilitaires inspirés de la doc
    'bg' => false,          // ajoute bg-base-100
    'rounded' => false,     // ajoute rounded-box
    'shadow' => false,      // true -> shadow (classe daisyUI)
    'title' => null,        // texte en-tête (li spécifique)
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'list';
    // Le composant est vertical par défaut. Un mode horizontal n'est pas prévu par DaisyUI pour list
    if ($bg) $classes .= ' bg-base-100';
    if ($rounded) $classes .= ' rounded-box';
    if ($shadow) $classes .= ' shadow';
?>

<ul <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
        <li class="p-4 pb-2 text-xs opacity-60 tracking-wide"><?php echo e($title); ?></li>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($slot); ?>

  </ul>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/list.blade.php ENDPATH**/ ?>