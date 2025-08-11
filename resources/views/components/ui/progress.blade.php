@props([
    'value' => null,
    'max' => 100,
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
])

@php
    $classes = 'progress';
    if ($color) $classes .= ' progress-'.$color;
@endphp

<progress @if(!is_null($value)) value="{{ $value }}" @endif max="{{ $max }}" {{ $attributes->merge(['class' => $classes]) }}></progress>
