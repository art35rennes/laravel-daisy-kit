<!-- Navbar -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Navbar</h2>
    <div class="space-y-4">
        <!-- Simple titre -->
        <x-daisy::ui.navbar shadow="sm">
            <x-slot:start>
                <a class="btn btn-ghost text-xl">DaisyKit</a>
            </x-slot:start>
        </x-daisy::ui.navbar>

        <!-- Titre + icône fin -->
        <x-daisy::ui.navbar shadow="sm">
            <x-slot:start>
                <a class="btn btn-ghost text-xl">DaisyKit</a>
            </x-slot:start>
            <x-slot:end>
                <button class="btn btn-square btn-ghost">
                    <x-heroicon-o-ellipsis-horizontal class="h-5 w-5" />
                </button>
            </x-slot:end>
        </x-daisy::ui.navbar>

        <!-- Icônes start/end + menu center responsive -->
        <x-daisy::ui.navbar bg="base-100" shadow="sm" centerHiddenBelow="lg">
            <x-slot:start>
                <button class="btn btn-square btn-ghost">
                    <x-heroicon-o-bars-3 class="h-5 w-5" />
                </button>
                <a class="btn btn-ghost text-xl">DaisyKit</a>
            </x-slot:start>
            <x-slot:center>
                <x-daisy::ui.menu :vertical="false" class="px-1">
                    <li><a>Item 1</a></li>
                    <li>
                        <details>
                            <summary>Parent</summary>
                            <ul class="p-2">
                                <li><a>Submenu 1</a></li>
                                <li><a>Submenu 2</a></li>
                            </ul>
                        </details>
                    </li>
                    <li><a>Item 3</a></li>
                </x-daisy::ui.menu>
            </x-slot:center>
            <x-slot:end>
                <x-daisy::ui.button>Button</x-daisy::ui.button>
            </x-slot:end>
        </x-daisy::ui.navbar>

        <!-- Couleurs -->
        <x-daisy::ui.navbar bg="neutral" text="neutral-content">
            <x-slot:start>
                <button class="btn btn-ghost text-xl">DaisyKit</button>
            </x-slot:start>
        </x-daisy::ui.navbar>
        <x-daisy::ui.navbar bg="base-300">
            <x-slot:start>
                <button class="btn btn-ghost text-xl">DaisyKit</button>
            </x-slot:start>
        </x-daisy::ui.navbar>
        <x-daisy::ui.navbar bg="primary" text="primary-content">
            <x-slot:start>
                <button class="btn btn-ghost text-xl">DaisyKit</button>
            </x-slot:start>
        </x-daisy::ui.navbar>
    </div>
</section>


