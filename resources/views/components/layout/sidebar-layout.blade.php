@props([
    'title' => null,
    'theme' => null,
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
    'sections' => [],
    // Responsive drawer behavior
    'drawerId' => 'layout-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false, // sidebar on right
    // Content container
    'container' => 'p-6',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div class="min-h-screen">
        <x-daisy::ui.drawer :id="$drawerId" :end="$end" :responsiveOpen="$responsiveOpen" :sideIsMenu="false" sideClass="w-auto" class="">
            <x-slot:content>
                <div class="bg-base-100 border-b border-base-content/10 px-4 h-14 flex items-center justify-between lg:justify-end">
                    <div class="flex items-center gap-2 lg:hidden">
                        <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost">
                            <x-bi-list class="h-6 w-6" />
                        </label>
                        @if($title)
                            <div class="font-semibold">{{ __($title) }}</div>
                        @endif
                    </div>
                    <div class="hidden lg:flex items-center gap-2">
                        {{ $topbarRight ?? '' }}
                    </div>
                </div>
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


