@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'breadcrumbs';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Fil d'Ariane" 
    category="navigation" 
    name="breadcrumbs"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Fil d'Ariane" 
            subtitle="Fil d'Ariane pour la navigation hiérarchique."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="breadcrumbs">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Accueil', 'href' => '/'],
                    ['label' => 'Produits', 'href' => '/products'],
                    ['label' => 'Détails'],
                ];
            @endphp
            <x-daisy::ui.navigation.breadcrumbs :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.navigation.breadcrumbs :items="[
    ['label' => 'Accueil', 'href' => '/'],
    ['label' => 'Produits', 'href' => '/products'],
    ['label' => 'Détails'],
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

    <x-daisy::docs.sections.variants name="breadcrumbs">
        <x-slot:preview>
            <div class="space-y-4">
                @php
                    $items = [
                        ['label' => 'Accueil', 'href' => '/'],
                        ['label' => 'Page actuelle'],
                    ];
                @endphp
                <div>
                    <p class="text-sm font-semibold mb-2">Avec icônes</p>
                    <x-daisy::ui.navigation.breadcrumbs :items="$items" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.navigation.breadcrumbs :items="[
    ['label' => 'Accueil', 'href' => '/'],
    ['label' => 'Page actuelle'],
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
                height="200px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
