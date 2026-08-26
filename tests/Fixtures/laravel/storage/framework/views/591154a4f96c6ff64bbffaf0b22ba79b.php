
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'field',
    'value' => [],
    'errors' => [],
    'readonly' => false,
    'formId' => null,
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
    'field',
    'value' => [],
    'errors' => [],
    'readonly' => false,
    'formId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $allErrors = $errors;
    $type = $field['type'] ?? 'text';
    $id = $field['id'] ?? uniqid('field-');
    $name = $field['name'] ?? $id;
    $label = $field['label'] ?? $name;
    $description = $field['description'] ?? null;
    $safeDomSeed = \Illuminate\Support\Str::slug((string) (($formId ?: 'daisy-form-viewer').'-'.$id));
    $fieldDomId = $safeDomSeed !== '' ? $safeDomSeed : 'daisy-form-field-'.uniqid();
    $controlId = $fieldDomId.'-control';
    $fieldValue = data_get($value, $name, data_get($value, $id, $field['default'] ?? null));
    $fieldErrors = array_values((array) data_get($allErrors, $name, []));
    $hasError = count($fieldErrors) > 0;
    // Hidden computed values still participate in payloads while staying out of the visible layout.
    $isComputedHidden = ($field['computed']['mode'] ?? null) === 'hidden';
    $isReadonly = (bool) $readonly || (($field['computed']['mode'] ?? null) === 'readonly');
    $options = array_values((array) ($field['options'] ?? []));
    $attrs = new \Illuminate\View\ComponentAttributeBag((array) ($field['attrs'] ?? []));
    $ui = (array) ($field['ui'] ?? []);
    $size = $ui['size'] ?? 'md';
    $color = $ui['color'] ?? null;
    $widthClass = match ($ui['width'] ?? 'full') {
        '1/4' => 'col-span-12 md:col-span-3',
        '1/3' => 'col-span-12 md:col-span-4',
        '1/2' => 'col-span-12 md:col-span-6',
        '2/3' => 'col-span-12 md:col-span-8',
        '3/4' => 'col-span-12 md:col-span-9',
        default => 'col-span-12',
    };
    $firstOption = $options[0] ?? null;
    $firstOptionValue = (string) (($firstOption['value'] ?? $firstOption['label'] ?? '') ?: '0');
    $firstOptionSuffix = \Illuminate\Support\Str::slug($firstOptionValue) ?: '0';
    $labelFor = match ($type) {
        'signature' => false,
        'radio' => count($options) > 0 ? $controlId.'-'.$firstOptionSuffix : null,
        default => $controlId,
    };
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'staticText'): ?>
    <div data-form-field="<?php echo e($id); ?>" class="<?php echo e($widthClass); ?> prose max-w-none text-base-content">
        <p><?php echo e($field['text'] ?? $label); ?></p>
    </div>
<?php elseif($isComputedHidden || $type === 'hidden'): ?>
    <input
        type="hidden"
        name="<?php echo e($name); ?>"
        value="<?php echo e(is_scalar($fieldValue) ? $fieldValue : ''); ?>"
        data-form-input="<?php echo e($id); ?>"
    />
