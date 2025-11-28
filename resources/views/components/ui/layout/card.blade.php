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
    // Construction des classes CSS selon les options (compact, side, imageFull, etc.).
    $root = 'card';
    if ($compact) $root .= ' card-compact';
    if ($side) $root .= ' card-side';
    if ($imageFull) $root .= ' image-full';
    if ($bordered) $root .= ' card-border';
    if ($dash) $root .= ' card-dash';

    // Mapping des tailles vers les classes daisyUI.
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

    // Couleur de fond : personnalisée ou base-100 par défaut.
    $bgClass = $color ? ' bg-'.$color : ' bg-base-100';
    $root .= $bgClass.' shadow';
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    {{-- Figure : image ou slot figure personnalisé (optionnel) --}}
    @if($imageUrl || isset($figure))
        @php
            // Classes par défaut pour rendre le média plus résilient selon le layout.
            $defaultImageClass = $imageFull
                ? 'w-full h-full object-cover' // image-full : image en overlay sur la carte.
                : ($side ? 'w-48 sm:w-64 object-cover' : 'w-full h-auto object-contain'); // side : image à côté, sinon image en haut.

            $finalImageClass = trim(($imageClass ?? '') ?: $defaultImageClass);

            // Classes pour le figure : overflow-hidden pour image-full et side (évite les débordements).
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

    {{-- Corps de la carte : titre, contenu, actions --}}
    <div class="card-body">
        @if($title)
            <h2 class="card-title">{{ $title }}</h2>
        @endif
        <div>{{ $slot }}</div>
        {{-- Actions : slot pour les boutons d'action (alignés à droite par défaut) --}}
        @isset($actions)
            <div class="card-actions justify-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
