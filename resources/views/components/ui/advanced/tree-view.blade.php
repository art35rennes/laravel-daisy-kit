@props([
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
])

@php
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
@endphp

<div
    id="{{ $treeId }}"
    class="w-full"
    data-module="{{ $module }}"
    data-treeview="1"
    data-selection="{{ $selection }}"
    data-value-mode="{{ $valueMode }}"
    data-initial-value='{{ $initialValueJson }}'
    data-initial-expand-paths='{{ $initialExpandPathsJson }}'
    data-disabled="{{ $disabled ? 'true' : 'false' }}"
    data-persist="{{ $persist ? 'true' : 'false' }}"
    data-control-size="{{ $controlSize }}"
    data-expand-label="{{ __('daisy::components.tree-view-expand', ['label' => ':label']) }}"
    data-collapse-label="{{ __('daisy::components.tree-view-collapse', ['label' => ':label']) }}"
    data-loading-label="{{ __('daisy::components.tree-view-loading') }}"
    data-load-error-label="{{ __('daisy::components.tree-view-load-error') }}"
    data-no-results-label="{{ __('daisy::components.tree-view-no-results') }}"
    @if($name) data-name="{{ $name }}" @endif
    @if($lazyUrl) data-lazy-url="{{ $lazyUrl }}" data-lazy-param="{{ $lazyParam }}" @endif
    data-search-enabled="{{ $search ? 'true' : 'false' }}"
    @if($search)
        data-search-min="{{ max(1, (int) $searchMin) }}"
        data-search-debounce="{{ max(0, (int) $searchDebounce) }}"
        data-search-auto="{{ $searchAuto ? 'true' : 'false' }}"
    @endif
    @if($searchUrl) data-search-url="{{ $searchUrl }}" data-search-param="{{ $searchParam }}" @endif
>
    @if($search)
        <div class="join mb-2 w-full" data-tree-search-container="1">
            <label class="sr-only" for="{{ $treeId }}-search">{{ __('daisy::components.tree-view-search') }}</label>
            <input id="{{ $treeId }}-search" type="search" class="input input-sm join-item w-full" placeholder="{{ $searchPlaceholderText }}" autocomplete="off" data-tree-search="1">
            @if(!$searchAuto)
                <button type="button" class="btn btn-sm join-item" data-tree-search-button="1">{{ __('daisy::components.tree-view-search-action') }}</button>
            @endif
        </div>
    @endif

    <ul role="tree" aria-label="{{ $treeLabel }}" @if($selection === 'multiple') aria-multiselectable="true" @endif data-tree="1" {{ $treeAttributes }}>
        @forelse($data as $node)
            @include('daisy::components.ui.partials.tree-node', [
                'node' => $node,
                'level' => 1,
                'treeId' => $treeId,
                'selection' => $selection,
                'valueMode' => $valueMode,
                'selectedValues' => $selectedValues,
                'name' => $name,
                'controlSize' => $controlSize,
                'disabledParent' => (bool) $disabled,
            ])
        @empty
            <li role="presentation" class="px-2 py-1 text-sm opacity-60" data-tree-empty="1">{{ __('daisy::components.tree-view-empty') }}</li>
        @endforelse
    </ul>

    <p class="sr-only" role="status" aria-live="polite" data-tree-status="1"></p>
</div>

@include('daisy::components.partials.assets')
