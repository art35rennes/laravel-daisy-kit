<div
    class="daisy-form-builder space-y-3"
    wire:click="clearSelection"
    data-module="form-builder"
    data-form-builder-livewire
    data-schema-id="<?php echo e($canonicalSchema['id'] ?? 'daisy-form-schema'); ?>"
>
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-box border border-base-300 bg-base-100 p-3">
        <div class="flex flex-wrap items-center gap-2">
            <div wire:click.stop>
                <?php if (isset($component)) { $__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.dropdown','data' => ['label' => __('daisy::form.builder.add_element'),'buttonClass' => 'btn btn-sm btn-primary','type' => 'card','contentClass' => 'dropdown-content z-20 mt-2 max-h-96 overflow-y-auto rounded-box border border-base-300 bg-base-100 shadow','cardBodyClass' => 'p-3','dataBuilderPalette' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.add_element')),'buttonClass' => 'btn btn-sm btn-primary','type' => 'card','contentClass' => 'dropdown-content z-20 mt-2 max-h-96 overflow-y-auto rounded-box border border-base-300 bg-base-100 shadow','cardBodyClass' => 'p-3','data-builder-palette' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fieldGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <section class="min-w-0">
                                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-base-content/50"><?php echo e($group['label']); ?></h3>
                                <div class="grid gap-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <button type="button" class="rounded-btn px-2 py-1.5 text-left text-sm hover:bg-base-200" wire:click.stop="addField('<?php echo e($fieldType['type']); ?>')" data-builder-add="<?php echo e($fieldType['type']); ?>">
                                            <span class="block truncate"><?php echo e($fieldType['label'] ?? $fieldType['type']); ?></span>
                                        </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </section>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9)): ?>
