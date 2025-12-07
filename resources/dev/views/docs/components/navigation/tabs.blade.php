@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'tabs';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Onglets" 
    category="navigation" 
    name="tabs"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Onglets" 
            subtitle="Onglets pour organiser le contenu."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="tabs">
        <x-slot:preview>
            @php
                $items = [
                    ['label' => 'Profil', 'active' => true],
                    ['label' => 'Sécurité'],
                    ['label' => 'Notifications'],
                ];
            @endphp
            <x-daisy::ui.navigation.tabs :items="$items" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$items = [
    ["label" => "Profil", "active" => true],
    ["label" => "Sécurité"],
    ["label" => "Notifications"]
];
@endphp
<x-daisy::ui.navigation.tabs :items="$items" />';
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

    <x-daisy::docs.sections.variants name="tabs">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    @php
                        $items = [['label' => 'Tab 1', 'active' => true], ['label' => 'Tab 2']];
                    @endphp
                    <div class="space-y-2">
                        <x-daisy::ui.navigation.tabs :items="$items" variant="box" />
                        <x-daisy::ui.navigation.tabs :items="$items" variant="bordered" />
                        <x-daisy::ui.navigation.tabs :items="$items" variant="lifted" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '@php
$items = [
    ["label" => "Tab 1", "active" => true],
    ["label" => "Tab 2"]
];
@endphp

{{-- Styles --}}
<x-daisy::ui.navigation.tabs :items="$items" variant="box" />
<x-daisy::ui.navigation.tabs :items="$items" variant="bordered" />
<x-daisy::ui.navigation.tabs :items="$items" variant="lifted" />';
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
