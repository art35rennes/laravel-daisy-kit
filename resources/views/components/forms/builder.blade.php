@props([
    'schema' => [],
    'name' => 'schema',
    'value' => [],
    'errors' => [],
    'preview' => true,
    'jsonEditor' => true,
])

@php
    $livewireAvailable = class_exists(\Livewire\Livewire::class) && app()->bound('livewire');
    $builderErrors = $errors instanceof \Illuminate\Support\ViewErrorBag
        ? $errors->getBag('default')->toArray()
        : ($errors instanceof \Illuminate\Contracts\Support\MessageProvider
            ? $errors->getMessageBag()->toArray()
            : (is_array($errors) ? $errors : []));
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'livewireAvailable' => $livewireAvailable,
        'schema' => is_array($schema) ? $schema : [],
    ]);
@endphp

<section
    {{ $attributes->class(['card', 'border', 'border-base-300', 'bg-base-100', 'shadow-sm', 'daisy-kit-forms-builder']) }}
    aria-busy="true"
    data-daisy-kit-module="forms-builder"
    data-daisy-kit-state="loading"
>
    <p class="alert" data-daisy-kit-status role="status">{{ $livewireAvailable ? __('Loading form builder…') : __('Forms Builder authoring requires optional Livewire 4.') }}</p>
    @if($livewireAvailable)
        @livewire('daisy-kit.forms.builder', [
            'schema' => is_array($schema) ? $schema : [],
            'name' => is_string($name) ? $name : 'schema',
            'value' => is_array($value) ? $value : [],
            'errors' => $builderErrors,
            'preview' => $preview === true,
            'jsonEditor' => $jsonEditor === true,
        ])
    @else
        <p class="alert alert-warning m-4" data-daisy-kit-forms-builder-unavailable>{{ __('The authoring enhancement is not installed.') }}</p>
    @endif
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
