    <?php if (isset($component)) { $__componentOriginal485eda4fa6037840e19445eddd3472f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal485eda4fa6037840e19445eddd3472f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.popover','data' => ['arrow' => true,'title' => 'Popover title']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.popover'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['arrow' => true,'title' => 'Popover title']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Popover content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal485eda4fa6037840e19445eddd3472f7)): ?>
<?php $attributes = $__attributesOriginal485eda4fa6037840e19445eddd3472f7; ?>
<?php unset($__attributesOriginal485eda4fa6037840e19445eddd3472f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal485eda4fa6037840e19445eddd3472f7)): ?>
<?php $component = $__componentOriginal485eda4fa6037840e19445eddd3472f7; ?>
<?php unset($__componentOriginal485eda4fa6037840e19445eddd3472f7); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/7eea364f863bde4dc02665a0001dff59.blade.php ENDPATH**/ ?>