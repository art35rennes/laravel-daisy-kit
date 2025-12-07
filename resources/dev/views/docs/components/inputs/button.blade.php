@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'button';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Bouton" 
    category="inputs" 
    name="button"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Bouton" 
            subtitle="Un composant d'action compatible daisyUI. Utilisez les props pour contrôler le style, la taille et l'état."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="button">
        <x-slot:preview>
            <x-daisy::ui.inputs.button>Envoyer</x-daisy::ui.inputs.button>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.button>Envoyer</x-daisy::ui.inputs.button>';
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

    <x-daisy::docs.sections.variants name="button">
        <x-slot:preview>
            <div class="flex flex-wrap items-center gap-3">
                <x-daisy::ui.inputs.button color="primary">Primary</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button color="secondary">Secondary</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button variant="outline">Outline</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button variant="ghost">Ghost</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button size="sm">Small</x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button size="lg">Large</x-daisy::ui.inputs.button>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '<x-daisy::ui.inputs.button color="primary">Primary</x-daisy::ui.inputs.button>
<x-daisy::ui.inputs.button color="secondary">Secondary</x-daisy::ui.inputs.button>
<x-daisy::ui.inputs.button variant="outline">Outline</x-daisy::ui.inputs.button>
<x-daisy::ui.inputs.button variant="ghost">Ghost</x-daisy::ui.inputs.button>
<x-daisy::ui.inputs.button size="sm">Small</x-daisy::ui.inputs.button>
<x-daisy::ui.inputs.button size="lg">Large</x-daisy::ui.inputs.button>';
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
                height="200px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
