@props([
    'title' => null,
    'theme' => null,
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
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
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div {{ $attributes->merge(['class' => 'min-h-screen']) }}>
        <x-daisy::ui.overlay.drawer :id="$drawerId" :end="$end" :responsiveOpen="$responsiveOpen" :sideIsMenu="false" sideClass="w-auto" class="">
            <x-slot:content>
                @if(!$hasNavbar)
                    <div class="bg-base-100 px-4 h-14 flex items-center justify-between lg:justify-end">
                        <div class="flex items-center gap-2 lg:hidden">
                            <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost">
                                <x-daisy::ui.advanced.icon :name="$menuIcon" size="lg" />
                            </label>
                            @if($title)
                                <div class="font-semibold">{{ __($title) }}</div>
                            @endif
                        </div>
                        <div class="hidden lg:flex items-center gap-2">
                            {{ $topbarRight ?? '' }}
                        </div>
                    </div>
                @endif
                <div class="{{ $container }} {{ $hasNavbar ? 'pt-16' : '' }}">
                    {{ $slot }}
                </div>
            </x-slot:content>
            <x-slot:side>
                <x-daisy::ui.navigation.sidebar 
                    :variant="$variant" 
                    :sideClass="$sideClass" 
                    :stickyAt="$stickyAt" 
                    :brand="$brand" 
                    :brandHref="$brandHref" 
                    :showBrand="$showBrand" 
                    :sections="$sections" 
                    :iconPrefix="$iconPrefix" 
                    class="h-full {{ $hasNavbar ? 'lg:h-[calc(100vh-4rem)]' : '' }}"
                />
            </x-slot:side>
        </x-daisy::ui.overlay.drawer>
    </div>
</x-daisy::layout.app>


