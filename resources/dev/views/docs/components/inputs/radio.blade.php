@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'radio';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Bouton radio" 
    category="inputs" 
    name="radio"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Bouton radio" 
            subtitle="Bouton radio compatible daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="radio">
        <x-slot:preview>
            <div class="space-y-2">
                <x-daisy::ui.inputs.radio name="gender" value="male" label="Homme" />
                <x-daisy::ui.inputs.radio name="gender" value="female" label="Femme" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.radio name="gender" value="male" label="Homme" />
<x-daisy::ui.inputs.radio name="gender" value="female" label="Femme" />';
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

    <x-daisy::docs.sections.variants name="radio">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.radio name="r1" value="1" color="primary" label="Primary" />
                        <x-daisy::ui.inputs.radio name="r2" value="2" color="secondary" label="Secondary" />
                        <x-daisy::ui.inputs.radio name="r3" value="3" color="accent" label="Accent" />
                        <x-daisy::ui.inputs.radio name="r4" value="4" color="success" label="Success" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.radio name="r5" value="1" size="xs" label="Extra Small" />
                        <x-daisy::ui.inputs.radio name="r6" value="2" size="sm" label="Small" />
                        <x-daisy::ui.inputs.radio name="r7" value="3" size="md" label="Medium" />
                        <x-daisy::ui.inputs.radio name="r8" value="4" size="lg" label="Large" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.radio name="r1" value="1" color="primary" label="Primary" />
<x-daisy::ui.inputs.radio name="r2" value="2" color="success" label="Success" />

{{-- Tailles --}}
<x-daisy::ui.inputs.radio name="r3" value="1" size="sm" label="Small" />
<x-daisy::ui.inputs.radio name="r4" value="2" size="lg" label="Large" />';
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
