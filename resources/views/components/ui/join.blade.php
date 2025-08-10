@props([
    'direction' => 'horizontal', // horizontal|vertical
])

@php
    $classes = 'join';
    if ($direction === 'vertical') $classes .= ' join-vertical';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
