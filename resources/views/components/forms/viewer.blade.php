@props([
    'schema' => [],
    'value' => [],
    'errors' => [],
    'readonly' => false,
    'submitMode' => null,
    'action' => null,
    'method' => 'POST',
    'validateOn' => 'submit',
])

@php
    $viewerErrors = $errors instanceof \Illuminate\Support\ViewErrorBag
        ? $errors->getBag('default')->toArray()
        : ($errors instanceof \Illuminate\Contracts\Support\MessageProvider
            ? $errors->getMessageBag()->toArray()
            : (is_array($errors) ? $errors : []));
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'schema' => is_array($schema) ? $schema : [],
        'value' => is_array($value) ? $value : [],
        'errors' => $viewerErrors,
        'readonly' => $readonly === true,
        'submitMode' => is_string($submitMode) ? $submitMode : null,
        'action' => is_string($action) ? $action : null,
        'method' => is_string($method) ? strtoupper($method) : 'POST',
        'validateOn' => is_string($validateOn) ? $validateOn : 'submit',
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
