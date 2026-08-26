<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,
    'items' => [],
    'type' => 'radio',
    'value' => null,
    'values' => [],
    'legend' => null,
    'hint' => null,
    'required' => false,
    'columns' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    'color' => 'primary',
    'size' => 'md',
    'showControl' => true,
    'iconPrefix' => null,
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
    'name' => null,
    'items' => [],
    'type' => 'radio',
    'value' => null,
    'values' => [],
    'legend' => null,
    'hint' => null,
    'required' => false,
    'columns' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    'color' => 'primary',
    'size' => 'md',
    'showControl' => true,
    'iconPrefix' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    if (blank($name)) {
        throw new \InvalidArgumentException('The [name] prop is required for the choice-card-group component.');
    }

    $inputType = $type === 'checkbox' ? 'checkbox' : 'radio';
    $inputName = $inputType === 'checkbox' && ! str_ends_with($name, '[]') ? "{$name}[]" : $name;
    $fieldId = $attributes->get('id') ?? 'choice-card-group-'.str_replace(['[', ']'], '', $name).'-'.uniqid();

    $selectedRadioValue = old($name, $value);
    $selectedCheckboxValues = old(str_replace('[]', '', $name), $values);

    if ($selectedCheckboxValues instanceof \Illuminate\Support\Collection) {
        $selectedCheckboxValues = $selectedCheckboxValues->all();
    }

    if (! is_array($selectedCheckboxValues)) {
        $selectedCheckboxValues = filled($selectedCheckboxValues) ? [$selectedCheckboxValues] : [];
    }

    $validColors = ['neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];
    $accentColor = in_array($color, $validColors, true) ? $color : 'primary';

    $controlClass = $inputType === 'checkbox' ? 'checkbox' : 'radio';
    $controlClass .= " {$controlClass}-{$accentColor}";

    $sizeClasses = match($size) {
        'sm' => [
            'card' => 'gap-3 p-4',
            'icon' => 'sm',
            'title' => 'text-sm',
            'description' => 'text-xs',
            'control' => $inputType === 'checkbox' ? 'checkbox-sm' : 'radio-sm',
        ],
        'lg' => [
            'card' => 'gap-4 p-6',
            'icon' => 'lg',
            'title' => 'text-base',
            'description' => 'text-sm',
            'control' => $inputType === 'checkbox' ? 'checkbox-md' : 'radio-md',
        ],
        default => [
            'card' => 'gap-4 p-5',
            'icon' => 'md',
            'title' => 'text-sm',
            'description' => 'text-sm',
            'control' => $inputType === 'checkbox' ? 'checkbox-sm' : 'radio-sm',
        ],
    };

    $controlClass .= ' '.$sizeClasses['control'];
?>

<fieldset
    <?php echo e($attributes->except('id')->merge(['class' => 'space-y-3'])); ?>

    id="<?php echo e($fieldId); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($legend): ?>
        <legend class="text-sm font-medium text-base-content">
            <?php echo e($legend); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                <span aria-hidden="true" class="ml-1 text-error">*</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </legend>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hint): ?>
        <p class="text-sm text-base-content/70"><?php echo e($hint); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="grid <?php echo e($columns); ?> gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $itemValue = is_array($item) ? ($item['value'] ?? $index) : $index;
                $itemLabel = is_array($item) ? ($item['label'] ?? $itemValue) : $item;
                $itemDescription = is_array($item) ? ($item['description'] ?? null) : null;
                $itemIcon = is_array($item) ? ($item['icon'] ?? null) : null;
                $itemBadge = is_array($item) ? ($item['badge'] ?? null) : null;
                $itemDisabled = is_array($item) ? (bool) ($item['disabled'] ?? false) : false;
                $itemId = "{$fieldId}-".\Illuminate\Support\Str::slug((string) $itemValue, '-');

                $isChecked = $inputType === 'checkbox'
                    ? in_array((string) $itemValue, array_map('strval', $selectedCheckboxValues), true)
                    : (string) $selectedRadioValue === (string) $itemValue;
            ?>

            <label
                for="<?php echo e($itemId); ?>"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'card card-border group relative block min-h-full bg-base-100 shadow-sm',
                    'cursor-pointer' => ! $itemDisabled,
                    'cursor-not-allowed opacity-60' => $itemDisabled,
                ]); ?>"
            >
                <input
                    id="<?php echo e($itemId); ?>"
                    type="<?php echo e($inputType); ?>"
                    name="<?php echo e($inputName); ?>"
                    value="<?php echo e($itemValue); ?>"
                    class="<?php echo e($showControl ? $controlClass.' pointer-events-none absolute right-4 top-4 z-10 bg-base-100' : 'sr-only'); ?>"
                    <?php if($isChecked): echo 'checked'; endif; ?>
                    <?php if($itemDisabled): echo 'disabled'; endif; ?>
                    <?php if($required && $inputType === 'radio' && $loop->first): echo 'required'; endif; ?>
                    <?php if($required && $inputType === 'checkbox'): ?> required <?php endif; ?>
                />

                <span class="flex min-h-full <?php echo e($sizeClasses['card']); ?> <?php echo e($showControl ? 'pe-12' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemIcon): ?>
                        <span class="mt-0.5 flex size-10 shrink-0 items-center justify-center rounded-field bg-base-200 text-base-content/70">
                            <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $itemIcon,'prefix' => $iconPrefix,'size' => $sizeClasses['icon']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($itemIcon),'prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sizeClasses['icon'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <span class="flex min-w-0 flex-1 flex-col gap-1">
                        <span class="flex items-start justify-between gap-3">
                            <span class="font-medium leading-6 text-base-content <?php echo e($sizeClasses['title']); ?>"><?php echo e($itemLabel); ?></span>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemBadge): ?>
                                <span class="badge badge-sm"><?php echo e($itemBadge); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemDescription): ?>
                            <span class="leading-5 text-base-content/70 <?php echo e($sizeClasses['description']); ?>"><?php echo e($itemDescription); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </span>
            </label>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</fieldset>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/choice-card-group.blade.php ENDPATH**/ ?>