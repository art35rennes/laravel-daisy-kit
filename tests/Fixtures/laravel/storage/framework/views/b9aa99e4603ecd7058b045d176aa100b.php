<?php if (isset($component)) { $__componentOriginal8dc5d6d0cd84502a5434400602999c2e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8dc5d6d0cd84502a5434400602999c2e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.advanced.blueprint','data' => ['showHeader' => false,'namePrefix' => 'demo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.advanced.blueprint'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show-header' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'name-prefix' => 'demo']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8dc5d6d0cd84502a5434400602999c2e)): ?>
<?php $attributes = $__attributesOriginal8dc5d6d0cd84502a5434400602999c2e; ?>
<?php unset($__attributesOriginal8dc5d6d0cd84502a5434400602999c2e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8dc5d6d0cd84502a5434400602999c2e)): ?>
<?php $component = $__componentOriginal8dc5d6d0cd84502a5434400602999c2e; ?>
<?php unset($__componentOriginal8dc5d6d0cd84502a5434400602999c2e); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/cf4424b37b19b167a3fec34f7c5f4cb2.blade.php ENDPATH**/ ?>