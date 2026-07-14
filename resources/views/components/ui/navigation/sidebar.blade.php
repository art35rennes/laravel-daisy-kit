@props([
    'variant' => 'wide', // slim|wide|auto|fit
    'collapsed' => false,
    'collapsible' => true,
    'expandOnHover' => false,
    'forceCollapsed' => null,
    'stickyAt' => 'lg',
    'collapseAt' => 'lg',
    'hasNavbar' => false,
    'end' => false,
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    'minWidth' => 'min-w-48',
    'maxWidth' => 'max-w-80',
    'storageKey' => null,
    'name' => null,
    'logo' => null,
    'logoAlt' => null,
    'brand' => null,
    'brandHref' => null,
    'brandUrl' => null,
    'brandCollapsed' => null,
    'showBrand' => true,
    'sections' => [],
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    'searchable' => false,
    'searchPlaceholder' => null,
    'searchEmptyLabel' => null,
    'searchResultsLabel' => null,
])

@php
    $isSlim = $variant === 'slim';
    $isAuto = $variant === 'auto';
    $isFit = $variant === 'fit';
    $hoverExpandable = (bool) $expandOnHover && ! isset($forceCollapsed);
    $effectiveCollapsed = isset($forceCollapsed) ? (bool) $forceCollapsed : ($hoverExpandable || (bool) $collapsed);

    if ($expandedWidth) {
        $wideWidthClass = $expandedWidth;
        $widthStrategy = 'configured';
    } elseif ($sideClass) {
        $wideWidthClass = $sideClass;
        $widthStrategy = 'custom';
    } elseif ($isSlim) {
        $wideWidthClass = 'w-20';
        $widthStrategy = 'slim';
    } elseif ($isFit) {
        $wideWidthClass = trim("w-fit {$minWidth} {$maxWidth}");
        $widthStrategy = 'fit';
    } else {
        $wideWidthClass = 'w-64';
        $widthStrategy = $isAuto ? 'auto' : 'wide';
    }

    $collapsedWidthClass = $collapsedWidth ?: 'w-20';
    $widthClass = $effectiveCollapsed ? $collapsedWidthClass : $wideWidthClass;

    $stickyClasses = [
        'sm' => $hasNavbar ? 'sm:sticky sm:top-16' : 'sm:sticky sm:top-0 sm:h-screen',
        'md' => $hasNavbar ? 'md:sticky md:top-16' : 'md:sticky md:top-0 md:h-screen',
        'lg' => $hasNavbar ? 'lg:sticky lg:top-16' : 'lg:sticky lg:top-0 lg:h-screen',
        'xl' => $hasNavbar ? 'xl:sticky xl:top-16' : 'xl:sticky xl:top-0 xl:h-screen',
        '2xl' => $hasNavbar ? '2xl:sticky 2xl:top-16' : '2xl:sticky 2xl:top-0 2xl:h-screen',
    ];
    $toggleBreakpointClasses = [
        'sm' => 'hidden sm:flex',
        'md' => 'hidden md:flex',
        'lg' => 'hidden lg:flex',
        'xl' => 'hidden xl:flex',
        '2xl' => 'hidden 2xl:flex',
    ];
    $stickyClass = $stickyAt ? ($stickyClasses[$stickyAt] ?? $stickyClasses['lg']) : '';
    $toggleBreakpointClass = $collapseAt ? ($toggleBreakpointClasses[$collapseAt] ?? $toggleBreakpointClasses['lg']) : 'flex';

    $rootClasses = trim("bg-base-200 text-base-content min-h-full flex flex-col overflow-visible transition-[width] duration-200 {$stickyClass} {$widthClass}");
    $collapseLabel = __('daisy::components.sidebar_collapse');
    $expandLabel = __('daisy::components.sidebar_expand');
    $toggleLabel = $effectiveCollapsed ? $expandLabel : $collapseLabel;
    $expandIcon = $end ? 'chevron-double-left' : 'chevron-double-right';
    $collapseIcon = $end ? 'chevron-double-right' : 'chevron-double-left';
    $searchPlaceholder ??= __('daisy::components.sidebar_search_placeholder');
    $searchEmptyLabel ??= __('daisy::components.sidebar_search_empty');
    $searchResultsLabel ??= __('daisy::components.sidebar_search_results');

    $normalizeHref = function ($url): string {
        if (! is_string($url) && ! $url instanceof \Stringable) {
            return '#';
        }

        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return '#';
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : '#';
    };

    $isItemActive = function (array $item): bool {
        if (! empty($item['active'])) {
            return true;
        }

        $routeNames = array_filter((array) data_get($item, 'activeRoutes', []));

        if (data_get($item, 'activeRoute')) {
            $routeNames[] = data_get($item, 'activeRoute');
        }

        return $routeNames !== []
            && collect($routeNames)->contains(fn ($routeName) => \Illuminate\Support\Facades\Route::currentRouteNamed($routeName));
    };

    $hasActiveDescendant = function (array $items) use (&$hasActiveDescendant, $isItemActive): bool {
        foreach ($items as $item) {
            if (data_get($item, 'visible', true) === false) {
                continue;
            }

            if ($isItemActive($item) || $hasActiveDescendant((array) data_get($item, 'children', []))) {
                return true;
            }
        }

        return false;
    };

    $resolvedBrandHref = $normalizeHref($brandHref ?: $brandUrl);
    $hasBrandLink = $resolvedBrandHref !== '#';
    $hasCustomBrandSlot = $brand instanceof \Illuminate\View\ComponentSlot;
    $hasCustomCollapsedBrand = $brandCollapsed instanceof \Illuminate\View\ComponentSlot || filled($brandCollapsed);
    $hasCustomLogoSlot = $logo instanceof \Illuminate\View\ComponentSlot;
    $resolvedName = $name ?? ($hasCustomBrandSlot ? null : $brand) ?? config('app.name', 'App');
    $resolvedLogoAlt = $logoAlt ?? ($resolvedName ? (string) $resolvedName : '');
