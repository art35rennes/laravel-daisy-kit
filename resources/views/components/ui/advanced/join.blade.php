@props([
    'direction' => 'horizontal', // horizontal|vertical
    // Ajouts responsives optionnels: ex "lg" pour ajouter lg:join-horizontal
    'horizontalAt' => null,
    'verticalAt' => null,
])

@php
    // Construction des classes CSS selon la direction et les breakpoints responsive.
    $classes = 'join';
    // Direction par défaut : horizontal (join-horizontal implicite).
    if ($direction === 'vertical') $classes .= ' join-vertical';
    // Orientation responsive : devient horizontal à partir d'un breakpoint.
    if ($horizontalAt) $classes .= ' ' . $horizontalAt . ':join-horizontal';
    // Orientation responsive : devient vertical à partir d'un breakpoint.
    if ($verticalAt) $classes .= ' ' . $verticalAt . ':join-vertical';
@endphp

{{-- Join : conteneur pour grouper des éléments (boutons, inputs, etc.) avec bordures jointes (pattern daisyUI) --}}
<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
