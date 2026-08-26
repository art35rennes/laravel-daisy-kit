<?php if (isset($component)) { $__componentOriginal2c474f4df5f03b80bb665732a45b2687 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c474f4df5f03b80bb665732a45b2687 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.calendar','data' => ['value' => '2026-07-13']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.calendar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => '2026-07-13']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c474f4df5f03b80bb665732a45b2687)): ?>
<?php $attributes = $__attributesOriginal2c474f4df5f03b80bb665732a45b2687; ?>
<?php unset($__attributesOriginal2c474f4df5f03b80bb665732a45b2687); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c474f4df5f03b80bb665732a45b2687)): ?>
<?php $component = $__componentOriginal2c474f4df5f03b80bb665732a45b2687; ?>
<?php unset($__componentOriginal2c474f4df5f03b80bb665732a45b2687); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/08cd09b12aee3a8594861f47c9681b9b.blade.php ENDPATH**/ ?>