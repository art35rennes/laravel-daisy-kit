@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'file-input';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Téléchargement de fichier" 
    category="inputs" 
    name="file-input"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Téléchargement de fichier" 
            subtitle="Champ de téléchargement de fichier compatible daisyUI."
            jsModule="file-input"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="file-input">
        <x-slot:preview>
            <x-daisy::ui.inputs.file-input name="avatar" accept="image/*" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.file-input name="avatar" accept="image/*" />';
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

    <x-daisy::docs.sections.variants name="file-input">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.file-input name="f1" color="primary" />
                        <x-daisy::ui.inputs.file-input name="f2" color="secondary" />
                        <x-daisy::ui.inputs.file-input name="f3" color="accent" />
                        <x-daisy::ui.inputs.file-input name="f4" color="success" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.file-input name="f5" variant="ghost" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.inputs.file-input name="f6" size="sm" />
                        <x-daisy::ui.inputs.file-input name="f7" size="md" />
                        <x-daisy::ui.inputs.file-input name="f8" size="lg" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.file-input name="f1" color="primary" />
<x-daisy::ui.inputs.file-input name="f2" color="success" />

{{-- Styles --}}
<x-daisy::ui.inputs.file-input name="f3" variant="ghost" />

{{-- Tailles --}}
<x-daisy::ui.inputs.file-input name="f4" size="sm" />
<x-daisy::ui.inputs.file-input name="f5" size="lg" />';
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
