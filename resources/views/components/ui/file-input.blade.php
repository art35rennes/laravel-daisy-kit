@props([
    'size' => 'md', // xs|sm|md|lg
    'variant' => 'bordered', // default|bordered|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'multiple' => false,
    'disabled' => false,
])

@php
    $sizeMap = [
        'xs' => 'file-input-xs',
        'sm' => 'file-input-sm',
        'md' => 'file-input-md',
        'lg' => 'file-input-lg',
    ];

    $classes = 'file-input w-full';
    if ($variant === 'bordered') $classes .= ' file-input-bordered';
    if ($variant === 'ghost') $classes .= ' file-input-ghost';
    if ($color) $classes .= ' file-input-'.$color;
    if (isset($sizeMap[$size])) $classes .= ' '.$sizeMap[$size];
@endphp

<input type="file" @multiple($multiple) @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }} />
