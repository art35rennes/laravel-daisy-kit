<?php if (isset($component)) { $__componentOriginal2bd2e2e95e69832921f1700c40c0420f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2bd2e2e95e69832921f1700c40c0420f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.form.builder','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.form.builder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2bd2e2e95e69832921f1700c40c0420f)): ?>
<?php $attributes = $__attributesOriginal2bd2e2e95e69832921f1700c40c0420f; ?>
<?php unset($__attributesOriginal2bd2e2e95e69832921f1700c40c0420f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2bd2e2e95e69832921f1700c40c0420f)): ?>
<?php $component = $__componentOriginal2bd2e2e95e69832921f1700c40c0420f; ?>
<?php unset($__componentOriginal2bd2e2e95e69832921f1700c40c0420f); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/44cddeee44c2f00d60d7353802cddc3e.blade.php ENDPATH**/ ?>