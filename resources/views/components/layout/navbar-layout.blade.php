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
            @isset($navbarHeading)
                <div class="ms-3 hidden min-w-0 max-w-xs flex-col justify-center leading-tight text-base-content sm:flex lg:max-w-md xl:max-w-xl [&>h1]:truncate [&>h1]:text-sm [&>h1]:font-semibold [&>h1]:leading-tight [&>p]:truncate [&>p]:text-xs [&>p]:leading-tight [&>p]:text-base-content/70" data-navbar-heading>
                    {{ $navbarHeading }}
                </div>
            @endisset
        </x-slot:start>
        <x-slot:center>
            {{ $navbarCenter ?? ($nav ?? null) }}
        </x-slot:center>
        <x-slot:end>
            <x-daisy::ui.advanced.theme-controller 
                variant="dropdown" 
                :themes="['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']"
                label="Theme"
                size="sm"
            />
            {{ $navbarEnd ?? ($actions ?? null) }}
        </x-slot:end>
    </x-daisy::ui.navigation.navbar>

    <main class="{{ $container }} pt-24">
        {{ $slot }}
    </main>

</x-daisy::layout.app>
