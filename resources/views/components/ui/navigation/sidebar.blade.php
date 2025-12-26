@props([
    // Largeur: "slim" (icônes uniquement), "wide" (fixe), "auto" (s'adapte au contenu), "fit" (optimisé)
    'variant' => 'wide', // slim|wide|auto|fit
    'collapsed' => false,
    // Autoriser l'utilisateur à plier/déplier (afficher le contrôleur)
    'collapsible' => true,
    // Forcer l'état plié/déplié et masquer le contrôleur
    'forceCollapsed' => null, // null|true|false
    // Breakpoint à partir duquel la sidebar est fixe/ouverte
    'stickyAt' => 'lg', // null|sm|md|lg|xl
    // Classes de largeur personnalisées si besoin
    'sideClass' => null,
    // Largeur minimum et maximum pour variant="fit"
    'minWidth' => 'min-w-48', // w-48 = 192px
    'maxWidth' => 'max-w-80', // w-80 = 320px
    // Clé de stockage (localStorage) pour mémoriser l'état plié/déplié côté client
    'storageKey' => null,
    // Nom/branding du site
    'brand' => null,
    // Lien du brand
    'brandHref' => null,
    // Afficher le brand dans la sidebar
    'showBrand' => true,
    // Navigation: tableau de sections
    // [
    //   ['label' => 'Section', 'items' => [
    //       ['label' => 'Item', 'href' => '#', 'icon' => 'home', 'active' => false],
    //       ['label' => 'Parent', 'icon' => 'folder', 'children' => [ ... ]],
    //   ]]
    // ]
    'sections' => [],
    // Icon configuration
    'iconPrefix' => 'bi',
    // Activer la recherche/filtre dans la sidebar
    'searchable' => false,
    // Placeholder pour le champ de recherche
    'searchPlaceholder' => 'Rechercher...',
])

@php
    // Détection des variantes de largeur (slim, auto, fit, wide).
    $isSlim = $variant === 'slim';
    $isAuto = $variant === 'auto';
    $isFit = $variant === 'fit';
    // État de collapse effectif : forceCollapsed a priorité sur collapsed.
    $effectiveCollapsed = isset($forceCollapsed) ? (bool)$forceCollapsed : (bool)$collapsed;
    
    // === CONFIGURATION DES LARGEURS ===
    // Détermination de la classe de largeur selon la variante et l'état collapsed.
    if ($sideClass) {
        // Largeur personnalisée fournie explicitement (priorité absolue).
        $wideWidthClass = $sideClass;
        $widthStrategy = 'custom';
    } elseif ($isSlim) {
        // Mode slim : icônes uniquement (largeur minimale fixe).
        $wideWidthClass = 'w-20';
        $widthStrategy = 'slim';
    } elseif ($isFit) {
        // Mode fit : largeur adaptative avec contraintes min/max (optimisé pour le contenu).
        $wideWidthClass = 'w-fit ' . $minWidth . ' ' . $maxWidth;
        $widthStrategy = 'fit';
    } elseif ($isAuto) {
        // Mode auto : largeur fixe initiale, ajustable dynamiquement par JavaScript.
        $wideWidthClass = 'w-64';
        $widthStrategy = 'auto';
    } else {
        // Mode wide : largeur fixe standard (défaut).
        $wideWidthClass = 'w-64';
        $widthStrategy = 'wide';
    }
    
    // Largeur en mode collapsed (toujours minimale, indépendamment de la variante).
    $collapsedWidthClass = 'w-20';
    $widthClass = $effectiveCollapsed ? $collapsedWidthClass : $wideWidthClass;

    // Classes de base pour le conteneur sidebar.
    $rootClasses = 'bg-base-200 text-base-content';
    // Gestion sticky/fixed selon breakpoint : positionnement relatif au viewport ou à la navbar.
    if ($stickyAt) {
        // Détection du contexte : sidebar dans un layout avec navbar (hauteur réduite).
        $customClass = $attributes->get('class', '');
        $hasReducedHeight = str_contains($customClass, 'h-[calc(100vh-4rem)]');
        
        if ($hasReducedHeight) {
            // Contexte navbar-sidebar : positionner sous la navbar (top-16 = 4rem).
            $rootClasses .= ' '.$stickyAt.':sticky '.$stickyAt.':top-16';
        } else {
            // Contexte normal : sidebar pleine hauteur, collée en haut.
            $rootClasses .= ' '.$stickyAt.':sticky '.$stickyAt.':top-0 '.$stickyAt.':h-screen';
        }
    }

    // === CLASSES DE CONTENEUR ===
    // Classes pour le conteneur du menu (utilise le composant menu de daisyUI).
    $menuContainerClass = 'menu p-2 flex-1';
    
    // Optimisations spécifiques pour le mode fit (ajustement dynamique de la largeur).
    if ($isFit) {
        $menuContainerClass .= ' sidebar-fit-content';
    }
    
    // === CLASSES FINALES ===
    // Classes de base pour la structure flexbox verticale.
    $baseClasses = 'min-h-full flex flex-col';
    
    // Activation des classes adaptatives pour les modes auto/fit (ajustement JS dynamique).
    if (($isAuto || $isFit) && !$effectiveCollapsed) {
        $baseClasses .= ' sidebar-adaptive';
    }
    
    // Classe spécifique pour le mode fit (largeur optimisée selon le contenu).
    if ($isFit && !$effectiveCollapsed) {
        $baseClasses .= ' sidebar-fit';
    }
    
    // Activation du module JavaScript de filtrage si searchable est activé et sidebar non collapsed.
    $needsFilterModule = $searchable && !$effectiveCollapsed;
