    <?php if (isset($component)) { $__componentOriginalad7495ae90ab1edd698755ada2aef844 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad7495ae90ab1edd698755ada2aef844 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.otp','data' => ['name' => 'code','length' => 6,'value' => '123456','size' => 'lg','color' => 'error','joined' => true,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.otp'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'code','length' => 6,'value' => '123456','size' => 'lg','color' => 'error','joined' => true,'required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalad7495ae90ab1edd698755ada2aef844)): ?>
<?php $attributes = $__attributesOriginalad7495ae90ab1edd698755ada2aef844; ?>
<?php unset($__attributesOriginalad7495ae90ab1edd698755ada2aef844); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalad7495ae90ab1edd698755ada2aef844)): ?>
<?php $component = $__componentOriginalad7495ae90ab1edd698755ada2aef844; ?>
<?php unset($__componentOriginalad7495ae90ab1edd698755ada2aef844); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/37d09ed74db9394fc2b55322c2cfd409.blade.php ENDPATH**/ ?>