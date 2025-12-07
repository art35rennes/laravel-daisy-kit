@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'toggle';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Interrupteur" 
    category="inputs" 
    name="toggle"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Interrupteur" 
            subtitle="Interrupteur compatible daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="toggle">
        <x-slot:preview>
            <x-daisy::ui.inputs.toggle name="notifications" label="Activer les notifications" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.toggle name="notifications" label="Activer les notifications" />';
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

    <x-daisy::docs.sections.variants name="toggle">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.toggle name="t1" color="primary" label="Primary" />
                        <x-daisy::ui.inputs.toggle name="t2" color="secondary" label="Secondary" />
                        <x-daisy::ui.inputs.toggle name="t3" color="accent" label="Accent" />
                        <x-daisy::ui.inputs.toggle name="t4" color="success" label="Success" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.toggle name="t5" size="xs" label="Extra Small" />
                        <x-daisy::ui.inputs.toggle name="t6" size="sm" label="Small" />
                        <x-daisy::ui.inputs.toggle name="t7" size="md" label="Medium" />
                        <x-daisy::ui.inputs.toggle name="t8" size="lg" label="Large" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.toggle name="t1" color="primary" label="Primary" />
<x-daisy::ui.inputs.toggle name="t2" color="success" label="Success" />

{{-- Tailles --}}
<x-daisy::ui.inputs.toggle name="t3" size="sm" label="Small" />
<x-daisy::ui.inputs.toggle name="t4" size="lg" label="Large" />';
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
