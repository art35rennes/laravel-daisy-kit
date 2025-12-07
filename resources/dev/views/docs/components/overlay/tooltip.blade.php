@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'overlay';
    $name = 'tooltip';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Info-bulle" 
    category="overlay" 
    name="tooltip"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Info-bulle" 
            subtitle="Info-bulle au survol."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="tooltip">
        <x-slot:preview>
            <x-daisy::ui.overlay.tooltip text="Cliquez pour en savoir plus">
                <x-daisy::ui.inputs.button>Survolez-moi</x-daisy::ui.inputs.button>
            </x-daisy::ui.overlay.tooltip>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.overlay.tooltip text="Cliquez pour en savoir plus">
    <x-daisy::ui.inputs.button>Survolez-moi</x-daisy::ui.inputs.button>
</x-daisy::ui.overlay.tooltip>';
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

    <x-daisy::docs.sections.variants name="tooltip">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Placements</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.overlay.tooltip text="Tooltip top" placement="top">
                            <x-daisy::ui.inputs.button size="sm">Top</x-daisy::ui.inputs.button>
                        </x-daisy::ui.overlay.tooltip>
                        <x-daisy::ui.overlay.tooltip text="Tooltip bottom" placement="bottom">
                            <x-daisy::ui.inputs.button size="sm">Bottom</x-daisy::ui.inputs.button>
                        </x-daisy::ui.overlay.tooltip>
                        <x-daisy::ui.overlay.tooltip text="Tooltip left" placement="left">
                            <x-daisy::ui.inputs.button size="sm">Left</x-daisy::ui.inputs.button>
                        </x-daisy::ui.overlay.tooltip>
                        <x-daisy::ui.overlay.tooltip text="Tooltip right" placement="right">
                            <x-daisy::ui.inputs.button size="sm">Right</x-daisy::ui.inputs.button>
                        </x-daisy::ui.overlay.tooltip>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Placements --}}
<x-daisy::ui.overlay.tooltip text="Tooltip top" placement="top">
    <x-daisy::ui.inputs.button>Top</x-daisy::ui.inputs.button>
</x-daisy::ui.overlay.tooltip>

<x-daisy::ui.overlay.tooltip text="Tooltip bottom" placement="bottom">
    <x-daisy::ui.inputs.button>Bottom</x-daisy::ui.inputs.button>
</x-daisy::ui.overlay.tooltip>';
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
