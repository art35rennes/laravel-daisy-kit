@props([
    'text' => null,
    'vertical' => false,
])

@php
    $classes = $vertical ? 'divider divider-horizontal' : 'divider';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $text ?? $slot }}
</div>


