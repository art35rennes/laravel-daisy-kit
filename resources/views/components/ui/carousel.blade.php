@props([
    'vertical' => false,
    'center' => false, // compat: if true and align not set, uses center
    'align' => 'start', // start|center|end
])

@php
    $classes = 'carousel rounded-box';
    if ($vertical) $classes .= ' carousel-vertical';
    // Snap alignment
    $snap = $align;
    if ($center && ($align === 'start' || empty($align))) {
        $snap = 'center';
    }
    if ($snap === 'center') {
        $classes .= ' carousel-center';
    } elseif ($snap === 'end') {
        $classes .= ' carousel-end';
    } else {
        $classes .= ' carousel-start';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
