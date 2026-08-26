<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,
    'id' => null,
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'disabled' => false,
    'readonly' => false,
    'options' => [],
    'value' => null,
    'values' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
    'placeholder' => 'Search...',
    'endpoint' => null,
    'param' => 'q',
    'debounce' => 500,
    'minChars' => 3,
    'fetchOnEmpty' => true,
    'default' => null,
    'maxItems' => null,
    'noResultsText' => 'No results found.',
    'loadingText' => 'Loading...',
    'errorText' => 'Unable to load results.',
    'selectedText' => 'selected',
    'module' => null,
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
    'id' => null,
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'disabled' => false,
    'readonly' => false,
    'options' => [],
    'value' => null,
    'values' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
    'placeholder' => 'Search...',
    'endpoint' => null,
    'param' => 'q',
    'debounce' => 500,
    'minChars' => 3,
    'fetchOnEmpty' => true,
    'default' => null,
    'maxItems' => null,
    'noResultsText' => 'No results found.',
    'loadingText' => 'Loading...',
    'errorText' => 'Unable to load results.',
    'selectedText' => 'selected',
    'module' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizeMap = [
        'xs' => ['select' => 'select-xs', 'badge' => 'badge-xs', 'icon' => 'xs', 'minHeight' => 'min-h-6', 'padding' => 'py-0.5'],
        'sm' => ['select' => 'select-sm', 'badge' => 'badge-xs', 'icon' => 'sm', 'minHeight' => 'min-h-8', 'padding' => 'py-1'],
        'md' => ['select' => 'select-md', 'badge' => 'badge-sm', 'icon' => 'sm', 'minHeight' => 'min-h-10', 'padding' => 'py-1.5'],
        'lg' => ['select' => 'select-lg', 'badge' => 'badge-md', 'icon' => 'md', 'minHeight' => 'min-h-12', 'padding' => 'py-2'],
        'xl' => ['select' => 'select-xl', 'badge' => 'badge-lg', 'icon' => 'md', 'minHeight' => 'min-h-14', 'padding' => 'py-2.5'],
    ];

    $resolvedSize = $sizeMap[$size] ?? $sizeMap['md'];
    $selectId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $submitName = is_string($name) && str_ends_with($name, '[]') ? $name : (($name ? $name.'[]' : ''));

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first(rtrim((string) $name, '[]')) : null);
    $hasError = filled($errorMessage);

    $oldInput = $name ? data_get(session()->get('_old_input', []), rtrim((string) $name, '[]'), old(rtrim((string) $name, '[]'), $values ?? $value)) : ($values ?? $value);
    $selectedInput = $bindOld && $name ? $oldInput : ($values ?? $value);
    $selectedValues = collect(is_iterable($selectedInput) && ! is_string($selectedInput) ? $selectedInput : (is_null($selectedInput) ? [] : [$selectedInput]))
        ->map(fn ($item) => is_array($item) ? (string) ($item['value'] ?? $item['id'] ?? '') : (string) $item)
        ->filter(fn (string $item) => $item !== '')
        ->unique()
        ->values();

    $normalizedOptions = collect(is_iterable($options) ? $options : [])
        ->map(function ($option): array {
            if (is_array($option)) {
                return [
                    'value' => (string) ($option['value'] ?? $option['id'] ?? ''),
                    'label' => (string) ($option['label'] ?? $option['name'] ?? $option['value'] ?? $option['id'] ?? ''),
                    'subtitle' => (string) ($option['subtitle'] ?? ''),
                    'avatar' => (string) ($option['avatar'] ?? ''),
                    'disabled' => (bool) ($option['disabled'] ?? false),
                ];
            }

            return [
                'value' => (string) $option,
                'label' => (string) $option,
                'subtitle' => '',
                'avatar' => '',
                'disabled' => false,
            ];
        })
        ->filter(fn (array $option) => $option['value'] !== '')
        ->values();

    $selectedOptions = $selectedValues
        ->map(function (string $selectedValue) use ($normalizedOptions): array {
            $matched = $normalizedOptions->first(fn (array $option) => (string) $option['value'] === $selectedValue);

            return $matched ?: [
                'value' => $selectedValue,
                'label' => $selectedValue,
                'subtitle' => '',
                'avatar' => '',
                'disabled' => false,
            ];
        });

    $isDisabled = (bool) $disabled;
    $isReadonly = (bool) $readonly;

    $rootClasses = 'dropdown w-full';
    $shellClasses = 'select daisy-multi-select relative flex h-auto '.$resolvedSize['minHeight'].' w-full items-center gap-2 '.$resolvedSize['padding'];
    $shellClasses .= $isReadonly ? ' daisy-multi-select-readonly cursor-default pr-3' : ' cursor-text pr-10';

    if ($variant === 'ghost') {
        $shellClasses .= ' select-ghost';
    }

    if ($color) {
        $shellClasses .= ' select-'.$color;
    }

    if ($hasError) {
        $shellClasses .= ' select-error';
    }

    $shellClasses .= ' '.$resolvedSize['select'];

    $badgeClasses = 'badge badge-soft max-w-full gap-1';
    $badgeClasses .= $color ? ' badge-'.$color : ' badge-neutral';
    $badgeClasses .= ' '.$resolvedSize['badge'];

    $removeButtonClasses = 'btn btn-ghost btn-xs btn-circle';

    $dataAttributes = [
        'data-module' => $module ?: 'multi-select',
        'data-name' => (string) ($name ?? ''),
        'data-submit-name' => $submitName,
        'data-disabled' => $isDisabled ? 'true' : 'false',
        'data-readonly' => $isReadonly ? 'true' : 'false',
        'data-debounce' => (string) (is_numeric($debounce) ? $debounce : 500),
        'data-min-chars' => (string) (is_numeric($minChars) ? $minChars : 3),
        'data-fetch-on-empty' => $fetchOnEmpty ? 'true' : 'false',
        'data-no-results-text' => (string) $noResultsText,
        'data-loading-text' => (string) $loadingText,
        'data-error-text' => (string) $errorText,
        'data-selected-text' => (string) $selectedText,
        'data-placeholder' => (string) $placeholder,
        'data-token-class' => $badgeClasses,
        'data-token-remove-class' => $removeButtonClasses,
    ];

    if ($endpoint) {
        $dataAttributes['data-endpoint'] = (string) $endpoint;
        $dataAttributes['data-param'] = (string) ($param ?: 'q');
    }

    if (! is_null($maxItems)) {
        $dataAttributes['data-max-items'] = (string) (int) $maxItems;
    }

    if (! is_null($default)) {
        try {
            $dataAttributes['data-default'] = json_encode($default, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            // Ignore invalid default payloads to keep rendering resilient.
        }
    }
?>

<div
    <?php echo e($attributes->except(['id', 'name'])->merge(['class' => $rootClasses])->merge($dataAttributes)); ?>

>
    <div class="<?php echo e($shellClasses); ?>" data-role="shell">
        <div class="flex min-w-0 grow flex-wrap items-center gap-1.5 overflow-hidden" data-role="selected">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <span class="<?php echo e($badgeClasses); ?>" data-multi-select-item data-value="<?php echo e($selectedOption['value']); ?>" data-label="<?php echo e($selectedOption['label']); ?>">
                    <span class="truncate"><?php echo e($selectedOption['label']); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($isReadonly)): ?>
                        <button
                            type="button"
                            class="<?php echo e($removeButtonClasses); ?>"
                            data-multi-select-remove
                            aria-label="Remove <?php echo e($selectedOption['label']); ?>"
                            <?php if($isDisabled): echo 'disabled'; endif; ?>
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isReadonly && $selectedOptions->isEmpty()): ?>
                <span class="text-sm text-base-content/60"><?php echo e($placeholder); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <input
                type="text"
                data-role="input"
                class="w-10 min-w-8 flex-1 basis-10 border-0 bg-transparent p-0 text-sm outline-none placeholder:text-base-content/60 <?php echo e($isReadonly ? 'hidden' : ''); ?>"
                <?php if($selectId): ?> id="<?php echo e($selectId); ?>" <?php endif; ?>
                autocomplete="off"
                placeholder="<?php echo e($selectedOptions->isEmpty() ? $placeholder : ''); ?>"
                role="combobox"
                aria-expanded="false"
                aria-autocomplete="list"
                <?php if($selectId): ?> aria-controls="<?php echo e($selectId); ?>-listbox" <?php endif; ?>
                <?php if($isReadonly): echo 'readonly'; endif; ?>
                <?php if($isReadonly): ?> tabindex="-1" <?php endif; ?>
                <?php if($hasError): ?> aria-invalid="true" <?php endif; ?>
                <?php if($describedBy): ?> aria-describedby="<?php echo e($describedBy); ?>" <?php endif; ?>
                <?php if($isDisabled): echo 'disabled'; endif; ?>
            />
        </div>
    </div>

    <select data-role="native" multiple hidden tabindex="-1" aria-hidden="true">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $normalizedOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <option
                value="<?php echo e($option['value']); ?>"
                <?php if($selectedValues->contains(fn (string $selectedValue) => $selectedValue === (string) $option['value'])): echo 'selected'; endif; ?>
                <?php if($option['disabled']): echo 'disabled'; endif; ?>
                <?php if($option['subtitle'] !== ''): ?> data-subtitle="<?php echo e($option['subtitle']); ?>" <?php endif; ?>
                <?php if($option['avatar'] !== ''): ?> data-avatar="<?php echo e($option['avatar']); ?>" <?php endif; ?>
            >
                <?php echo e($option['label']); ?>

            </option>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </select>

    <div data-role="hidden-inputs">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submitName !== ''): ?>
                <input type="hidden" name="<?php echo e($submitName); ?>" value="<?php echo e($selectedOption['value']); ?>" data-multi-select-hidden />
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <ul
        class="dropdown-content menu z-10 mt-2 hidden max-h-72 w-full overflow-auto rounded-box bg-base-100 p-2 shadow"
        <?php if($selectId): ?> id="<?php echo e($selectId); ?>-listbox" <?php endif; ?>
        role="listbox"
        data-role="list"
        aria-multiselectable="true"
    ></ul>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasError): ?>
        <p class="validator-hint mt-1 text-error" data-role="message"><?php echo e($errorMessage); ?></p>
    <?php else: ?>
        <p class="validator-hint mt-1 hidden text-error" data-role="message"></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/multi-select.blade.php ENDPATH**/ ?>