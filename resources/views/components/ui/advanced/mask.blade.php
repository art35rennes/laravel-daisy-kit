@props([
    'shape' => 'squircle', // squircle|heart|hexagon|triangle|star etc
    'src' => null,
    'alt' => '',
    'class' => '',
    // Modificateur: mask-half-1 | mask-half-2
    // Valeurs acceptées: 1, 2, 'first', 'second'
    'half' => null,
])

@php
    $classes = 'mask mask-'.$shape;
    if ($half === 1 || $half === '1' || $half === 'first') {
        $classes .= ' mask-half-1';
    } elseif ($half === 2 || $half === '2' || $half === 'second') {
        $classes .= ' mask-half-2';
    }
    if (!empty($class)) {
        $classes .= ' '.$class;
    }
@endphp

@if($src)
    <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $classes]) }} />
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </div>
@endif
