@props([
    'items' => [],
    'label' => 'Tree',
    'multiple' => false,
    'name' => null,
    'persistenceKey' => null,
    'searchable' => false,
    'searchSource' => null,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'items' => $items,
        'multiple' => $multiple,
        'name' => $name,
        'persistenceKey' => $persistenceKey,
        'searchable' => $searchable,
        'searchSource' => $searchSource,
    ]);
@endphp

<section {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['card', 'border', 'border-base-300', 'bg-base-100', 'shadow-sm', 'daisy-kit-tree']) }} data-daisy-kit-module="tree">
    <p class="alert alert-error" data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div class="card-body" data-daisy-kit-content>
        @if ($searchable)
            <label class="form-control w-full">
                <span class="label-text">Search tree</span>
                <input class="input input-bordered w-full" data-daisy-kit-tree-search type="search" autocomplete="off">
            </label>
        @endif
        <ul data-daisy-kit-tree-root aria-label="{{ $label }}" role="tree"></ul>
        @if (is_string($name) && $name !== '')
            <input data-daisy-kit-tree-value name="{{ $name }}" type="hidden" value="[]">
        @endif
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
