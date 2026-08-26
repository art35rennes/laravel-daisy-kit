<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'selectedField',
    'propertyGroups',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'selectedField',
    'propertyGroups',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="space-y-5" data-builder-field-editor>
    <?php
        $properties = collect($propertyGroups)
            ->flatMap(fn (array $group): array => $group['properties'] ?? [])
            ->values();
        $propertiesByPath = $properties->keyBy('path');
        $fieldName = (string) ($selectedField['name'] ?? '');
        $fieldId = (string) ($selectedField['id'] ?? '');
        $hasPayloadName = $propertiesByPath->has('name');
        $hasCustomId = ! $hasPayloadName || $fieldName === '' || $fieldId !== $fieldName;
        $editorTabsId = 'daisy-form-builder-field-editor-tabs-'.($fieldId !== '' ? $fieldId : 'field');
        $componentPaths = $properties
            ->pluck('path')
            ->filter(fn (string $path): bool => str_starts_with($path, 'attrs.') || (str_starts_with($path, 'ui.') && $path !== 'ui.width'))
            ->values()
            ->all();
        $dataPaths = $hasPayloadName ? ['default', 'options', 'rules'] : [];
        $editorSections = collect([
            [
                'id' => 'general',
                'label' => __('daisy::form.builder.editor_tabs.general'),
                'help' => __('daisy::form.builder.editor_tabs_help.general'),
                'paths' => ['label', 'description', 'text'],
                'always' => true,
            ],
            [
                'id' => 'data',
                'label' => __('daisy::form.builder.editor_tabs.data'),
                'help' => __('daisy::form.builder.editor_tabs_help.data'),
                'paths' => $dataPaths,
            ],
            [
                'id' => 'display',
                'label' => __('daisy::form.builder.editor_tabs.display'),
                'help' => __('daisy::form.builder.editor_tabs_help.display'),
                'paths' => array_values(array_merge(['ui.width'], $componentPaths)),
            ],
            [
                'id' => 'logic',
                'label' => __('daisy::form.builder.editor_tabs.logic'),
                'help' => __('daisy::form.builder.editor_tabs_help.logic'),
                'paths' => ['visibleWhen', 'computed'],
            ],
        ])
            ->filter(fn (array $section): bool => ($section['always'] ?? false)
                || collect($section['paths'])->contains(fn (string $path): bool => $propertiesByPath->has($path)))
            ->values()
            ->all();
    ?>

    <div
        class="tabs tabs-border daisy-form-builder-editor-tabs"
        data-builder-editor-tabs
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $editorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionIndex => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <input
                type="radio"
                name="<?php echo e($editorTabsId); ?>"
                class="tab"
                aria-label="<?php echo e($section['label']); ?>"
                <?php if($sectionIndex === 0): echo 'checked'; endif; ?>
            />

            <section class="tab-content space-y-4 pt-4" data-builder-editor-tab-panel="<?php echo e($section['id']); ?>">
                <div class="rounded-box border border-base-300 bg-base-200/40 p-3">
                    <h4 class="text-sm font-semibold"><?php echo e($section['label']); ?></h4>
                    <p class="mt-1 text-xs text-base-content/60"><?php echo e($section['help']); ?></p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($section['id'] === 'general'): ?>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasPayloadName && $propertiesByPath->has('name')): ?>
                            <?php
                                $property = $propertiesByPath->get('name');
                            ?>
                            <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['type' => 'text','size' => 'sm','value' => ''.e($fieldName).'','dataBuilderFieldName' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','size' => 'sm','value' => ''.e($fieldName).'','data-builder-field-name' => true]); ?>
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
                                 <?php $__env->slot('hintSlot', null, []); ?> 
                                    <?php echo e($property['help'] ?? 'name'); ?>

                                    <code class="kbd kbd-xs ms-1">name</code>
                                 <?php $__env->endSlot(); ?>
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

                            <div class="space-y-3 rounded-box border border-base-300 p-3">
                                <label class="flex items-start gap-3 text-sm">
                                    <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['checked' => $hasCustomId,'dataBuilderCustomId' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasCustomId),'data-builder-custom-id' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $attributes = $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $component = $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
                                    <span>
                                        <span class="block font-medium"><?php echo e(__('daisy::form.builder.custom_field_id')); ?></span>
                                        <span class="block text-xs text-base-content/60"><?php echo e(__('daisy::form.builder.custom_field_id_help')); ?></span>
                                    </span>
                                </label>

                                <?php
                                    $property = $propertiesByPath->get('id');
                                ?>
                                <div data-builder-custom-id-panel <?php if(! $hasCustomId): ?> hidden <?php endif; ?>>
                                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['type' => 'text','size' => 'sm','value' => ''.e($fieldId).'','wire:change' => 'updateSelectedPath(\'id\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','size' => 'sm','value' => ''.e($fieldId).'','wire:change' => 'updateSelectedPath(\'id\', $event.target.value)']); ?>
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
                                         <?php $__env->slot('hintSlot', null, []); ?> 
                                            <?php echo e($property['help'] ?? 'id'); ?>

                                            <code class="kbd kbd-xs ms-1">id</code>
                                         <?php $__env->endSlot(); ?>
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
                        <?php elseif($propertiesByPath->has('id')): ?>
                            <?php
                                $property = $propertiesByPath->get('id');
                            ?>
                            <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['type' => 'text','size' => 'sm','value' => ''.e($fieldId).'','wire:change' => 'updateSelectedPath(\'id\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','size' => 'sm','value' => ''.e($fieldId).'','wire:change' => 'updateSelectedPath(\'id\', $event.target.value)']); ?>
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
                                 <?php $__env->slot('hintSlot', null, []); ?> 
                                    <?php echo e($property['help'] ?? 'id'); ?>

                                    <code class="kbd kbd-xs ms-1">id</code>
                                 <?php $__env->endSlot(); ?>
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
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['paths']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(! $propertiesByPath->has($path)) continue; ?>
                    <?php
                        $property = $propertiesByPath->get($path);
                        $path = $property['path'];
                        $control = $property['control'];
                        $current = data_get($selectedField, $path);
                    ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($control === 'options'): ?>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="text-sm font-medium"><?php echo e($property['label']); ?></span>
                                <p class="text-xs text-base-content/60"><?php echo e($property['help'] ?? $path); ?></p>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'xs','variant' => 'outline','color' => 'primary','wire:click' => 'addSelectedOption']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'xs','variant' => 'outline','color' => 'primary','wire:click' => 'addSelectedOption']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('daisy::form.builder.add_option')); ?> <?php echo $__env->renderComponent(); ?>
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

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_values((array) ($selectedField['options'] ?? [])); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] gap-2">
                                <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['size' => 'sm','value' => ''.e($option['label'] ?? '').'','wire:change' => 'updateSelectedOption('.e($index).', \'label\', $event.target.value)','ariaLabel' => ''.e(__('daisy::form.builder.option_label')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','value' => ''.e($option['label'] ?? '').'','wire:change' => 'updateSelectedOption('.e($index).', \'label\', $event.target.value)','aria-label' => ''.e(__('daisy::form.builder.option_label')).'']); ?>
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
                                <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['size' => 'sm','value' => ''.e($option['value'] ?? '').'','wire:change' => 'updateSelectedOption('.e($index).', \'value\', $event.target.value)','ariaLabel' => ''.e(__('daisy::form.builder.option_value')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','value' => ''.e($option['value'] ?? '').'','wire:change' => 'updateSelectedOption('.e($index).', \'value\', $event.target.value)','aria-label' => ''.e(__('daisy::form.builder.option_value')).'']); ?>
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
                                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','color' => 'error','square' => true,'wire:click' => 'removeSelectedOption('.e($index).')','ariaLabel' => ''.e(__('daisy::form.builder.remove_option')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','color' => 'error','square' => true,'wire:click' => 'removeSelectedOption('.e($index).')','aria-label' => ''.e(__('daisy::form.builder.remove_option')).'']); ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php elseif($control === 'select'): ?>
                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label'],'hint' => $property['help'] ?? $path]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label']),'hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['help'] ?? $path)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['size' => 'sm','wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $property['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($option); ?>" <?php if((string) ($current ?? '') === (string) $option): echo 'selected'; endif; ?>><?php echo e($option === '' ? __('daisy::form.builder.default_option') : $option); ?></option>
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
                         <?php $__env->slot('hintSlot', null, []); ?> 
                            <?php echo e($property['help'] ?? $path); ?>

                            <code class="kbd kbd-xs ms-1"><?php echo e($path); ?></code>
                         <?php $__env->endSlot(); ?>
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
                <?php elseif($control === 'boolean'): ?>
                    <label class="flex items-center gap-2 text-sm">
                        <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['checked' => (bool) $current,'wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.checked)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $current),'wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.checked)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $attributes = $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $component = $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
                        <span>
                            <span class="block"><?php echo e($property['label']); ?></span>
                            <span class="block text-xs text-base-content/60"><?php echo e($property['help'] ?? $path); ?> <code class="kbd kbd-xs"><?php echo e($path); ?></code></span>
                        </span>
                    </label>
                <?php elseif($control === 'textarea'): ?>
                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginale7580ac62991553be731e04dcee1e44e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7580ac62991553be731e04dcee1e44e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.textarea','data' => ['rows' => '3','size' => 'sm','wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rows' => '3','size' => 'sm','wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.value)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(is_scalar($current) ? $current : ''); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7580ac62991553be731e04dcee1e44e)): ?>
