<?php if (isset($component)) { $__componentOriginal5efd72db83161bae6e1dc57d5f89d224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.card','data' => ['selectable' => true,'checked' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selectable' => true,'checked' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Plan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $attributes = $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $component = $__componentOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/ab1abdded51f1f977da33b85e423a0a9.blade.php ENDPATH**/ ?>