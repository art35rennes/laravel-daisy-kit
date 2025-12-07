@props([
    'title',
    'subtitle',
    'jsModule' => null,
])

@php
    $jsModule = $jsModule ?? $attributes->get('js-module');
@endphp

<section id="intro">
    <h1>{{ $title }}</h1>
    <h2>{{ $subtitle }}</h2>
    @if($jsModule)
        <div class="alert alert-info mt-4">
            <span>Ce composant nécessite le module JavaScript <code>{{ $jsModule }}</code>.</span>
        </div>
    @endif
</section>

