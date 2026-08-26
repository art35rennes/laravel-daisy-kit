    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['columns' => [
            ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
        ],'rows' => [
            ['profile' => ['label' => '<Open>', 'href' => 'javascript:alert(1)', 'target' => '_blank']],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['profile' => ['label' => '<Open>', 'href' => 'javascript:alert(1)', 'target' => '_blank']],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/f35e81860eaf2433fd01a9c362a88e9a.blade.php ENDPATH**/ ?>