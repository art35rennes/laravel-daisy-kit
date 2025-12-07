@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'table';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Tableau" 
    category="data-display" 
    name="table"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Tableau" 
            subtitle="Tableau de données avec fonctionnalités avancées."
            jsModule="table"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="table">
        <x-slot:preview>
            @php
                $headers = ['Nom', 'Email', 'Rôle'];
                $rows = [
                    ['Jean Dupont', 'jean@example.com', 'Admin'],
                    ['Marie Martin', 'marie@example.com', 'Utilisateur'],
                ];
            @endphp
            <x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$headers = ["Nom", "Email", "Rôle"];
$rows = [
    ["Jean Dupont", "jean@example.com", "Admin"],
    ["Marie Martin", "marie@example.com", "Utilisateur"],
];
@endphp

<x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" />';
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

    <x-daisy::docs.sections.variants name="table">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    @php
                        $headers = ['Nom', 'Email'];
                        $rows = [['Jean', 'jean@example.com'], ['Marie', 'marie@example.com']];
                    @endphp
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs mb-1">Zebra</p>
                            <x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" zebra />
                        </div>
                        <div>
                            <p class="text-xs mb-1">Avec bordures</p>
                            <x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" bordered />
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs mb-1">Small</p>
                            <x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" size="sm" />
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '@php
$headers = ["Nom", "Email"];
$rows = [["Jean", "jean@example.com"], ["Marie", "marie@example.com"]];
@endphp

{{-- Styles --}}
<x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" zebra />
<x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" bordered />

{{-- Tailles --}}
<x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" size="sm" />';
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
