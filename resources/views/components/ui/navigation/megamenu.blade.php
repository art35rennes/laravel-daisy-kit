@props([
    'mode' => null,
    'size' => 'md',
    'activeIndicator' => true,
])

@php
    $classes = ['megamenu'];

    if (in_array($mode, ['wide', 'full', 'vertical'], true)) {
        $classes[] = "megamenu-{$mode}";
    }

    if (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true)) {
        $classes[] = "megamenu-{$size}";
    }
@endphp

<nav {{ $attributes->class($classes) }}>
    @if($activeIndicator)
        <span class="megamenu-active" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</nav>
