@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'chart';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Graphique" 
    category="advanced" 
    name="chart"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Graphique" 
            subtitle="Graphiques interactifs avec Chart.js pour visualiser des données."
            jsModule="chart"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="chart">
        <x-slot:preview>
            @php
                $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'];
                $datasets = [
                    ['label' => 'Ventes', 'data' => [100, 200, 150, 300, 250]]
                ];
            @endphp
            <x-daisy::ui.advanced.chart type="line" :labels="$labels" :datasets="$datasets" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.advanced.chart
    type="line"
    :labels="['Jan', 'Fév', 'Mar', 'Avr', 'Mai']"
    :datasets="[
        ['label' => 'Ventes', 'data' => [100, 200, 150, 300, 250]],
    ]"
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="chart">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Graphique en barres</p>
                    @php
                        $barLabels = ['Jan', 'Fév', 'Mar'];
                        $barDatasets = [['label' => 'Ventes', 'data' => [100, 200, 150]]];
                    @endphp
                    <x-daisy::ui.advanced.chart type="bar" :labels="$barLabels" :datasets="$barDatasets" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Graphique circulaire</p>
                    @php
                        $pieLabels = ['Rouge', 'Vert', 'Bleu'];
                        $pieDatasets = [['data' => [30, 40, 30]]];
                    @endphp
                    <x-daisy::ui.advanced.chart type="pie" :labels="$pieLabels" :datasets="$pieDatasets" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.advanced.chart
    type="bar"
    :labels="['Jan', 'Fév', 'Mar']"
    :datasets="[
        ['label' => 'Ventes', 'data' => [100, 200, 150]],
    ]"
/>

<x-daisy::ui.advanced.chart
    type="pie"
    :labels="['Rouge', 'Vert', 'Bleu']"
    :datasets="[
        ['data' => [30, 40, 30]],
    ]"
/>
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
