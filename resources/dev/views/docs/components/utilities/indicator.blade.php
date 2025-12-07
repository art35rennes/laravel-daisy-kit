@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'indicator';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Indicateur" 
    category="utilities" 
    name="indicator"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Indicateur" 
            subtitle="Indicateur positionné sur un élément."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="indicator">
        <x-slot:preview>
            <x-daisy::ui.utilities.indicator label="3" color="error">
                <x-daisy::ui.inputs.button>Notifications</x-daisy::ui.inputs.button>
            </x-daisy::ui.utilities.indicator>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.indicator label="3" color="error">
    <x-daisy::ui.inputs.button>Notifications</x-daisy::ui.inputs.button>
</x-daisy::ui.utilities.indicator>';
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

    <x-daisy::docs.sections.variants name="indicator">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.utilities.indicator label="5" color="primary">
                            <x-daisy::ui.inputs.button>Primary</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator label="2" color="success">
                            <x-daisy::ui.inputs.button>Success</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator label="9" color="warning">
                            <x-daisy::ui.inputs.button>Warning</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator label="1" color="error">
                            <x-daisy::ui.inputs.button>Error</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Positions</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.utilities.indicator label="1" position="top-start">
                            <x-daisy::ui.inputs.button size="sm">Top Start</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator label="2" position="top-end">
                            <x-daisy::ui.inputs.button size="sm">Top End</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator label="3" position="bottom-start">
                            <x-daisy::ui.inputs.button size="sm">Bottom Start</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator label="4" position="bottom-end">
                            <x-daisy::ui.inputs.button size="sm">Bottom End</x-daisy::ui.inputs.button>
                        </x-daisy::ui.utilities.indicator>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Type status</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.utilities.indicator type="status" statusColor="success">
                            <x-daisy::ui.data-display.avatar src="https://i.pravatar.cc/150?img=1" />
                        </x-daisy::ui.utilities.indicator>
                        <x-daisy::ui.utilities.indicator type="status" statusColor="error">
                            <x-daisy::ui.data-display.avatar src="https://i.pravatar.cc/150?img=2" />
                        </x-daisy::ui.utilities.indicator>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.utilities.indicator label="5" color="primary">
    <x-daisy::ui.inputs.button>Primary</x-daisy::ui.inputs.button>
</x-daisy::ui.utilities.indicator>

{{-- Positions --}}
<x-daisy::ui.utilities.indicator label="1" position="top-start">
    <x-daisy::ui.inputs.button>Top Start</x-daisy::ui.inputs.button>
</x-daisy::ui.utilities.indicator>

<x-daisy::ui.utilities.indicator label="2" position="bottom-end">
    <x-daisy::ui.inputs.button>Bottom End</x-daisy::ui.inputs.button>
</x-daisy::ui.utilities.indicator>

{{-- Type status --}}
<x-daisy::ui.utilities.indicator type="status" statusColor="success">
    <x-daisy::ui.data-display.avatar src="..." />
</x-daisy::ui.utilities.indicator>';
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
                height="350px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
