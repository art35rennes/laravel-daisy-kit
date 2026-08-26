    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['id' => 'upload-viewer','method' => 'POST','schema' => [
            'version' => '1.0',
            'id' => 'upload',
            'fields' => [
                [
                    'id' => 'documents',
                    'type' => 'section',
                    'label' => 'Documents',
                    'fields' => [
                        [
                            'id' => 'attachments',
                            'type' => 'file',
                            'name' => 'attachments',
                            'label' => 'Attachments',
                            'attrs' => [
                                'accept' => '.pdf,image/*',
                                'multiple' => true,
                            ],
                            'ui' => [
                                'size' => 'sm',
                                'color' => 'primary',
                            ],
                        ],
                    ],
                ],
            ],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'upload-viewer','method' => 'POST','schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'upload',
            'fields' => [
                [
                    'id' => 'documents',
                    'type' => 'section',
                    'label' => 'Documents',
                    'fields' => [
                        [
                            'id' => 'attachments',
                            'type' => 'file',
                            'name' => 'attachments',
                            'label' => 'Attachments',
                            'attrs' => [
                                'accept' => '.pdf,image/*',
                                'multiple' => true,
                            ],
                            'ui' => [
                                'size' => 'sm',
                                'color' => 'primary',
                            ],
                        ],
                    ],
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/6406b098dac128dcdaf213fb614a8b45.blade.php ENDPATH**/ ?>