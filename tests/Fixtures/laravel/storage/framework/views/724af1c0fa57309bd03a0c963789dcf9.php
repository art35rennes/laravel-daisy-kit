    <?php if (isset($component)) { $__componentOriginal6733a308a8cd1df300620df9a802af3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6733a308a8cd1df300620df9a802af3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.ordered-list','data' => ['items' => [['id' => 'plan', 'label' => 'Plan V2']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.ordered-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['id' => 'plan', 'label' => 'Plan V2']])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6733a308a8cd1df300620df9a802af3f)): ?>
<?php $attributes = $__attributesOriginal6733a308a8cd1df300620df9a802af3f; ?>
<?php unset($__attributesOriginal6733a308a8cd1df300620df9a802af3f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6733a308a8cd1df300620df9a802af3f)): ?>
<?php $component = $__componentOriginal6733a308a8cd1df300620df9a802af3f; ?>
<?php unset($__componentOriginal6733a308a8cd1df300620df9a802af3f); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/e3e959620526b611034e831317aae000.blade.php ENDPATH**/ ?>