@props([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
])

@php
    $sizeMap = [
        'xs' => 'toggle-xs',
        'sm' => 'toggle-sm',
        'md' => 'toggle-md',
        'lg' => 'toggle-lg',
        'xl' => 'toggle-xl',
    ];

    $classes = 'toggle';

    if ($color) {
        $classes .= ' toggle-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<input type="checkbox" role="switch" @checked($checked) @disabled($disabled) @if($indeterminate) aria-checked="mixed" data-indeterminate="true" @endif {{ $attributes->merge(['class' => $classes]) }} />


