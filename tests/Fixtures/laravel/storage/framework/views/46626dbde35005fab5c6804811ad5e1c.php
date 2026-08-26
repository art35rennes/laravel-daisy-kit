    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['mode' => 'server','endpoint' => '/audits','rowKey' => 'id','rowDetail' => 'modal','rowDetailView' => 'table-test::table.actions','columnResizing' => true,'columns' => [
            ['key' => 'created_at', 'label' => 'Created', 'filterable' => true, 'filter' => ['type' => 'date']],
        ],'filters' => [
            ['key' => 'period', 'label' => 'Period', 'type' => 'date-range', 'filterKeyFrom' => 'started_after', 'filterKeyTo' => 'started_before'],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => 'server','endpoint' => '/audits','row-key' => 'id','row-detail' => 'modal','row-detail-view' => 'table-test::table.actions','column-resizing' => true,'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'created_at', 'label' => 'Created', 'filterable' => true, 'filter' => ['type' => 'date']],
        ]),'filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'period', 'label' => 'Period', 'type' => 'date-range', 'filterKeyFrom' => 'started_after', 'filterKeyTo' => 'started_before'],
        ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal60cd61a283b1a188e2653a28cf5f25be)): ?>
<?php $attributes = $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be; ?>
<?php unset($__attributesOriginal60cd61a283b1a188e2653a28cf5f25be); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal60cd61a283b1a188e2653a28cf5f25be)): ?>
<?php $component = $__componentOriginal60cd61a283b1a188e2653a28cf5f25be; ?>
<?php unset($__componentOriginal60cd61a283b1a188e2653a28cf5f25be); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/b4b8ad868e598f1a8b3a33d973c2b917.blade.php ENDPATH**/ ?>