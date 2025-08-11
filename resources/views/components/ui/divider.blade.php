@props([
    'text' => null,
    // Direction: by défaut vertical. Utiliser horizontal=true ou horizontalAt (ex: 'lg') pour horizontal
    'horizontal' => false,
    'horizontalAt' => null, // ex: 'lg' → lg:divider-horizontal
    // Couleur: neutral|primary|secondary|accent|success|warning|info|error
    'color' => null,
    // Placement du texte: start|end|null
    'position' => null,
])

@php
    $classes = 'divider';
    if ($horizontal) {
        $classes .= ' divider-horizontal';
    } elseif ($horizontalAt) {
        $classes .= ' '.$horizontalAt.':divider-horizontal';
    }
    if ($color) $classes .= ' divider-'.$color;
    if (in_array($position, ['start','end'], true)) $classes .= ' divider-'.$position;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $text ?? $slot }}
</div>


