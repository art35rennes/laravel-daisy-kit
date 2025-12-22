@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'filter';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Filtre" 
    category="advanced" 
    name="filter"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Filtre" 
            subtitle="Filtre avec boutons radio et réinitialisation automatique."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="filter">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Tous', 'value' => 'all'],
                    ['label' => 'Actifs', 'value' => 'active'],
                    ['label' => 'Archivés', 'value' => 'archived'],
                ];
            @endphp
            <x-daisy::ui.advanced.filter name="status" :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.advanced.filter name="status" :items="[
    ['label' => 'Tous', 'value' => 'all'],
    ['label' => 'Actifs', 'value' => 'active'],
    ['label' => 'Archivés', 'value' => 'archived'],
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

    <x-daisy::docs.sections.variants name="filter">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Filtre par statut</p>
                    @php
                        $statusItems = [
                            ['label' => 'Tous', 'value' => 'all'],
                            ['label' => 'Actifs', 'value' => 'active'],
                            ['label' => 'Inactifs', 'value' => 'inactive'],
                        ];
                    @endphp
                    <x-daisy::ui.advanced.filter name="status-filter" :items="$statusItems" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Filtre par catégorie</p>
                    @php
                        $categoryItems = [
                            ['label' => 'Toutes', 'value' => 'all'],
                            ['label' => 'Produits', 'value' => 'products'],
                            ['label' => 'Services', 'value' => 'services'],
                        ];
                    @endphp
                    <x-daisy::ui.advanced.filter name="category-filter" :items="$categoryItems" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.advanced.filter name="status-filter" :items="[
    ['label' => 'Tous', 'value' => 'all'],
    ['label' => 'Actifs', 'value' => 'active'],
    ['label' => 'Inactifs', 'value' => 'inactive'],
]" />

<x-daisy::ui.advanced.filter name="category-filter" :items="[
    ['label' => 'Toutes', 'value' => 'all'],
    ['label' => 'Produits', 'value' => 'products'],
    ['label' => 'Services', 'value' => 'services'],
]" />
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
