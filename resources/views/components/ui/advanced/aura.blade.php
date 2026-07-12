@props([
    'as' => 'div',
    'variant' => 'default',
    'size' => 'md',
])

@php
    $tag = in_array($as, ['div', 'span', 'section', 'article', 'aside', 'figure'], true) ? $as : 'div';
    $classes = ['aura'];

    if (in_array($variant, ['dual', 'rainbow', 'holo', 'gold', 'silver', 'glow'], true)) {
        $classes[] = "aura-{$variant}";
    }

    if (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true)) {
        $classes[] = "aura-{$size}";
    }
@endphp

<{{ $tag }} {{ $attributes->class($classes) }}>
    {{ $slot }}
</{{ $tag }}>
