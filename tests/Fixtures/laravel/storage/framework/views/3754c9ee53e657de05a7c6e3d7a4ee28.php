<?php if (isset($component)) { $__componentOriginal8c86404fd6a5f8d0f0c6d311db0bfca0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c86404fd6a5f8d0f0c6d311db0bfca0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.auth.two-factor','data' => ['useRecoveryCode' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.auth.two-factor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['use-recovery-code' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c86404fd6a5f8d0f0c6d311db0bfca0)): ?>
<?php $attributes = $__attributesOriginal8c86404fd6a5f8d0f0c6d311db0bfca0; ?>
<?php unset($__attributesOriginal8c86404fd6a5f8d0f0c6d311db0bfca0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c86404fd6a5f8d0f0c6d311db0bfca0)): ?>
<?php $component = $__componentOriginal8c86404fd6a5f8d0f0c6d311db0bfca0; ?>
<?php unset($__componentOriginal8c86404fd6a5f8d0f0c6d311db0bfca0); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/75be2eb555d557f6ed08cdc27ccd1b94.blade.php ENDPATH**/ ?>