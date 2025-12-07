@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'join';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Groupe" 
    category="advanced" 
    name="join"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Groupe" 
            subtitle="Groupe d'éléments joints sans espace."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="join">
        <x-slot:preview>
            <x-daisy::ui.advanced.join>
                <x-daisy::ui.inputs.button>Gauche</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button>Milieu</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button>Droite</x-daisy::ui.inputs.button>
            </x-daisy::ui.advanced.join>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.join>
    <x-daisy::ui.inputs.button>Gauche</x-daisy::ui.inputs.button>
    <x-daisy::ui.inputs.button>Milieu</x-daisy::ui.inputs.button>
    <x-daisy::ui.inputs.button>Droite</x-daisy::ui.inputs.button>
</x-daisy::ui.advanced.join>';
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

    <x-daisy::docs.sections.variants name="join">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Vertical</p>
                    <x-daisy::ui.advanced.join vertical>
                        <x-daisy::ui.inputs.button>Haut</x-daisy::ui.inputs.button>
                        <x-daisy::ui.inputs.button>Milieu</x-daisy::ui.inputs.button>
                        <x-daisy::ui.inputs.button>Bas</x-daisy::ui.inputs.button>
                    </x-daisy::ui.advanced.join>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec inputs</p>
                    <x-daisy::ui.advanced.join>
                        <x-daisy::ui.inputs.input placeholder="Rechercher..." />
                        <x-daisy::ui.inputs.button>Rechercher</x-daisy::ui.inputs.button>
                    </x-daisy::ui.advanced.join>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Vertical --}}
<x-daisy::ui.advanced.join vertical>
    <x-daisy::ui.inputs.button>Haut</x-daisy::ui.inputs.button>
    <x-daisy::ui.inputs.button>Bas</x-daisy::ui.inputs.button>
</x-daisy::ui.advanced.join>

{{-- Avec inputs --}}
<x-daisy::ui.advanced.join>
    <x-daisy::ui.inputs.input placeholder="Rechercher..." />
    <x-daisy::ui.inputs.button>Rechercher</x-daisy::ui.inputs.button>
</x-daisy::ui.advanced.join>';
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
