    <?php if (isset($component)) { $__componentOriginal9bb0178607a492116e5ecda2e9031c68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9bb0178607a492116e5ecda2e9031c68 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.theme-controller','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.theme-controller'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9bb0178607a492116e5ecda2e9031c68)): ?>
<?php $attributes = $__attributesOriginal9bb0178607a492116e5ecda2e9031c68; ?>
<?php unset($__attributesOriginal9bb0178607a492116e5ecda2e9031c68); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9bb0178607a492116e5ecda2e9031c68)): ?>
<?php $component = $__componentOriginal9bb0178607a492116e5ecda2e9031c68; ?>
<?php unset($__componentOriginal9bb0178607a492116e5ecda2e9031c68); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/7d9f750065802803aae1305e87025f34.blade.php ENDPATH**/ ?>