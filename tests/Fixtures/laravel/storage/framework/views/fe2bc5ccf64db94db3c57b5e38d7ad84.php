    <?php if (isset($component)) { $__componentOriginald4aaa4b01baa94db46e38e4697384b0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.theme-selector','data' => ['position' => 'relative','placement' => 'bottom-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.theme-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'relative','placement' => 'bottom-left']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $attributes = $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $component = $__componentOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
    <?php echo $__env->yieldPushContent('scripts'); ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/3a0ec529dd7edc493ac1cf6cae0cd252.blade.php ENDPATH**/ ?>