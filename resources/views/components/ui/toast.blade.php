@props([
    'position' => 'end', // start|center|end
])

@php
    $pos = [
        'start' => 'toast-start',
        'center' => 'toast-center',
        'end' => 'toast-end',
    ][$position] ?? 'toast-end';
@endphp

<div {{ $attributes->merge(['class' => 'toast '.$pos]) }}>
    {{ $slot }}
</div>


