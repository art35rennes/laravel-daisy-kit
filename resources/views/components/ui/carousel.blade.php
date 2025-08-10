@props([
    'vertical' => false,
    'center' => false,
])

@php
    $classes = 'carousel rounded-box';
    if ($vertical) $classes .= ' carousel-vertical';
    if ($center) $classes .= ' carousel-center';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