<?php elseif($type === 'tabs'): ?>
    <?php
        $tabs = array_values((array) ($field['fields'] ?? []));
        $tabRadioName = 'daisy-form-tabs-'.\Illuminate\Support\Str::slug((string) $id);
    ?>
    <section data-form-field="<?php echo e($id); ?>" class="<?php echo e($widthClass); ?> space-y-3">
        <div class="space-y-1">
            <h3 class="font-medium"><?php echo e($label); ?></h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
                <p class="text-sm text-base-content/70"><?php echo e($description); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="tabs tabs-box tabs-top w-full">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $tabId = $tab['id'] ?? "{$id}-{$index}";
                    $tabLabel = $tab['label'] ?? $tabId;
                    $tabFields = array_values((array) ($tab['fields'] ?? []));
                ?>
                <input
                    type="radio"
                    name="<?php echo e($tabRadioName); ?>"
                    class="tab"
                    aria-label="<?php echo e($tabLabel); ?>"
                    <?php if($index === 0): echo 'checked'; endif; ?>
                />
                <div class="tab-content border-base-300 bg-base-100 p-4">
                    <div data-form-field="<?php echo e($tabId); ?>" class="grid grid-cols-12 gap-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($tab['description'] ?? null) && count($tabFields) > 0): ?>
                            <p class="text-sm text-base-content/70"><?php echo e($tab['description']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($tabFields) > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php echo $__env->make('daisy::components.forms.partials.field', [
                                    'field' => $child,
                                    'value' => $value,
                                    'errors' => $allErrors,
                                    'readonly' => $readonly,
                                    'formId' => $formId,
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php elseif(($tab['type'] ?? null) !== 'tabs'): ?>
                            <?php echo $__env->make('daisy::components.forms.partials.field', [
                                'field' => $tab,
                                'value' => $value,
                                'errors' => $allErrors,
                                'readonly' => $readonly,
                                'formId' => $formId,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <p class="text-sm text-base-content/60">No fields configured.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-sm text-base-content/60">No tabs configured.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
<?php elseif(in_array($type, ['section', 'wizardStep'], true)): ?>
    <fieldset data-form-field="<?php echo e($id); ?>" class="<?php echo e($widthClass); ?> grid grid-cols-12 gap-4 rounded-box border border-base-300 p-4">
        <legend class="px-2 font-medium"><?php echo e($label); ?></legend>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
            <p class="col-span-12 text-sm text-base-content/70"><?php echo e($description); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = (array) ($field['fields'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php echo $__env->make('daisy::components.forms.partials.field', [
                'field' => $child,
                'value' => $value,
                'errors' => $allErrors,
                'readonly' => $readonly,
                'formId' => $formId,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </fieldset>
<?php else: ?>
    <?php
        $errors = new \Illuminate\Support\ViewErrorBag();
    ?>
    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => $name,'for' => $labelFor,'label' => $label,'hint' => $description,'error' => $fieldErrors[0] ?? null,'dataFormField' => ''.e($id).'','class' => ''.e($widthClass).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'for' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($labelFor),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($description),'error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldErrors[0] ?? null),'data-form-field' => ''.e($id).'','class' => ''.e($widthClass).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'textarea'): ?>
            <?php if (isset($component)) { $__componentOriginale7580ac62991553be731e04dcee1e44e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7580ac62991553be731e04dcee1e44e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.textarea','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','placeholder' => ''.e($attrs->get('placeholder')).'','dataFormInput' => ''.e($id).'','disabled' => $readonly,'size' => $size,'color' => $color,'rows' => $attrs->get('rows', 4),'readonly' => $isReadonly && ! $readonly,'class' => \Illuminate\Support\Arr::toCssClasses([$hasError ? 'textarea-error' : null])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','placeholder' => ''.e($attrs->get('placeholder')).'','data-form-input' => ''.e($id).'','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('rows', 4)),'readonly' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isReadonly && ! $readonly),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([$hasError ? 'textarea-error' : null]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(is_scalar($fieldValue) ? $fieldValue : ''); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7580ac62991553be731e04dcee1e44e)): ?>
<?php $attributes = $__attributesOriginale7580ac62991553be731e04dcee1e44e; ?>
<?php unset($__attributesOriginale7580ac62991553be731e04dcee1e44e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7580ac62991553be731e04dcee1e44e)): ?>
<?php $component = $__componentOriginale7580ac62991553be731e04dcee1e44e; ?>
<?php unset($__componentOriginale7580ac62991553be731e04dcee1e44e); ?>
<?php endif; ?>
        <?php elseif($type === 'select'): ?>
            <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','placeholder' => ''.e($attrs->get('placeholder')).'','dataFormInput' => ''.e($id).'','disabled' => $readonly,'size' => $size,'color' => $color,'class' => \Illuminate\Support\Arr::toCssClasses([$hasError ? 'select-error' : null])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','placeholder' => ''.e($attrs->get('placeholder')).'','data-form-input' => ''.e($id).'','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([$hasError ? 'select-error' : null]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <option value=""></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $optionValue = (string) ($option['value'] ?? $option['label'] ?? '');
                        $optionLabel = (string) ($option['label'] ?? $optionValue);
                    ?>
                    <option value="<?php echo e($optionValue); ?>" <?php if((string) $fieldValue === $optionValue): echo 'selected'; endif; ?> <?php if((bool) ($option['disabled'] ?? false)): echo 'disabled'; endif; ?>>
                        <?php echo e($optionLabel); ?>

                    </option>
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
        <?php elseif($type === 'radio'): ?>
            <div class="flex flex-wrap gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $optionValue = (string) ($option['value'] ?? $option['label'] ?? '');
                        $optionLabel = (string) ($option['label'] ?? $optionValue);
                        $optionId = $controlId.'-'.\Illuminate\Support\Str::slug($optionValue !== '' ? $optionValue : (string) $loop->index);
                    ?>
                    <label class="inline-flex items-center gap-2" for="<?php echo e($optionId); ?>">
                        <?php if (isset($component)) { $__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.radio','data' => ['id' => ''.e($optionId).'','name' => $name,'value' => $optionValue,'checked' => (string) $fieldValue === $optionValue,'disabled' => $readonly,'size' => $size,'color' => $color,'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($optionId).'','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($optionValue),'checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $fieldValue === $optionValue),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'data-form-input' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1)): ?>
<?php $attributes = $__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1; ?>
<?php unset($__attributesOriginalf93c0df128cb49cf4ebf1ca9ffd637b1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1)): ?>
<?php $component = $__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1; ?>
<?php unset($__componentOriginalf93c0df128cb49cf4ebf1ca9ffd637b1); ?>
<?php endif; ?>
                        <span><?php echo e($optionLabel); ?></span>
                    </label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php elseif($type === 'checkbox'): ?>
            <label class="inline-flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.checkbox','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => '1','checked' => (bool) $fieldValue,'disabled' => $readonly,'size' => $size,'color' => $color,'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $fieldValue),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'data-form-input' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $attributes = $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $component = $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
                <span><?php echo e($label); ?></span>
            </label>
        <?php elseif($type === 'toggle'): ?>
            <label class="inline-flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => '1','checked' => (bool) $fieldValue,'disabled' => $readonly,'size' => $size,'color' => $color,'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $fieldValue),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'data-form-input' => ''.e($id).'']); ?>
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
                <span><?php echo e($label); ?></span>
            </label>
        <?php elseif($type === 'range'): ?>
            <?php if (isset($component)) { $__componentOriginal10ad05071a6832c005a49ec6f828332a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10ad05071a6832c005a49ec6f828332a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.range','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => $fieldValue,'disabled' => $readonly,'size' => $size,'color' => $color,'min' => $attrs->get('min', 0),'max' => $attrs->get('max', 100),'step' => $attrs->get('step', 1),'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldValue),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('min', 0)),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('max', 100)),'step' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('step', 1)),'data-form-input' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10ad05071a6832c005a49ec6f828332a)): ?>
<?php $attributes = $__attributesOriginal10ad05071a6832c005a49ec6f828332a; ?>
<?php unset($__attributesOriginal10ad05071a6832c005a49ec6f828332a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10ad05071a6832c005a49ec6f828332a)): ?>
<?php $component = $__componentOriginal10ad05071a6832c005a49ec6f828332a; ?>
<?php unset($__componentOriginal10ad05071a6832c005a49ec6f828332a); ?>
<?php endif; ?>
        <?php elseif($type === 'file'): ?>
            <?php if (isset($component)) { $__componentOriginal5b7f7d644cd20bd84a9839240e623b24 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b7f7d644cd20bd84a9839240e623b24 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.file-input','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','accept' => ''.e($attrs->get('accept')).'','disabled' => $readonly,'size' => $size,'color' => $color,'multiple' => (bool) $attrs->get('multiple', false),'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.file-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','accept' => ''.e($attrs->get('accept')).'','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool) $attrs->get('multiple', false)),'data-form-input' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b7f7d644cd20bd84a9839240e623b24)): ?>
<?php $attributes = $__attributesOriginal5b7f7d644cd20bd84a9839240e623b24; ?>
<?php unset($__attributesOriginal5b7f7d644cd20bd84a9839240e623b24); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b7f7d644cd20bd84a9839240e623b24)): ?>
<?php $component = $__componentOriginal5b7f7d644cd20bd84a9839240e623b24; ?>
<?php unset($__componentOriginal5b7f7d644cd20bd84a9839240e623b24); ?>
<?php endif; ?>
        <?php elseif($type === 'signature'): ?>
            <?php if (isset($component)) { $__componentOriginal0994af87a37da25009238d969d458d6b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0994af87a37da25009238d969d458d6b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.sign','data' => ['name' => ''.e($name).'','value' => $fieldValue,'width' => $attrs->get('width', 400),'height' => $attrs->get('height', 200),'penColor' => $attrs->get('penColor', '#000000'),'minWidth' => $attrs->get('minWidth', 0.5),'maxWidth' => $attrs->get('maxWidth', 2.5),'velocityFilterWeight' => $attrs->get('velocityFilterWeight', 0.7),'responsive' => filter_var($attrs->get('responsive', true), FILTER_VALIDATE_BOOL),'showActions' => filter_var($attrs->get('showActions', ! $readonly), FILTER_VALIDATE_BOOL),'downloadFormat' => $attrs->get('downloadFormat', 'png'),'downloadFilename' => $attrs->get('downloadFilename', $name),'disabled' => $readonly,'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.sign'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($name).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldValue),'width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('width', 400)),'height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('height', 200)),'pen-color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('penColor', '#000000')),'min-width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('minWidth', 0.5)),'max-width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('maxWidth', 2.5)),'velocity-filter-weight' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('velocityFilterWeight', 0.7)),'responsive' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('responsive', true), FILTER_VALIDATE_BOOL)),'show-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('showActions', ! $readonly), FILTER_VALIDATE_BOOL)),'download-format' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('downloadFormat', 'png')),'download-filename' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('downloadFilename', $name)),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'data-form-input' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0994af87a37da25009238d969d458d6b)): ?>
<?php $attributes = $__attributesOriginal0994af87a37da25009238d969d458d6b; ?>
<?php unset($__attributesOriginal0994af87a37da25009238d969d458d6b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0994af87a37da25009238d969d458d6b)): ?>
<?php $component = $__componentOriginal0994af87a37da25009238d969d458d6b; ?>
<?php unset($__componentOriginal0994af87a37da25009238d969d458d6b); ?>
<?php endif; ?>
        <?php elseif($type === 'color'): ?>
            <?php if (isset($component)) { $__componentOriginal11a0739b0dc1eccfd965e70a6223062a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11a0739b0dc1eccfd965e70a6223062a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.color-picker','data' => ['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => is_scalar($fieldValue) ? $fieldValue : '#563d7c','mode' => $attrs->get('mode', 'advanced'),'dropdown' => filter_var($attrs->get('dropdown', true), FILTER_VALIDATE_BOOL),'swatches' => (array) $attrs->get('swatches', []),'swatchesHeight' => $attrs->get('swatchesHeight', 0),'showPalette' => filter_var($attrs->get('showPalette', true), FILTER_VALIDATE_BOOL),'showInputs' => filter_var($attrs->get('showInputs', true), FILTER_VALIDATE_BOOL),'showFormatToggle' => filter_var($attrs->get('showFormatToggle', true), FILTER_VALIDATE_BOOL),'showAlpha' => filter_var($attrs->get('showAlpha', true), FILTER_VALIDATE_BOOL),'showHue' => filter_var($attrs->get('showHue', true), FILTER_VALIDATE_BOOL),'disabled' => $readonly,'dataFormInput' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.color-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','name' => ''.e($name).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(is_scalar($fieldValue) ? $fieldValue : '#563d7c'),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('mode', 'advanced')),'dropdown' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('dropdown', true), FILTER_VALIDATE_BOOL)),'swatches' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((array) $attrs->get('swatches', [])),'swatches-height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->get('swatchesHeight', 0)),'show-palette' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('showPalette', true), FILTER_VALIDATE_BOOL)),'show-inputs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('showInputs', true), FILTER_VALIDATE_BOOL)),'show-format-toggle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('showFormatToggle', true), FILTER_VALIDATE_BOOL)),'show-alpha' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('showAlpha', true), FILTER_VALIDATE_BOOL)),'show-hue' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('showHue', true), FILTER_VALIDATE_BOOL)),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'data-form-input' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11a0739b0dc1eccfd965e70a6223062a)): ?>
<?php $attributes = $__attributesOriginal11a0739b0dc1eccfd965e70a6223062a; ?>
<?php unset($__attributesOriginal11a0739b0dc1eccfd965e70a6223062a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11a0739b0dc1eccfd965e70a6223062a)): ?>
<?php $component = $__componentOriginal11a0739b0dc1eccfd965e70a6223062a; ?>
<?php unset($__componentOriginal11a0739b0dc1eccfd965e70a6223062a); ?>
<?php endif; ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['id' => ''.e($controlId).'','type' => ''.e(in_array($type, ['email', 'tel', 'url', 'password', 'number', 'date', 'time', 'datetime-local', 'month', 'color'], true) ? $type : 'text').'','name' => ''.e($name).'','value' => ''.e(is_scalar($fieldValue) ? $fieldValue : '').'','placeholder' => ''.e($attrs->get('placeholder')).'','autocomplete' => ''.e($attrs->get('autocomplete')).'','min' => ''.e($attrs->get('min')).'','max' => ''.e($attrs->get('max')).'','step' => ''.e($attrs->get('step')).'','obfuscate' => filter_var($attrs->get('obfuscate', false), FILTER_VALIDATE_BOOL),'obfuscateChar' => ''.e($attrs->get('obfuscateChar')).'','obfuscateKeepEnd' => ''.e($attrs->get('obfuscateKeepEnd')).'','inputMask' => filled($attrs->get('mask')) || filled($attrs->get('customMask')) || filled($attrs->get('customValidator')) || filter_var($attrs->get('inputMask', false), FILTER_VALIDATE_BOOL),'mask' => filled($attrs->get('mask')) ? $attrs->get('mask') : null,'maskCharPlaceholder' => filled($attrs->get('maskCharPlaceholder')) ? $attrs->get('maskCharPlaceholder') : null,'maskPlaceholder' => $attrs->has('maskPlaceholder') ? filter_var($attrs->get('maskPlaceholder'), FILTER_VALIDATE_BOOL) : null,'inputPlaceholder' => $attrs->has('inputPlaceholder') ? filter_var($attrs->get('inputPlaceholder'), FILTER_VALIDATE_BOOL) : null,'clearIncomplete' => $attrs->has('clearIncomplete') ? filter_var($attrs->get('clearIncomplete'), FILTER_VALIDATE_BOOL) : null,'customMask' => filled($attrs->get('customMask')) ? $attrs->get('customMask') : null,'customValidator' => filled($attrs->get('customValidator')) ? $attrs->get('customValidator') : null,'disabled' => $readonly,'size' => $size,'color' => $color,'dataFormInput' => ''.e($id).'','readonly' => $isReadonly && ! $readonly,'class' => \Illuminate\Support\Arr::toCssClasses([$hasError ? 'input-error' : null])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e($controlId).'','type' => ''.e(in_array($type, ['email', 'tel', 'url', 'password', 'number', 'date', 'time', 'datetime-local', 'month', 'color'], true) ? $type : 'text').'','name' => ''.e($name).'','value' => ''.e(is_scalar($fieldValue) ? $fieldValue : '').'','placeholder' => ''.e($attrs->get('placeholder')).'','autocomplete' => ''.e($attrs->get('autocomplete')).'','min' => ''.e($attrs->get('min')).'','max' => ''.e($attrs->get('max')).'','step' => ''.e($attrs->get('step')).'','obfuscate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($attrs->get('obfuscate', false), FILTER_VALIDATE_BOOL)),'obfuscate-char' => ''.e($attrs->get('obfuscateChar')).'','obfuscate-keep-end' => ''.e($attrs->get('obfuscateKeepEnd')).'','input-mask' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($attrs->get('mask')) || filled($attrs->get('customMask')) || filled($attrs->get('customValidator')) || filter_var($attrs->get('inputMask', false), FILTER_VALIDATE_BOOL)),'mask' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($attrs->get('mask')) ? $attrs->get('mask') : null),'mask-char-placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($attrs->get('maskCharPlaceholder')) ? $attrs->get('maskCharPlaceholder') : null),'mask-placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->has('maskPlaceholder') ? filter_var($attrs->get('maskPlaceholder'), FILTER_VALIDATE_BOOL) : null),'input-placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->has('inputPlaceholder') ? filter_var($attrs->get('inputPlaceholder'), FILTER_VALIDATE_BOOL) : null),'clear-incomplete' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attrs->has('clearIncomplete') ? filter_var($attrs->get('clearIncomplete'), FILTER_VALIDATE_BOOL) : null),'custom-mask' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($attrs->get('customMask')) ? $attrs->get('customMask') : null),'custom-validator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($attrs->get('customValidator')) ? $attrs->get('customValidator') : null),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($readonly),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($color),'data-form-input' => ''.e($id).'','readonly' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isReadonly && ! $readonly),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([$hasError ? 'input-error' : null]))]); ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <p class="mt-1 hidden text-sm text-error" data-form-errors="<?php echo e($id); ?>"></p>
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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/forms/partials/field.blade.php ENDPATH**/ ?>