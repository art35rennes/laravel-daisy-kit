@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'status';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Statut" 
    category="data-display" 
    name="status"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Statut" 
            subtitle="Indicateur de statut visuel."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="status">
        <x-slot:preview>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.status color="success" />
                <span>En ligne</span>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.status color="success" />

{{-- Avec label --}}
<x-daisy::ui.data-display.status color="success" label="En ligne" />';
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

    <x-daisy::docs.sections.variants name="status">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.data-display.status color="primary" />
                        <x-daisy::ui.data-display.status color="secondary" />
                        <x-daisy::ui.data-display.status color="success" />
                        <x-daisy::ui.data-display.status color="warning" />
                        <x-daisy::ui.data-display.status color="error" />
                        <x-daisy::ui.data-display.status color="info" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.data-display.status color="success" size="xs" />
                        <x-daisy::ui.data-display.status color="success" size="sm" />
                        <x-daisy::ui.data-display.status color="success" size="md" />
                        <x-daisy::ui.data-display.status color="success" size="lg" />
                        <x-daisy::ui.data-display.status color="success" size="xl" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.data-display.status color="primary" />
<x-daisy::ui.data-display.status color="success" />
<x-daisy::ui.data-display.status color="error" />

{{-- Tailles --}}
<x-daisy::ui.data-display.status color="success" size="xs" />
<x-daisy::ui.data-display.status color="success" size="sm" />
<x-daisy::ui.data-display.status color="success" size="lg" />';
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
