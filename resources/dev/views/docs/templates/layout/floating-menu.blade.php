@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Layout avec floating menu" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Layout avec floating menu</h1>
        <p class="text-base-content/70">Page avec un menu flottant positionné sur les bords de l'écran, organisé en groupes de boutons.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue de démo :</strong> <code>{{ "view('daisy::demo.templates.test-floating-menu')" }}</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>@php
$groups = [
    [
        'items' => [
            ['icon' => 'pencil', 'label' => 'Edit', 'active' => false],
            ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
            ['icon' => 'code-slash', 'label' => 'Code', 'active' => false],
        ]
    ],
    [
        'items' => [
            ['icon' => 'phone', 'label' => 'Mobile', 'active' => false],
            ['icon' => 'tablet', 'label' => 'Tablet', 'active' => false],
            ['icon' => 'laptop', 'label' => 'Desktop', 'active' => false],
        ]
    ],
    [
        'items' => [
            ['icon' => 'sun', 'label' => 'Light', 'active' => false],
            ['icon' => 'moon', 'label' => 'Dark', 'active' => false],
        ]
    ],
];
@endphp

<x-daisy::layout.navbar-layout title="Page avec Floating Menu">
    {{-- Floating Menu --}}
    <x-daisy::ui.navigation.floating-menu 
        position="left"
        :groups="$groups"
    />

    {{-- Contenu de la page --}}
    <div class="space-y-6">
        {{-- Votre contenu ici --}}
    </div>
</x-daisy::layout.navbar-layout></code></pre>
        </div>
    </section>

    <section id="usage" class="mt-10">
        <h2>Utilisation</h2>
        <p class="text-base-content/70 mb-4">
            Le composant <code>floating-menu</code> peut être utilisé dans n'importe quel layout pour ajouter un menu flottant avec des groupes de boutons.
        </p>
        
        <div class="alert alert-info mt-4">
            <x-daisy::ui.advanced.icon name="info-circle" size="lg" />
            <span>
                Le menu est positionné en <code>fixed</code> et reste visible lors du défilement de la page.
                Utilisez les props <code>position</code> et <code>orientation</code> pour contrôler son placement.
            </span>
        </div>

        <h3 class="mt-6 mb-3">Structure des groupes</h3>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>$groups = [
    [
        'items' => [
            [
                'icon' => 'pencil',      // Nom de l'icône (obligatoire)
                'label' => 'Edit',       // Label pour l'accessibilité
                'active' => false,       // État actif du bouton
                'href' => '#',           // URL (optionnel, crée un lien)
                'onclick' => '...',      // Action JavaScript (optionnel)
            ],
            // ... autres boutons
        ]
    ],
    // ... autres groupes
];</code></pre>
        </div>

        <h3 class="mt-6 mb-3">Positions disponibles</h3>
        <ul class="list-disc list-inside space-y-1 text-sm">
            <li><code>left</code> - Menu vertical à gauche (par défaut)</li>
            <li><code>right</code> - Menu vertical à droite</li>
            <li><code>top</code> - Menu horizontal en haut (nécessite <code>orientation="horizontal"</code>)</li>
            <li><code>bottom</code> - Menu horizontal en bas (nécessite <code>orientation="horizontal"</code>)</li>
        </ul>
    </section>

    <section id="api" class="mt-10">
        <h2>Props du composant</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Type</th>
                        <th>Défaut</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>position</code></td>
                        <td>string</td>
                        <td><code>left</code></td>
                        <td>Position du menu : <code>left</code>, <code>right</code>, <code>top</code>, <code>bottom</code></td>
                    </tr>
                    <tr>
                        <td><code>orientation</code></td>
                        <td>string</td>
                        <td><code>vertical</code></td>
                        <td>Orientation des boutons : <code>vertical</code> ou <code>horizontal</code></td>
                    </tr>
                    <tr>
                        <td><code>buttonSize</code></td>
                        <td>string</td>
                        <td><code>md</code></td>
                        <td>Taille des boutons : <code>xs</code>, <code>sm</code>, <code>md</code>, <code>lg</code>, <code>xl</code></td>
                    </tr>
                    <tr>
                        <td><code>buttonVariant</code></td>
                        <td>string</td>
                        <td><code>ghost</code></td>
                        <td>Variant des boutons : <code>solid</code>, <code>outline</code>, <code>ghost</code>, <code>link</code>, <code>soft</code>, <code>dash</code></td>
                    </tr>
                    <tr>
                        <td><code>buttonColor</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Couleur des boutons : <code>primary</code>, <code>secondary</code>, <code>accent</code>, etc.</td>
                    </tr>
                    <tr>
                        <td><code>groups</code></td>
                        <td>array</td>
                        <td><code>[]</code></td>
                        <td>Tableau de groupes, chaque groupe contient un tableau <code>items</code></td>
                    </tr>
                    <tr>
                        <td><code>groupSpacing</code></td>
                        <td>float</td>
                        <td><code>1.5</code></td>
                        <td>Espacement entre les groupes (en rem)</td>
                    </tr>
                    <tr>
                        <td><code>itemSpacing</code></td>
                        <td>float</td>
                        <td><code>0.5</code></td>
                        <td>Espacement entre les boutons dans un groupe (en rem)</td>
                    </tr>
                    <tr>
                        <td><code>bg</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Afficher le fond du menu</td>
                    </tr>
                    <tr>
                        <td><code>rounded</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Bordure arrondie</td>
                    </tr>
                    <tr>
                        <td><code>shadow</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Ombre du menu</td>
                    </tr>
                    <tr>
                        <td><code>padding</code></td>
                        <td>string</td>
                        <td><code>p-2</code></td>
                        <td>Classes de padding du conteneur</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

