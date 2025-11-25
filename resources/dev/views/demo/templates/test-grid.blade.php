<x-daisy::layout.app title="Grid Template" :container="false">
    <div class="container mx-auto p-6">
        <x-daisy::ui.layout.grid-layout :gap="4" align="start">
            <div class="col-12">
                <h1 class="text-3xl font-bold mb-4">Grid Template</h1>
                <p class="text-base-content/70 mb-6">
                    Template avec grille 12 colonnes responsive (classes Bootstrap-like) pour structurer le contenu.
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
            
            <div class="col-12 col-lg-8">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title">Contenu principal</h2>
                        <p>col-12 col-lg-8</p>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-4">
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h2 class="card-title">Sidebar</h2>
                        <p>col-12 col-lg-4</p>
                    </div>
                </div>
            </div>
        </x-daisy::ui.layout.grid-layout>
    </div>
</x-daisy::layout.app>