<?php $attributes = $__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9; ?>
<?php unset($__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9)): ?>
<?php $component = $__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9; ?>
<?php unset($__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9); ?>
<?php endif; ?>
            </div>

            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'secondary','wire:click.stop' => 'addStep']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'secondary','wire:click.stop' => 'addStep']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('daisy::form.builder.add_step')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'collapseAllFields','dataBuilderCollapseAll' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'collapseAllFields','data-builder-collapse-all' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('icon', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-arrows-collapse'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('daisy::form.builder.collapse_all')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'expandAllFields','dataBuilderExpandAll' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'expandAllFields','data-builder-expand-all' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('icon', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-arrows-expand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('daisy::form.builder.expand_all')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>

            <details class="dropdown" data-builder-schema-settings>
                <summary class="btn btn-sm btn-outline btn-info list-none">
                    <?php echo e(__('daisy::form.builder.schema_settings')); ?>

                </summary>

                <div class="dropdown-content z-20 mt-2 rounded-box border border-base-300 bg-base-100 p-3 shadow">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold"><?php echo e(__('daisy::form.builder.schema_settings')); ?></h3>
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'xs','variant' => 'ghost','color' => 'neutral','square' => true,'dataBuilderCloseMenu' => true,'ariaLabel' => ''.e(__('daisy::form.builder.close_menu')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'xs','variant' => 'ghost','color' => 'neutral','square' => true,'data-builder-close-menu' => true,'aria-label' => ''.e(__('daisy::form.builder.close_menu')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-x'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => __('daisy::form.builder.schema_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.schema_id'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['size' => 'sm','value' => ''.e($canonicalSchema['id'] ?? '').'','wire:change' => 'updateSchemaKey(\'id\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','value' => ''.e($canonicalSchema['id'] ?? '').'','wire:change' => 'updateSchemaKey(\'id\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => __('daisy::form.builder.schema_title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.schema_title'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['size' => 'sm','value' => ''.e(data_get($canonicalSchema, 'meta.title', '')).'','wire:change' => 'updateSchemaKey(\'meta.title\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','value' => ''.e(data_get($canonicalSchema, 'meta.title', '')).'','wire:change' => 'updateSchemaKey(\'meta.title\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => __('daisy::form.builder.schema_description'),'class' => 'md:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.schema_description')),'class' => 'md:col-span-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginale7580ac62991553be731e04dcee1e44e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7580ac62991553be731e04dcee1e44e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.textarea','data' => ['rows' => '2','size' => 'sm','wire:change' => 'updateSchemaKey(\'meta.description\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rows' => '2','size' => 'sm','wire:change' => 'updateSchemaKey(\'meta.description\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(data_get($canonicalSchema, 'meta.description', '')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7580ac62991553be731e04dcee1e44e)): ?>
<?php $attributes = $__attributesOriginale7580ac62991553be731e04dcee1e44e; ?>
<?php unset($__attributesOriginale7580ac62991553be731e04dcee1e44e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7580ac62991553be731e04dcee1e44e)): ?>
<?php $component = $__componentOriginale7580ac62991553be731e04dcee1e44e; ?>
<?php unset($__componentOriginale7580ac62991553be731e04dcee1e44e); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => __('daisy::form.builder.layout_type')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.layout_type'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['size' => 'sm','wire:change' => 'updateSchemaKey(\'layout.type\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','wire:change' => 'updateSchemaKey(\'layout.type\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['one-page', 'sections', 'multi-step']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layoutType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($layoutType); ?>" <?php if(data_get($canonicalSchema, 'layout.type') === $layoutType): echo 'selected'; endif; ?>><?php echo e(__("daisy::form.builder.layout_types.{$layoutType}")); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => __('daisy::form.builder.submit_mode')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.submit_mode'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['size' => 'sm','wire:change' => 'updateSchemaKey(\'submit.mode\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','wire:change' => 'updateSchemaKey(\'submit.mode\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['event', 'html', 'fetch', 'none']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submitMode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($submitMode); ?>" <?php if(data_get($canonicalSchema, 'submit.mode') === $submitMode): echo 'selected'; endif; ?>><?php echo e(__("daisy::form.builder.submit_modes.{$submitMode}")); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => __('daisy::form.builder.submit_label')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.submit_label'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['size' => 'sm','value' => ''.e(data_get($canonicalSchema, 'submit.label', '')).'','wire:change' => 'updateSchemaKey(\'submit.label\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','value' => ''.e(data_get($canonicalSchema, 'submit.label', '')).'','wire:change' => 'updateSchemaKey(\'submit.label\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                    </div>
                </div>
            </details>

            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'undo','disabled' => count($undoStack) === 0,'dataBuilderUndo' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'undo','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($undoStack) === 0),'data-builder-undo' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('icon', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-arrow-counterclockwise'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('daisy::form.builder.undo')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'redo','disabled' => count($redoStack) === 0,'dataBuilderRedo' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','wire:click' => 'redo','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(count($redoStack) === 0),'data-builder-redo' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('icon', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-arrow-clockwise'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('daisy::form.builder.redo')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'success','dataBuilderExport' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'success','data-builder-export' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('icon', null, []); ?> 
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-download'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                 <?php $__env->endSlot(); ?>
                <?php echo e(__('daisy::form.builder.export_json')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($functionCatalog) > 0): ?>
                <?php if (isset($component)) { $__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.dropdown','data' => ['label' => __('daisy::form.builder.functions'),'buttonClass' => 'btn btn-sm btn-ghost','contentClass' => 'dropdown-content z-20 mt-2 w-72 rounded-box border border-base-300 bg-base-100 p-3 shadow','type' => 'card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::form.builder.functions')),'buttonClass' => 'btn btn-sm btn-ghost','contentClass' => 'dropdown-content z-20 mt-2 w-72 rounded-box border border-base-300 bg-base-100 p-3 shadow','type' => 'card']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <ul class="space-y-1 text-xs" data-builder-functions>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $functionCatalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $definition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li class="rounded-box bg-base-200 px-2 py-1">
                                <?php echo e(trim(($definition['name'] ?? '').' '.($definition['signature'] ?? ''))); ?>

                            </li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9)): ?>
<?php $attributes = $__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9; ?>
<?php unset($__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9)): ?>
<?php $component = $__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9; ?>
<?php unset($__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['size' => 'sm','class' => 'w-56','placeholder' => ''.e(__('daisy::form.builder.search_fields')).'','wire:model.live.debounce.250ms' => 'fieldSearch','dataBuilderSearch' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','class' => 'w-56','placeholder' => ''.e(__('daisy::form.builder.search_fields')).'','wire:model.live.debounce.250ms' => 'fieldSearch','data-builder-search' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($diagnostics) > 0): ?>
                <span class="badge badge-error badge-outline"><?php echo e(trans_choice('daisy::form.builder.errors_count', count($diagnostics), ['count' => count($diagnostics)])); ?></span>
            <?php else: ?>
                <span class="badge badge-success badge-outline"><?php echo e(__('daisy::form.builder.valid_schema')); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="badge badge-outline"><?php echo e(trans_choice('daisy::form.builder.fields_count', count($allFields), ['count' => count($allFields)])); ?></span>
        </div>
    </div>

    <div class="tabs tabs-box lg:grid lg:grid-cols-[minmax(0,1.25fr)_minmax(28rem,1fr)] lg:items-start lg:gap-4">
        <input type="radio" name="daisy-form-builder-mobile-tabs" class="tab lg:hidden" aria-label="<?php echo e(__('daisy::form.builder.builder_tab')); ?>" checked />
        <section class="tab-content border-base-300 bg-base-100 p-3 lg:!block lg:border-0 lg:bg-transparent lg:p-0" data-builder-authoring>
            <div class="min-w-0 rounded-box border border-base-300 bg-base-100 p-4">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold"><?php echo e(__('daisy::form.builder.surface')); ?></h2>
                        <p class="text-sm text-base-content/60"><?php echo e(__('daisy::form.builder.surface_help')); ?></p>
                    </div>
                </div>

                <div class="tabs tabs-border">
                    <input type="radio" name="daisy-form-builder-authoring-tabs" class="tab" aria-label="<?php echo e(__('daisy::form.builder.visual_tab')); ?>" checked />
                    <div class="tab-content pt-4">
                        <div class="min-w-0 overflow-x-auto rounded-box border border-base-300" data-builder-outline>
                            <table class="table table-xs daisy-form-builder-table">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('daisy::form.builder.columns.element')); ?></th>
                                        <th><?php echo e(__('daisy::form.builder.columns.type')); ?></th>
                                        <th><?php echo e(__('daisy::form.builder.columns.state')); ?></th>
                                        <th class="text-center"><?php echo e(__('daisy::form.builder.columns.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $fieldParents = collect($flatFields)
                                            ->mapWithKeys(fn (array $field): array => [(string) ($field['id'] ?? '') => $field['_parent'] ?? null])
                                            ->all();
                                        $fieldDescendants = [];

                                        foreach (array_keys($fieldParents) as $fieldId) {
                                            $fieldDescendants[$fieldId] = [];
                                        }

                                        $fieldSiblingsByParent = [];

                                        foreach ($fieldParents as $fieldId => $parentId) {
                                            $parentKey = $parentId ?? '__root';
                                            $fieldSiblingsByParent[$parentKey] ??= [];
                                            $fieldSiblingsByParent[$parentKey][] = $fieldId;

                                            while ($parentId) {
                                                $fieldDescendants[$parentId][] = $fieldId;
                                                $parentId = $fieldParents[$parentId] ?? null;
                                            }
                                        }
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $flatFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php
                                            $isSelected = $selectedId === ($field['id'] ?? null);
                                            $fieldDiagnostics = $diagnosticsByField[$field['id'] ?? ''] ?? [];
                                            $isContainer = is_array($field['fields'] ?? null);
                                            $fieldId = (string) ($field['id'] ?? '');
                                            $parentId = $fieldParents[$fieldId] ?? null;
                                            $parentKey = $parentId ?? '__root';
                                            $siblings = $fieldSiblingsByParent[$parentKey] ?? [];
                                            $siblingIndex = array_search($fieldId, $siblings, true);
                                            $siblingIndex = is_int($siblingIndex) ? $siblingIndex : 0;
                                            $previousSiblingId = $siblings[$siblingIndex - 1] ?? null;
                                            $isLastDirectSibling = $fieldId === end($siblings);
                                            $hasExpandableChildren = $isContainer && count((array) ($field['fields'] ?? [])) > 0;
                                            $isCollapsed = $collapsedFieldIds[$field['id'] ?? ''] ?? false;
                                            $depth = max(0, (int) ($field['_depth'] ?? 0));
                                            $status = count($fieldDiagnostics) > 0
                                                ? ['label' => __('daisy::form.builder.state_invalid'), 'class' => 'badge-error text-error']
                                                : ((array_key_exists('visibleWhen', $field) || array_key_exists('computed', $field))
                                                    ? ['label' => __('daisy::form.builder.state_conditional'), 'class' => 'badge-warning text-warning']
                                                    : ['label' => __('daisy::form.builder.state_valid'), 'class' => 'badge-success text-success']);
                                            $type = (string) ($field['type'] ?? 'text');
                                            $typeBadgeClass = match ($type) {
                                                'text', 'email', 'tel', 'url', 'password', 'number', 'textarea', 'color' => 'badge-info text-info',
                                                'select', 'radio', 'checkbox', 'toggle', 'range' => 'badge-secondary text-secondary',
                                                'date', 'time', 'datetime-local', 'month' => 'badge-warning text-warning',
                                                'file', 'signature' => 'badge-accent text-accent',
                                                'staticText' => 'badge-success text-success',
                                                'section', 'tabs', 'wizardStep' => 'badge-primary text-primary',
                                                'hidden' => 'badge-neutral text-neutral',
                                                default => 'badge-outline text-base-content',
                                            };
                                        ?>
                                        <tr class="daisy-form-builder-drop-row" data-builder-drop-row>
                                            <td colspan="4">
                                                <button
                                                    type="button"
                                                    class="daisy-form-builder-drop-zone daisy-form-builder-drop-zone-position"
                                                    data-builder-drop-depth="<?php echo e(min($depth, 12)); ?>"
                                                    data-builder-drop-tone="<?php echo e($depth % 16); ?>"
                                                    data-builder-drop-target="<?php echo e($fieldId); ?>"
                                                    data-builder-drop-descendants='<?php echo json_encode($fieldDescendants[$fieldId] ?? [], 15, 512) ?>'
                                                    data-builder-drop-kind="position"
                                                    data-builder-drop-action="before"
                                                    data-builder-drop-parent="<?php echo e($parentKey); ?>"
                                                    data-builder-drop-index="<?php echo e($siblingIndex); ?>"
                                                    data-builder-drop-previous="<?php echo e($previousSiblingId ?? ''); ?>"
                                                    data-builder-drop-zone="before"
                                                    aria-label="<?php echo e(__('daisy::form.builder.drop_position')); ?>"
                                                >
                                                    <span class="daisy-form-builder-drop-icon">+</span>
                                                    <span><?php echo e(__('daisy::form.builder.drop_position')); ?></span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr
                                            class="group relative <?php echo e($isSelected ? 'bg-primary/5' : ''); ?>"
                                            data-builder-field="<?php echo e($field['id'] ?? ''); ?>"
                                            data-builder-field-depth="<?php echo e($depth); ?>"
                                            <?php if($isSelected): ?> data-builder-selected="true" <?php endif; ?>
                                            wire:click.stop="selectField('<?php echo e($field['id']); ?>')"
                                        >
                                            <td>
                                                <div
                                                    class="daisy-form-builder-field-main min-w-0"
                                                >
                                                    <span
                                                        class="daisy-form-builder-drag-handle relative z-10 flex size-10 cursor-grab select-none items-center justify-center rounded-btn border border-base-300 bg-base-100 text-base-content/55 transition hover:border-primary hover:bg-primary/10 hover:text-primary active:cursor-grabbing group-hover:text-base-content"
                                                        data-builder-drag-handle
                                                        data-builder-drag-field="<?php echo e($fieldId); ?>"
                                                        data-builder-drag-descendants='<?php echo json_encode($fieldDescendants[$fieldId] ?? [], 15, 512) ?>'
                                                        data-builder-drag-parent="<?php echo e($parentKey); ?>"
                                                        data-builder-drag-index="<?php echo e($siblingIndex); ?>"
                                                        data-builder-stop-propagation
                                                        aria-label="<?php echo e(__('daisy::form.builder.drag_handle')); ?>"
                                                    >
                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-grip-vertical'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5 pointer-events-none']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                    </span>
                                                    <div class="daisy-form-builder-field-label min-w-0">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasExpandableChildren): ?>
                                                            <button
                                                                type="button"
                                                                class="btn btn-ghost btn-xs !h-6 !min-h-6 !w-5 p-0 text-base-content/55"
                                                                wire:click.stop="toggleFieldCollapsed('<?php echo e($field['id']); ?>')"
                                                                data-builder-collapse="<?php echo e($isCollapsed ? 'closed' : 'open'); ?>"
                                                                aria-label="<?php echo e($isCollapsed ? __('daisy::form.builder.expand') : __('daisy::form.builder.collapse')); ?>"
                                                            >
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCollapsed): ?>
                                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-chevron-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                                <?php else: ?>
                                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </button>
                                                        <?php else: ?>
                                                            <span></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <button
                                                            type="button"
                                                            class="min-w-0 text-left"
                                                            wire:click.stop="selectField('<?php echo e($field['id']); ?>')"
                                                            data-builder-select
                                                            <?php if($isSelected): ?> aria-current="true" <?php endif; ?>
                                                        >
                                                            <span class="block truncate text-sm font-medium"><?php echo e($field['label'] ?? $field['id']); ?></span>
                                                            <span class="block truncate text-xs text-base-content/60"><?php echo e($field['id'] ?? ''); ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-sm badge-outline font-mono daisy-form-builder-type-badge <?php echo e($typeBadgeClass); ?>" data-builder-type-badge="<?php echo e($type); ?>"><?php echo e($type); ?></span></td>
                                            <td><span class="badge badge-sm badge-outline <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span></td>
                                            <td>
                                                <div class="flex justify-center gap-1.5">
                                                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'info','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'editField(\''.e($field['id']).'\')','dataBuilderEdit' => true,'ariaLabel' => ''.e(__('daisy::form.builder.edit_field')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'info','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'editField(\''.e($field['id']).'\')','data-builder-edit' => true,'aria-label' => ''.e(__('daisy::form.builder.edit_field')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-pencil'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                                                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'moveField(\''.e($field['id']).'\', -1)','dataBuilderMove' => 'up','ariaLabel' => ''.e(__('daisy::form.builder.move_up')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'moveField(\''.e($field['id']).'\', -1)','data-builder-move' => 'up','aria-label' => ''.e(__('daisy::form.builder.move_up')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-arrow-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                                                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'moveField(\''.e($field['id']).'\', 1)','dataBuilderMove' => 'down','ariaLabel' => ''.e(__('daisy::form.builder.move_down')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'neutral','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'moveField(\''.e($field['id']).'\', 1)','data-builder-move' => 'down','aria-label' => ''.e(__('daisy::form.builder.move_down')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-arrow-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                                                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'error','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'removeField(\''.e($field['id']).'\')','dataBuilderDelete' => true,'ariaLabel' => ''.e(__('daisy::form.builder.remove')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'outline','color' => 'error','square' => true,'class' => '!min-h-9 !h-9 !w-9','wire:click.stop' => 'removeField(\''.e($field['id']).'\')','data-builder-delete' => true,'aria-label' => ''.e(__('daisy::form.builder.remove')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-x'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isContainer && (! $hasExpandableChildren || $isCollapsed)): ?>
                                            <tr class="daisy-form-builder-drop-row daisy-form-builder-drop-row-inside" data-builder-drop-row>
                                                <td colspan="4">
                                                    <button
                                                        type="button"
                                                        class="daisy-form-builder-drop-zone daisy-form-builder-drop-zone-position"
                                                        data-builder-drop-depth="<?php echo e(min($depth + 1, 12)); ?>"
                                                        data-builder-drop-tone="<?php echo e(($depth + 1) % 16); ?>"
                                                        data-builder-drop-target="<?php echo e($fieldId); ?>"
                                                        data-builder-drop-descendants='<?php echo json_encode($fieldDescendants[$fieldId] ?? [], 15, 512) ?>'
                                                        data-builder-drop-kind="inside"
                                                        data-builder-drop-action="inside"
                                                        data-builder-drop-parent="<?php echo e($fieldId); ?>"
                                                        data-builder-drop-index="<?php echo e(count((array) ($field['fields'] ?? []))); ?>"
                                                        data-builder-drop-zone="inside"
                                                        aria-label="<?php echo e(__('daisy::form.builder.drop_inside')); ?>"
                                                    >
                                                        <span class="daisy-form-builder-drop-icon">+</span>
                                                        <span><?php echo e(__('daisy::form.builder.drop_inside')); ?></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLastDirectSibling): ?>
                                            <tr class="daisy-form-builder-drop-row" data-builder-drop-row>
                                                <td colspan="4">
                                                    <button
                                                        type="button"
                                                        class="daisy-form-builder-drop-zone daisy-form-builder-drop-zone-position"
                                                        data-builder-drop-depth="<?php echo e(min($depth, 12)); ?>"
                                                        data-builder-drop-tone="<?php echo e($depth % 16); ?>"
                                                        data-builder-drop-target="<?php echo e($fieldId); ?>"
                                                        data-builder-drop-descendants='<?php echo json_encode($fieldDescendants[$fieldId] ?? [], 15, 512) ?>'
                                                        data-builder-drop-kind="position"
                                                        data-builder-drop-action="after"
                                                        data-builder-drop-parent="<?php echo e($parentKey); ?>"
                                                        data-builder-drop-index="<?php echo e(count($siblings)); ?>"
                                                        data-builder-drop-previous="<?php echo e($fieldId); ?>"
                                                        data-builder-drop-zone="after"
                                                        aria-label="<?php echo e(__('daisy::form.builder.drop_position_end')); ?>"
                                                    >
                                                        <span class="daisy-form-builder-drop-icon">+</span>
                                                        <span><?php echo e(__('daisy::form.builder.drop_position_end')); ?></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-sm text-base-content/60">
                                                <?php echo e(__('daisy::form.builder.no_fields_match')); ?>

                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jsonEditor): ?>
                        <input type="radio" name="daisy-form-builder-authoring-tabs" class="tab" aria-label="<?php echo e(__('daisy::form.builder.json_tab')); ?>" />
                        <div class="tab-content pt-4">
                            <?php if (isset($component)) { $__componentOriginal7f2198e36a93467cb80ff8453529a12c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7f2198e36a93467cb80ff8453529a12c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.code-editor','data' => ['language' => 'json','value' => $canonicalJson,'height' => '28rem','fontSize' => '0.85rem','showFoldAll' => true,'showUnfoldAll' => true,'showFormat' => true,'showCopy' => true,'wire:ignore' => true,'wire:key' => 'daisy-form-builder-schema-json-'.e(md5($canonicalJson)).'','dataBuilderJson' => true,'dataBuilderJsonDebounce' => '700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.code-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['language' => 'json','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canonicalJson),'height' => '28rem','font-size' => '0.85rem','show-fold-all' => true,'show-unfold-all' => true,'show-format' => true,'show-copy' => true,'wire:ignore' => true,'wire:key' => 'daisy-form-builder-schema-json-'.e(md5($canonicalJson)).'','data-builder-json' => true,'data-builder-json-debounce' => '700']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7f2198e36a93467cb80ff8453529a12c)): ?>
<?php $attributes = $__attributesOriginal7f2198e36a93467cb80ff8453529a12c; ?>
<?php unset($__attributesOriginal7f2198e36a93467cb80ff8453529a12c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7f2198e36a93467cb80ff8453529a12c)): ?>
<?php $component = $__componentOriginal7f2198e36a93467cb80ff8453529a12c; ?>
<?php unset($__componentOriginal7f2198e36a93467cb80ff8453529a12c); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($name): ?>
                    <textarea name="<?php echo e($name); ?>" class="hidden" data-builder-hidden><?php echo e($canonicalJson); ?></textarea>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <script type="application/json" data-builder-export-json><?php echo json_encode($canonicalSchema, 15, 512) ?></script>
            </div>
        </section>

        <input type="radio" name="daisy-form-builder-mobile-tabs" class="tab lg:hidden" aria-label="<?php echo e(__('daisy::form.builder.preview_tab')); ?>" />
        <section class="tab-content border-base-300 bg-base-100 p-3 lg:!block lg:border-0 lg:bg-transparent lg:p-0" data-builder-preview-panel>
            <div class="space-y-4 lg:sticky lg:top-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($preview): ?>
                    <div class="rounded-box border border-base-300 bg-base-100 p-4" data-builder-stop-propagation data-builder-preview>
                        <h2 class="mb-3 font-semibold"><?php echo e(__('daisy::form.builder.preview')); ?></h2>
                        <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'daisy-form-builder-viewer-'.e(md5($canonicalJson)).''; ?>wire:key="daisy-form-builder-viewer-<?php echo e(md5($canonicalJson)); ?>">
                            <?php if (isset($component)) { $__componentOriginald486fd47b3d505837fe881ab44f482da = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald486fd47b3d505837fe881ab44f482da = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.viewer','data' => ['schema' => $canonicalSchema,'value' => $value,'errors' => $errors,'submitMode' => $viewerSubmitMode]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.viewer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canonicalSchema),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'errors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors),'submit-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($viewerSubmitMode)]); ?>
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
<?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="rounded-box border border-error/40 bg-error/5 p-3 <?php echo e(count($diagnostics) === 0 ? 'hidden' : ''); ?>" data-builder-diagnostics-panel>
                    <h2 class="mb-2 font-semibold text-error"><?php echo e(__('daisy::form.builder.diagnostics')); ?></h2>
                    <ul class="list-disc space-y-1 ps-4 text-sm text-error" data-builder-diagnostics>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $diagnostics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $diagnostic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><?php echo e($diagnostic['message'] ?? $diagnostic['code'] ?? 'Invalid schema.'); ?></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedField): ?>
        <?php if (isset($component)) { $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.modal','data' => ['id' => 'daisy-form-builder-field-editor','open' => $fieldEditorOpen,'backdrop' => false,'closeButton' => false,'teleport' => false,'size' => '4xl','vertical' => 'middle','boxClass' => 'border border-base-300 shadow-xl','dataBuilderEditorModal' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'daisy-form-builder-field-editor','open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldEditorOpen),'backdrop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'close-button' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'teleport' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'size' => '4xl','vertical' => 'middle','boxClass' => 'border border-base-300 shadow-xl','data-builder-editor-modal' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="daisy-form-builder-editor" wire:click.stop>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-base-content/60"><?php echo e(__('daisy::form.builder.edit_field')); ?></p>
                        <h3 class="text-lg font-semibold"><?php echo e($selectedField['label'] ?? $selectedField['id']); ?></h3>
                        <p class="text-sm text-base-content/60"><?php echo e($selectedField['id'] ?? ''); ?> · <?php echo e($selectedField['type'] ?? 'text'); ?></p>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','color' => 'neutral','square' => true,'wire:click.stop' => 'cancelFieldEditor','ariaLabel' => ''.e(__('daisy::form.builder.cancel_editor')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','color' => 'neutral','square' => true,'wire:click.stop' => 'cancelFieldEditor','aria-label' => ''.e(__('daisy::form.builder.cancel_editor')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
× <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                </div>

                <?php echo $__env->make('daisy::livewire.form-builder-field-properties', [
                    'selectedField' => $selectedField,
                    'propertyGroups' => $propertyGroups,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

             <?php $__env->slot('actions', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','variant' => 'outline','color' => 'error','wire:click.stop' => 'cancelFieldEditor','dataBuilderEditorCancel' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'outline','color' => 'error','wire:click.stop' => 'cancelFieldEditor','data-builder-editor-cancel' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('daisy::form.builder.cancel_editor')); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','color' => 'success','wire:click.stop' => 'closeFieldEditor','dataBuilderEditorConfirm' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','color' => 'success','wire:click.stop' => 'closeFieldEditor','data-builder-editor-confirm' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('daisy::form.builder.confirm_editor')); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $attributes = $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $component = $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/livewire/form-builder.blade.php ENDPATH**/ ?>