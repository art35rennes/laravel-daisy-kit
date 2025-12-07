@php
    $groups = [
        [
            'items' => [
                ['icon' => 'pencil', 'label' => 'Edit', 'active' => false, 'onclick' => "alert('Edit mode')"],
                ['icon' => 'eye', 'label' => 'Preview', 'active' => true, 'onclick' => "alert('Preview mode')"],
                ['icon' => 'code-slash', 'label' => 'Code', 'active' => false, 'onclick' => "alert('Code view')"],
            ]
        ],
        [
            'items' => [
                ['icon' => 'phone', 'label' => 'Mobile', 'active' => false, 'onclick' => "alert('Mobile view')"],
                ['icon' => 'tablet', 'label' => 'Tablet', 'active' => false, 'onclick' => "alert('Tablet view')"],
                ['icon' => 'laptop', 'label' => 'Desktop', 'active' => false, 'onclick' => "alert('Desktop view')"],
            ]
        ],
        [
            'items' => [
                ['icon' => 'sun', 'label' => 'Light', 'active' => false, 'onclick' => "document.documentElement.setAttribute('data-theme', 'light')"],
                ['icon' => 'moon', 'label' => 'Dark', 'active' => false, 'onclick' => "document.documentElement.setAttribute('data-theme', 'dark')"],
            ]
        ],
    ];
@endphp

<x-daisy::layout.navbar-layout title="Floating Menu Example">
    <x-slot:brand>
        <a class="btn btn-ghost text-xl">DaisyKit</a>
    </x-slot:brand>
    <x-slot:nav>
        <x-daisy::ui.navigation.menu :vertical="false" class="px-1">
            <li><a>Overview</a></li>
            <li><a>Components</a></li>
            <li><a>Templates</a></li>
        </x-daisy::ui.navigation.menu>
    </x-slot:nav>
    <x-slot:actions>
        <button class="btn btn-ghost btn-circle">
            <x-daisy::ui.advanced.icon name="bell" size="lg" />
        </button>
    </x-slot:actions>

    {{-- Floating Menu --}}
    <x-daisy::ui.navigation.floating-menu 
        position="left"
        :groups="$groups"
    />

    <div class="space-y-6">
        <div class="hero bg-base-200 rounded-box">
            <div class="hero-content text-center">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">Floating Menu</h1>
                    <p class="py-6">
                        Exemple d'utilisation du composant floating menu avec des groupes de boutons.
                        Le menu est positionné à gauche de l'écran.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Card 1</h2>
                    <p>Contenu de la première carte.</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Card 2</h2>
                    <p>Contenu de la deuxième carte.</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Card 3</h2>
                    <p>Contenu de la troisième carte.</p>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <x-daisy::ui.advanced.icon name="info-circle" size="lg" />
            <span>
                Cliquez sur les boutons du menu flottant à gauche pour tester les interactions.
                Les boutons peuvent être configurés avec des actions JavaScript via l'attribut <code>onclick</code>.
            </span>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Configuration</h2>
                <div class="space-y-2 text-sm">
                    <p><strong>Position :</strong> <code>left</code> (peut être <code>right</code>, <code>top</code>, <code>bottom</code>)</p>
                    <p><strong>Orientation :</strong> <code>vertical</code> (par défaut, ou <code>horizontal</code> pour top/bottom)</p>
                    <p><strong>Groupes :</strong> 3 groupes séparés visuellement</p>
                    <p><strong>Boutons actifs :</strong> Le bouton "Preview" est actif par défaut</p>
                </div>
            </div>
        </div>
    </div>
</x-daisy::layout.navbar-layout>

