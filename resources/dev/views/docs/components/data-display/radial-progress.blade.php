@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'radial-progress';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Progression radiale" 
    category="data-display" 
    name="radial-progress"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Progression radiale" 
            subtitle="Barre de progression circulaire."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="radial-progress">
        <x-slot:preview>
            <x-daisy::ui.data-display.radial-progress value="85" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.radial-progress value="85" />';
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

    <x-daisy::docs.sections.variants name="radial-progress">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-4">
                        <x-daisy::ui.data-display.radial-progress value="75" color="primary" />
                        <x-daisy::ui.data-display.radial-progress value="75" color="secondary" />
                        <x-daisy::ui.data-display.radial-progress value="75" color="accent" />
                        <x-daisy::ui.data-display.radial-progress value="75" color="success" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Valeurs</p>
                    <div class="flex flex-wrap items-center gap-4">
                        <x-daisy::ui.data-display.radial-progress value="0" />
                        <x-daisy::ui.data-display.radial-progress value="25" />
                        <x-daisy::ui.data-display.radial-progress value="50" />
                        <x-daisy::ui.data-display.radial-progress value="75" />
                        <x-daisy::ui.data-display.radial-progress value="100" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.data-display.radial-progress value="75" color="primary" />
<x-daisy::ui.data-display.radial-progress value="75" color="success" />

{{-- Valeurs --}}
<x-daisy::ui.data-display.radial-progress value="25" />
<x-daisy::ui.data-display.radial-progress value="50" />
<x-daisy::ui.data-display.radial-progress value="100" />';
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
