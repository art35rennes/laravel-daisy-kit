    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['schema' => [
            'version' => '1.0',
            'id' => 'contact',
            'meta' => ['title' => 'Contact'],
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email', 'rules' => ['required', 'email']],
                ['id' => 'total', 'type' => 'number', 'name' => 'total', 'label' => 'Total', 'computed' => ['type' => 'jsonata', 'expression' => '1 + 1', 'dependsOn' => [], 'mode' => 'readonly']],
            ],
            'submit' => ['mode' => 'event', 'label' => 'Send'],
        ],'value' => ['email' => 'jane@example.com']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
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
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email', 'rules' => ['required', 'email']],
                ['id' => 'total', 'type' => 'number', 'name' => 'total', 'label' => 'Total', 'computed' => ['type' => 'jsonata', 'expression' => '1 + 1', 'dependsOn' => [], 'mode' => 'readonly']],
            ],
            'submit' => ['mode' => 'event', 'label' => 'Send'],
        ]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['email' => 'jane@example.com'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald486fd47b3d505837fe881ab44f482da)): ?>
<?php $attributes = $__attributesOriginald486fd47b3d505837fe881ab44f482da; ?>
<?php unset($__attributesOriginald486fd47b3d505837fe881ab44f482da); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald486fd47b3d505837fe881ab44f482da)): ?>
<?php $component = $__componentOriginald486fd47b3d505837fe881ab44f482da; ?>
<?php unset($__componentOriginald486fd47b3d505837fe881ab44f482da); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/b62ee002dfc48aec9680413c683f0c5a.blade.php ENDPATH**/ ?>