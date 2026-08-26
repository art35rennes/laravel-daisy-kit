

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Content container
    'container' => 'container mx-auto p-6',
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
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Content container
    'container' => 'container mx-auto p-6',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $slot = $slot ?? '';
?>

<?php if (isset($component)) { $__componentOriginal8ae823e922000497b0e9a660b4733032 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ae823e922000497b0e9a660b4733032 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.navbar-layout','data' => ['title' => $title,'theme' => $theme,'navbarBg' => $navbarBg,'navbarText' => $navbarText,'navbarShadow' => $navbarShadow,'navbarFixed' => $navbarFixed,'navbarFixedPosition' => $navbarFixedPosition,'container' => $container]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.navbar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'navbarBg' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarBg),'navbarText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarText),'navbarShadow' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarShadow),'navbarFixed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarFixed),'navbarFixedPosition' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarFixedPosition),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($container)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php echo e($slot); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ae823e922000497b0e9a660b4733032)): ?>
<?php $attributes = $__attributesOriginal8ae823e922000497b0e9a660b4733032; ?>
<?php unset($__attributesOriginal8ae823e922000497b0e9a660b4733032); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ae823e922000497b0e9a660b4733032)): ?>
<?php $component = $__componentOriginal8ae823e922000497b0e9a660b4733032; ?>
<?php unset($__componentOriginal8ae823e922000497b0e9a660b4733032); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/layout/navbar.blade.php ENDPATH**/ ?>