<?php if (isset($component)) { $__componentOriginalc21c4df2e91f3afea348e13cb8b73c86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc21c4df2e91f3afea348e13cb8b73c86 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.auth.login-simple','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.auth.login-simple'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc21c4df2e91f3afea348e13cb8b73c86)): ?>
<?php $attributes = $__attributesOriginalc21c4df2e91f3afea348e13cb8b73c86; ?>
<?php unset($__attributesOriginalc21c4df2e91f3afea348e13cb8b73c86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc21c4df2e91f3afea348e13cb8b73c86)): ?>
<?php $component = $__componentOriginalc21c4df2e91f3afea348e13cb8b73c86; ?>
<?php unset($__componentOriginalc21c4df2e91f3afea348e13cb8b73c86); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/9eea0c402663d163d04c50f70e40a25d.blade.php ENDPATH**/ ?>