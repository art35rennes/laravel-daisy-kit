@props([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Content container
    'container' => 'container mx-auto p-6',
])

<x-daisy::layout.navbar-layout
    :title="$title"
    :theme="$theme"
    :navbarBg="$navbarBg"
    :navbarText="$navbarText"
    :navbarShadow="$navbarShadow"
    :navbarFixed="$navbarFixed"
    :navbarFixedPosition="$navbarFixedPosition"
    :container="$container"
>
    {{ $slot }}
</x-daisy::layout.navbar-layout>


