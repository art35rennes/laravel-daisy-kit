@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'progress';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Barre de progression" 
    category="data-display" 
    name="progress"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Barre de progression" 
            subtitle="Barre de progression linéaire."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="progress">
        <x-slot:preview>
            <x-daisy::ui.data-display.progress value="75" max="100" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.progress value="75" max="100" />';
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

    <x-daisy::docs.sections.variants name="progress">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.data-display.progress value="50" max="100" color="primary" />
                        <x-daisy::ui.data-display.progress value="60" max="100" color="secondary" />
                        <x-daisy::ui.data-display.progress value="70" max="100" color="accent" />
                        <x-daisy::ui.data-display.progress value="80" max="100" color="success" />
                        <x-daisy::ui.data-display.progress value="90" max="100" color="warning" />
                        <x-daisy::ui.data-display.progress value="100" max="100" color="error" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Valeurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.data-display.progress value="0" max="100" />
                        <x-daisy::ui.data-display.progress value="25" max="100" />
                        <x-daisy::ui.data-display.progress value="50" max="100" />
                        <x-daisy::ui.data-display.progress value="75" max="100" />
                        <x-daisy::ui.data-display.progress value="100" max="100" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.data-display.progress value="50" max="100" color="primary" />
<x-daisy::ui.data-display.progress value="70" max="100" color="success" />
<x-daisy::ui.data-display.progress value="90" max="100" color="error" />

{{-- Valeurs --}}
<x-daisy::ui.data-display.progress value="25" max="100" />
<x-daisy::ui.data-display.progress value="50" max="100" />
<x-daisy::ui.data-display.progress value="100" max="100" />';
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
