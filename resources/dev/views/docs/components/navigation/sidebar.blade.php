@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'sidebar';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Barre latérale" 
    category="navigation" 
    name="sidebar"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Barre latérale" 
            subtitle="Barre latérale de navigation."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="sidebar">
        <x-slot:preview>
            @php
                $items = [
                    [
                        'label' => 'Navigation',
                        'items' => [
                            ['label' => 'Dashboard', 'href' => '#', 'icon' => 'house'],
                            ['label' => 'Utilisateurs', 'href' => '#', 'icon' => 'people'],
                            ['label' => 'Paramètres', 'href' => '#', 'icon' => 'gear'],
                        ],
                    ],
                ];
            @endphp
            <div class="h-64 border border-base-300 rounded-box overflow-hidden">
                <x-daisy::ui.navigation.sidebar :items="$items" brand="Mon App" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$items = [
    [
        "label" => "Navigation",
        "items" => [
            ["label" => "Dashboard", "href" => "#", "icon" => "house"],
            ["label" => "Utilisateurs", "href" => "#", "icon" => "people"],
            ["label" => "Paramètres", "href" => "#", "icon" => "gear"]
        ]
    ]
];
@endphp
<x-daisy::ui.navigation.sidebar :items="$items" brand="Mon App" />';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$baseCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="sidebar">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Variants de largeur</p>
                    @php
                        $items = [
                            [
                                'label' => 'Menu',
                                'items' => [
                                    ['label' => 'Item 1', 'href' => '#', 'icon' => 'house'],
                                ],
                            ],
                        ];
                    @endphp
                    <div class="space-y-2">
                        <div class="h-32 border border-base-300 rounded-box overflow-hidden">
                            <x-daisy::ui.navigation.sidebar :items="$items" variant="slim" brand="Slim" />
                        </div>
                        <div class="h-32 border border-base-300 rounded-box overflow-hidden">
                            <x-daisy::ui.navigation.sidebar :items="$items" variant="wide" brand="Wide" />
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '@php
$items = [
    [
        "label" => "Menu",
        "items" => [
            ["label" => "Item 1", "href" => "#", "icon" => "house"]
        ]
    ]
];
@endphp

{{-- Slim (icônes uniquement) --}}
<x-daisy::ui.navigation.sidebar :items="$items" variant="slim" />

{{-- Wide (largeur fixe) --}}
<x-daisy::ui.navigation.sidebar :items="$items" variant="wide" />';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$variantsCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
