@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'transfer';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Transfert" 
    category="advanced" 
    name="transfer"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Transfert" 
            subtitle="Transfert d'éléments entre deux listes avec sélection multiple."
            jsModule="transfer"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="transfer">
        <x-slot:preview>
            @php
                $source = [
                    ['id' => 1, 'label' => 'Item 1'],
                    ['id' => 2, 'label' => 'Item 2'],
                    ['id' => 3, 'label' => 'Item 3'],
                    ['id' => 4, 'label' => 'Item 4'],
                ];
            @endphp
            <x-daisy::ui.advanced.transfer :source="$source" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
@php
$source = [
    ["id" => 1, "label" => "Item 1"],
    ["id" => 2, "label" => "Item 2"],
    ["id" => 3, "label" => "Item 3"],
    ["id" => 4, "label" => "Item 4"]
];
@endphp
<x-daisy::ui.advanced.transfer :source="$source" />
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
