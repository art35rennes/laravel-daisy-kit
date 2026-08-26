<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'open' => false,
    'disabled' => false,
    // Compact mode reduces paddings/min-height to fit in dense UIs (like sidebars).
    'compact' => false,
    // Méthode d'interaction: checkbox | focus | details
    'method' => 'checkbox',
    // Forcer l'état visuel: null | open | close (non supporté sur details)
    'force' => null,
    // Styles utilitaires
    'bordered' => false,
    'bg' => false,
    'titleClass' => 'text-lg font-medium',
    'contentClass' => 'text-sm',
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
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'open' => false,
    'disabled' => false,
    // Compact mode reduces paddings/min-height to fit in dense UIs (like sidebars).
    'compact' => false,
    // Méthode d'interaction: checkbox | focus | details
    'method' => 'checkbox',
    // Forcer l'état visuel: null | open | close (non supporté sur details)
    'force' => null,
    // Styles utilitaires
    'bordered' => false,
    'bg' => false,
    'titleClass' => 'text-lg font-medium',
    'contentClass' => 'text-sm',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon les options (arrow/plus, force, styles).
    $root = 'collapse';
    $root .= $arrow ? ' collapse-arrow' : ' collapse-plus';
    // États forcés : override l'état par défaut (utile pour animations ou contrôle externe).
    if ($force === 'open') $root .= ' collapse-open';
    if ($force === 'close') $root .= ' collapse-close';
    if ($bordered) $root .= ' card-border';
    if ($bg) $root .= ' bg-base-100';

    if ($compact) {
        // Mode compact : réduit drastiquement les paddings et min-height pour une UI dense (sidebar).
        // Utilise !important pour override les styles daisyUI par défaut.
        $titleClass = trim($titleClass.' !min-h-0 !py-1.5 !px-2 !text-sm');
        $contentClass = trim($contentClass.' !px-2 !pb-1.5 !pt-0');
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method === 'details'): ?>
    
    <details <?php echo e($attributes->merge(['class' => $root])); ?> <?php if($open): ?> open <?php endif; ?>>
        <summary class="collapse-title <?php echo e($titleClass); ?>"><?php echo e($title); ?></summary>
        <div class="collapse-content <?php echo e($contentClass); ?>"><?php echo e($slot); ?></div>
    </details>
<?php elseif($method === 'focus'): ?>
    
    <div <?php echo e($attributes->merge(['class' => $root])); ?> tabindex="0">
        <div class="collapse-title <?php echo e($titleClass); ?>"><?php echo e($title); ?></div>
        <div class="collapse-content <?php echo e($contentClass); ?>"><?php echo e($slot); ?></div>
    </div>
<?php else: ?>
    
    <div <?php echo e($attributes->merge(['class' => $root])); ?>>
        <input type="checkbox" <?php if($open): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> />
        <div class="collapse-title <?php echo e($titleClass); ?>"><?php echo e($title); ?></div>
        <div class="collapse-content <?php echo e($contentClass); ?>"><?php echo e($slot); ?></div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/collapse.blade.php ENDPATH**/ ?>