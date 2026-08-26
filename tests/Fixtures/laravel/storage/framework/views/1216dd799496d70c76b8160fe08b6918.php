    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['columns' => [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions', 'view' => 'table-test::table.actions'],
            ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
        ],'rows' => [
            ['id' => 1, 'name' => 'Jane', 'actions' => 'open', 'profile' => ['label' => 'Profile', 'href' => 'https://example.test/users/1', 'target' => '_blank']],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions', 'view' => 'table-test::table.actions'],
            ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['id' => 1, 'name' => 'Jane', 'actions' => 'open', 'profile' => ['label' => 'Profile', 'href' => 'https://example.test/users/1', 'target' => '_blank']],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/04cd0caa1df72d548faa869fa68f4d79.blade.php ENDPATH**/ ?>