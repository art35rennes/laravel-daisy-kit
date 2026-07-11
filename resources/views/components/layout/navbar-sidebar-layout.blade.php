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
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
    'brandUrl' => null,
    'brandCollapsed' => null,
    'showBrand' => true,
    'sections' => [],
    'drawerId' => 'layout-nav-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false,
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    // Content container
    'container' => 'p-6',
    'showThemeController' => true,
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
    'themeLabel' => 'Theme',
])

<div class="min-h-screen">
    {{-- Utilise sidebar-layout avec hasNavbar=true --}}
    <x-daisy::layout.sidebar-layout
        :title="$title"
        :theme="$theme"
        :variant="$variant"
        :sideClass="$sideClass"
        :expandedWidth="$expandedWidth"
        :collapsedWidth="$collapsedWidth"
        :stickyAt="$stickyAt"
        :brand="$brand"
        :brandHref="$brandHref"
        :brandUrl="$brandUrl"
        :brandCollapsed="$brandCollapsed"
        :showBrand="$showBrand"
        :sections="$sections"
        :drawerId="$drawerId"
        :responsiveOpen="$responsiveOpen"
        :end="$end"
        :menuIcon="$menuIcon"
        :iconPrefix="$iconPrefix"
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
                    <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
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
