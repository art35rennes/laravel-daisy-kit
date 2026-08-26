    <?php if (isset($component)) { $__componentOriginal2c474f4df5f03b80bb665732a45b2687 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2c474f4df5f03b80bb665732a45b2687 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.calendar','data' => ['provider' => 'vanilla','mode' => 'range','months' => 2,'value' => '2026-07-13,2026-07-18','min' => '2026-07-01','max' => '2026-07-31','locale' => 'fr-FR','name' => 'stay_dates','inputId' => 'stay-dates','ariaLabel' => 'Dates du séjour','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.calendar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['provider' => 'vanilla','mode' => 'range','months' => 2,'value' => '2026-07-13,2026-07-18','min' => '2026-07-01','max' => '2026-07-31','locale' => 'fr-FR','name' => 'stay_dates','input-id' => 'stay-dates','aria-label' => 'Dates du séjour','class' => 'w-full']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2c474f4df5f03b80bb665732a45b2687)): ?>
<?php $attributes = $__attributesOriginal2c474f4df5f03b80bb665732a45b2687; ?>
<?php unset($__attributesOriginal2c474f4df5f03b80bb665732a45b2687); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2c474f4df5f03b80bb665732a45b2687)): ?>
<?php $component = $__componentOriginal2c474f4df5f03b80bb665732a45b2687; ?>
<?php unset($__componentOriginal2c474f4df5f03b80bb665732a45b2687); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/09bfc3b17e0219db1ab67e3855b68c09.blade.php ENDPATH**/ ?>