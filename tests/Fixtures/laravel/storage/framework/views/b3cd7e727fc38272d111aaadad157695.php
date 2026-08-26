    <?php if (isset($component)) { $__componentOriginal86694e4259b0cf121887dc1976bac695 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86694e4259b0cf121887dc1976bac695 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.profile.profile-settings','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.profile.profile-settings'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('summary', null, []); ?> <span>Summary</span> <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal86694e4259b0cf121887dc1976bac695)): ?>
<?php $attributes = $__attributesOriginal86694e4259b0cf121887dc1976bac695; ?>
<?php unset($__attributesOriginal86694e4259b0cf121887dc1976bac695); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal86694e4259b0cf121887dc1976bac695)): ?>
<?php $component = $__componentOriginal86694e4259b0cf121887dc1976bac695; ?>
<?php unset($__componentOriginal86694e4259b0cf121887dc1976bac695); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/5784843b153075fe1be500557b09c299.blade.php ENDPATH**/ ?>