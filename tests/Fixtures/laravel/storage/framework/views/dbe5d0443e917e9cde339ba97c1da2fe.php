    <?php if (isset($component)) { $__componentOriginal8239c0a55b15d04e12c0ad7334916316 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8239c0a55b15d04e12c0ad7334916316 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.auth-shell','data' => ['backgroundClass' => 'auth-brand-background']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.auth-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['background-class' => 'auth-brand-background']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8239c0a55b15d04e12c0ad7334916316)): ?>
<?php $attributes = $__attributesOriginal8239c0a55b15d04e12c0ad7334916316; ?>
<?php unset($__attributesOriginal8239c0a55b15d04e12c0ad7334916316); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8239c0a55b15d04e12c0ad7334916316)): ?>
<?php $component = $__componentOriginal8239c0a55b15d04e12c0ad7334916316; ?>
<?php unset($__componentOriginal8239c0a55b15d04e12c0ad7334916316); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/e785772d19adc0e24f75b77db05cad07.blade.php ENDPATH**/ ?>