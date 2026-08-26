@props([
    'schema' => [],
    'value' => [],
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'schema' => is_array($schema) ? $schema : [],
        'value' => is_array($value) ? $value : [],
    ]);
@endphp

<section
    {{ $attributes->class(['daisy-kit-forms-viewer']) }}
    aria-busy="true"
    data-daisy-kit-module="forms-viewer"
    data-daisy-kit-state="loading"
>
    <p data-daisy-kit-status role="status">Loading form…</p>
    <form data-daisy-kit-forms-content></form>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
