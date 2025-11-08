@props([
    'title' => null,
    'imageUrl' => null,
    'bordered' => false,
    'compact' => false,
    'side' => false,
    'imageFull' => false,
    'color' => null, // base-100 (default) or any bg-* utility
    // Styles DaisyUI supplémentaires
    'dash' => false, // card-dash
    'size' => 'md',  // xs|sm|md|lg|xl
    // Accessibilité image
    'imageAlt' => '',
    // Résilience média
    'imageClass' => null,   // classes appliquées à l'image si fournies (prioritaires)
    'figureClass' => null,  // classes appliquées au <figure>
])

@php
    $root = 'card';
    if ($compact) $root .= ' card-compact';
    if ($side) $root .= ' card-side';
    if ($imageFull) $root .= ' image-full';
    if ($bordered) $root .= ' card-border';
    if ($dash) $root .= ' card-dash';

    $sizeMap = [
        'xs' => 'card-xs',
        'sm' => 'card-sm',
        'md' => 'card-md',
        'lg' => 'card-lg',
        'xl' => 'card-xl',
    ];
    if (isset($sizeMap[$size])) {
        $root .= ' ' . $sizeMap[$size];
    }

    $bgClass = $color ? ' bg-'.$color : ' bg-base-100';
    $root .= $bgClass.' shadow';
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    @if($imageUrl || isset($figure))
        @php
            // Classes par défaut pour rendre le média plus résilient
            $defaultImageClass = $imageFull
                ? 'w-full h-full object-cover'
                : ($side ? 'w-48 sm:w-64 object-cover' : 'w-full h-auto object-contain');

            $finalImageClass = trim(($imageClass ?? '') ?: $defaultImageClass);

            $defaultFigureClass = $imageFull
                ? 'overflow-hidden'
                : ($side ? 'overflow-hidden' : '');

            $finalFigureClass = trim(($figureClass ?? '') ?: $defaultFigureClass);
        @endphp
        <figure class="{{ $finalFigureClass }}">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="{{ $finalImageClass }}" loading="lazy" />
            @else
                {{ $figure }}
            @endif
        </figure>
    @endif

    <div class="card-body">
        @if($title)
            <h2 class="card-title">{{ $title }}</h2>
        @endif
        <div>{{ $slot }}</div>
        @isset($actions)
            <div class="card-actions justify-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
