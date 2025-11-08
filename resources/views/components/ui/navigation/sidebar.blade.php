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
])

@php
    $isSlim = $variant === 'slim';
    $isAuto = $variant === 'auto';
    $isFit = $variant === 'fit';
    $effectiveCollapsed = isset($forceCollapsed) ? (bool)$forceCollapsed : (bool)$collapsed;
    
    // === CONFIGURATION DES LARGEURS ===
    if ($sideClass) {
        // Largeur personnalisée fournie
        $wideWidthClass = $sideClass;
        $widthStrategy = 'custom';
    } elseif ($isSlim) {
        // Mode slim : icônes uniquement
        $wideWidthClass = 'w-20';
        $widthStrategy = 'slim';
    } elseif ($isFit) {
        // Mode fit : optimisé avec min/max
        $wideWidthClass = 'w-fit ' . $minWidth . ' ' . $maxWidth;
        $widthStrategy = 'fit';
    } elseif ($isAuto) {
        // Mode auto : largeur fixe pour le JavaScript
        $wideWidthClass = 'w-64';
        $widthStrategy = 'auto';
    } else {
        // Mode wide : largeur fixe standard
        $wideWidthClass = 'w-64';
        $widthStrategy = 'wide';
    }
    
    $collapsedWidthClass = 'w-20';
    $widthClass = $effectiveCollapsed ? $collapsedWidthClass : $wideWidthClass;

    $rootClasses = 'bg-base-200 text-base-content';
    // Gestion sticky/fixed selon breakpoint
    if ($stickyAt) {
        // Détecter si on est dans un contexte avec navbar (hauteur réduite)
        $customClass = $attributes->get('class', '');
        $hasReducedHeight = str_contains($customClass, 'h-[calc(100vh-4rem)]');
        
        if ($hasReducedHeight) {
            // Dans le contexte navbar-sidebar, positionner sous la navbar
            $rootClasses .= ' '.$stickyAt.':sticky '.$stickyAt.':top-16';
        } else {
            // Contexte normal
            $rootClasses .= ' '.$stickyAt.':sticky '.$stickyAt.':top-0 '.$stickyAt.':h-screen';
        }
    }

    // === CLASSES DE CONTENEUR ===
    $menuContainerClass = 'menu p-2 flex-1';
    
    // Optimisations pour le mode fit
    if ($isFit) {
        $menuContainerClass .= ' sidebar-fit-content';
    }
    
    // === CLASSES FINALES ===
    $baseClasses = 'min-h-full flex flex-col';
    
    // Ajouter les classes pour modes adaptatifs
    if (($isAuto || $isFit) && !$effectiveCollapsed) {
        $baseClasses .= ' sidebar-adaptive';
    }
    
    if ($isFit && !$effectiveCollapsed) {
        $baseClasses .= ' sidebar-fit';
    }
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
    <ul class="{{ $menuContainerClass }}">
        @forelse($sections as $section)
            @if(!empty($section['label']))
                <li class="menu-title">{{ __($section['label']) }}</li>
            @endif
            @foreach(($section['items'] ?? []) as $item)
                @php
                    $hasChildren = !empty($item['children']);
                    $isActive = !empty($item['active']) || collect($item['children'] ?? [])->contains(fn($c) => !empty($c['active']));
                @endphp
                @if($hasChildren)
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
            {{ $slot }}
        @endforelse
    </ul>
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
