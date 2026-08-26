@props([
    'items' => [],
    'label' => 'Tree',
    'multiple' => false,
    'name' => null,
    'persistenceKey' => null,
    'searchable' => false,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'items' => $items,
        'multiple' => $multiple,
        'name' => $name,
        'persistenceKey' => $persistenceKey,
        'searchable' => $searchable,
    ]);
@endphp

<section {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['daisy-kit-tree']) }} data-daisy-kit-module="tree">
    <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div data-daisy-kit-content>
        @if ($searchable)
            <label>
                <span>Search tree</span>
                <input data-daisy-kit-tree-search type="search" autocomplete="off">
            </label>
        @endif
        <ul data-daisy-kit-tree-root aria-label="{{ $label }}" role="tree"></ul>
        @if (is_string($name) && $name !== '')
            <input data-daisy-kit-tree-value name="{{ $name }}" type="hidden" value="[]">
        @endif
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
