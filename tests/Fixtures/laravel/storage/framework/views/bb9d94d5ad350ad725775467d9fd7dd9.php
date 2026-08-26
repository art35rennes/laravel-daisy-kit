<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'position' => 'fixed',
    'placement' => 'top-right',
    'themes' => null,
    'offsetClass' => null,
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
    'position' => 'fixed',
    'placement' => 'top-right',
    'themes' => null,
    'offsetClass' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if (isset($component)) { $__componentOriginald4aaa4b01baa94db46e38e4697384b0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.theme-selector','data' => ['position' => $position,'placement' => $placement,'themes' => $themes,'offsetClass' => $offsetClass,'attributes' => $attributes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.theme-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($position),'placement' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placement),'themes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themes),'offset-class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($offsetClass),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $attributes = $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $component = $__componentOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/partials/theme-selector.blade.php ENDPATH**/ ?>