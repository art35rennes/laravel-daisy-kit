@props([
    // Largeur: "slim" (icônes uniquement), "wide" (fixe), "auto" (s'adapte au contenu), "fit" (optimisé)
    'variant' => 'wide', // slim|wide|auto|fit
    'collapsed' => false,
    // Autoriser l'utilisateur à plier/déplier (afficher le contrôleur)
    'collapsible' => true,
    // Ouvrir temporairement la sidebar au survol/focus tout en la gardant minifiée par défaut
    'expandOnHover' => false,
    // Forcer l'état plié/déplié et masquer le contrôleur
    'forceCollapsed' => null, // null|true|false
    // Breakpoint à partir duquel la sidebar est fixe/ouverte
    'stickyAt' => 'lg', // null|sm|md|lg|xl
    // Classes de largeur personnalisées si besoin
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    // Largeur minimum et maximum pour variant="fit"
    'minWidth' => 'min-w-48', // w-48 = 192px
    'maxWidth' => 'max-w-80', // w-80 = 320px
    // Clé de stockage (localStorage) pour mémoriser l'état plié/déplié côté client
    'storageKey' => null,
    // Nom/branding du site
    'brand' => null,
    // Lien du brand
    'brandHref' => null,
    'brandUrl' => null,
    'brandCollapsed' => null,
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
    // État de collapse effectif : forceCollapsed a priorité sur collapsed et le mode hover.
    $hoverExpandable = (bool)$expandOnHover && !isset($forceCollapsed);
    $effectiveCollapsed = isset($forceCollapsed) ? (bool)$forceCollapsed : ($hoverExpandable || (bool)$collapsed);
    
    // === CONFIGURATION DES LARGEURS ===
    // Détermination de la classe de largeur selon la variante et l'état collapsed.
    if ($expandedWidth) {
        $wideWidthClass = $expandedWidth;
        $widthStrategy = 'configured';
    } elseif ($sideClass) {
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
    $collapsedWidthClass = $collapsedWidth ?: 'w-20';
    $widthClass = $effectiveCollapsed ? $collapsedWidthClass : $wideWidthClass;
    $collapsedItemClasses = $effectiveCollapsed ? 'justify-center gap-0' : 'gap-2';
    $collapsedPanelClasses = $effectiveCollapsed ? 'px-2 justify-center' : 'px-3 justify-start';
    $collapsedToggleClasses = $effectiveCollapsed ? 'btn-square mx-auto justify-center' : 'w-full justify-start gap-2';
    $collapsedFooterClasses = $effectiveCollapsed ? 'flex justify-center' : '';
    $collapsedMenuClasses = $effectiveCollapsed ? 'sidebar-menu-collapsed' : '';

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
    $menuContainerClass = trim('menu p-2 flex-1 '.$collapsedMenuClasses);
    
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
    $collapseLabel = __('daisy::components.sidebar_collapse');
    $expandLabel = __('daisy::components.sidebar_expand');
    $toggleLabel = $effectiveCollapsed ? $expandLabel : $collapseLabel;

    $normalizeHref = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return '#';
        }

        $url = trim((string) $url);

        if ($url === '') {
            return '#';
        }

        if ($url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : '#';
    };

    $resolvedBrandHref = $brandHref ?: $brandUrl;
    $normalizedBrandHref = $normalizeHref($resolvedBrandHref);
    $hasBrandLink = $normalizedBrandHref !== '#';
    $hasCustomBrandSlot = $brand instanceof \Illuminate\View\ComponentSlot;
    $hasCollapsedBrand = $brandCollapsed instanceof \Illuminate\View\ComponentSlot || filled($brandCollapsed);
@endphp

<aside {{ $attributes->merge(['class' => trim($rootClasses.' '.$widthClass.' '.$baseClasses)]) }}
       data-sidebar-root
       data-width-strategy="{{ $widthStrategy }}"
       data-wide-class="{{ $wideWidthClass }}" 
       data-collapsed-class="{{ $collapsedWidthClass }}"
       data-collapsed="{{ $effectiveCollapsed ? '1' : '0' }}" 
       data-expanded-label="{{ $collapseLabel }}"
       data-collapsed-label="{{ $expandLabel }}"
       @if($hoverExpandable) data-expand-on-hover="1" @endif
       @if($isAuto) data-sidebar-auto @endif
       @if($isFit) data-sidebar-fit @endif
       @if($storageKey) data-storage-key="{{ $storageKey }}" @endif>
    @if($showBrand)
        <div class="h-14 border-b border-base-content/10 flex w-full items-center gap-2 {{ $collapsedPanelClasses }}" data-sidebar-brand>
            <div class="{{ $effectiveCollapsed ? 'hidden' : 'flex' }} min-w-0 flex-1 items-center gap-2" data-sidebar-brand-expanded>
                @if($hasBrandLink && $hasCustomBrandSlot)
                    <a href="{{ $normalizedBrandHref }}" class="flex min-w-0 items-center gap-2">
                        {{ $brand }}
                    </a>
                @elseif($hasBrandLink)
                    <a href="{{ $normalizedBrandHref }}" class="flex min-w-0 items-center gap-2">
                        <div class="font-bold text-lg truncate">{{ $brand ?: config('app.name', 'App') }}</div>
                    </a>
                @elseif($hasCustomBrandSlot)
                    {{ $brand }}
                @elseif($brand)
                    <div class="font-bold text-lg truncate sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ $brand ?: config('app.name', 'App') }}</div>
                @else
                    <div class="font-bold text-lg truncate sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ config('app.name', 'App') }}</div>
                @endif
            </div>
            <div class="{{ $effectiveCollapsed ? 'flex w-full' : 'hidden' }} min-h-10 items-center justify-center" data-sidebar-brand-collapsed aria-hidden="{{ $effectiveCollapsed ? 'false' : 'true' }}">
                @if($hasCollapsedBrand)
                    {{ $brandCollapsed }}
                @elseif($isSlim)
                    <span class="opacity-50 text-xs">&nbsp;</span>
                @endif
            </div>
        </div>
    @endif
    @if($searchable && (! $effectiveCollapsed || $hoverExpandable))
        <div class="px-2 py-2 border-b border-base-content/10" data-module="menu-filter" data-sidebar-hover-content aria-hidden="{{ $effectiveCollapsed ? 'true' : 'false' }}">
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
    <ul class="{{ $menuContainerClass }}" data-sidebar-menu @if($needsFilterModule) data-menu-filter-target @endif>
        @forelse($sections as $section)
            {{-- Titre de section optionnel --}}
            @if(!empty($section['label']))
                <li class="menu-title sidebar-section-title sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ __($section['label']) }}</li>
            @endif
            {{-- Items de navigation : support des items simples et des items avec sous-menu --}}
            @foreach(($section['items'] ?? []) as $item)
                @php
                    if (data_get($item, 'visible', true) === false) {
                        continue;
                    }
                    $routeNames = array_filter((array) data_get($item, 'activeRoutes', []));
                    if (data_get($item, 'activeRoute')) {
                        $routeNames[] = data_get($item, 'activeRoute');
                    }
                    $routeIsActive = $routeNames !== [] && collect($routeNames)->contains(fn ($routeName) => \Illuminate\Support\Facades\Route::currentRouteNamed($routeName));
                    // Détection de la présence d'enfants (sous-menu).
                    $hasChildren = !empty($item['children']);
                    // Un item est actif s'il est marqué actif ou si un de ses enfants est actif.
                    $isActive = !empty($item['active']) || $routeIsActive || collect($item['children'] ?? [])->contains(function ($child) {
                        if (data_get($child, 'visible', true) === false) {
                            return false;
                        }

                        $childRouteNames = array_filter((array) data_get($child, 'activeRoutes', []));
                        if (data_get($child, 'activeRoute')) {
                            $childRouteNames[] = data_get($child, 'activeRoute');
                        }

                        return ! empty($child['active']) || ($childRouteNames !== [] && collect($childRouteNames)->contains(fn ($routeName) => \Illuminate\Support\Facades\Route::currentRouteNamed($routeName)));
                    });
                @endphp
                @if($hasChildren)
                    {{-- Item avec sous-menu : utilise <details> pour le collapse natif --}}
                    <li>
                        <details {{ $isActive ? 'open' : '' }}>
                            <summary class="flex items-center {{ $collapsedItemClasses }} {{ $isActive ? 'menu-active' : '' }}" title="{{ __($item['label'] ?? '') }}" aria-label="{{ __($item['label'] ?? '') }}" data-sidebar-row>
                                @if(!empty($item['icon']))
                                    <x-daisy::ui.advanced.icon :name="$item['icon']" :prefix="$iconPrefix" size="md" />
                                @endif
                                <span class="sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ __($item['label'] ?? '') }}</span>
                            </summary>
                            <ul data-sidebar-submenu aria-hidden="{{ $effectiveCollapsed ? 'true' : 'false' }}">
                                @foreach($item['children'] as $child)
                                    @continue(data_get($child, 'visible', true) === false)
                                    @php
                                        $childRouteNames = array_filter((array) data_get($child, 'activeRoutes', []));
                                        if (data_get($child, 'activeRoute')) {
                                            $childRouteNames[] = data_get($child, 'activeRoute');
                                        }
                                        $childIsActive = ! empty($child['active']) || ($childRouteNames !== [] && collect($childRouteNames)->contains(fn ($routeName) => \Illuminate\Support\Facades\Route::currentRouteNamed($routeName)));
                                    @endphp
                                    <li>
                                        <a href="{{ $normalizeHref($child['href'] ?? '#') }}" class="flex items-center {{ $collapsedItemClasses }} {{ $childIsActive ? 'menu-active' : '' }}" title="{{ __($child['label'] ?? '') }}" aria-label="{{ __($child['label'] ?? '') }}" data-sidebar-row>
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
                        <a href="{{ $normalizeHref($item['href'] ?? '#') }}" class="flex items-center {{ $collapsedItemClasses }} {{ $isActive ? 'menu-active' : '' }}" title="{{ __($item['label'] ?? '') }}" aria-label="{{ __($item['label'] ?? '') }}" data-sidebar-row>
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
    <div class="border-t border-base-content/10 p-2 {{ $collapsedFooterClasses }}" data-sidebar-footer>
        @php $showToggle = $collapsible && !isset($forceCollapsed) && ! $hoverExpandable; @endphp
        @if($showToggle)
            <button type="button" class="btn btn-ghost btn-sm sidebar-toggle {{ $collapsedToggleClasses }}" title="{{ $toggleLabel }}" aria-label="{{ $toggleLabel }}" aria-expanded="{{ $effectiveCollapsed ? 'false' : 'true' }}">
                <span class="sidebar-label-toggle {{ $effectiveCollapsed ? 'sr-only' : '' }}">{{ $toggleLabel }}</span>
                <span data-sidebar-icon-collapsed class="{{ $effectiveCollapsed ? '' : 'hidden' }}">
                    <x-daisy::ui.advanced.icon name="chevron-double-right" :prefix="$iconPrefix" size="sm" />
                </span>
                <span data-sidebar-icon-expanded class="{{ $effectiveCollapsed ? 'hidden' : '' }}">
                    <x-daisy::ui.advanced.icon name="chevron-double-left" :prefix="$iconPrefix" size="sm" />
                </span>
            </button>
        @endif
    </div>
 </aside>


@include('daisy::components.partials.assets')
