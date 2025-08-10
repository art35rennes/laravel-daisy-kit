@props([
    'vertical' => true,
])

@php
    $classes = 'list'.($vertical ? '' : ' list-vertical:!false');
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</ul>
