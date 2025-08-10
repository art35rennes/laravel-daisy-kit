@props([
    'mobile' => false,
    'position' => 'bottom', // bottom|top
])

@php
    $classes = 'dock';
    if ($mobile) $classes .= ' dock-mobile';
    if ($position === 'bottom') $classes .= ' dock-bottom';
    if ($position === 'top') $classes .= ' dock-top';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
