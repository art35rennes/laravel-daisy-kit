<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::common.loading'),
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('daisy::common.loading'),
    'size' => 'lg',
    'fullScreen' => false,
    'skeletonCount' => 3, // For skeleton type
    'theme' => null,
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
    'title' => __('daisy::common.loading'),
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('daisy::common.loading'),
    'size' => 'lg',
    'fullScreen' => false,
    'skeletonCount' => 3, // For skeleton type
    'theme' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $containerClass = $fullScreen ? 'min-h-screen' : 'min-h-[calc(100vh-8rem)]';
?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="<?php echo e($containerClass); ?> flex items-center justify-center">
        <?php if (isset($component)) { $__componentOriginalf52c05ee0bff6db7cee754dc0891e014 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf52c05ee0bff6db7cee754dc0891e014 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.errors.loading-state-content','data' => ['type' => $type,'message' => $message,'size' => $size,'skeletonCount' => $skeletonCount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.errors.loading-state-content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($type),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'skeletonCount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($skeletonCount)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf52c05ee0bff6db7cee754dc0891e014)): ?>
<?php $attributes = $__attributesOriginalf52c05ee0bff6db7cee754dc0891e014; ?>
<?php unset($__attributesOriginalf52c05ee0bff6db7cee754dc0891e014); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf52c05ee0bff6db7cee754dc0891e014)): ?>
<?php $component = $__componentOriginalf52c05ee0bff6db7cee754dc0891e014; ?>
<?php unset($__componentOriginalf52c05ee0bff6db7cee754dc0891e014); ?>
<?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $attributes = $__attributesOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__attributesOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $component = $__componentOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__componentOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/errors/loading-state.blade.php ENDPATH**/ ?>