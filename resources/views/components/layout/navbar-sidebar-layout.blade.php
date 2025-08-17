@props([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    // Sidebar options (héritées de sidebar-layout)
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
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
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div class="min-h-screen">
        {{-- Navbar en haut --}}
        <x-daisy::ui.navbar :bg="$navbarBg" :text="$navbarText" :shadow="$navbarShadow" :fixed="false">
            <x-slot:start>
                <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                    <x-daisy::ui.icon :name="$menuIcon" size="lg" />
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

        {{-- Utilise sidebar-layout avec hasNavbar=true --}}
        <x-daisy::layout.sidebar-layout 
            :title="$title" 
            :theme="$theme"
            :variant="$variant"
            :sideClass="$sideClass"
            :stickyAt="$stickyAt"
            :brand="$brand"
            :brandHref="$brandHref"
            :showBrand="$showBrand"
            :sections="$sections"
            :drawerId="$drawerId"
            :responsiveOpen="$responsiveOpen"
            :end="$end"
            :menuIcon="$menuIcon"
            :iconPrefix="$iconPrefix"
            :container="$container"
            :hasNavbar="true"
        >
            {{ $slot }}
        </x-daisy::layout.sidebar-layout>
    </div>
</x-daisy::layout.app>


