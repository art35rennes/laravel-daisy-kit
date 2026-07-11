@props([
    'title' => null,
    'theme' => null,
    // Sidebar options
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
    // Responsive drawer behavior
    'drawerId' => 'layout-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false, // sidebar on right
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    // Content container
    'container' => 'p-6',
    // Layout options
    'hasNavbar' => false,
    'showThemeController' => true,
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
    'themeLabel' => 'Theme',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div {{ $attributes->merge(['class' => 'min-h-screen']) }}>
        <x-daisy::ui.overlay.drawer :id="$drawerId" :end="$end" :responsiveOpen="$responsiveOpen" :sideIsMenu="false" sideClass="w-auto" class="">
            <x-slot:content>
                @if(!$hasNavbar)
                    <div class="bg-base-100 px-4 h-14 flex items-center justify-between gap-4 lg:justify-end">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex items-center gap-2 lg:hidden">
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
                        <div class="hidden shrink-0 lg:flex items-center gap-2">
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
                <div class="{{ $container }} {{ $hasNavbar && ! isset($topbar) ? 'pt-16' : '' }}">
                    {{ $slot }}
                </div>
            </x-slot:content>
            <x-slot:side>
                <x-daisy::ui.navigation.sidebar 
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
                    :iconPrefix="$iconPrefix" 
                    class="h-full {{ $hasNavbar ? 'lg:h-[calc(100vh-4rem)]' : '' }}"
                >
                    @isset($sidebarFooter)
                        <x-slot:footer>{{ $sidebarFooter }}</x-slot:footer>
                    @endisset
                </x-daisy::ui.navigation.sidebar>
            </x-slot:side>
        </x-daisy::ui.overlay.drawer>
    </div>
</x-daisy::layout.app>
