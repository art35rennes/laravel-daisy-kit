    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['rowKey' => 'id','editable' => [
            'enabled' => true,
            'mode' => 'row',
            'update' => ['strategy' => 'local'],
            'create' => [
                'enabled' => true,
                'strategy' => 'remote',
                'endpoint' => ['url' => '/projects', 'method' => 'POST'],
                'defaults' => ['status' => 'draft'],
            ],
        ],'columns' => [
            ['key' => 'name', 'label' => 'Name', 'editor' => ['type' => 'text', 'required' => true]],
            ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'select', 'options' => [['value' => 'draft', 'label' => 'Draft']]]],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row-key' => 'id','editable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'enabled' => true,
            'mode' => 'row',
            'update' => ['strategy' => 'local'],
            'create' => [
                'enabled' => true,
                'strategy' => 'remote',
                'endpoint' => ['url' => '/projects', 'method' => 'POST'],
                'defaults' => ['status' => 'draft'],
            ],
        ]),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'name', 'label' => 'Name', 'editor' => ['type' => 'text', 'required' => true]],
            ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'select', 'options' => [['value' => 'draft', 'label' => 'Draft']]]],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/46d70b27914d67f328e7cd442ac67cb6.blade.php ENDPATH**/ ?>