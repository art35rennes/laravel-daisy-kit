@php
    $treeHasValue = array_key_exists('value', get_defined_vars()) || (isset($attributes) && $attributes->has('value'));
@endphp
@props([
    'items' => [], 'label' => null, 'multiple' => false, 'value' => null,
    'valueMode' => 'leaves', 'disabled' => false, 'name' => null,
    'persistenceKey' => null, 'initialExpandPaths' => [],
    'searchable' => false, 'searchSource' => null, 'searchMode' => 'auto',
    'searchMatch' => 'includes', 'searchDebounce' => 200, 'searchMin' => 0,
    'searchParam' => 'query', 'highlightMatches' => false, 'nodeView' => null, 'labels' => [],
])
@php
    $tree = \Art35rennes\DaisyKit\Tree\TreeConfiguration::make([
        'items' => $items, 'multiple' => $multiple, 'value' => $value,
        'hasInitialValue' => $treeHasValue, 'valueMode' => $valueMode,
        'disabled' => $disabled, 'name' => $name, 'persistenceKey' => $persistenceKey,
        'initialExpandPaths' => $initialExpandPaths, 'searchable' => $searchable,
        'searchSource' => $searchSource, 'searchMode' => $searchMode, 'searchMatch' => $searchMatch,
        'searchDebounce' => $searchDebounce, 'searchMin' => $searchMin,
        'searchParam' => $searchParam, 'highlightMatches' => $highlightMatches,
        'nodeView' => $nodeView, 'labels' => $labels,
    ]);
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode($tree['configuration']);
    $treeLabels = $tree['configuration']['labels'];
    $initialValues = $multiple ? $tree['configuration']['value'] : ($value === null ? [] : [(string) $value]);
@endphp

<section {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['card', 'border', 'border-base-300', 'bg-base-100', 'shadow-sm', 'daisy-kit-tree']) }} data-daisy-kit-module="tree">
    <div class="daisy-kit-tree__content" data-daisy-kit-content>
        @include('daisy-kit::internal.tree.toolbar')
        <p class="alert alert-error" data-daisy-kit-status hidden role="status" aria-live="polite"></p>
        <ul class="daisy-kit-tree__root" data-daisy-kit-tree-root aria-label="{{ $label ?? $treeLabels['label'] }}" role="tree"></ul>
        <p class="text-sm text-base-content/65" data-daisy-kit-tree-empty hidden role="status"></p>
        @include('daisy-kit::internal.tree.footer')
        @if($name !== null)
            <input data-daisy-kit-tree-value name="{{ $name }}" type="hidden" value="{{ json_encode($initialValues, JSON_THROW_ON_ERROR) }}" @disabled($disabled)>
        @endif
    </div>
    @foreach($tree['templates'] as $nodeId => $nodeMarkup)
        <template data-daisy-kit-tree-template="{{ $nodeId }}">{!! $nodeMarkup !!}</template>
    @endforeach
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
