@props([
    'text' => null,
    'open' => false,
    'position' => 'top', // top|right|bottom|left
    'color' => null, // primary|secondary|accent|info|success|warning|error
])

@php
    $classes = 'tooltip';
    $pos = ['top','right','bottom','left'];
    if (in_array($position, $pos)) $classes .= ' tooltip-'.$position;
    if ($open) $classes .= ' tooltip-open';
    if ($color) $classes .= ' tooltip-'.$color;
@endphp

<div data-tip="{{ $text }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
