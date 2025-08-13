@props([
    'href' => '#',
    'color' => 'primary', // null|primary|secondary|accent|info|success|warning|error|neutral
    // underline=true => underline only on hover (link-hover)
    // underline=false => always underlined (only link)
    'underline' => true,
    'external' => false,
])

@php
    $classes = 'link';
    if ($underline) $classes .= ' link-hover';
    if ($color) $classes .= ' link-'.$color;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'target' => $external ? '_blank' : null, 'rel' => $external ? 'noopener noreferrer' : null]) }}>
    {{ $slot }}
    @if($external)
        <x-bi-box-arrow-up-right class="ml-1 align-[-2px] h-4 w-4" />
    @endif
</a>


