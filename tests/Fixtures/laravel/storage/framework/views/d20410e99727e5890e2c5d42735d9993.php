<?php if (isset($component)) { $__componentOriginalf307598521fbe55b23c0716cb1c0604b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf307598521fbe55b23c0716cb1c0604b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.reporting.operations-dashboard','data' => ['showSummary' => true,'detailModal' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.reporting.operations-dashboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show-summary' => true,'detail-modal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf307598521fbe55b23c0716cb1c0604b)): ?>
<?php $attributes = $__attributesOriginalf307598521fbe55b23c0716cb1c0604b; ?>
<?php unset($__attributesOriginalf307598521fbe55b23c0716cb1c0604b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf307598521fbe55b23c0716cb1c0604b)): ?>
<?php $component = $__componentOriginalf307598521fbe55b23c0716cb1c0604b; ?>
<?php unset($__componentOriginalf307598521fbe55b23c0716cb1c0604b); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/91216b79136cb66b79caf7530ac19a12.blade.php ENDPATH**/ ?>