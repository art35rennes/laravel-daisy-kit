@props([
    'type' => 'text',
    'size' => 'md',        // xs | sm | md | lg | xl
    'variant' => null,     // null | ghost
    'color' => null,       // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
])

@php
    $sizeMap = [
        'xs' => 'input-xs',
        'sm' => 'input-sm',
        'md' => 'input-md',
        'lg' => 'input-lg',
        'xl' => 'input-xl',
    ];

    $classes = 'input w-full';

    if ($variant === 'ghost') {
        $classes .= ' input-ghost';
    }

    if ($color) {
        $classes .= ' input-'.$color;
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<input type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }} />


