@props([
    'vertical' => true,
    'size' => null, // xs|sm|md|lg|xl
    // Rendre horizontal à partir d'un breakpoint: sm|md|lg|xl (ajoute "sm:menu-horizontal" ...)
    'horizontalAt' => null,
    // Styles de conteneur
    'bg' => true,
    'rounded' => true,
    // Titre optionnel en tête
    'title' => null,
    // Filtrage
    'filterable' => false,
    'filterPlaceholder' => 'Rechercher...',
])

@php
    // Construction des classes CSS selon les options (background, rounded, orientation, taille).
    $classes = 'menu';
    if ($bg) $classes .= ' bg-base-100';
    if ($rounded) $classes .= ' rounded-box';
    // Orientation : vertical par défaut, horizontal si explicitement désactivé ou via breakpoint.
    if (!$vertical) $classes .= ' menu-horizontal';
    // Orientation responsive : devient horizontal à partir d'un breakpoint (ex: md:menu-horizontal).
    if ($horizontalAt && in_array($horizontalAt, ['sm','md','lg','xl'], true)) {
        $classes .= ' '.$horizontalAt.':menu-horizontal';
    }
    if ($size) $classes .= ' menu-'.$size;

    // Générer un ID unique pour le filtre si activé
    $filterId = $filterable ? 'menu-filter-'.uniqid() : null;
@endphp

@if($filterable)
    <div data-module="menu-filter" class="mb-4">
        <input 
            type="text" 
            data-menu-filter-input
            placeholder="{{ $filterPlaceholder }}"
            class="input input-sm w-full"
        />
        <ul {{ $attributes->merge(['class' => $classes, 'data-menu-filter-target' => true]) }}>
            {{-- Titre optionnel : affiché en tête du menu (utilise menu-title de daisyUI) --}}
            @if($title)
                <li class="menu-title">{{ $title }}</li>
            @endif
            {{-- Contenu du menu : items passés via slot (liens, boutons, sous-menus, etc.) --}}
            {{ $slot }}
        </ul>
    </div>
@else
    <ul {{ $attributes->merge(['class' => $classes]) }}>
        {{-- Titre optionnel : affiché en tête du menu (utilise menu-title de daisyUI) --}}
        @if($title)
            <li class="menu-title">{{ $title }}</li>
        @endif
        {{-- Contenu du menu : items passés via slot (liens, boutons, sous-menus, etc.) --}}
        {{ $slot }}
    </ul>
@endif
