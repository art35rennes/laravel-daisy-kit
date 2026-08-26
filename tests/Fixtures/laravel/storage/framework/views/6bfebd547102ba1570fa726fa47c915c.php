<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => 'bi-inbox',
    'title' => __('daisy::common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'actionVariant' => 'primary',
    'size' => 'md',
    'illustration' => null, // Custom illustration image
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
    'icon' => 'bi-inbox',
    'title' => __('daisy::common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'actionVariant' => 'primary',
    'size' => 'md',
    'illustration' => null, // Custom illustration image
    'theme' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

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

    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
        <?php if (isset($component)) { $__componentOriginal5efd72db83161bae6e1dc57d5f89d224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.card','data' => ['class' => 'max-w-md w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'max-w-md w-full']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($illustration): ?>
                 <?php $__env->slot('figure', null, []); ?> 
                    <img src="<?php echo e($illustration); ?>" alt="" class="w-full h-auto" />
                 <?php $__env->endSlot(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <div class="card-body">
                <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['icon' => $icon,'title' => $title,'message' => $message,'actionLabel' => $actionLabel,'actionUrl' => $actionUrl,'actionColor' => $actionVariant,'size' => $size]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message),'actionLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionLabel),'actionUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionUrl),'actionColor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionVariant),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $attributes = $__attributesOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__attributesOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $component = $__componentOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__componentOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $attributes = $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $component = $__componentOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
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

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/errors/empty-state.blade.php ENDPATH**/ ?>