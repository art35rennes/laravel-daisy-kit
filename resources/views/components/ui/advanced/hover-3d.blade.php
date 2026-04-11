@props([
    /** @var string Container element: div, a, or span. */
    'as' => 'div',
])

@php
    $tag = in_array($as, ['div', 'a', 'span'], true) ? $as : 'div';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'hover-3d']) }}>
    {{ $slot }}
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
</{{ $tag }}>
