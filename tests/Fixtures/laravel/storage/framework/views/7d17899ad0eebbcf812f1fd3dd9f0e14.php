    <?php if (isset($component)) { $__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.breadcrumbs','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <li><a href="/">Home</a></li>
        <li><span aria-current="page">Manual</span></li>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf)): ?>
<?php $attributes = $__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf; ?>
<?php unset($__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf)): ?>
<?php $component = $__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf; ?>
<?php unset($__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/08e8d35a41211a80d0f1c3aff74ef800.blade.php ENDPATH**/ ?>