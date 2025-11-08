@props([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'variant' => 'solid', // solid|soft|outline|dash
    'icon' => null,
    'title' => null,
    // API "callout" friendly
    'heading' => null,     // alias de title
    'text' => null,        // contenu texte (alias du slot)
    'inline' => false,     // actions en ligne
    'iconInHeading' => false, // placer l'icône dans le heading
    // Orientation
    'vertical' => null,       // bool|null
    'horizontal' => null,     // bool|null
    'horizontalAt' => null,   // ex: 'sm' → alert-vertical sm:alert-horizontal
])

@php
    $classes = 'alert';
    // Color (supporte alias danger→error pour compat callout)
    $colorKey = $color === 'danger' ? 'error' : $color;
    $classes .= ' alert-'.$colorKey;
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

    // Inline actions → aligner verticalement au centre
    if ($inline) {
        $classes .= ' items-center';
    }

    $headingText = $heading ?? $title;
    
    // Gestion de l'icône pour éviter l'erreur BladeUI\Icons\Svg
    $iconHtml = null;
    if ($icon) {
        if (is_string($icon)) {
            $iconHtml = $icon;
        } elseif (is_object($icon) && method_exists($icon, 'toHtml')) {
            $iconHtml = $icon->toHtml();
        } elseif (is_object($icon) && method_exists($icon, '__toString')) {
            $iconHtml = (string) $icon;
        } else {
            $iconHtml = '';
        }
    }
@endphp

<div {{ $attributes->merge(['role' => 'alert', 'class' => $classes]) }}>
    @if($iconHtml && !$iconInHeading)
        <span class="shrink-0">{!! $iconHtml !!}</span>
    @endif
    <div class="flex-1">
        @if($headingText)
            <h3 class="font-medium flex items-center gap-2">
                @if($iconHtml && $iconInHeading)
                    <span class="shrink-0">{!! $iconHtml !!}</span>
                @endif
                <span>{{ $headingText }}</span>
            </h3>
        @endif
        <div class="text-sm">{!! $text !== null ? e($text) : $slot !!}</div>
    </div>
    @isset($actions)
        <div class="flex items-center gap-2 flex-wrap justify-start sm:justify-end">{{ $actions }}</div>
    @endisset
    @isset($controls)
        <div class="ms-2">{{ $controls }}</div>
    @endisset
</div>
