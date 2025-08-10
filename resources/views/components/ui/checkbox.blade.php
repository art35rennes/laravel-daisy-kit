@props([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg
    'checked' => false,
    'disabled' => false,
])

@php
    $sizeMap = [
        'xs' => 'checkbox-xs',
        'sm' => 'checkbox-sm',
        'md' => 'checkbox-md',
        'lg' => 'checkbox-lg',
    ];

    $classes = 'checkbox';

    if ($color) {
        $classes .= ' checkbox-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<input type="checkbox" @checked($checked) @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }} />


