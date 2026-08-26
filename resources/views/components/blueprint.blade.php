@props([
    'nodes' => [],
    'edges' => [],
    'label' => 'Blueprint',
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
    </div>

    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'nodes' => $nodes,
        'edges' => $edges,
        'label' => $label,
    ]) !!}</script>
</section>
