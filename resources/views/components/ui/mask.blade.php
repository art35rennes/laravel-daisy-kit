@props([
    'shape' => 'squircle', // squircle|heart|hexagon|triangle|star etc
    'src' => null,
    'alt' => '',
    'class' => '',
])

@php
    $classes = 'mask mask-'.$shape.' '.$class;
@endphp

@if($src)
    <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $classes]) }} />
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </div>
@endif
