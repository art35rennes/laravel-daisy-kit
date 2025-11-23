@props([
    'statusCode' => 500,
    'title' => null, // Auto-generated if null
])

@php
    // Génération automatique du titre si non fourni
    if ($title === null) {
        $title = __('errors.'.$statusCode.'_title', ['default' => __('errors.error_title', ['code' => $statusCode])]);
    }
@endphp

<div class="flex flex-col items-center gap-4">
    <x-daisy::ui.data-display.badge color="error" size="lg">
        {{ $statusCode }}
    </x-daisy::ui.data-display.badge>
    
    <h1 class="text-3xl font-bold text-base-content">
        {{ $title }}
    </h1>
</div>

