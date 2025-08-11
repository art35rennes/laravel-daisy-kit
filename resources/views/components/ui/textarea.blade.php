@props([
    'size' => 'md',        // xs | sm | md | lg | xl
    'variant' => null,     // null | ghost
    'color' => null,       // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
    'rows' => 4,
])

@php
    $sizeMap = [
        'xs' => 'textarea-xs',
        'sm' => 'textarea-sm',
        'md' => 'textarea-md',
        'lg' => 'textarea-lg',
        'xl' => 'textarea-xl',
    ];

    $classes = 'textarea w-full';

    if ($variant === 'ghost') {
        $classes .= ' textarea-ghost';
    }

    if ($color) {
        $classes .= ' textarea-'.$color;
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<textarea rows="{{ $rows }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</textarea>


