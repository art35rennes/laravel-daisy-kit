@props([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'variant' => 'solid', // solid|soft|outline|dash
    'icon' => null,
    'title' => null,
    // Orientation
    'vertical' => null,       // bool|null
    'horizontal' => null,     // bool|null
    'horizontalAt' => null,   // ex: 'sm' → alert-vertical sm:alert-horizontal
])

@php
    $classes = 'alert';
    // Color
    $classes .= ' alert-'.$color;
    // Variant
    $variantMap = [
        'soft' => 'alert-soft',
        'outline' => 'alert-outline',
        'dash' => 'alert-dash',
    ];
    if (isset($variantMap[$variant])) $classes .= ' '.$variantMap[$variant];

    // Orientation
    if ($horizontalAt) {
        $classes .= ' alert-vertical '.($horizontalAt).':alert-horizontal';
    } elseif (!is_null($vertical) || !is_null($horizontal)) {
        if ($vertical) $classes .= ' alert-vertical';
        if ($horizontal) $classes .= ' alert-horizontal';
    }
@endphp

<div {{ $attributes->merge(['role' => 'alert', 'class' => $classes]) }}>
    @if($icon)
        <span class="shrink-0">{!! $icon !!}</span>
    @endif
    <div class="flex-1">
        @if($title)
            <h3 class="font-medium">{{ $title }}</h3>
        @endif
        <div class="text-sm">{{ $slot }}</div>
    </div>
    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>


