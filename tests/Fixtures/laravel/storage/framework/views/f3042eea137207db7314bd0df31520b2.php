    <?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => 'Package Test']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Package Test']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <p>Package body</p>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $attributes = $__attributesOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__attributesOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $component = $__componentOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__componentOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/ef4044e06e585b088d3bf50d2c4b3d87.blade.php ENDPATH**/ ?>