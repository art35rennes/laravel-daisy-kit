    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['selection' => 'multiple','rowKey' => 'uuid','columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],'rows' => [
            ['uuid' => 'user-1', 'name' => 'Jane'],
            ['uuid' => 'user-2', 'name' => 'John'],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['selection' => 'multiple','row-key' => 'uuid','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'name', 'label' => 'Name'],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['uuid' => 'user-1', 'name' => 'Jane'],
            ['uuid' => 'user-2', 'name' => 'John'],
        ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('bulkActions', null, []); ?> 
            <button type="button" data-table-bulk-action="archive">Archive</button>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal60cd61a283b1a188e2653a28cf5f25be)): ?>
<?php $attributes = $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be; ?>
<?php unset($__attributesOriginal60cd61a283b1a188e2653a28cf5f25be); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal60cd61a283b1a188e2653a28cf5f25be)): ?>
<?php $component = $__componentOriginal60cd61a283b1a188e2653a28cf5f25be; ?>
<?php unset($__componentOriginal60cd61a283b1a188e2653a28cf5f25be); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/41a035a3fc53fb1ae840aaa03320d4c6.blade.php ENDPATH**/ ?>