@endphp

<aside {{ $attributes->merge(['class' => trim($rootClasses.' '.$widthClass.' '.$baseClasses)]) }}
       data-sidebar-root
       data-width-strategy="{{ $widthStrategy }}"
       data-wide-class="{{ $wideWidthClass }}" 
       data-collapsed-class="{{ $collapsedWidthClass }}"
       data-collapsed="{{ $effectiveCollapsed ? '1' : '0' }}" 
       @if($isAuto) data-sidebar-auto @endif
       @if($isFit) data-sidebar-fit @endif
       @if($storageKey) data-storage-key="{{ $storageKey }}" @endif>
    @if($showBrand)
        <div class="px-4 py-3 border-b border-base-content/10 flex items-center gap-2">
            <a href="{{ $brandHref ?: '#' }}" class="flex items-center gap-2 flex-1">
                <div class="font-bold text-lg truncate sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ $brand ?: config('app.name', 'App') }}</div>
            </a>
            @if($isSlim)
                <span class="opacity-50 text-xs">&nbsp;</span>
            @endif
        </div>
    @endif
    @if($searchable && !$effectiveCollapsed)
        <div class="px-2 py-2 border-b border-base-content/10" data-module="menu-filter">
            <label class="input input-sm">
                <x-daisy::ui.advanced.icon name="search" :prefix="$iconPrefix" size="sm" class="opacity-50" />
                <input type="search" 
                       class="grow" 
                       placeholder="{{ $searchPlaceholder }}"
                       data-menu-filter-input
                       aria-label="Rechercher dans le menu">
            </label>
        </div>
    @endif
    {{-- Menu de navigation : structure hiérarchique (sections > items > children) --}}
    <ul class="{{ $menuContainerClass }}" @if($needsFilterModule) data-menu-filter-target @else data-sidebar-menu @endif>
        @forelse($sections as $section)
            {{-- Titre de section optionnel --}}
            @if(!empty($section['label']))
                <li class="menu-title">{{ __($section['label']) }}</li>
            @endif
            {{-- Items de navigation : support des items simples et des items avec sous-menu --}}
            @foreach(($section['items'] ?? []) as $item)
                @php
                    // Détection de la présence d'enfants (sous-menu).
                    $hasChildren = !empty($item['children']);
                    // Un item est actif s'il est marqué actif ou si un de ses enfants est actif.
                    $isActive = !empty($item['active']) || collect($item['children'] ?? [])->contains(fn($c) => !empty($c['active']));
                @endphp
                @if($hasChildren)
                    {{-- Item avec sous-menu : utilise <details> pour le collapse natif --}}
                    <li>
                        <details {{ $isActive ? 'open' : '' }}>
                            <summary class="flex items-center gap-2">
                                @if(!empty($item['icon']))
                                    <x-daisy::ui.advanced.icon :name="$item['icon']" :prefix="$iconPrefix" size="md" />
                                @endif
                                <span class="sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ __($item['label'] ?? '') }}</span>
                            </summary>
                            <ul>
                                @foreach($item['children'] as $child)
                                    <li>
                                        <a href="{{ $child['href'] ?? '#' }}" class="flex items-center gap-2 {{ !empty($child['active']) ? 'menu-active' : '' }}">
                                            @if(!empty($child['icon']))
                                                <x-daisy::ui.advanced.icon :name="$child['icon']" :prefix="$iconPrefix" size="md" />
                                            @endif
                                            <span class="sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ __($child['label'] ?? '') }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </li>
                @else
                    {{-- Item simple : lien direct sans sous-menu --}}
                    <li>
                        <a href="{{ $item['href'] ?? '#' }}" class="flex items-center gap-2 {{ $isActive ? 'menu-active' : '' }}">
                            @if(!empty($item['icon']))
                                <x-daisy::ui.advanced.icon :name="$item['icon']" :prefix="$iconPrefix" size="md" />
                            @endif
                            <span class="sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ __($item['label'] ?? '') }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        @empty
            {{-- Slot par défaut si aucune section n'est fournie --}}
            {{ $slot }}
        @endforelse
    </ul>
    {{-- Contrôle de collapse : bouton pour plier/déplier la sidebar (si autorisé et non forcé) --}}
    <div class="p-2 border-t border-base-content/10">
        @php $showToggle = $collapsible && !isset($forceCollapsed); @endphp
        @if($showToggle)
            <button type="button" class="btn btn-ghost btn-sm w-full justify-between sidebar-toggle">
                <span class="sidebar-label-toggle">{{ $effectiveCollapsed ? __('Expand') : __('Collapse') }}</span>
                @if($effectiveCollapsed)
                    <x-daisy::ui.advanced.icon name="chevron-double-right" :prefix="$iconPrefix" size="sm" />
                @else
                    <x-daisy::ui.advanced.icon name="chevron-double-left" :prefix="$iconPrefix" size="sm" />
                @endif
            </button>
        @endif
    </div>
 </aside>


@include('daisy::components.partials.assets')
