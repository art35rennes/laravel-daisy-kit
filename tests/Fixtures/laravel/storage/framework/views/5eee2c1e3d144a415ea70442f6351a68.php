    <?php if (isset($component)) { $__componentOriginal6666b5692c41b746fd3d6b2014eaae33 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6666b5692c41b746fd3d6b2014eaae33 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.utilities.truncate-text','data' => ['text' => 'A long customer-facing label','title' => 'Full label','tag' => 'p','tooltip' => false,'lines' => 3,'class' => 'text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.utilities.truncate-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'A long customer-facing label','title' => 'Full label','tag' => 'p','tooltip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'lines' => 3,'class' => 'text-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6666b5692c41b746fd3d6b2014eaae33)): ?>
<?php $attributes = $__attributesOriginal6666b5692c41b746fd3d6b2014eaae33; ?>
<?php unset($__attributesOriginal6666b5692c41b746fd3d6b2014eaae33); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6666b5692c41b746fd3d6b2014eaae33)): ?>
<?php $component = $__componentOriginal6666b5692c41b746fd3d6b2014eaae33; ?>
<?php unset($__componentOriginal6666b5692c41b746fd3d6b2014eaae33); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/155f08e9fe409f5b1dc8a297a171c71d.blade.php ENDPATH**/ ?>