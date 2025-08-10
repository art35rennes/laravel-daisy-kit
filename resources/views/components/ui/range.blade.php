@props([
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => null,
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
])

@php
    $classes = 'range';
    if ($color) $classes .= ' range-'.$color;
@endphp

<input type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" @if(!is_null($value)) value="{{ $value }}" @endif {{ $attributes->merge(['class' => $classes]) }} />
