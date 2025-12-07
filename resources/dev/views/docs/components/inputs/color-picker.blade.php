@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'color-picker';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Sélecteur de couleur" 
    category="inputs" 
    name="color-picker"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Sélecteur de couleur" 
            subtitle="Sélecteur de couleur avec support JavaScript."
            jsModule="color-picker"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="color-picker">
        <x-slot:preview>
            <x-daisy::ui.inputs.color-picker name="theme-color" value="#3b82f6" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.color-picker name="theme-color" value="#3b82f6" />';
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

    <x-daisy::docs.sections.variants name="color-picker">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs prédéfinies</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.color-picker name="c1" value="#3b82f6" />
                        <x-daisy::ui.inputs.color-picker name="c2" value="#10b981" />
                        <x-daisy::ui.inputs.color-picker name="c3" value="#f59e0b" />
                        <x-daisy::ui.inputs.color-picker name="c4" value="#ef4444" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec label</p>
                    <div class="space-y-2">
                        <x-daisy::ui.advanced.label for="color">Couleur du thème</x-daisy::ui.advanced.label>
                        <x-daisy::ui.inputs.color-picker id="color" name="color" value="#8b5cf6" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs prédéfinies --}}
<x-daisy::ui.inputs.color-picker name="c1" value="#3b82f6" />
<x-daisy::ui.inputs.color-picker name="c2" value="#10b981" />

{{-- Avec label --}}
<x-daisy::ui.advanced.label for="color">Couleur du thème</x-daisy::ui.advanced.label>
<x-daisy::ui.inputs.color-picker id="color" name="color" value="#8b5cf6" />';
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
