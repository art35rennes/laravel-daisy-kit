@props([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
])

@php
    $sizeMap = [
        'xs' => 'checkbox-xs',
        'sm' => 'checkbox-sm',
        'md' => 'checkbox-md',
        'lg' => 'checkbox-lg',
        'xl' => 'checkbox-xl',
    ];

    $classes = 'checkbox';

    if ($color) {
        $classes .= ' checkbox-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<input type="checkbox" @checked($checked) @disabled($disabled) @if($indeterminate) aria-checked="mixed" data-indeterminate="true" @endif {{ $attributes->merge(['class' => $classes]) }} />


