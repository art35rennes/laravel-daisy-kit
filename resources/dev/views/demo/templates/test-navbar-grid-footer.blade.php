<x-daisy::layout.app title="Navbar Grid Footer Template" :container="false">
    {{-- Navbar --}}
    <x-daisy::ui.navigation.navbar 
        bg="base-100" 
        shadow="sm" 
        :fixed="true" 
        fixedPosition="top"
    >
        <x-slot:start>
            <a class="btn btn-ghost text-xl">DaisyKit</a>
        </x-slot:start>
        <x-slot:center>
            <x-daisy::ui.navigation.menu :vertical="false" class="px-1">
                <li><a>Accueil</a></li>
                <li><a>À propos</a></li>
                <li><a>Contact</a></li>
            </x-daisy::ui.navigation.menu>
        </x-slot:center>
        <x-slot:end>
            <button class="btn btn-primary">Connexion</button>
        </x-slot:end>
    </x-daisy::ui.navigation.navbar>

    {{-- Main content with grid --}}
    <main class="container mx-auto p-6 pt-24 min-h-screen">
        <x-daisy::ui.layout.grid-layout :gap="4" align="start">
            <div class="col-12">
                <h1 class="text-3xl font-bold mb-4">Navbar Grid Footer Template</h1>
                <p class="text-base-content/70 mb-6">
                    Template complet avec navbar, grille responsive et footer, solution tout-en-un pour pages structurées.
                </p>
            </div>
            
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title">Colonne 1</h2>
                        <p>col-12 col-md-6 col-lg-4</p>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title">Colonne 2</h2>
                        <p>col-12 col-md-6 col-lg-4</p>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title">Colonne 3</h2>
                        <p>col-12 col-md-6 col-lg-4</p>
                    </div>
                </div>
            </div>
        </x-daisy::ui.layout.grid-layout>
    </main>

    {{-- Footer --}}
    <x-daisy::ui.layout.footer-layout
        bg="base-200"
        text="base-content"
        padding="p-10"
        :center="false"
        :horizontal="false"
        :showDivider="true"
    >
        <x-slot:columns>
            <nav>
                <h6 class="footer-title">Navigation</h6>
                <a class="link link-hover">Accueil</a>
                <a class="link link-hover">À propos</a>
                <a class="link link-hover">Contact</a>
            </nav>
            <nav>
                <h6 class="footer-title">Support</h6>
                <a class="link link-hover">Documentation</a>
                <a class="link link-hover">FAQ</a>
                <a class="link link-hover">Support</a>
            </nav>
        </x-slot:columns>
        <x-slot:copyright>
            <p>© {{ date('Y') }} DaisyKit. Tous droits réservés.</p>
        </x-slot:copyright>
    </x-daisy::ui.layout.footer-layout>
</x-daisy::layout.app>
