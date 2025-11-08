<div class="space-y-4">
    <div class="font-semibold">Grid Layout</div>

    <!-- 1) Grille simple, fallback responsive -->
    <div class="card bg-base-100">
        <div class="card-body">
            <div class="mb-2 text-sm opacity-70">Grille simple (col-sm-12 col-xl-4)</div>
            <x-daisy::ui.layout.grid-layout gap="6" align="start">
                <div class="col-sm-12 col-xl-4"><div class="p-4 bg-base-200 rounded">Col 1</div></div>
                <div class="col-sm-12 col-xl-4"><div class="p-4 bg-base-200 rounded">Col 2</div></div>
                <div class="col-sm-12 col-xl-4"><div class="p-4 bg-base-200 rounded">Col 3</div></div>
            </x-daisy::ui.layout.grid-layout>
        </div>
    </div>

    <!-- 2) Largeurs variées, offsets -->
    <div class="card bg-base-100">
        <div class="card-body">
            <div class="mb-2 text-sm opacity-70">Offsets et largeurs variées</div>
            <x-daisy::ui.layout.grid-layout gap="4" align="center" class="bg-base-100">
                <div class="col-12 col-md-6 col-lg-3"><div class="p-4 bg-base-200 rounded">A</div></div>
                <div class="col-12 col-md-6 col-lg-6 offset-lg-3"><div class="p-4 bg-base-200 rounded">B (offset lg-3)</div></div>
                <div class="col-12 col-md-12 col-lg-3"><div class="p-4 bg-base-200 rounded">C</div></div>
            </x-daisy::ui.layout.grid-layout>
        </div>
    </div>

    <!-- 3) Layout magazine -->
    <div class="card bg-base-100">
        <div class="card-body">
            <div class="mb-2 text-sm opacity-70">Layout “magazine”</div>
            <x-daisy::ui.layout.grid-layout gap="3">
                <div class="col-12 col-md-8"><div class="p-4 bg-base-200 rounded">Contenu principal</div></div>
                <div class="col-12 col-md-4"><div class="p-4 bg-base-200 rounded">Sidebar</div></div>
                <div class="col-12 col-md-4"><div class="p-4 bg-base-200 rounded">Bloc A</div></div>
                <div class="col-12 col-md-4"><div class="p-4 bg-base-200 rounded">Bloc B</div></div>
                <div class="col-12 col-md-4"><div class="p-4 bg-base-200 rounded">Bloc C</div></div>
            </x-daisy::ui.layout.grid-layout>
        </div>
    </div>

    <!-- 4) DaisyUI inside -->
    <div class="card bg-base-100">
        <div class="card-body">
            <div class="mb-2 text-sm opacity-70">Composants DaisyUI dans la grille</div>
            <x-daisy::ui.layout.grid-layout gap="5" align="start">
                <div class="col-12 col-lg-6">
                    <x-daisy::ui.layout.card title="Carte 1">
                        <div>Contenu</div>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-daisy::ui.layout.card title="Carte 2">
                        <div>Contenu</div>
                    </x-daisy::ui.layout.card>
                </div>
            </x-daisy::ui.layout.grid-layout>
        </div>
    </div>

    <!-- 5) Exemple visuel (cartes avec images & badges) -->
    <div class="card bg-base-100">
        <div class="card-body">
            <div class="mb-2 text-sm opacity-70">Exemple visuel: cartes produits (images, badges, boutons)</div>
            <x-daisy::ui.layout.grid-layout gap="4" class="items-stretch">
                <div class="col-12 col-sm-6 col-lg-4">
                    <x-daisy::ui.layout.card title="Strawberry" imageUrl="/img/food/dummy-600x450-Strawberry.jpg" imageAlt="Strawberry">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-primary badge-sm">Food</span>
                            <span class="badge badge-ghost badge-sm">New</span>
                        </div>
                        <x-slot:actions>
                            <button class="btn btn-primary btn-sm">Commander</button>
                        </x-slot:actions>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <x-daisy::ui.layout.card title="Bottle" imageUrl="/img/object/dummy-600x450-Bottle.jpg" imageAlt="Bottle">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-accent badge-sm">Object</span>
                            <span class="badge badge-ghost badge-sm">Promo</span>
                        </div>
                        <x-slot:actions>
                            <button class="btn btn-secondary btn-sm">Détails</button>
                        </x-slot:actions>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <x-daisy::ui.layout.card title="Chip" imageUrl="/img/business/dummy-600x450-Chip.jpg" imageAlt="Chip">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-info badge-sm">Business</span>
                            <span class="badge badge-ghost badge-sm">Hot</span>
                        </div>
                        <x-slot:actions>
                            <button class="btn btn-outline btn-sm">Ajouter</button>
                        </x-slot:actions>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-md-8">
                    <x-daisy::ui.layout.card title="Windflower" imageUrl="/img/object/dummy-600x450-Windflower.jpg" imageAlt="Windflower">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-success badge-sm">Feature</span>
                            <span class="badge badge-ghost badge-sm">LG 8/12</span>
                        </div>
                        <x-slot:actions>
                            <button class="btn btn-primary btn-sm">En savoir plus</button>
                        </x-slot:actions>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-md-4">
                    <x-daisy::ui.layout.card title="Rosa" imageUrl="/img/people/dummy-600x450-Rosa.jpg" imageAlt="Rosa">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-warning badge-sm">Portrait</span>
                            <span class="badge badge-ghost badge-sm">MD 4/12</span>
                        </div>
                        <x-slot:actions>
                            <button class="btn btn-ghost btn-sm">Voir</button>
                        </x-slot:actions>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-sm-6 col-lg-6 offset-lg-3">
                    <x-daisy::ui.layout.card title="Utrecht" imageUrl="/img/divers/dummy-576x1024-Utrecht.jpg" imageAlt="Utrecht" :imageFull="false">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-neutral badge-sm">Divers</span>
                            <span class="badge badge-ghost badge-sm">Offset lg-3</span>
                        </div>
                        <x-slot:actions>
                            <button class="btn btn-outline btn-sm">Explorer</button>
                        </x-slot:actions>
                    </x-daisy::ui.layout.card>
                </div>
            </x-daisy::ui.layout.grid-layout>
        </div>
    </div>
</div>


