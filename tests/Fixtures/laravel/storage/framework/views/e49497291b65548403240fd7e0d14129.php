    <?php if (isset($component)) { $__componentOriginal2c474f4df5f03b80bb665732a45b2687 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c474f4df5f03b80bb665732a45b2687 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.calendar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.calendar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('previous', null, []); ?> Earlier <?php $__env->endSlot(); ?>
         <?php $__env->slot('next', null, []); ?> Later <?php $__env->endSlot(); ?>
         <?php $__env->slot('heading', null, []); ?> Choose a date <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c474f4df5f03b80bb665732a45b2687)): ?>
<?php $attributes = $__attributesOriginal2c474f4df5f03b80bb665732a45b2687; ?>
<?php unset($__attributesOriginal2c474f4df5f03b80bb665732a45b2687); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c474f4df5f03b80bb665732a45b2687)): ?>
<?php $component = $__componentOriginal2c474f4df5f03b80bb665732a45b2687; ?>
<?php unset($__componentOriginal2c474f4df5f03b80bb665732a45b2687); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/58aa7311f3c66dd19359cb13a51b389e.blade.php ENDPATH**/ ?>