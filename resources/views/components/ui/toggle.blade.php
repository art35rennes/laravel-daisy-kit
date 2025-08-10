@props([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg
    'checked' => false,
    'disabled' => false,
])

@php
    $sizeMap = [
        'xs' => 'toggle-xs',
        'sm' => 'toggle-sm',
        'md' => 'toggle-md',
        'lg' => 'toggle-lg',
    ];

    $classes = 'toggle';

    if ($color) {
        $classes .= ' toggle-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<input type="checkbox" role="switch" @checked($checked) @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }} />


