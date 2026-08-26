    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['linkPolicy' => ['allowedSchemes' => ['javascript']],'columns' => [
            ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
            ['key' => 'danger', 'label' => 'Danger', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['javascript']]],
        ],'rows' => [
            ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123'], 'danger' => ['label' => '<Bad>', 'href' => 'javascript:alert(1)']],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['link-policy' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['allowedSchemes' => ['javascript']]),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
            ['key' => 'danger', 'label' => 'Danger', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['javascript']]],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123'], 'danger' => ['label' => '<Bad>', 'href' => 'javascript:alert(1)']],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/73742a6affd1ee41d318015b5e05e5c9.blade.php ENDPATH**/ ?>