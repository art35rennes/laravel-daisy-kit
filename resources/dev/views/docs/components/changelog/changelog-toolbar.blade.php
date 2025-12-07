@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'changelog';
    $name = 'changelog-toolbar';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Changelog Toolbar" 
    category="changelog" 
    name="changelog-toolbar"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Changelog Toolbar" 
            subtitle="Barre d'outils pour le changelog avec recherche et filtres."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="changelog-toolbar">
        <x-slot:preview>
            <x-daisy::ui.changelog.changelog-toolbar :showSearch="true" :showFilters="true" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.changelog.changelog-toolbar :showSearch="true" :showFilters="true" />';
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
