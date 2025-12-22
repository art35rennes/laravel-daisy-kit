@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'stepper';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Assistant" 
    category="navigation" 
    name="stepper"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Assistant" 
            subtitle="Assistant pas à pas avec navigation."
            jsModule="stepper"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="stepper">
        <x-slot:preview>
            @php
                $items = [
                    ['title' => 'Étape 1', 'content' => 'Contenu de l\'étape 1'],
                    ['title' => 'Étape 2', 'content' => 'Contenu de l\'étape 2'],
                    ['title' => 'Étape 3', 'content' => 'Contenu de l\'étape 3'],
                ];
            @endphp
            <x-daisy::ui.navigation.stepper :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.navigation.stepper :items="[
    ['title' => 'Étape 1', 'content' => 'Contenu de l\'étape 1'],
    ['title' => 'Étape 2', 'content' => 'Contenu de l\'étape 2'],
    ['title' => 'Étape 3', 'content' => 'Contenu de l\'étape 3'],
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
