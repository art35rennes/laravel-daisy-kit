    <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['preset' => 'no-results','iconName' => 'bi-lock','message' => 'Adjust filters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['preset' => 'no-results','icon-name' => 'bi-lock','message' => 'Adjust filters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <button type="button">Reset filters</button>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $attributes = $__attributesOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__attributesOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $component = $__componentOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__componentOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/9bb1ba56499880a9faee0efd7ffedd4e.blade.php ENDPATH**/ ?>