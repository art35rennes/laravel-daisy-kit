@props([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'size' => 'md', // xs|sm|md|lg
    'variant' => 'solid', // solid|outline|soft|ghost
])

@php
    $sizeMap = [
        'xs' => 'badge-xs',
        'sm' => 'badge-sm',
        'md' => 'badge-md',
        'lg' => 'badge-lg',
    ];
    $classes = 'badge';
    $classes .= ' badge-'.$color;
    if (isset($sizeMap[$size])) $classes .= ' '.$sizeMap[$size];
    if ($variant === 'outline') $classes .= ' badge-outline';
    if ($variant === 'soft') $classes .= ' badge-soft';
    if ($variant === 'ghost') $classes .= ' badge-ghost';
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>