<?php $attributes = $__attributesOriginale7580ac62991553be731e04dcee1e44e; ?>
<?php unset($__attributesOriginale7580ac62991553be731e04dcee1e44e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7580ac62991553be731e04dcee1e44e)): ?>
<?php $component = $__componentOriginale7580ac62991553be731e04dcee1e44e; ?>
<?php unset($__componentOriginale7580ac62991553be731e04dcee1e44e); ?>
<?php endif; ?>
                         <?php $__env->slot('hintSlot', null, []); ?> 
                            <?php echo e($property['help'] ?? $path); ?>

                            <code class="kbd kbd-xs ms-1"><?php echo e($path); ?></code>
                         <?php $__env->endSlot(); ?>
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
                <?php elseif($control === 'json'): ?>
                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginal7f2198e36a93467cb80ff8453529a12c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7f2198e36a93467cb80ff8453529a12c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.code-editor','data' => ['language' => 'json','value' => json_encode($current ?? ($path === 'default' ? null : []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),'height' => '11rem','fontSize' => '0.78rem','showFoldAll' => false,'showUnfoldAll' => false,'showFormat' => true,'showCopy' => true,'wire:ignore' => true,'wire:key' => 'daisy-form-builder-field-json-'.e($selectedField['id'] ?? 'field').'-'.e(str_replace('.', '-', $path)).'','dataBuilderJsonProperty' => ''.e($path).'','dataBuilderJsonDebounce' => '500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.code-editor'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['language' => 'json','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(json_encode($current ?? ($path === 'default' ? null : []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)),'height' => '11rem','font-size' => '0.78rem','show-fold-all' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'show-unfold-all' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'show-format' => true,'show-copy' => true,'wire:ignore' => true,'wire:key' => 'daisy-form-builder-field-json-'.e($selectedField['id'] ?? 'field').'-'.e(str_replace('.', '-', $path)).'','data-builder-json-property' => ''.e($path).'','data-builder-json-debounce' => '500']); ?>
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
                         <?php $__env->slot('hintSlot', null, []); ?> 
                            <?php echo e($property['help'] ?? $path); ?>

                            <code class="kbd kbd-xs ms-1"><?php echo e($path); ?></code>
                         <?php $__env->endSlot(); ?>
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
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => $property['label']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property['label'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['type' => ''.e($control === 'number' ? 'number' : 'text').'','size' => 'sm','value' => ''.e(is_scalar($current) ? $current : '').'','wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.value)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => ''.e($control === 'number' ? 'number' : 'text').'','size' => 'sm','value' => ''.e(is_scalar($current) ? $current : '').'','wire:change' => 'updateSelectedPath(\''.e($path).'\', $event.target.value)']); ?>
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
                         <?php $__env->slot('hintSlot', null, []); ?> 
                            <?php echo e($property['help'] ?? $path); ?>

                            <code class="kbd kbd-xs ms-1"><?php echo e($path); ?></code>
                         <?php $__env->endSlot(); ?>
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
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </section>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/livewire/form-builder-field-properties.blade.php ENDPATH**/ ?>