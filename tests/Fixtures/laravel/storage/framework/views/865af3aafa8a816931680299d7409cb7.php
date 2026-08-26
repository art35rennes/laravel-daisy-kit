    <?php if (isset($component)) { $__componentOriginalb355cab2b2984b49b730ce467e13f652 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb355cab2b2984b49b730ce467e13f652 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.radial-progress','data' => ['value' => 70,'size' => '7rem','thickness' => '0.7rem']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.radial-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 70,'size' => '7rem','thickness' => '0.7rem']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
70% <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb355cab2b2984b49b730ce467e13f652)): ?>
<?php $attributes = $__attributesOriginalb355cab2b2984b49b730ce467e13f652; ?>
<?php unset($__attributesOriginalb355cab2b2984b49b730ce467e13f652); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb355cab2b2984b49b730ce467e13f652)): ?>
<?php $component = $__componentOriginalb355cab2b2984b49b730ce467e13f652; ?>
<?php unset($__componentOriginalb355cab2b2984b49b730ce467e13f652); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/caf0cd424b1dbaa6ac9eacc6f542a52c.blade.php ENDPATH**/ ?>