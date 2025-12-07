@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'changelog';
    $name = 'changelog-version-item';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Changelog Version Item" 
    category="changelog" 
    name="changelog-version-item"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Changelog Version Item" 
            subtitle="Élément de version dans un changelog avec date et liste de changements."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="changelog-version-item">
        <x-slot:preview>
            @php
                $items = [
                    ['type' => 'added', 'description' => 'Nouvelle fonctionnalité de recherche'],
                    ['type' => 'fixed', 'description' => 'Correction d\'un bug dans le formulaire'],
                    ['type' => 'changed', 'description' => 'Amélioration des performances de chargement'],
                ];
            @endphp
            <x-daisy::ui.changelog.changelog-version-item 
                version="1.0.0" 
                date="2024-01-15" 
                :items="$items" 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
@php
$items = [
    ["type" => "added", "description" => "Nouvelle fonctionnalité de recherche"],
    ["type" => "fixed", "description" => "Correction d'un bug dans le formulaire"],
    ["type" => "changed", "description" => "Amélioration des performances de chargement"]
];
@endphp
<x-daisy::ui.changelog.changelog-version-item 
    version="1.0.0" 
    date="2024-01-15" 
    :items="$items" 
/>
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
