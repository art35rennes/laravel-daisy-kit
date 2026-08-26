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
         <?php $__env->slot('navbarHeading', null, []); ?> 
            <h1>Suivi et validation des interventions</h1>
            <p>Controlez la qualite des donnees recues</p>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('navbarCenter', null, []); ?> <span data-navbar-center>Search</span> <?php $__env->endSlot(); ?>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/51087054c06f0e4cd420970495b55cff.blade.php ENDPATH**/ ?>