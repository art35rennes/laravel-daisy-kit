@props([
    'size' => 'md',         // xs | sm | md | lg | xl
    'variant' => null,      // null | ghost
    'color' => null,        // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
])

@php
    $sizeMap = [
        'xs' => 'select-xs',
        'sm' => 'select-sm',
        'md' => 'select-md',
        'lg' => 'select-lg',
        'xl' => 'select-xl',
    ];

    $classes = 'select w-full';

    if ($variant === 'ghost') {
        $classes .= ' select-ghost';
    }

    if ($color) {
        $classes .= ' select-'.$color;
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }
@endphp

<select @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    {{-- Expecting <option> children --}}
    {{-- Example: <x-ui.select><option>One</option></x-ui.select> --}}
</select>


