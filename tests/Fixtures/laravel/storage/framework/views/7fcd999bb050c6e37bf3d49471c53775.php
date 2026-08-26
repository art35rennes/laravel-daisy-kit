    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['linkPolicy' => ['allowedSchemes' => ['myapp']],'columns' => [
            ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
            ['key' => 'scan', 'label' => 'Scan', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['intent']]],
        ],'rows' => [
            ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123', 'target' => '_blank'], 'scan' => ['label' => 'Scan', 'href' => 'intent://scan/#Intent;scheme=zxing;end']],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['link-policy' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['allowedSchemes' => ['myapp']]),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
            ['key' => 'scan', 'label' => 'Scan', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['intent']]],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123', 'target' => '_blank'], 'scan' => ['label' => 'Scan', 'href' => 'intent://scan/#Intent;scheme=zxing;end']],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/d9932e979a7628ec2fca337d0d3b293a.blade.php ENDPATH**/ ?>