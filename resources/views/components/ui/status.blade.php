@props([
    'color' => 'success', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg|xl
    // Accessibilité et balise
    'label' => null, // aria-label
    'as' => 'span', // span|div
])

@php
    $classes = 'status';
    $classes .= ' status-'.$color;
    $classes .= ' status-'.$size;
    $tag = in_array($as, ['div','span'], true) ? $as : 'span';
@endphp

<{{ $tag }} {!! $label ? 'aria-label="'.e($label).'"' : '' !!} {{ $attributes->merge(['class' => $classes]) }}></{{ $tag }}>
