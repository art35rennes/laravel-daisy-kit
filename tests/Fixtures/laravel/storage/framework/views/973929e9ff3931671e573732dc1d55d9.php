<?php if (isset($component)) { $__componentOriginald5e44e4ffeb12c3af0cc30a3ee47068a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5e44e4ffeb12c3af0cc30a3ee47068a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.datatable','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.datatable'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5e44e4ffeb12c3af0cc30a3ee47068a)): ?>
<?php $attributes = $__attributesOriginald5e44e4ffeb12c3af0cc30a3ee47068a; ?>
<?php unset($__attributesOriginald5e44e4ffeb12c3af0cc30a3ee47068a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5e44e4ffeb12c3af0cc30a3ee47068a)): ?>
<?php $component = $__componentOriginald5e44e4ffeb12c3af0cc30a3ee47068a; ?>
<?php unset($__componentOriginald5e44e4ffeb12c3af0cc30a3ee47068a); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/564c34192d5091f9fbd9a4dbc4f6a9d4.blade.php ENDPATH**/ ?>