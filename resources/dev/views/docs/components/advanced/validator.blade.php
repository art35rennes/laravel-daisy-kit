@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'validator';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Validateur" 
    category="advanced" 
    name="validator"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Validateur" 
            subtitle="Validation de formulaire visuelle avec états d'erreur et de succès."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="validator">
        <x-slot:preview>
            <div class="space-y-4">
                <x-daisy::ui.inputs.input 
                    name="email" 
                    type="email" 
                    placeholder="Email"
                    class="validator"
                />
                <x-daisy::ui.advanced.validator state="error" message="Ce champ est requis" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.input 
    name="email" 
    type="email" 
    placeholder="Email"
    class="validator"
/>
<x-daisy::ui.advanced.validator state="error" message="Ce champ est requis" />';
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

    <x-daisy::docs.sections.variants name="validator">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">État d'erreur</p>
                    <x-daisy::ui.inputs.input 
                        name="email-error" 
                        type="email" 
                        placeholder="Email invalide"
                        class="validator"
                    />
                    <x-daisy::ui.advanced.validator state="error" message="L'email n'est pas valide" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">État de succès</p>
                    <x-daisy::ui.inputs.input 
                        name="email-success" 
                        type="email" 
                        placeholder="Email valide"
                        class="validator"
                    />
                    <x-daisy::ui.advanced.validator state="success" message="Email valide" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- État d\'erreur --}}
<x-daisy::ui.inputs.input 
    name="email" 
    type="email" 
    placeholder="Email invalide"
    class="validator"
/>
<x-daisy::ui.advanced.validator state="error" message="L\'email n\'est pas valide" />

{{-- État de succès --}}
<x-daisy::ui.inputs.input 
    name="email" 
    type="email" 
    placeholder="Email valide"
    class="validator"
/>
<x-daisy::ui.advanced.validator state="success" message="Email valide" />';
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
