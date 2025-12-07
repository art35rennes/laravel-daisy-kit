@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'grid-layout';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Grid Layout" 
    category="layout" 
    name="grid-layout"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Grid Layout" 
            subtitle="Layout en grille responsive pour organiser le contenu en colonnes."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="grid-layout">
        <x-slot:preview>
            <x-daisy::ui.layout.grid-layout>
                <div class="col-12 col-md-6 col-lg-4">
                    <x-daisy::ui.layout.card title="Colonne 1">
                        <p>Contenu de la première colonne</p>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <x-daisy::ui.layout.card title="Colonne 2">
                        <p>Contenu de la deuxième colonne</p>
                    </x-daisy::ui.layout.card>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <x-daisy::ui.layout.card title="Colonne 3">
                        <p>Contenu de la troisième colonne</p>
                    </x-daisy::ui.layout.card>
                </div>
            </x-daisy::ui.layout.grid-layout>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.grid-layout>
    <div class="col-12 col-md-6 col-lg-4">
        <x-daisy::ui.layout.card title="Colonne 1">
            <p>Contenu de la première colonne</p>
        </x-daisy::ui.layout.card>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <x-daisy::ui.layout.card title="Colonne 2">
            <p>Contenu de la deuxième colonne</p>
        </x-daisy::ui.layout.card>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <x-daisy::ui.layout.card title="Colonne 3">
            <p>Contenu de la troisième colonne</p>
        </x-daisy::ui.layout.card>
    </div>
</x-daisy::ui.layout.grid-layout>';
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