@endphp

<aside
    {{ $attributes->merge(['class' => $rootClasses]) }}
    data-sidebar-root
    data-sidebar-side="{{ $end ? 'end' : 'start' }}"
    data-width-strategy="{{ $widthStrategy }}"
    data-wide-class="{{ $wideWidthClass }}"
    data-collapsed-class="{{ $collapsedWidthClass }}"
    data-collapsed="{{ $effectiveCollapsed ? '1' : '0' }}"
    data-collapse-at="{{ $collapseAt ?? 'none' }}"
    data-expanded-label="{{ $collapseLabel }}"
    data-collapsed-label="{{ $expandLabel }}"
    @if($hoverExpandable) data-expand-on-hover="1" @endif
    @if(isset($forceCollapsed)) data-force-collapsed="{{ $forceCollapsed ? '1' : '0' }}" @endif
    @if($storageKey) data-storage-key="{{ $storageKey }}" @endif
>
    @if($showBrand)
        <header class="flex h-14 w-full shrink-0 items-center gap-2 border-b border-base-content/10 px-3" data-sidebar-brand>
            <div class="flex min-w-0 flex-1 items-center justify-center gap-2" data-sidebar-brand-content>
                @if($hasBrandLink)
                    <a href="{{ $resolvedBrandHref }}" class="flex min-w-0 items-center gap-2" data-sidebar-brand-link>
                @endif

                @if($hasCustomLogoSlot)
                    <span class="shrink-0" data-sidebar-logo>{{ $logo }}</span>
                @elseif(filled($logo))
                    <img src="{{ $logo }}" alt="{{ $resolvedLogoAlt }}" class="size-8 shrink-0 object-contain" data-sidebar-logo>
                @endif

                @if($hasCustomBrandSlot)
                    <span class="min-w-0 sidebar-label">{{ $brand }}</span>
                @elseif(filled($resolvedName))
                    <span class="min-w-0 truncate text-lg font-bold sidebar-label" data-sidebar-name>{{ $resolvedName }}</span>
                @endif

                @if($hasBrandLink)
                    </a>
                @endif

                @if($hasCustomCollapsedBrand)
                    <span class="hidden shrink-0" data-sidebar-brand-collapsed>{{ $brandCollapsed }}</span>
                @endif
            </div>
        </header>
    @endif

    @if($searchable)
        <div class="shrink-0 border-b border-base-content/10 p-2" data-sidebar-search-region>
            <label class="input input-sm w-full">
                <x-daisy::ui.advanced.icon name="search" :prefix="$iconPrefix" size="sm" class="opacity-50" />
                <input
                    type="search"
                    class="grow"
                    placeholder="{{ $searchPlaceholder }}"
                    aria-label="{{ $searchPlaceholder }}"
                    autocomplete="off"
                    data-sidebar-search
                >
            </label>
            <p class="sr-only" aria-live="polite" data-sidebar-search-status data-results-label="{{ $searchResultsLabel }}"></p>
        </div>
    @endif

    <ul class="menu min-h-0 w-full flex-1 flex-nowrap overflow-y-auto p-2" data-sidebar-menu data-sidebar-scroll-region @if($searchable) data-menu-filter-target @endif>
        @forelse($sections as $sectionIndex => $section)
            <li data-sidebar-section data-sidebar-section-id="{{ $sectionIndex }}">
                @if(! empty($section['label']))
                    <h2 class="menu-title sidebar-label" data-sidebar-section-title>{{ __($section['label']) }}</h2>
                @endif
                <ul>
                    @foreach(($section['items'] ?? []) as $item)
                        @continue(data_get($item, 'visible', true) === false)
                        @include('daisy::components.ui.navigation.partials.sidebar-item', [
                            'item' => $item,
                            'depth' => 0,
                            'sectionId' => $sectionIndex,
                            'iconPrefix' => $iconPrefix,
                            'fallbackIcon' => $fallbackIcon,
                            'normalizeHref' => $normalizeHref,
                            'isItemActive' => $isItemActive,
                            'hasActiveDescendant' => $hasActiveDescendant,
                        ])
                    @endforeach
                </ul>
            </li>
        @empty
            {{ $slot }}
        @endforelse
    </ul>

    @if($searchable)
        <p class="hidden shrink-0 px-3 py-4 text-center text-sm text-base-content/60" data-sidebar-search-empty>{{ $searchEmptyLabel }}</p>
    @endif

    @if(isset($footer) || ($collapsible && ! isset($forceCollapsed) && ! $hoverExpandable))
        <footer class="flex shrink-0 flex-col items-center border-t border-base-content/10 p-2" data-sidebar-footer>
            @isset($footer)
                <div class="mb-2 w-full text-center text-xs text-base-content/50 sidebar-label">
                    {{ $footer }}
                </div>
            @endisset

            @if($collapsible && ! isset($forceCollapsed) && ! $hoverExpandable)
                <button
                    type="button"
                    class="btn btn-ghost btn-sm justify-center gap-2 sidebar-toggle {{ $toggleBreakpointClass }}"
                    title="{{ $toggleLabel }}"
                    aria-label="{{ $toggleLabel }}"
                    aria-expanded="{{ $effectiveCollapsed ? 'false' : 'true' }}"
                    data-sidebar-toggle
                >
                    <span class="sidebar-label" data-sidebar-toggle-label>{{ $toggleLabel }}</span>
                    <span data-sidebar-icon-collapsed @if(! $effectiveCollapsed) hidden @endif>
                        <x-daisy::ui.advanced.icon :name="$expandIcon" :prefix="$iconPrefix" size="sm" />
                    </span>
                    <span data-sidebar-icon-expanded @if($effectiveCollapsed) hidden @endif>
                        <x-daisy::ui.advanced.icon :name="$collapseIcon" :prefix="$iconPrefix" size="sm" />
                    </span>
                </button>
            @endif
        </footer>
    @endif
</aside>

@include('daisy::components.partials.assets')
