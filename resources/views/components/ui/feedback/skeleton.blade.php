@props([
    'width' => null, // e.g. w-32
    'height' => null, // e.g. h-6
    'rounded' => null, // none|sm|md|lg|xl|full
])

@php
    $classes = 'skeleton';
    if ($width) $classes .= ' '.$width;
    if ($height) $classes .= ' '.$height;
    if ($rounded) {
        $map = [
            'none' => 'rounded-none',
            'sm' => 'rounded',
            'md' => 'rounded-md',
            'lg' => 'rounded-lg',
            'xl' => 'rounded-xl',
            'full' => 'rounded-full',
        ];
        $classes .= ' '.($map[$rounded] ?? '');
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}></div>
