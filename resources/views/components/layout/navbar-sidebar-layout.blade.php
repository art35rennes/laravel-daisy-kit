@props([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
    'sections' => [],
    'drawerId' => 'layout-nav-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false,
    // Content container
    'container' => 'p-6',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div class="min-h-screen">
        <x-daisy::ui.navbar :bg="$navbarBg" :text="$navbarText" :shadow="$navbarShadow" :fixed="false">
            <x-slot:start>
                <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                    <x-heroicon-o-bars-3 class="h-6 w-6" />
                </label>
                {{ $brand ?? '' }}
            </x-slot:start>
            <x-slot:center>
                {{ $nav ?? '' }}
            </x-slot:center>
            <x-slot:end>
                {{ $actions ?? '' }}
            </x-slot:end>
        </x-daisy::ui.navbar>

        <x-daisy::ui.drawer :id="$drawerId" :end="$end" :responsiveOpen="$responsiveOpen" class="pt-14">
            <x-slot:content>
                <div class="{{ $container }}">
                    {{ $slot }}
                </div>
            </x-slot:content>
            <x-slot:side>
                <x-daisy::ui.sidebar :variant="$variant" :sideClass="$sideClass" :stickyAt="$stickyAt" :brand="$brand" :brandHref="$brandHref" :sections="$sections" class="min-h-full" />
            </x-slot:side>
        </x-daisy::ui.drawer>
    </div>
</x-daisy::layout.app>


