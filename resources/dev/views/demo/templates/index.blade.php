<x-daisy::layout.app title="Templates" :container="true">
    <div class="max-w-4xl mx-auto py-8">
        <!-- En-tête simple -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-2">Templates</h1>
            <p class="text-base-content/70">Choisissez un layout pour votre application</p>
        </div>

        <!-- Navigation simple -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-daisy::ui.button 
                variant="outline" 
                color="primary"
                size="lg"
                href="{{ route('layouts.navbar') }}"
                tag="a"
                class="h-20 text-lg"
            >
                <x-daisy::ui.icon name="list" size="lg" class="mr-3" />
                Navbar
            </x-daisy::ui.button>

            <x-daisy::ui.button 
                variant="outline" 
                color="success"
                size="lg"
                href="{{ route('layouts.sidebar') }}"
                tag="a"
                class="h-20 text-lg"
            >
                <x-daisy::ui.icon name="layers" size="lg" class="mr-3" />
                Sidebar
            </x-daisy::ui.button>

            <x-daisy::ui.button 
                variant="outline" 
                color="warning"
                size="lg"
                href="{{ route('layouts.navbar-sidebar') }}"
                tag="a"
                class="h-20 text-lg"
            >
                <x-daisy::ui.icon name="grid-3x3" size="lg" class="mr-3" />
                Navbar + Sidebar
            </x-daisy::ui.button>

            <x-daisy::ui.button 
                variant="outline" 
                color="accent"
                size="lg"
                href="{{ route('demo') }}"
                tag="a"
                class="h-20 text-lg"
            >
                <x-daisy::ui.icon name="puzzle" size="lg" class="mr-3" />
                Tous les composants
            </x-daisy::ui.button>
        </div>

        <!-- Description des templates -->
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Navbar Layout</h3>
                    <p class="text-sm">Barre de navigation en haut de page avec menu horizontal et actions.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('layouts.navbar') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Sidebar Layout</h3>
                    <p class="text-sm">Barre latérale de navigation avec menu vertical et sous-menus.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('layouts.sidebar') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Navbar + Sidebar</h3>
                    <p class="text-sm">Combinaison navbar et sidebar pour applications complexes.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('layouts.navbar-sidebar') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-daisy::layout.app>
