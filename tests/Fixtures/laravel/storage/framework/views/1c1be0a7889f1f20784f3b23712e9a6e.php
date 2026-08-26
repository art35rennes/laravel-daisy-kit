    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['rowKey' => 'id','tableLayout' => 'auto','minWidth' => '128rem','scrollX' => 'always','externalFilters' => true,'livewireMode' => 'ignore','columns' => [
            ['key' => '_action', 'label' => 'Actions', 'type' => 'actions'],
            ['key' => 'status_badge', 'label' => 'Status', 'align' => 'center', 'width' => '140px', 'cell' => ['renderer' => 'trusted-html']],
            ['key' => 'postal_address', 'label' => 'Address', 'truncate' => 2, 'width' => '260px', 'minWidth' => 'max-content', 'nowrap' => true],
        ],'rows' => [
            ['id' => 'row-1', '_action' => ['action' => 'open', 'label' => 'Open'], 'status_badge' => '<span class=&quot;badge&quot;>Open</span>', 'postal_address' => '12 rue longue'],
        ],'filters' => [
            ['key' => 'status', 'label' => 'Status', 'type' => 'text'],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row-key' => 'id','table-layout' => 'auto','min-width' => '128rem','scroll-x' => 'always','external-filters' => true,'livewire-mode' => 'ignore','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => '_action', 'label' => 'Actions', 'type' => 'actions'],
            ['key' => 'status_badge', 'label' => 'Status', 'align' => 'center', 'width' => '140px', 'cell' => ['renderer' => 'trusted-html']],
            ['key' => 'postal_address', 'label' => 'Address', 'truncate' => 2, 'width' => '260px', 'minWidth' => 'max-content', 'nowrap' => true],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['id' => 'row-1', '_action' => ['action' => 'open', 'label' => 'Open'], 'status_badge' => '<span class=&quot;badge&quot;>Open</span>', 'postal_address' => '12 rue longue'],
        ]),'filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'status', 'label' => 'Status', 'type' => 'text'],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/efad26587baaedd18e3e157a0b43c7ba.blade.php ENDPATH**/ ?>