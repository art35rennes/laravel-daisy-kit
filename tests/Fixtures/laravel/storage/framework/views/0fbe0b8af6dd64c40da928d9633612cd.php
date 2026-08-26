    <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['schema' => [
            'version' => '1.0',
            'id' => 'profile',
            'fields' => [
                [
                    'id' => 'profile_tabs',
                    'type' => 'tabs',
                    'label' => 'Profile sections',
                    'fields' => [
                        [
                            'id' => 'contact_tab',
                            'type' => 'section',
                            'label' => 'Contact',
                            'fields' => [
                                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                            ],
                        ],
                        [
                            'id' => 'bio_tab',
                            'type' => 'section',
                            'label' => 'Bio',
                            'fields' => [
                                ['id' => 'bio', 'type' => 'textarea', 'name' => 'bio', 'label' => 'Bio'],
                            ],
                        ],
                    ],
                ],
            ],
        ],'value' => ['email' => 'jane@example.com', 'bio' => 'Builder friendly']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'version' => '1.0',
            'id' => 'profile',
            'fields' => [
                [
                    'id' => 'profile_tabs',
                    'type' => 'tabs',
                    'label' => 'Profile sections',
                    'fields' => [
                        [
                            'id' => 'contact_tab',
                            'type' => 'section',
                            'label' => 'Contact',
                            'fields' => [
                                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                            ],
                        ],
                        [
                            'id' => 'bio_tab',
                            'type' => 'section',
                            'label' => 'Bio',
                            'fields' => [
                                ['id' => 'bio', 'type' => 'textarea', 'name' => 'bio', 'label' => 'Bio'],
                            ],
                        ],
                    ],
                ],
            ],
        ]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['email' => 'jane@example.com', 'bio' => 'Builder friendly'])]); ?>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/216571eb9077b36c8e5e942cf8b43288.blade.php ENDPATH**/ ?>