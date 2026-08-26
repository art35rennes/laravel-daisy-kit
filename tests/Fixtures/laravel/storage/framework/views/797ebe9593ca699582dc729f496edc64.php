<?php if (isset($component)) { $__componentOriginal20a4ceb3b3404e61d117b654f6ac2237 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal20a4ceb3b3404e61d117b654f6ac2237 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.auth.reset-password','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.auth.reset-password'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal20a4ceb3b3404e61d117b654f6ac2237)): ?>
<?php $attributes = $__attributesOriginal20a4ceb3b3404e61d117b654f6ac2237; ?>
<?php unset($__attributesOriginal20a4ceb3b3404e61d117b654f6ac2237); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal20a4ceb3b3404e61d117b654f6ac2237)): ?>
<?php $component = $__componentOriginal20a4ceb3b3404e61d117b654f6ac2237; ?>
<?php unset($__componentOriginal20a4ceb3b3404e61d117b654f6ac2237); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/fd82f88cabe8fdf0308cefe799f7826c.blade.php ENDPATH**/ ?>