@props([
    'value' => 0,
    'size' => null, // e.g. 6rem, 80px
    'thickness' => null, // e.g. 4px
    'color' => null, // text-primary etc without text- prefix
    'showValue' => true,
    // Accessibilité / échelle
    'min' => 0,
    'max' => 100,
])

@php
    $classes = 'radial-progress';
    if ($color) $classes .= ' text-'.$color;

    $min = (int) $min;
    $max = max($min + 1, (int) $max);
    $rawValue = is_numeric($value) ? (float) $value : 0.0;
    $percent = (int) round(max(0, min(100, (($rawValue - $min) / ($max - $min)) * 100)));

    $style = "--value: {$percent};";
    if ($size) $style .= " --size: {$size};";
    if ($thickness) $style .= " --thickness: {$thickness};";
@endphp

<div style="{{ $style }}" {{ $attributes->merge(['class' => $classes, 'role' => 'progressbar', 'aria-valuemin' => $min, 'aria-valuemax' => $max, 'aria-valuenow' => $rawValue]) }}>
    @if($showValue)
        {{ $slot->isNotEmpty() ? $slot : $percent.'%' }}
    @endif
</div>
