@props([
    'value' => 0,
    'size' => null, // e.g. 6rem, 80px
    'thickness' => null, // e.g. 4px
    'color' => null, // text-primary etc without text- prefix
    'showValue' => true,
])

@php
    $classes = 'radial-progress';
    if ($color) $classes .= ' text-'.$color;

    $style = "--value: {$value};";
    if ($size) $style .= " --size: {$size};";
    if ($thickness) $style .= " --thickness: {$thickness};";
@endphp

<div style="{{ $style }}" {{ $attributes->merge(['class' => $classes, 'role' => 'progressbar', 'aria-valuenow' => $value]) }}>
    @if($showValue)
        {{ $slot->isNotEmpty() ? $slot : $value.'%' }}
    @endif
</div>
