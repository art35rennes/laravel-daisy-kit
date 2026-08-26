@props([
    'schema' => [],
    'value' => [],
    'errors' => [],
    'readonly' => false,
    'submitMode' => null,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'schema' => is_array($schema) ? $schema : [],
        'value' => is_array($value) ? $value : [],
        'errors' => is_array($errors) ? $errors : [],
        'readonly' => $readonly === true,
        'submitMode' => is_string($submitMode) ? $submitMode : null,
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
