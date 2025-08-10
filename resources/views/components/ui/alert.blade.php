@props([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'variant' => 'solid', // solid|soft
    'icon' => null,
    'title' => null,
])

@php
    $classes = 'alert';
    // Color
    $classes .= ' alert-'.$color;
    // Variant
    if ($variant === 'soft') {
        $classes .= ' alert-soft';
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


