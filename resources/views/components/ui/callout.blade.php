@props([
    // Variant: secondary|success|warning|danger (map vers DaisyUI info/success/warning/error)
    'variant' => 'secondary',
    // Couleur Tailwind optionnelle (ex: blue, emerald...) → on délègue au class utilisateur
    'color' => null,
    // Icône (SVG Blade Icons recommandé)
    'icon' => null,
    // Heading/text raccourcis
    'heading' => null,
    'text' => null,
    // Actions inline
    'inline' => false,
    // Déplacer l'icône dans le heading
    'iconInHeading' => false,
])

@php
    // Mapping variant→color DaisyUI
    $variantToColor = [
        'secondary' => 'info',
        'success' => 'success',
        'warning' => 'warning',
        'danger' => 'error',
    ];
    $alertColor = $variantToColor[$variant] ?? 'info';
    $classes = trim('callout '.($color ? 'callout-'.$color : ''));
@endphp

<x-daisy::ui.alert
    :color="$alertColor"
    :inline="$inline"
    :icon="$icon"
    :iconInHeading="$iconInHeading"
    :heading="$heading"
    :text="$text"
    {{ $attributes->merge(['class' => $classes]) }}>
    @if($text === null)
        {{ $slot }}
    @endif
    @isset($actions)
        <x-slot:actions>{{ $actions }}</x-slot:actions>
    @endisset
    @isset($controls)
        <x-slot:controls>{{ $controls }}</x-slot:controls>
    @endisset
</x-daisy::ui.alert>


