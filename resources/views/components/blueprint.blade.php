@props([
    'nodes' => [],
    'edges' => [],
    'label' => 'Blueprint',
    'editable' => false,
    'name' => null,
    'value' => null,
])

<section
    {{ $attributes->merge(['data-daisy-kit-module' => 'blueprint']) }}
    aria-label="{{ $label }}"
>
    <p data-daisy-kit-status hidden role="alert"></p>

    <div data-daisy-kit-content>
        <svg
            aria-hidden="true"
            data-daisy-kit-blueprint-canvas
            focusable="false"
        ></svg>
        <p data-daisy-kit-empty hidden>No blueprint nodes are available.</p>
        <input data-daisy-kit-blueprint-value @if(is_string($name) && $name !== '') name="{{ $name }}" @endif type="hidden">
    </div>

    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'nodes' => $nodes,
        'edges' => $edges,
        'label' => $label,
        'editable' => $editable,
        'name' => $name,
        'value' => $value,
    ]) !!}</script>
</section>
