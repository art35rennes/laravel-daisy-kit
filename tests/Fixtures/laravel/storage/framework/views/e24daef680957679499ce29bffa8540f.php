    <?php if (isset($component)) { $__componentOriginaldc5e7500762c7585e14eb88a9a935f99 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc5e7500762c7585e14eb88a9a935f99 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.auth.login-split','data' => ['forgotPasswordUrl' => '/forgot-password','showSignup' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.auth.login-split'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['forgot-password-url' => '/forgot-password','show-signup' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc5e7500762c7585e14eb88a9a935f99)): ?>
<?php $attributes = $__attributesOriginaldc5e7500762c7585e14eb88a9a935f99; ?>
<?php unset($__attributesOriginaldc5e7500762c7585e14eb88a9a935f99); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc5e7500762c7585e14eb88a9a935f99)): ?>
<?php $component = $__componentOriginaldc5e7500762c7585e14eb88a9a935f99; ?>
<?php unset($__componentOriginaldc5e7500762c7585e14eb88a9a935f99); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/6e8d399ee909575fc35bcb99aecf549b.blade.php ENDPATH**/ ?>