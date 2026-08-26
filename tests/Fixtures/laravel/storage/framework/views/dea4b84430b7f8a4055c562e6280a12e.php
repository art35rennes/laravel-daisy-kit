<?php if (isset($component)) { $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.blueprint','data' => ['mode' => 'view','value' => []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.blueprint'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => 'view','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $attributes = $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $component = $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/5ece5e422de7ae51c3ed678d29caa377.blade.php ENDPATH**/ ?>