@props([
    'title' => null,
    'theme' => null,
    // Couleurs et styles de la navbar
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Classe container du contenu principal
    'container' => 'container mx-auto p-6',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <x-daisy::ui.navigation.navbar :bg="$navbarBg" :text="$navbarText" :shadow="$navbarShadow" :fixed="$navbarFixed" :fixedPosition="$navbarFixedPosition">
        <x-slot:start>
            {{ $navbarStart ?? ($brand ?? null) }}
        </x-slot:start>
        <x-slot:center>
            {{ $navbarCenter ?? ($nav ?? null) }}
        </x-slot:center>
        <x-slot:end>
            {{ $navbarEnd ?? ($actions ?? null) }}
        </x-slot:end>
    </x-daisy::ui.navigation.navbar>

    <main class="{{ $container }} pt-24">
        {{ $slot }}
    </main>
</x-daisy::layout.app>


