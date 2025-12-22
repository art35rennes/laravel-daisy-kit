@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'scrollspy';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Navigation au défilement" 
    category="advanced" 
    name="scrollspy"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Navigation au défilement" 
            subtitle="Navigation automatique qui met à jour l'élément actif selon la position de défilement."
            jsModule="scrollspy"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="scrollspy">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Section 1', 'href' => '#section1'],
                    ['label' => 'Section 2', 'href' => '#section2'],
                    ['label' => 'Section 3', 'href' => '#section3'],
                ];
            @endphp
            <div class="w-64">
                <x-daisy::ui.advanced.scrollspy :items="$items" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.advanced.scrollspy :items="[
    ['label' => 'Section 1', 'href' => '#section1'],
    ['label' => 'Section 2', 'href' => '#section2'],
    ['label' => 'Section 3', 'href' => '#section3'],
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
