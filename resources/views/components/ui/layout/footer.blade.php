@props([
    'bg' => 'base-200',     // bg token or null to omit
    'text' => 'base-content', // text token or null to omit
    'padding' => 'p-10',
    'center' => false,        // footer-center
    'horizontal' => false,    // footer-horizontal (toujours horizontal)
    'horizontalAt' => null,   // sm|md|lg|xl → sm:footer-horizontal
])

@php
    $classes = 'footer';
    if ($center) $classes .= ' footer-center';
    if ($horizontal) $classes .= ' footer-horizontal';
    if ($horizontalAt) $classes .= ' '.$horizontalAt.':footer-horizontal';
    if ($bg) $classes .= ' bg-'.$bg;
    if ($text) $classes .= ' text-'.$text;
    if ($padding) $classes .= ' '.$padding;
@endphp

<footer {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</footer>
