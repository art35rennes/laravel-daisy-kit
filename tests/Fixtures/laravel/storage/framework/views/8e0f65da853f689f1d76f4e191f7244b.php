    <?php if (isset($component)) { $__componentOriginaldbaa6086e6afca2de8e7644ee4db6e1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbaa6086e6afca2de8e7644ee4db6e1d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.token-input','data' => ['name' => 'recipients','values' => ['alice@example.com']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.token-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'recipients','values' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['alice@example.com'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldbaa6086e6afca2de8e7644ee4db6e1d)): ?>
<?php $attributes = $__attributesOriginaldbaa6086e6afca2de8e7644ee4db6e1d; ?>
<?php unset($__attributesOriginaldbaa6086e6afca2de8e7644ee4db6e1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldbaa6086e6afca2de8e7644ee4db6e1d)): ?>
<?php $component = $__componentOriginaldbaa6086e6afca2de8e7644ee4db6e1d; ?>
<?php unset($__componentOriginaldbaa6086e6afca2de8e7644ee4db6e1d); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/19310571dbcf521776383a61014b4694.blade.php ENDPATH**/ ?>