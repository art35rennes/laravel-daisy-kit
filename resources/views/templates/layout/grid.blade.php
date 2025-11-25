@props([
    'title' => null,
    'theme' => null,
    // Grid options
    'gap' => 4,
    'align' => 'start', // start|center|end
    'container' => true,
    'containerClass' => 'container mx-auto p-6',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    @if($container)
        <div class="{{ $containerClass }}">
            <x-daisy::ui.layout.grid-layout :gap="$gap" :align="$align">
                {{ $slot }}
            </x-daisy::ui.layout.grid-layout>
        </div>
    @else
        <x-daisy::ui.layout.grid-layout :gap="$gap" :align="$align">
            {{ $slot }}
        </x-daisy::ui.layout.grid-layout>
    @endif
</x-daisy::layout.app>


