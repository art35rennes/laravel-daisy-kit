@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'timeline';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chronologie" 
    category="data-display" 
    name="timeline"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chronologie" 
            subtitle="Chronologie d'événements."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="timeline">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Commande créée', 'time' => '10:00'],
                    ['label' => 'En préparation', 'time' => '11:30', 'current' => true],
                    ['label' => 'Expédiée', 'time' => null],
                ];
            @endphp
            <x-daisy::ui.data-display.timeline :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$items = [
    ["label" => "Commande créée", "time" => "10:00"],
    ["label" => "En préparation", "time" => "11:30", "current" => true],
    ["label" => "Expédiée", "time" => null]
];
@endphp
<x-daisy::ui.data-display.timeline :items="$items" />';
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

    <x-daisy::docs.sections.variants name="timeline">
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
                        <x-daisy::ui.data-display.timeline :items="$items" color="primary" />
                        <x-daisy::ui.data-display.timeline :items="$items" color="success" />
                        <x-daisy::ui.data-display.timeline :items="$items" color="warning" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Horizontal</p>
                    @php
                        $items = [
                            ['label' => 'Étape 1', 'current' => true],
                            ['label' => 'Étape 2'],
                            ['label' => 'Étape 3'],
                        ];
                    @endphp
                    <x-daisy::ui.data-display.timeline :items="$items" horizontal />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '@php
$items = [
    ["label" => "Étape 1", "current" => true],
    ["label" => "Étape 2"]
];
@endphp

{{-- Couleurs --}}
<x-daisy::ui.data-display.timeline :items="$items" color="primary" />
<x-daisy::ui.data-display.timeline :items="$items" color="success" />

{{-- Horizontal --}}
<x-daisy::ui.data-display.timeline :items="$items" horizontal />';
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
