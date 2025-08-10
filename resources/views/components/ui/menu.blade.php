@props([
    'vertical' => true,
    'size' => null, // xs|sm|md|lg
])

@php
    $classes = 'menu bg-base-100 rounded-box';
    if (!$vertical) $classes .= ' menu-horizontal';
    if ($size) $classes .= ' menu-'.$size;
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</ul>
