    <?php if (isset($component)) { $__componentOriginald20b33cb1c35ea0e8504db023e077668 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald20b33cb1c35ea0e8504db023e077668 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.sidebar','data' => ['brandUrl' => '/dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['brand-url' => '/dashboard']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('brand', null, []); ?> 
            <span data-expanded-brand>Expanded brand</span>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald20b33cb1c35ea0e8504db023e077668)): ?>
<?php $attributes = $__attributesOriginald20b33cb1c35ea0e8504db023e077668; ?>
<?php unset($__attributesOriginald20b33cb1c35ea0e8504db023e077668); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald20b33cb1c35ea0e8504db023e077668)): ?>
<?php $component = $__componentOriginald20b33cb1c35ea0e8504db023e077668; ?>
<?php unset($__componentOriginald20b33cb1c35ea0e8504db023e077668); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/3090ff574be6d58b5e7da2072b93220d.blade.php ENDPATH**/ ?>