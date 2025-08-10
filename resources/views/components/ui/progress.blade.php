@props([
    'value' => null,
    'max' => 100,
    'size' => null, // xs|sm|md|lg
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
])

@php
    $classes = 'progress w-full';
    if ($size) $classes .= ' progress-'.$size;
    if ($color) $classes .= ' progress-'.$color;
@endphp

<progress @if(!is_null($value)) value="{{ $value }}" @endif max="{{ $max }}" {{ $attributes->merge(['class' => $classes]) }}></progress>
