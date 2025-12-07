@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'crud-layout';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="CRUD Layout" 
    category="layout" 
    name="crud-layout"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="CRUD Layout" 
            subtitle="Layout structuré pour les formulaires CRUD avec sections et actions."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="crud-layout">
        <x-slot:preview>
            <x-daisy::ui.layout.crud-layout>
                <x-daisy::ui.layout.crud-section title="Informations générales" description="Détails de base">
                    <x-daisy::ui.inputs.input name="name" placeholder="Nom" />
                    <x-daisy::ui.inputs.input name="email" type="email" placeholder="Email" />
                </x-daisy::ui.layout.crud-section>
                <x-slot:actions>
                    <x-daisy::ui.inputs.button>Enregistrer</x-daisy::ui.inputs.button>
                    <x-daisy::ui.inputs.button variant="ghost">Annuler</x-daisy::ui.inputs.button>
                </x-slot:actions>
            </x-daisy::ui.layout.crud-layout>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.crud-layout>
    <x-daisy::ui.layout.crud-section title="Informations générales" description="Détails de base">
        <x-daisy::ui.inputs.input name="name" placeholder="Nom" />
        <x-daisy::ui.inputs.input name="email" type="email" placeholder="Email" />
    </x-daisy::ui.layout.crud-section>
    <x-slot:actions>
        <x-daisy::ui.inputs.button>Enregistrer</x-daisy::ui.inputs.button>
        <x-daisy::ui.inputs.button variant="ghost">Annuler</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.layout.crud-layout>';
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
