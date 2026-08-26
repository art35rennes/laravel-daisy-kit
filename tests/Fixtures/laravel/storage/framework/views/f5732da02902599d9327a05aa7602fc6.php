    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['schema' => [
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                [
                    'id' => 'name',
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Name',
                    'attrs' => [
                        'placeholder' => 'Jane Doe',
                        'autocomplete' => 'name',
                        'mask' => '999-999',
                        'maskCharPlaceholder' => '_',
                        'maskPlaceholder' => true,
                        'inputPlaceholder' => true,
                        'clearIncomplete' => true,
                        'obfuscate' => true,
                        'obfuscateChar' => '*',
                        'obfuscateKeepEnd' => 2,
                    ],
                    'ui' => ['size' => 'sm', 'color' => 'primary', 'width' => '1/2'],
                ],
                [
                    'id' => 'score',
                    'type' => 'range',
                    'name' => 'score',
                    'label' => 'Score',
                    'attrs' => ['min' => 10, 'max' => 90, 'step' => 5],
                    'ui' => ['size' => 'lg', 'color' => 'accent'],
                ],
            ],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                [
                    'id' => 'name',
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Name',
                    'attrs' => [
                        'placeholder' => 'Jane Doe',
                        'autocomplete' => 'name',
                        'mask' => '999-999',
                        'maskCharPlaceholder' => '_',
                        'maskPlaceholder' => true,
                        'inputPlaceholder' => true,
                        'clearIncomplete' => true,
                        'obfuscate' => true,
                        'obfuscateChar' => '*',
                        'obfuscateKeepEnd' => 2,
                    ],
                    'ui' => ['size' => 'sm', 'color' => 'primary', 'width' => '1/2'],
                ],
                [
                    'id' => 'score',
                    'type' => 'range',
                    'name' => 'score',
                    'label' => 'Score',
                    'attrs' => ['min' => 10, 'max' => 90, 'step' => 5],
                    'ui' => ['size' => 'lg', 'color' => 'accent'],
                ],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/37c8e583c8e85a88abc1610d10f448d4.blade.php ENDPATH**/ ?>