@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'sidebar-navigation';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Sidebar Navigation" 
    category="navigation" 
    name="sidebar-navigation"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Sidebar Navigation" 
            subtitle="Navigation latérale avec support des menus hiérarchiques et état actif."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="sidebar-navigation">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Introduction', 'href' => '/docs'],
                    [
                        'label' => 'Composants', 
                        'children' => [
                            ['label' => 'Boutons', 'href' => '/docs/inputs/button'],
                            ['label' => 'Formulaires', 'href' => '/docs/inputs/input'],
                        ]
                    ],
                    ['label' => 'Templates', 'href' => '/docs/templates'],
                ];
            @endphp
            <div class="w-64">
                <x-daisy::ui.navigation.sidebar-navigation :items="$items" current="/docs/inputs/button" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.navigation.sidebar-navigation :items="[
    ['label' => 'Introduction', 'href' => '/docs'],
    [
        'label' => 'Composants',
        'children' => [
            ['label' => 'Boutons', 'href' => '/docs/inputs/button'],
            ['label' => 'Formulaires', 'href' => '/docs/inputs/input'],
        ],
    ],
    ['label' => 'Templates', 'href' => '/docs/templates'],
]" current="/docs/inputs/button" />
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
