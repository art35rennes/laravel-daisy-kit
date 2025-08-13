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
    
    // Position
    $validPositions = ['top','right','bottom','left'];
    if (in_array($position, $validPositions)) {
        $classes .= ' tooltip-'.$position;
    }
    
    // Open state
    if ($open) {
        $classes .= ' tooltip-open';
    }
    
    // Color
    $validColors = ['neutral','primary','secondary','accent','info','success','warning','error'];
    if ($color && in_array($color, $validColors)) {
        $classes .= ' tooltip-'.$color;
    }
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
