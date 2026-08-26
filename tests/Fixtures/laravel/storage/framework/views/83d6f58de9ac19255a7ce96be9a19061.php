    <?php if (isset($component)) { $__componentOriginal8ae823e922000497b0e9a660b4733032 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ae823e922000497b0e9a660b4733032 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.navbar-layout','data' => ['showThemeController' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.navbar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show-theme-controller' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('navbarCenter', null, []); ?> <span data-navbar-center>Search</span> <?php $__env->endSlot(); ?>
        Content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ae823e922000497b0e9a660b4733032)): ?>
<?php $attributes = $__attributesOriginal8ae823e922000497b0e9a660b4733032; ?>
<?php unset($__attributesOriginal8ae823e922000497b0e9a660b4733032); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ae823e922000497b0e9a660b4733032)): ?>
<?php $component = $__componentOriginal8ae823e922000497b0e9a660b4733032; ?>
<?php unset($__componentOriginal8ae823e922000497b0e9a660b4733032); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/a1dafd1743fd7024cee1e19b5532ee83.blade.php ENDPATH**/ ?>