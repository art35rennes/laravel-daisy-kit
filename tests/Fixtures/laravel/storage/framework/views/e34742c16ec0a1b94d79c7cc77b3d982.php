    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['id' => 'brand-viewer','schema' => [
            'version' => '1.0',
            'id' => 'brand',
            'fields' => [
                [
                    'id' => 'brand_color',
                    'type' => 'color',
                    'name' => 'brand_color',
                    'label' => 'Brand color',
                    'attrs' => [
                        'mode' => 'advanced',
                        'dropdown' => true,
                        'swatches' => [['#123456', '#abcdef']],
                        'swatchesHeight' => 120,
                        'showAlpha' => false,
                        'showFormatToggle' => true,
                    ],
                ],
            ],
        ],'value' => ['brand_color' => '#123456']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'brand-viewer','schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'brand',
            'fields' => [
                [
                    'id' => 'brand_color',
                    'type' => 'color',
                    'name' => 'brand_color',
                    'label' => 'Brand color',
                    'attrs' => [
                        'mode' => 'advanced',
                        'dropdown' => true,
                        'swatches' => [['#123456', '#abcdef']],
                        'swatchesHeight' => 120,
                        'showAlpha' => false,
                        'showFormatToggle' => true,
                    ],
                ],
            ],
        ]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['brand_color' => '#123456'])]); ?>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/91eaa50ae5e466193fe2d77fb98e5508.blade.php ENDPATH**/ ?>