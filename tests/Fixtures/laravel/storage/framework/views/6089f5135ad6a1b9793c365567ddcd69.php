    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['id' => 'contact-viewer','schema' => [
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                [
                    'id' => 'civility',
                    'type' => 'radio',
                    'name' => 'civility',
                    'label' => 'Civility',
                    'options' => [
                        ['value' => 'mr', 'label' => 'Mr'],
                        ['value' => 'mrs', 'label' => 'Mrs'],
                    ],
                ],
                ['id' => 'terms', 'type' => 'toggle', 'name' => 'terms', 'label' => 'Terms'],
                ['id' => 'signature', 'type' => 'signature', 'name' => 'signature', 'label' => 'Signature'],
            ],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'contact-viewer','schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                [
                    'id' => 'civility',
                    'type' => 'radio',
                    'name' => 'civility',
                    'label' => 'Civility',
                    'options' => [
                        ['value' => 'mr', 'label' => 'Mr'],
                        ['value' => 'mrs', 'label' => 'Mrs'],
                    ],
                ],
                ['id' => 'terms', 'type' => 'toggle', 'name' => 'terms', 'label' => 'Terms'],
                ['id' => 'signature', 'type' => 'signature', 'name' => 'signature', 'label' => 'Signature'],
            ],
        ])]); ?>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/5c979326396b6b6807bfd442aa2c5796.blade.php ENDPATH**/ ?>