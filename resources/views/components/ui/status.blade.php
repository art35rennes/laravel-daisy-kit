@props([
    'color' => 'success', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg|xl
])

@php
    $classes = 'status';
    $classes .= ' status-'.$color;
    $classes .= ' status-'.$size;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}></span>
