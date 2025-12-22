@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'table-of-contents';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Table des matières" 
    category="navigation" 
    name="table-of-contents"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Table des matières" 
            subtitle="Navigation automatique vers les sections d'une page."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="table-of-contents">
        <x-slot:preview>
            @php
                $sections = [
                    ['id' => 'intro', 'label' => 'Introduction'],
                    ['id' => 'base', 'label' => 'Exemple de base'],
                    ['id' => 'variants', 'label' => 'Variantes'],
                    ['id' => 'api', 'label' => 'API'],
                ];
            @endphp
            <x-daisy::ui.navigation.table-of-contents :sections="$sections" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.navigation.table-of-contents :sections="[
    ['id' => 'intro', 'label' => 'Introduction'],
    ['id' => 'base', 'label' => 'Exemple de base'],
    ['id' => 'variants', 'label' => 'Variantes'],
    ['id' => 'api', 'label' => 'API'],
]" />
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
                height="200px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
