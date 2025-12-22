@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'accordion';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Accordion" 
    category="advanced" 
    name="accordion"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Accordion" 
            subtitle="Accordéon pour afficher/masquer du contenu."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="accordion">
        <x-slot:preview>
            @php
                $items = [
                    ['title' => 'Section 1', 'content' => 'Contenu de la première section'],
                    ['title' => 'Section 2', 'content' => 'Contenu de la deuxième section'],
                    ['title' => 'Section 3', 'content' => 'Contenu de la troisième section'],
                ];
            @endphp
            <x-daisy::ui.advanced.accordion :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.advanced.accordion :items="[
    ['title' => 'Section 1', 'content' => 'Contenu de la première section'],
    ['title' => 'Section 2', 'content' => 'Contenu de la deuxième section'],
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

    <x-daisy::docs.sections.variants name="accordion">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Avec icône flèche</p>
                    @php
                        $items = [
                            ['title' => 'Section 1', 'content' => 'Contenu avec flèche'],
                            ['title' => 'Section 2', 'content' => 'Contenu avec flèche'],
                        ];
                    @endphp
                    <x-daisy::ui.advanced.accordion :items="$items" icon="arrow" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec icône plus</p>
                    @php
                        $items = [
                            ['title' => 'Section 1', 'content' => 'Contenu avec plus'],
                            ['title' => 'Section 2', 'content' => 'Contenu avec plus'],
                        ];
                    @endphp
                    <x-daisy::ui.advanced.accordion :items="$items" icon="plus" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.advanced.accordion :items="[
    ['title' => 'Section 1', 'content' => 'Contenu'],
    ['title' => 'Section 2', 'content' => 'Contenu'],
]" icon="arrow" />

<x-daisy::ui.advanced.accordion :items="[
    ['title' => 'Section 1', 'content' => 'Contenu'],
    ['title' => 'Section 2', 'content' => 'Contenu'],
]" icon="plus" />
CODE;
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
