@props([
    'schema' => [],
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'schema' => is_array($schema) ? $schema : [],
    ]);
@endphp

<section
    {{ $attributes->class(['daisy-kit-forms-builder']) }}
    aria-busy="true"
    data-daisy-kit-module="forms-builder"
    data-daisy-kit-state="loading"
>
    <p data-daisy-kit-status role="status">Loading form builder…</p>
    <div data-daisy-kit-forms-builder-content></div>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
