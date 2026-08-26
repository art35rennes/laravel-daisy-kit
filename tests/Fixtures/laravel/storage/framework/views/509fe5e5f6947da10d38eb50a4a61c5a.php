    <?php if (isset($component)) { $__componentOriginal65da8be3abf0702790c6a63da305415d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65da8be3abf0702790c6a63da305415d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.builder','data' => ['name' => 'schema','functionCatalog' => [
            ['name' => '$uuid', 'signature' => '<s:s>', 'description' => 'UUID'],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.builder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'schema','functionCatalog' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['name' => '$uuid', 'signature' => '<s:s>', 'description' => 'UUID'],
        ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65da8be3abf0702790c6a63da305415d)): ?>
<?php $attributes = $__attributesOriginal65da8be3abf0702790c6a63da305415d; ?>
<?php unset($__attributesOriginal65da8be3abf0702790c6a63da305415d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65da8be3abf0702790c6a63da305415d)): ?>
<?php $component = $__componentOriginal65da8be3abf0702790c6a63da305415d; ?>
<?php unset($__componentOriginal65da8be3abf0702790c6a63da305415d); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/17dd2901243aa9ee12c048820d2e18dd.blade.php ENDPATH**/ ?>