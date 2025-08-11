@props([
    'text' => null,
    'open' => false,
    'position' => 'top', // top|right|bottom|left
    'color' => null, // neutral|primary|secondary|accent|info|success|warning|error
    // Utiliser un contenu personnalisé au lieu de data-tip
    'content' => null,
])

@php
    $classes = 'tooltip';
    $pos = ['top','right','bottom','left'];
    if (in_array($position, $pos)) $classes .= ' tooltip-'.$position;
    if ($open) $classes .= ' tooltip-open';
    if ($color) $classes .= ' tooltip-'.$color;
@endphp

<div @if(!is_null($text) && empty($content)) data-tip="{{ $text }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    @if(!empty($content) || isset($contentSlot))
        <div class="tooltip-content">
            @isset($contentSlot)
                {{ $contentSlot }}
            @else
                {!! $content !!}
            @endisset
        </div>
    @endif
    {{ $slot }}
</div>
