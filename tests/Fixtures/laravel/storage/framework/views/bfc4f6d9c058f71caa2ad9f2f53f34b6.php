    <?php if (isset($component)) { $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.hero','data' => ['imageUrl' => '/img/example.jpg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['image-url' => '/img/example.jpg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1)): ?>
<?php $attributes = $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1; ?>
<?php unset($__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal40312bcd153c4f1bbfbe6543713be4a1)): ?>
<?php $component = $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1; ?>
<?php unset($__componentOriginal40312bcd153c4f1bbfbe6543713be4a1); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/d3e1f184a88e25e19282073d8e1e1204.blade.php ENDPATH**/ ?>