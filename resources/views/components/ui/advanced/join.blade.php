@props([
    'direction' => 'horizontal', // horizontal|vertical
    // Ajouts responsives optionnels: ex "lg" pour ajouter lg:join-horizontal
    'horizontalAt' => null,
    'verticalAt' => null,
])

@php
    $classes = 'join';
    if ($direction === 'vertical') $classes .= ' join-vertical';
    if ($horizontalAt) $classes .= ' ' . $horizontalAt . ':join-horizontal';
    if ($verticalAt) $classes .= ' ' . $verticalAt . ':join-vertical';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
