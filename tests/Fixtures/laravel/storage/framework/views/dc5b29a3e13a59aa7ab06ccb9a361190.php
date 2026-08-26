    <?php if (isset($component)) { $__componentOriginalcbcbb17075ca6a7d41dffb83a751129b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.navbar-sidebar-layout','data' => ['showThemeController' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.navbar-sidebar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show-theme-controller' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('navbarStart', null, []); ?> <span data-navbar-start>Start</span> <?php $__env->endSlot(); ?>
         <?php $__env->slot('navbarCenter', null, []); ?> <span data-navbar-center>Center</span> <?php $__env->endSlot(); ?>
         <?php $__env->slot('navbarEnd', null, []); ?> <span data-navbar-end>End</span> <?php $__env->endSlot(); ?>
        Content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b)): ?>
<?php $attributes = $__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b; ?>
<?php unset($__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbcbb17075ca6a7d41dffb83a751129b)): ?>
<?php $component = $__componentOriginalcbcbb17075ca6a7d41dffb83a751129b; ?>
<?php unset($__componentOriginalcbcbb17075ca6a7d41dffb83a751129b); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/211fb644fd1396dcd0ac6d6abb6d9472.blade.php ENDPATH**/ ?>