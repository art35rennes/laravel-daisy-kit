    <?php if (isset($component)) { $__componentOriginale7e85473731da6b37d137760a2def5ca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7e85473731da6b37d137760a2def5ca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.megamenu','data' => ['mode' => 'full','size' => 'lg','id' => 'main-menu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.megamenu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => 'full','size' => 'lg','id' => 'main-menu']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <button popovertarget="services">Services</button>
        <div id="services" popover>Links</div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7e85473731da6b37d137760a2def5ca)): ?>
<?php $attributes = $__attributesOriginale7e85473731da6b37d137760a2def5ca; ?>
<?php unset($__attributesOriginale7e85473731da6b37d137760a2def5ca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7e85473731da6b37d137760a2def5ca)): ?>
<?php $component = $__componentOriginale7e85473731da6b37d137760a2def5ca; ?>
<?php unset($__componentOriginale7e85473731da6b37d137760a2def5ca); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/4cf17866a0937cb856a144c6227f767d.blade.php ENDPATH**/ ?>