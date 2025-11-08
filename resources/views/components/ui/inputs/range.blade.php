@props([
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => null,
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'size' => null,  // xs|sm|md|lg|xl
    // Custom CSS variables (optional)
    'bg' => null,        // --range-bg
    'thumb' => null,     // --range-thumb
    'fill' => null,      // --range-fill (e.g., 0)
    'noFill' => false,   // convenience to set --range-fill: 0
])

@php
    $classes = 'range';
    if ($color) $classes .= ' range-'.$color;
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) $classes .= ' range-'.$size;

    $style = '';
    if (!is_null($bg)) $style .= " --range-bg: {$bg};";
    if (!is_null($thumb)) $style .= " --range-thumb: {$thumb};";
    if (!is_null($fill)) $style .= " --range-fill: {$fill};";
    elseif ($noFill) $style .= ' --range-fill: 0;';
    $style = trim($style);
@endphp

<input type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" @if(!is_null($value)) value="{{ $value }}" @endif {{ $attributes->merge(['class' => $classes, 'style' => $style ?: null]) }} />
