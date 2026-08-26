@props([
    'items' => [],
    'label' => 'Tree',
    'multiple' => false,
    'name' => null,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'items' => $items,
        'multiple' => $multiple,
        'name' => $name,
    ]);
@endphp

<section {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['daisy-kit-tree']) }} data-daisy-kit-module="tree">
    <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div data-daisy-kit-content>
        <ul data-daisy-kit-tree-root aria-label="{{ $label }}" role="tree"></ul>
        @if (is_string($name) && $name !== '')
            <input data-daisy-kit-tree-value name="{{ $name }}" type="hidden" value="[]">
        @endif
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
