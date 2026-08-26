    <?php if (isset($component)) { $__componentOriginald20b33cb1c35ea0e8504db023e077668 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald20b33cb1c35ea0e8504db023e077668 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.sidebar','data' => ['expandedWidth' => 'w-72','collapsedWidth' => 'w-16','collapsed' => true,'sections' => [['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['expanded-width' => 'w-72','collapsed-width' => 'w-16','collapsed' => true,'sections' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('brand', null, []); ?> 
            <span data-expanded-brand>Expanded brand</span>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('brandCollapsed', null, []); ?> 
            <span data-collapsed-brand>DK</span>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/bc4fc3ea0bd41f6ec113a153cd02484a.blade.php ENDPATH**/ ?>