@props([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarContainer' => null,
    // Sidebar options (héritées de sidebar-layout)
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
    'drawerId' => 'layout-nav-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false,
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    // Content container
    'container' => 'p-6',
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
    $responsiveBreakpoint = array_key_exists($responsiveOpen, $mobileOnlyClasses) ? $responsiveOpen : 'lg';
    $mobileOnlyClass = $mobileOnlyClasses[$responsiveBreakpoint];
    $collapseAt ??= $responsiveBreakpoint;
@endphp

<div class="min-h-screen">
    {{-- Utilise sidebar-layout avec hasNavbar=true --}}
    <x-daisy::layout.sidebar-layout
        :title="$title"
        :theme="$theme"
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
        :drawerId="$drawerId"
        :responsiveOpen="$responsiveOpen"
        :end="$end"
        :menuIcon="$menuIcon"
        :iconPrefix="$iconPrefix"
        :fallbackIcon="$fallbackIcon"
        :container="$container"
        :hasNavbar="true"
        :showThemeController="$showThemeController"
        :themes="$themes"
        :themeLabel="$themeLabel"
    >
        @isset($sidebarFooter)
            <x-slot:sidebarFooter>{{ $sidebarFooter }}</x-slot:sidebarFooter>
        @endisset
        <x-slot:topbar>
            <x-daisy::ui.navigation.navbar :bg="$navbarBg" :text="$navbarText" :shadow="$navbarShadow" :fixed="false" :container="$navbarContainer" data-navbar-sidebar-topbar>
                <x-slot:start>
                    <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost {{ $mobileOnlyClass }}">
                        <x-daisy::ui.advanced.icon :name="$menuIcon" size="lg" />
                    </label>
                    {{ $navbarStart ?? ($brand ?? '') }}
                    @isset($navbarHeading)
                        <div class="ms-3 hidden min-w-0 max-w-xs flex-col justify-center leading-tight text-base-content sm:flex lg:max-w-md xl:max-w-xl [&>h1]:truncate [&>h1]:text-sm [&>h1]:font-semibold [&>h1]:leading-tight [&>p]:truncate [&>p]:text-xs [&>p]:leading-tight [&>p]:text-base-content/70" data-navbar-heading>
                            {{ $navbarHeading }}
                        </div>
                    @endisset
                </x-slot:start>
                <x-slot:center>
                    {{ $navbarCenter ?? ($nav ?? '') }}
                </x-slot:center>
                <x-slot:end>
                    @if($showThemeController)
                        <x-daisy::ui.advanced.theme-controller
                            variant="dropdown"
                            :themes="$themes"
                            :label="$themeLabel"
                            size="sm"
                        />
                    @endif
                    {{ $navbarEnd ?? ($actions ?? '') }}
                </x-slot:end>
            </x-daisy::ui.navigation.navbar>
        </x-slot:topbar>
        {{ $slot }}
    </x-daisy::layout.sidebar-layout>
</div>
