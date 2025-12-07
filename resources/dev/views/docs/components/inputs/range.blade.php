@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'range';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Curseur" 
    category="inputs" 
    name="range"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Curseur" 
            subtitle="Curseur de sélection de valeur compatible daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="range">
        <x-slot:preview>
            <x-daisy::ui.inputs.range name="volume" min="0" max="100" value="50" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.range name="volume" min="0" max="100" value="50" />';
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

    <x-daisy::docs.sections.variants name="range">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.range name="r1" min="0" max="100" value="50" color="primary" />
                        <x-daisy::ui.inputs.range name="r2" min="0" max="100" value="50" color="secondary" />
                        <x-daisy::ui.inputs.range name="r3" min="0" max="100" value="50" color="accent" />
                        <x-daisy::ui.inputs.range name="r4" min="0" max="100" value="50" color="success" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.range name="r5" min="0" max="100" value="50" size="xs" />
                        <x-daisy::ui.inputs.range name="r6" min="0" max="100" value="50" size="sm" />
                        <x-daisy::ui.inputs.range name="r7" min="0" max="100" value="50" size="md" />
                        <x-daisy::ui.inputs.range name="r8" min="0" max="100" value="50" size="lg" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.range name="r1" min="0" max="100" value="50" color="primary" />
<x-daisy::ui.inputs.range name="r2" min="0" max="100" value="50" color="success" />

{{-- Tailles --}}
<x-daisy::ui.inputs.range name="r3" min="0" max="100" value="50" size="sm" />
<x-daisy::ui.inputs.range name="r4" min="0" max="100" value="50" size="lg" />';
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
