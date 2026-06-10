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

    $dynamicAttributes = [];
    if (is_numeric($fill)) {
        $fillToken = (int) round(max(0, min(100, (float) $fill)));
        $classes .= ' daisy-range-fill-'.$fillToken;
    } elseif ($noFill) {
        $classes .= ' daisy-range-no-fill';
    }
@endphp

<input type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" @if(!is_null($value)) value="{{ $value }}" @endif {{ $attributes->merge($dynamicAttributes + ['class' => $classes]) }} />
