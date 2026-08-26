<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Couleurs et styles
    'bg' => 'base-100',        // any bg-* token (ex: base-100, neutral, primary)
    'text' => null,            // optional text-* token (ex: base-content, primary-content)
    'shadow' => null,          // null|sm|md|lg
    'rounded' => false,
    'container' => null,
    // Position
    'fixed' => false,
    'fixedPosition' => 'top',  // top|bottom
    // Cacher le center sous un breakpoint (ex: 'lg' → hidden lg:flex)
    'centerHiddenBelow' => null, // sm|md|lg|xl
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
    // Couleurs et styles
    'bg' => 'base-100',        // any bg-* token (ex: base-100, neutral, primary)
    'text' => null,            // optional text-* token (ex: base-content, primary-content)
    'shadow' => null,          // null|sm|md|lg
    'rounded' => false,
    'container' => null,
    // Position
    'fixed' => false,
    'fixedPosition' => 'top',  // top|bottom
    // Cacher le center sous un breakpoint (ex: 'lg' → hidden lg:flex)
    'centerHiddenBelow' => null, // sm|md|lg|xl
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'navbar bg-'.$bg;
    if ($text) $classes .= ' text-'.$text;
    if ($shadow) $classes .= ' shadow';
    if ($rounded) $classes .= ' rounded-box';
    if ($fixed) $classes .= ' fixed '.$fixedPosition.'-0 left-0 right-0 z-50';

    $centerClasses = 'navbar-center';
    if ($centerHiddenBelow && in_array($centerHiddenBelow, ['sm','md','lg','xl'], true)) {
        $centerClasses .= ' hidden '.$centerHiddenBelow.':flex';
    }
?>

<div <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <div class="<?php echo e(trim('daisy-navbar-container '.($container ?: ''))); ?>" <?php if($container): ?> data-navbar-container <?php endif; ?>>
        <div class="navbar-start">
            <?php echo e($start ?? ($brand ?? '')); ?>

        </div>
        <div class="<?php echo e($centerClasses); ?>">
            <?php echo e($center ?? ($nav ?? '')); ?>

        </div>
        <div class="navbar-end">
            <?php echo e($end ?? ($actions ?? '')); ?>

        </div>
    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/navbar.blade.php ENDPATH**/ ?>