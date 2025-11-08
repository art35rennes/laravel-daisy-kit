@props([
    'shape' => 'spinner', // spinner | dots | ring | ball | bars | infinity
    'size' => 'md',       // xs | sm | md | lg | xl
    'color' => null,      // primary | secondary | accent | info | success | warning | error | neutral
])

@php
    $sizeMap = [
        'xs' => 'loading-xs',
        'sm' => 'loading-sm',
        'md' => 'loading-md',
        'lg' => 'loading-lg',
        'xl' => 'loading-xl',
    ];

    $classes = 'loading';
    $classes .= ' loading-'.$shape;
    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
    if (!empty($color)) {
        $classes .= ' text-'.$color;
    }
@endphp

<span aria-live="polite" aria-busy="true" {{ $attributes->merge(['class' => $classes]) }}></span>


