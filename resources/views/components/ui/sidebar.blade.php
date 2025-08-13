@props([
    // Largeur: "slim" (~w-20), "wide" (~w-72), ou valeur custom via sideClass
    'variant' => 'wide', // slim|wide
    'collapsed' => false,
    // Autoriser l'utilisateur à plier/déplier (afficher le contrôleur)
    'collapsible' => true,
    // Forcer l'état plié/déplié et masquer le contrôleur
    'forceCollapsed' => null, // null|true|false
    // Breakpoint à partir duquel la sidebar est fixe/ouverte
    'stickyAt' => 'lg', // null|sm|md|lg|xl
    // Classes de largeur personnalisées si besoin
    'sideClass' => null,
    // Clé de stockage (localStorage) pour mémoriser l'état plié/déplié côté client
    'storageKey' => null,
    // Nom/branding du site
    'brand' => null,
    // Lien du brand
    'brandHref' => null,
    // Navigation: tableau de sections
    // [
    //   ['label' => 'Section', 'items' => [
    //       ['label' => 'Item', 'href' => '#', 'icon' => 'home', 'active' => false],
    //       ['label' => 'Parent', 'icon' => 'folder', 'children' => [ ... ]],
    //   ]]
    // ]
    'sections' => [],
])

@php
    $isSlim = $variant === 'slim';
    $effectiveCollapsed = isset($forceCollapsed) ? (bool)$forceCollapsed : (bool)$collapsed;
    $wideWidthClass = $sideClass ?: ($isSlim ? 'w-20' : 'w-[12rem]');
    $collapsedWidthClass = 'w-20';
    $widthClass = $effectiveCollapsed ? $collapsedWidthClass : $wideWidthClass;

    $rootClasses = 'bg-base-200 text-base-content';
    // Gestion sticky/fixed selon breakpoint
    if ($stickyAt) {
        $rootClasses .= ' '.$stickyAt.':sticky '.$stickyAt.':top-0 '.$stickyAt.':h-screen';
    }

    $menuContainerClass = 'menu p-2 flex-1';
@endphp

<aside {{ $attributes->merge(['class' => trim($rootClasses.' '.$widthClass.' min-h-full flex flex-col')]) }}
       data-sidebar-root
       data-wide-class="{{ $wideWidthClass }}" data-collapsed-class="{{ $collapsedWidthClass }}"
       data-collapsed="{{ $effectiveCollapsed ? '1' : '0' }}" @if($storageKey) data-storage-key="{{ $storageKey }}" @endif>
    <div class="px-4 py-3 border-b border-base-content/10 flex items-center gap-2">
        <a href="{{ $brandHref ?: '#' }}" class="flex items-center gap-2 flex-1">
            <div class="font-bold text-lg truncate sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ $brand ?: config('app.name', 'App') }}</div>
        </a>
        @if($isSlim)
            <span class="opacity-50 text-xs">&nbsp;</span>
        @endif
    </div>
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
                                    <x-dynamic-component :component="'bi-'.str_replace('_','-',$item['icon'])" class="w-5 h-5" />
                                @endif
                                <span class="sidebar-label {{ $effectiveCollapsed ? 'hidden' : '' }}">{{ __($item['label'] ?? '') }}</span>
                            </summary>
                            <ul>
                                @foreach($item['children'] as $child)
                                    <li>
                                        <a href="{{ $child['href'] ?? '#' }}" class="flex items-center gap-2 {{ !empty($child['active']) ? 'menu-active' : '' }}">
                                            @if(!empty($child['icon']))
                                                <x-dynamic-component :component="'bi-'.str_replace('_','-',$child['icon'])" class="w-5 h-5" />
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
                                <x-dynamic-component :component="'bi-'.str_replace('_','-',$item['icon'])" class="w-5 h-5" />
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
                    <x-bi-chevron-double-right class="w-4 h-4" />
                @else
                    <x-bi-chevron-double-left class="w-4 h-4" />
                @endif
            </button>
        @endif
    </div>
 </aside>


@include('daisy::components.partials.assets')
