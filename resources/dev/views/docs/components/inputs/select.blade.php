@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'select';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Liste déroulante" 
    category="inputs" 
    name="select"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Liste déroulante" 
            subtitle="Liste déroulante compatible daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="select">
        <x-slot:preview>
            <x-daisy::ui.inputs.select name="country">
                <option value="">Choisir un pays</option>
                <option value="fr">France</option>
                <option value="be">Belgique</option>
                <option value="ch">Suisse</option>
            </x-daisy::ui.inputs.select>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.select name="country">
    <option value="">Choisir un pays</option>
    <option value="fr">France</option>
    <option value="be">Belgique</option>
    <option value="ch">Suisse</option>
</x-daisy::ui.inputs.select>';
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

    <x-daisy::docs.sections.variants name="select">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.select color="primary">
                            <option>Primary</option>
                        </x-daisy::ui.inputs.select>
                        <x-daisy::ui.inputs.select color="secondary">
                            <option>Secondary</option>
                        </x-daisy::ui.inputs.select>
                        <x-daisy::ui.inputs.select color="error">
                            <option>Error</option>
                        </x-daisy::ui.inputs.select>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.select variant="ghost">
                            <option>Ghost</option>
                        </x-daisy::ui.inputs.select>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.select size="sm">
                            <option>Small</option>
                        </x-daisy::ui.inputs.select>
                        <x-daisy::ui.inputs.select size="lg">
                            <option>Large</option>
                        </x-daisy::ui.inputs.select>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.select color="primary">
    <option>Primary</option>
</x-daisy::ui.inputs.select>
<x-daisy::ui.inputs.select color="error">
    <option>Error</option>
</x-daisy::ui.inputs.select>

{{-- Styles --}}
<x-daisy::ui.inputs.select variant="ghost">
    <option>Ghost</option>
</x-daisy::ui.inputs.select>

{{-- Tailles --}}
<x-daisy::ui.inputs.select size="sm">
    <option>Small</option>
</x-daisy::ui.inputs.select>
<x-daisy::ui.inputs.select size="lg">
    <option>Large</option>
</x-daisy::ui.inputs.select>';
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
