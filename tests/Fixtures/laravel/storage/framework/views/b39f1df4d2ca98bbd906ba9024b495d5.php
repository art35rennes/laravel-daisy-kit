<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => null,
    'label' => null,
    'data' => [],
    'value' => null,
    'name' => null,
    'selection' => 'multiple',
    'valueMode' => 'leaves',
    'initialExpandPaths' => [],
    'disabled' => false,
    'persist' => false,
    'controlSize' => 'sm',
    'lazyUrl' => null,
    'lazyParam' => 'node',
    'search' => false,
    'searchUrl' => null,
    'searchParam' => 'q',
    'searchPlaceholder' => null,
    'searchMin' => 2,
    'searchDebounce' => 300,
    'searchAuto' => true,
    'module' => 'treeview',
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
    'id' => null,
    'label' => null,
    'data' => [],
    'value' => null,
    'name' => null,
    'selection' => 'multiple',
    'valueMode' => 'leaves',
    'initialExpandPaths' => [],
    'disabled' => false,
    'persist' => false,
    'controlSize' => 'sm',
    'lazyUrl' => null,
    'lazyParam' => 'node',
    'search' => false,
    'searchUrl' => null,
    'searchParam' => 'q',
    'searchPlaceholder' => null,
    'searchMin' => 2,
    'searchDebounce' => 300,
    'searchAuto' => true,
    'module' => 'treeview',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    if ($persist && blank($id)) {
        throw new InvalidArgumentException('The tree view requires an explicit id when persistence is enabled.');
    }

    if (! in_array($selection, ['single', 'multiple'], true)) {
        throw new InvalidArgumentException('The tree view selection must be single or multiple.');
    }

    if (! in_array($valueMode, ['leaves', 'selected-roots'], true)) {
        throw new InvalidArgumentException('The tree view value mode must be leaves or selected-roots.');
    }

    $validateNodes = function (array $nodes) use (&$validateNodes): void {
        foreach ($nodes as $node) {
            if (! is_array($node) || ! array_key_exists('id', $node) || ! array_key_exists('label', $node)) {
                throw new InvalidArgumentException('Each tree view node requires an id and a label.');
            }

            $children = is_array($node['children'] ?? null) ? $node['children'] : [];

            if (($node['lazy'] ?? false) === true && $children !== []) {
                throw new InvalidArgumentException('A lazy tree view node cannot include children. Use initialExpandPaths to hydrate selections.');
            }

            $validateNodes($children);
        }
    };

    $validateNodes($data);

    $treeId = $id ?: 'tree-'.uniqid();
    $treeLabel = $label ?: __('daisy::components.tree-view');
    $searchPlaceholderText = $searchPlaceholder ?: __('daisy::components.tree-view-search-placeholder');
    $selectedValues = $selection === 'multiple'
        ? array_values(array_map('strval', is_array($value) ? $value : array_filter([$value], fn ($item) => ! is_null($item))))
        : array_values(array_filter([is_null($value) ? null : (string) $value], fn ($item) => ! is_null($item)));
    $normalizedInitialExpandPaths = collect($initialExpandPaths)
        ->filter(fn (mixed $path): bool => is_array($path))
        ->map(fn (array $path): array => array_values(array_map('strval', array_filter($path, fn (mixed $id): bool => $id !== null && $id !== ''))))
        ->filter(fn (array $path): bool => $path !== [])
        ->values()
        ->all();
    $initialValue = $selection === 'single' ? ($selectedValues[0] ?? null) : $selectedValues;
    $initialValueJson = json_encode($initialValue, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
    $initialExpandPathsJson = json_encode($normalizedInitialExpandPaths, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
    $treeAttributes = $attributes->except('id')->class(['menu menu-sm bg-base-100 rounded-box p-2']);
?>

<div
    id="<?php echo e($treeId); ?>"
    class="w-full"
    data-module="<?php echo e($module); ?>"
    data-treeview="1"
    data-selection="<?php echo e($selection); ?>"
    data-value-mode="<?php echo e($valueMode); ?>"
    data-initial-value='<?php echo e($initialValueJson); ?>'
    data-initial-expand-paths='<?php echo e($initialExpandPathsJson); ?>'
    data-disabled="<?php echo e($disabled ? 'true' : 'false'); ?>"
    data-persist="<?php echo e($persist ? 'true' : 'false'); ?>"
    data-control-size="<?php echo e($controlSize); ?>"
    data-expand-label="<?php echo e(__('daisy::components.tree-view-expand', ['label' => ':label'])); ?>"
    data-collapse-label="<?php echo e(__('daisy::components.tree-view-collapse', ['label' => ':label'])); ?>"
    data-loading-label="<?php echo e(__('daisy::components.tree-view-loading')); ?>"
    data-load-error-label="<?php echo e(__('daisy::components.tree-view-load-error')); ?>"
    data-no-results-label="<?php echo e(__('daisy::components.tree-view-no-results')); ?>"
    <?php if($name): ?> data-name="<?php echo e($name); ?>" <?php endif; ?>
    <?php if($lazyUrl): ?> data-lazy-url="<?php echo e($lazyUrl); ?>" data-lazy-param="<?php echo e($lazyParam); ?>" <?php endif; ?>
    data-search-enabled="<?php echo e($search ? 'true' : 'false'); ?>"
    <?php if($search): ?>
        data-search-min="<?php echo e(max(1, (int) $searchMin)); ?>"
        data-search-debounce="<?php echo e(max(0, (int) $searchDebounce)); ?>"
        data-search-auto="<?php echo e($searchAuto ? 'true' : 'false'); ?>"
    <?php endif; ?>
    <?php if($searchUrl): ?> data-search-url="<?php echo e($searchUrl); ?>" data-search-param="<?php echo e($searchParam); ?>" <?php endif; ?>
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
        <div class="join mb-2 w-full" data-tree-search-container="1">
            <label class="sr-only" for="<?php echo e($treeId); ?>-search"><?php echo e(__('daisy::components.tree-view-search')); ?></label>
            <input id="<?php echo e($treeId); ?>-search" type="search" class="input input-sm join-item w-full" placeholder="<?php echo e($searchPlaceholderText); ?>" autocomplete="off" data-tree-search="1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$searchAuto): ?>
                <button type="button" class="btn btn-sm join-item" data-tree-search-button="1"><?php echo e(__('daisy::components.tree-view-search-action')); ?></button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <ul role="tree" aria-label="<?php echo e($treeLabel); ?>" <?php if($selection === 'multiple'): ?> aria-multiselectable="true" <?php endif; ?> data-tree="1" <?php echo e($treeAttributes); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php echo $__env->make('daisy::components.ui.partials.tree-node', [
                'node' => $node,
                'level' => 1,
                'treeId' => $treeId,
                'selection' => $selection,
                'valueMode' => $valueMode,
                'selectedValues' => $selectedValues,
                'name' => $name,
                'controlSize' => $controlSize,
                'disabledParent' => (bool) $disabled,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <li role="presentation" class="px-2 py-1 text-sm opacity-60" data-tree-empty="1"><?php echo e(__('daisy::components.tree-view-empty')); ?></li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>

    <p class="sr-only" role="status" aria-live="polite" data-tree-status="1"></p>
</div>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/tree-view.blade.php ENDPATH**/ ?>