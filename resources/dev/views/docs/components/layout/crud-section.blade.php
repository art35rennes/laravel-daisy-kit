@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'crud-section';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="CRUD Section" 
    category="layout" 
    name="crud-section"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="CRUD Section" 
            subtitle="Section de formulaire avec titre et description pour les layouts CRUD."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="crud-section">
        <x-slot:preview>
            <x-daisy::ui.layout.crud-section title="Informations générales" description="Détails de base du formulaire">
                <x-daisy::ui.inputs.input name="name" placeholder="Nom" />
                <x-daisy::ui.inputs.input name="email" type="email" placeholder="Email" />
            </x-daisy::ui.layout.crud-section>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.crud-section title="Informations générales" description="Détails de base du formulaire">
    <x-daisy::ui.inputs.input name="name" placeholder="Nom" />
    <x-daisy::ui.inputs.input name="email" type="email" placeholder="Email" />
</x-daisy::ui.layout.crud-section>';
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

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
