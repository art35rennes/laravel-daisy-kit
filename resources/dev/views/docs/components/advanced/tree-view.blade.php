@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'tree-view';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Vue arborescente" 
    category="advanced" 
    name="tree-view"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Vue arborescente" 
            subtitle="Vue arborescente hiérarchique pour afficher des structures de données imbriquées."
            jsModule="treeview"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="tree-view">
        <x-slot:preview>
            @php
                $data = [
                    [
                        'id' => 1, 
                        'label' => 'Dossier racine', 
                        'children' => [
                            [
                                'id' => 2, 
                                'label' => 'Sous-dossier',
                                'children' => [
                                    ['id' => 3, 'label' => 'Fichier 1'],
                                    ['id' => 4, 'label' => 'Fichier 2']
                                ]
                            ],
                            ['id' => 5, 'label' => 'Fichier 3']
                        ]
                    ]
                ];
            @endphp
            <x-daisy::ui.advanced.tree-view :data="$data" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
@php
$data = [
    [
        "id" => 1, 
        "label" => "Dossier racine", 
        "children" => [
            [
                "id" => 2, 
                "label" => "Sous-dossier",
                "children" => [
                    ["id" => 3, "label" => "Fichier 1"],
                    ["id" => 4, "label" => "Fichier 2"]
                ]
            ],
            ["id" => 5, "label" => "Fichier 3"]
        ]
    ]
];
@endphp
<x-daisy::ui.advanced.tree-view :data="$data" />
CODE;
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
