@props([
    'name' => null,
    'value' => null,
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg
    'checked' => false,
    'disabled' => false,
])

@php
    $sizeMap = [
        'xs' => 'radio-xs',
        'sm' => 'radio-sm',
        'md' => 'radio-md',
        'lg' => 'radio-lg',
    ];

    $classes = 'radio';

    if ($color) {
        $classes .= ' radio-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<input type="radio" @if($name) name="{{ $name }}" @endif @if(!is_null($value)) value="{{ $value }}" @endif @checked($checked) @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }} />


