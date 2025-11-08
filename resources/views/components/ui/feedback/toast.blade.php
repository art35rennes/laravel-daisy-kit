@props([
    // Compat: position = horizontal (start|center|end)
    'position' => 'end',
    'horizontal' => null, // start|center|end
    'vertical' => 'bottom', // top|middle|bottom
])

@php
    $h = $horizontal ?? $position;
    $horizontalClass = [
        'start' => 'toast-start',
        'center' => 'toast-center',
        'end' => 'toast-end',
    ][$h] ?? 'toast-end';

    $verticalClass = [
        'top' => 'toast-top',
        'middle' => 'toast-middle',
        'bottom' => 'toast-bottom',
    ][$vertical] ?? 'toast-bottom';
@endphp

<div {{ $attributes->merge(['class' => 'toast '.$horizontalClass.' '.$verticalClass]) }}>
    {{ $slot }}
</div>


