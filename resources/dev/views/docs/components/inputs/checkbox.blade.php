@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'checkbox';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Case à cocher" 
    category="inputs" 
    name="checkbox"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Case à cocher" 
            subtitle="Case à cocher compatible daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="checkbox">
        <x-slot:preview>
            <x-daisy::ui.inputs.checkbox name="terms" label="J'accepte les conditions" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.checkbox name="terms" label="J\'accepte les conditions" />';
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

    <x-daisy::docs.sections.variants name="checkbox">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.checkbox name="cb1" color="primary" label="Primary" />
                        <x-daisy::ui.inputs.checkbox name="cb2" color="secondary" label="Secondary" />
                        <x-daisy::ui.inputs.checkbox name="cb3" color="accent" label="Accent" />
                        <x-daisy::ui.inputs.checkbox name="cb4" color="success" label="Success" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.checkbox name="cb5" size="xs" label="Extra Small" />
                        <x-daisy::ui.inputs.checkbox name="cb6" size="sm" label="Small" />
                        <x-daisy::ui.inputs.checkbox name="cb7" size="md" label="Medium" />
                        <x-daisy::ui.inputs.checkbox name="cb8" size="lg" label="Large" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.checkbox name="cb1" color="primary" label="Primary" />
<x-daisy::ui.inputs.checkbox name="cb2" color="success" label="Success" />

{{-- Tailles --}}
<x-daisy::ui.inputs.checkbox name="cb3" size="sm" label="Small" />
<x-daisy::ui.inputs.checkbox name="cb4" size="lg" label="Large" />';
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
