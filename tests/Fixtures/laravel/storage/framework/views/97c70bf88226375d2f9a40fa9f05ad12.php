    <?php if (isset($component)) { $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.modal','data' => ['id' => 'side-panel','title' => 'Side panel','horizontal' => 'end','teleport' => false,'open' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'side-panel','title' => 'Side panel','horizontal' => 'end','teleport' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'open' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Body
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $attributes = $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $component = $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/a6204a588590336178f8b9f1197d679a.blade.php ENDPATH**/ ?>