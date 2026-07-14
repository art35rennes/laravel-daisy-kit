@props([
    'title' => null,
    'theme' => null,
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    'collapsed' => false,
    'collapsible' => true,
    'forceCollapsed' => null,
    'expandOnHover' => false,
    'stickyAt' => 'lg',
    'collapseAt' => null,
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
    'searchable' => false,
    'searchPlaceholder' => null,
    'searchEmptyLabel' => null,
    'searchResultsLabel' => null,
    // Responsive drawer behavior
    'drawerId' => 'layout-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false, // sidebar on right
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    // Content container
    'container' => 'p-6',
    // Layout options
    'hasNavbar' => false,
    'showThemeController' => true,
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
    'themeLabel' => 'Theme',
])

@php
    $mobileOnlyClasses = [
        'sm' => 'sm:hidden',
        'md' => 'md:hidden',
        'lg' => 'lg:hidden',
        'xl' => 'xl:hidden',
        '2xl' => '2xl:hidden',
    ];
    $desktopOnlyClasses = [
        'sm' => 'hidden sm:flex',
        'md' => 'hidden md:flex',
        'lg' => 'hidden lg:flex',
        'xl' => 'hidden xl:flex',
        '2xl' => 'hidden 2xl:flex',
    ];
    $sidebarHeights = [
        'sm' => 'sm:h-[calc(100vh-4rem)]',
        'md' => 'md:h-[calc(100vh-4rem)]',
        'lg' => 'lg:h-[calc(100vh-4rem)]',
        'xl' => 'xl:h-[calc(100vh-4rem)]',
        '2xl' => '2xl:h-[calc(100vh-4rem)]',
    ];
    $responsiveBreakpoint = array_key_exists($responsiveOpen, $mobileOnlyClasses) ? $responsiveOpen : 'lg';
    $mobileOnlyClass = $mobileOnlyClasses[$responsiveBreakpoint];
    $desktopOnlyClass = $desktopOnlyClasses[$responsiveBreakpoint];
    $sidebarHeightClass = $sidebarHeights[$responsiveBreakpoint];
    $collapseAt ??= $responsiveBreakpoint;
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div {{ $attributes->merge(['class' => 'min-h-screen']) }}>
        <x-daisy::ui.overlay.drawer :id="$drawerId" :end="$end" :responsiveOpen="$responsiveOpen" :sideIsMenu="false" sideClass="w-auto" class="">
            <x-slot:content>
                @if(!$hasNavbar)
                    <div class="bg-base-100 px-4 h-14 flex items-center justify-between gap-4 lg:justify-end">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex items-center gap-2 {{ $mobileOnlyClass }}">
                                <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost">
                                    <x-daisy::ui.advanced.icon :name="$menuIcon" size="lg" />
                                </label>
                                @if($title)
                                    <div class="font-semibold">{{ __($title) }}</div>
                                @endif
                            </div>
                            @isset($navbarHeading)
                                <div class="hidden min-w-0 max-w-xs flex-col justify-center leading-tight text-base-content md:flex lg:max-w-md xl:max-w-xl [&>h1]:truncate [&>h1]:text-sm [&>h1]:font-semibold [&>h1]:leading-tight [&>p]:truncate [&>p]:text-xs [&>p]:leading-tight [&>p]:text-base-content/70" data-navbar-heading>
                                    {{ $navbarHeading }}
                                </div>
                            @endisset
                        </div>
                        <div class="shrink-0 items-center gap-2 {{ $desktopOnlyClass }}">
                            @if($showThemeController)
                                <x-daisy::ui.advanced.theme-controller
                                    variant="dropdown"
                                    :themes="$themes"
                                    :label="$themeLabel"
                                    size="sm"
                                />
                            @endif
                            {{ $topbarRight ?? '' }}
                        </div>
                    </div>
                @endif
                @if($hasNavbar && isset($topbar))
                    {{ $topbar }}
                @endif
                <div class="min-w-0 {{ $container }} {{ $hasNavbar && ! isset($topbar) ? 'pt-16' : '' }}">
                    {{ $slot }}
                </div>
            </x-slot:content>
            <x-slot:side>
                <x-daisy::ui.navigation.sidebar 
                    :variant="$variant" 
                    :sideClass="$sideClass" 
                    :expandedWidth="$expandedWidth"
                    :collapsedWidth="$collapsedWidth"
                    :collapsed="$collapsed"
                    :collapsible="$collapsible"
                    :forceCollapsed="$forceCollapsed"
                    :expandOnHover="$expandOnHover"
                    :stickyAt="$stickyAt"
                    :collapseAt="$collapseAt"
                    :hasNavbar="$hasNavbar"
                    :end="$end"
                    :storageKey="$storageKey"
                    :name="$name"
                    :logo="$logo"
                    :logoAlt="$logoAlt"
                    :brand="$brand" 
                    :brandHref="$brandHref" 
                    :brandUrl="$brandUrl"
                    :brandCollapsed="$brandCollapsed"
                    :showBrand="$showBrand" 
                    :sections="$sections" 
                    :searchable="$searchable"
                    :searchPlaceholder="$searchPlaceholder"
                    :searchEmptyLabel="$searchEmptyLabel"
                    :searchResultsLabel="$searchResultsLabel"
                    :iconPrefix="$iconPrefix"
                    :fallbackIcon="$fallbackIcon"
                    class="h-full {{ $hasNavbar ? $sidebarHeightClass : '' }}"
                >
                    @isset($sidebarFooter)
                        <x-slot:footer>{{ $sidebarFooter }}</x-slot:footer>
                    @endisset
                </x-daisy::ui.navigation.sidebar>
            </x-slot:side>
        </x-daisy::ui.overlay.drawer>
    </div>
</x-daisy::layout.app>
