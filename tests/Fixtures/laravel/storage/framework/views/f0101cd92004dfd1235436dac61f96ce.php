    <?php if (isset($component)) { $__componentOriginal5231fb4d41e601feb3f2e47a68463472 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5231fb4d41e601feb3f2e47a68463472 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.multi-select','data' => ['name' => 'tags','values' => ['laravel'],'options' => [['value' => 'laravel', 'label' => 'Laravel']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.multi-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'tags','values' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['laravel']),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['value' => 'laravel', 'label' => 'Laravel']])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5231fb4d41e601feb3f2e47a68463472)): ?>
<?php $attributes = $__attributesOriginal5231fb4d41e601feb3f2e47a68463472; ?>
<?php unset($__attributesOriginal5231fb4d41e601feb3f2e47a68463472); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5231fb4d41e601feb3f2e47a68463472)): ?>
<?php $component = $__componentOriginal5231fb4d41e601feb3f2e47a68463472; ?>
<?php unset($__componentOriginal5231fb4d41e601feb3f2e47a68463472); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/d5d1abc3c733db052af8f830836b4890.blade.php ENDPATH**/ ?>