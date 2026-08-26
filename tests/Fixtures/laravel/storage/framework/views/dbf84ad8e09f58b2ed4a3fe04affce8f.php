<?php if (isset($component)) { $__componentOriginal10ad05071a6832c005a49ec6f828332a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10ad05071a6832c005a49ec6f828332a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.range','data' => ['vertical' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['vertical' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10ad05071a6832c005a49ec6f828332a)): ?>
<?php $attributes = $__attributesOriginal10ad05071a6832c005a49ec6f828332a; ?>
<?php unset($__attributesOriginal10ad05071a6832c005a49ec6f828332a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10ad05071a6832c005a49ec6f828332a)): ?>
<?php $component = $__componentOriginal10ad05071a6832c005a49ec6f828332a; ?>
<?php unset($__componentOriginal10ad05071a6832c005a49ec6f828332a); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/d5544395f49c18774ee46d184e3368fa.blade.php ENDPATH**/ ?>