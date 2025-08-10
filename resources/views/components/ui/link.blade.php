@props([
    'href' => '#',
    'color' => 'primary', // primary|secondary|accent|info|success|warning|error|neutral
    'underline' => true,
    'external' => false,
])

@php
    $classes = 'link';
    if ($underline) $classes .= ' link-hover';
    $classes .= ' link-'.$color;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'target' => $external ? '_blank' : null, 'rel' => $external ? 'noopener noreferrer' : null]) }}>
    {{ $slot }}
    @if($external)
        <x-daisy::ui.icon name="external-link" class="ml-1 align-[-2px]" />
    @endif
</a>


