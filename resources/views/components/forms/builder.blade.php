{{--
    Livewire-backed DaisyFormSchema authoring surface.

    The builder state lives in Art35rennes\DaisyKit\FormKit\Livewire\FormBuilder.
    Preview rendering is delegated to the real `x-daisy::forms.viewer` component from the Livewire view.
--}}
@props([
    'schema' => null,
    'fieldTypes' => null,
    'functionCatalog' => null,
    'preview' => true,
    'jsonEditor' => true,
    'name' => null,
    'value' => [],
    'errors' => [],
    'viewerSubmitMode' => null,
])

@php
    $schema = is_string($schema) ? (json_decode($schema, true) ?: []) : ($schema ?? []);
    $fieldTypes = is_string($fieldTypes) ? (json_decode($fieldTypes, true) ?: []) : ($fieldTypes ?? []);
    $functionCatalog = $functionCatalog ?? config('daisy-kit.forms.jsonata.function_catalog', []);
    $functionCatalog = is_string($functionCatalog) ? (json_decode($functionCatalog, true) ?: []) : (array) $functionCatalog;
    $value = is_string($value) ? (json_decode($value, true) ?: []) : (array) $value;
    $errors = is_string($errors) ? (json_decode($errors, true) ?: []) : (array) $errors;
@endphp

<div {{ $attributes->merge(['class' => 'daisy-form-builder-shell']) }}>
    @livewire('daisy.form-builder', [
        'schema' => $schema,
        'fieldTypes' => $fieldTypes,
        'functionCatalog' => $functionCatalog ?? config('daisy-kit.forms.jsonata.function_catalog', []),
        'preview' => (bool) $preview,
        'jsonEditor' => (bool) $jsonEditor,
        'name' => $name,
        'value' => $value,
        'errors' => $errors,
        'viewerSubmitMode' => $viewerSubmitMode,
    ])
</div>
