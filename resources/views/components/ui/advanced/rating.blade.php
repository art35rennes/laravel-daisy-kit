@props([
    'name' => 'rating',
    'count' => 5,
    'value' => 0,
    'half' => false,
    'shape' => 'star-2', // star|star-2|heart|circle etc (mask-*)
    'color' => null, // bg-primary etc without bg- prefix
    'readOnly' => false,
    'size' => null, // xs|sm|md|lg|xl
    'clearable' => false, // ajoute un input rating-hidden pour pouvoir clear
])

@php
    // Construction des classes CSS selon les options (half, size).
    $wrapper = 'rating';
    if ($half) $wrapper .= ' rating-half';
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) $wrapper .= ' rating-'.$size;
    // Base de la classe mask : définit la forme (star, heart, circle, etc.).
    $maskBase = 'mask mask-'.$shape;
    // Couleur : personnalisée ou warning par défaut (jaune/orange pour les étoiles).
    $colorClass = $color ? ' bg-'.$color : ' bg-warning';
@endphp

<div {{ $attributes->merge(['class' => $wrapper]) }}>
    @if($readOnly)
        {{-- Mode lecture seule : affiche uniquement la valeur (pas d'interaction) --}}
        @for($i = 1; $i <= $count; $i++)
            @php 
                // Détection de l'item actuel : correspond à la valeur affichée.
                $isCurrent = (!$half && (int)$value === $i) || ($half && ($value === $i || $value === ($i - 0.5))); 
            @endphp
            @if(!$half)
                {{-- Mode entier : une seule div par étoile --}}
                <div class="{{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" @if($isCurrent) aria-current="true" @endif></div>
            @else
                {{-- Mode demi-étoile : deux divs par étoile (mask-half-1 et mask-half-2) --}}
                <div class="mask mask-half-1 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ ($i - 0.5) }} star" @if($value === ($i - 0.5)) aria-current="true" @endif></div>
                <div class="mask mask-half-2 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" @if($value === $i) aria-current="true" @endif></div>
            @endif
        @endfor
    @else
        {{-- Mode interactif : radio buttons pour sélectionner la note --}}
        @if($clearable)
            {{-- Radio caché pour réinitialiser la note (rating-hidden de daisyUI) --}}
            <input type="radio" name="{{ $name }}" value="0" class="rating-hidden" aria-label="Clear rating" />
        @endif
        @if(!$half)
            {{-- Mode entier : un radio par étoile --}}
            @for($i = 1; $i <= $count; $i++)
                <input type="radio" name="{{ $name }}" class="{{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" value="{{ $i }}" @checked($value == $i) />
            @endfor
        @else
            {{-- Mode demi-étoile : deux radios par étoile (permet 0.5, 1, 1.5, 2, etc.) --}}
            @for($i = 1; $i <= $count; $i++)
                <input type="radio" name="{{ $name }}" class="mask mask-half-1 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ ($i - 0.5) }} star" value="{{ ($i - 0.5) }}" @checked($value == ($i - 0.5)) />
                <input type="radio" name="{{ $name }}" class="mask mask-half-2 {{ $maskBase }}{{ $colorClass ? ' '.$colorClass : '' }}" aria-label="{{ $i }} star" value="{{ $i }}" @checked($value == $i) />
            @endfor
        @endif
    @endif
</div>
