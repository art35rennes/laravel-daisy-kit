    <?php if (isset($component)) { $__componentOriginal2bd2e2e95e69832921f1700c40c0420f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2bd2e2e95e69832921f1700c40c0420f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.form.builder','data' => ['schema' => [
            'version' => '1.0',
            'id' => 'contact',
            'meta' => ['title' => 'Contact'],
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ],'value' => ['email' => 'ada@example.com'],'schemaName' => 'form_schema']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.form.builder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'contact',
            'meta' => ['title' => 'Contact'],
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['email' => 'ada@example.com']),'schema-name' => 'form_schema']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2bd2e2e95e69832921f1700c40c0420f)): ?>
<?php $attributes = $__attributesOriginal2bd2e2e95e69832921f1700c40c0420f; ?>
<?php unset($__attributesOriginal2bd2e2e95e69832921f1700c40c0420f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2bd2e2e95e69832921f1700c40c0420f)): ?>
<?php $component = $__componentOriginal2bd2e2e95e69832921f1700c40c0420f; ?>
<?php unset($__componentOriginal2bd2e2e95e69832921f1700c40c0420f); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/f14821b03b199f5feab650d56facfdc3.blade.php ENDPATH**/ ?>