@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'stat';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Statistique" 
    category="data-display" 
    name="stat"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Statistique" 
            subtitle="Statistique avec titre, valeur et description."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="stat">
        <x-slot:preview>
            <x-daisy::ui.data-display.stat title="Ventes" value="1,234" desc="+20% ce mois" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.stat title="Ventes" value="1,234" desc="+20% ce mois" />';
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

    <x-daisy::docs.sections.variants name="stat">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Groupe de statistiques</p>
                    <div class="stats shadow">
                        <x-daisy::ui.data-display.stat title="Ventes" value="1,234" desc="+20% ce mois" />
                        <x-daisy::ui.data-display.stat title="Utilisateurs" value="5,678" desc="+15% ce mois" />
                        <x-daisy::ui.data-display.stat title="Revenus" value="9,876€" desc="+12% ce mois" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec icône</p>
                    <x-daisy::ui.data-display.stat title="Nouveaux clients" value="42" desc="Ce mois">
                        <x-slot:figure>
                            <x-bi-people class="w-8 h-8" />
                        </x-slot:figure>
                    </x-daisy::ui.data-display.stat>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Groupe de statistiques --}}
<div class="stats shadow">
    <x-daisy::ui.data-display.stat title="Ventes" value="1,234" desc="+20% ce mois" />
    <x-daisy::ui.data-display.stat title="Utilisateurs" value="5,678" desc="+15% ce mois" />
    <x-daisy::ui.data-display.stat title="Revenus" value="9,876€" desc="+12% ce mois" />
</div>

{{-- Avec icône --}}
<x-daisy::ui.data-display.stat title="Nouveaux clients" value="42" desc="Ce mois">
    <x-slot:figure>
        <x-bi-people class="w-8 h-8" />
    </x-slot:figure>
</x-daisy::ui.data-display.stat>';
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
