@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'textarea';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Zone de texte" 
    category="inputs" 
    name="textarea"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Zone de texte" 
            subtitle="Zone de texte multiligne compatible daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="textarea">
        <x-slot:preview>
            <x-daisy::ui.inputs.textarea name="message" placeholder="Votre message..." rows="4"></x-daisy::ui.inputs.textarea>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.textarea name="message" placeholder="Votre message..." rows="4"></x-daisy::ui.inputs.textarea>';
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

    <x-daisy::docs.sections.variants name="textarea">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.textarea color="primary" placeholder="Primary" rows="3"></x-daisy::ui.inputs.textarea>
                        <x-daisy::ui.inputs.textarea color="secondary" placeholder="Secondary" rows="3"></x-daisy::ui.inputs.textarea>
                        <x-daisy::ui.inputs.textarea color="error" placeholder="Error" rows="3"></x-daisy::ui.inputs.textarea>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.textarea variant="ghost" placeholder="Ghost" rows="3"></x-daisy::ui.inputs.textarea>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.textarea size="sm" placeholder="Small" rows="3"></x-daisy::ui.inputs.textarea>
                        <x-daisy::ui.inputs.textarea size="lg" placeholder="Large" rows="3"></x-daisy::ui.inputs.textarea>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.textarea color="primary" placeholder="Primary" rows="3"></x-daisy::ui.inputs.textarea>
<x-daisy::ui.inputs.textarea color="error" placeholder="Error" rows="3"></x-daisy::ui.inputs.textarea>

{{-- Styles --}}
<x-daisy::ui.inputs.textarea variant="ghost" placeholder="Ghost" rows="3"></x-daisy::ui.inputs.textarea>

{{-- Tailles --}}
<x-daisy::ui.inputs.textarea size="sm" placeholder="Small" rows="3"></x-daisy::ui.inputs.textarea>
<x-daisy::ui.inputs.textarea size="lg" placeholder="Large" rows="3"></x-daisy::ui.inputs.textarea>';
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
