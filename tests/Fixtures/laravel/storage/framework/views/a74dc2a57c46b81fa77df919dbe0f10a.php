    <?php if (isset($component)) { $__componentOriginalbe69f52a68d3708f38c4da18c7056e41 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe69f52a68d3708f38c4da18c7056e41 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-layout','data' => ['actionsAlignment' => 'between']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions-alignment' => 'between']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('header', null, []); ?> <h1>Edit profile</h1> <?php $__env->endSlot(); ?>
        <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => 'Profile','stickyAside' => true,'actionsAlignment' => 'start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Profile','sticky-aside' => true,'actions-alignment' => 'start']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('headerActions', null, []); ?> <a href="/help">Help</a> <?php $__env->endSlot(); ?>
             <?php $__env->slot('aside', null, []); ?> <p>Aside help</p> <?php $__env->endSlot(); ?>
            Main form
             <?php $__env->slot('actions', null, []); ?> <button type="button">Save</button> <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
         <?php $__env->slot('actions', null, []); ?> <button type="button">Cancel</button><button type="submit">Save all</button> <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe69f52a68d3708f38c4da18c7056e41)): ?>
<?php $attributes = $__attributesOriginalbe69f52a68d3708f38c4da18c7056e41; ?>
<?php unset($__attributesOriginalbe69f52a68d3708f38c4da18c7056e41); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe69f52a68d3708f38c4da18c7056e41)): ?>
<?php $component = $__componentOriginalbe69f52a68d3708f38c4da18c7056e41; ?>
<?php unset($__componentOriginalbe69f52a68d3708f38c4da18c7056e41); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/54692e4537eebd045ca981b8c435e644.blade.php ENDPATH**/ ?>