    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['schema' => [
            'version' => '1.0',
            'id' => 'agreement',
            'fields' => [
                [
                    'id' => 'signature',
                    'type' => 'signature',
                    'name' => 'signature',
                    'label' => 'Signature',
                    'attrs' => [
                        'width' => 620,
                        'height' => 240,
                        'penColor' => '#123456',
                        'minWidth' => 1,
                        'maxWidth' => 4,
                        'velocityFilterWeight' => 0.4,
                        'responsive' => true,
                        'showActions' => false,
                        'downloadFormat' => 'svg',
                        'downloadFilename' => 'agreement-signature',
                    ],
                ],
            ],
        ],'value' => ['signature' => 'data:image/png;base64,abc']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'agreement',
            'fields' => [
                [
                    'id' => 'signature',
                    'type' => 'signature',
                    'name' => 'signature',
                    'label' => 'Signature',
                    'attrs' => [
                        'width' => 620,
                        'height' => 240,
                        'penColor' => '#123456',
                        'minWidth' => 1,
                        'maxWidth' => 4,
                        'velocityFilterWeight' => 0.4,
                        'responsive' => true,
                        'showActions' => false,
                        'downloadFormat' => 'svg',
                        'downloadFilename' => 'agreement-signature',
                    ],
                ],
            ],
        ]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['signature' => 'data:image/png;base64,abc'])]); ?>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/3ba7a2b6efddc97a34f59520a725e9ec.blade.php ENDPATH**/ ?>