@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'steps';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Étapes" 
    category="navigation" 
    name="steps"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Étapes" 
            subtitle="Indicateur d'étapes dans un processus."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="steps">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Informations'],
                    ['label' => 'Paiement', 'current' => true],
                    ['label' => 'Confirmation'],
                ];
            @endphp
            <x-daisy::ui.navigation.steps :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.navigation.steps :items="[
    ['label' => 'Informations'],
    ['label' => 'Paiement', 'current' => true],
    ['label' => 'Confirmation'],
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

    <x-daisy::docs.sections.variants name="steps">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    @php
                        $items = [
                            ['label' => 'Étape 1', 'current' => true],
                            ['label' => 'Étape 2'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        <x-daisy::ui.navigation.steps :items="$items" color="primary" />
                        <x-daisy::ui.navigation.steps :items="$items" color="secondary" />
                        <x-daisy::ui.navigation.steps :items="$items" color="accent" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Vertical</p>
                    @php
                        $items = [
                            ['label' => 'Étape 1', 'current' => true],
                            ['label' => 'Étape 2'],
                            ['label' => 'Étape 3'],
                        ];
                    @endphp
                    <x-daisy::ui.navigation.steps :items="$items" vertical />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.navigation.steps :items="[
    ['label' => 'Étape 1', 'current' => true],
    ['label' => 'Étape 2'],
]" color="primary" />
<x-daisy::ui.navigation.steps :items="[
    ['label' => 'Étape 1', 'current' => true],
    ['label' => 'Étape 2'],
]" color="accent" />

<x-daisy::ui.navigation.steps :items="[
    ['label' => 'Étape 1', 'current' => true],
    ['label' => 'Étape 2'],
    ['label' => 'Étape 3'],
]" vertical />
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
