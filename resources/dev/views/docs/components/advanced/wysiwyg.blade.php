@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'wysiwyg';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Éditeur WYSIWYG" 
    category="advanced" 
    name="wysiwyg"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Éditeur WYSIWYG" 
            subtitle="Éditeur de texte riche (What You See Is What You Get) pour la création de contenu."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="wysiwyg">
        <x-slot:preview>
            <x-daisy::ui.advanced.wysiwyg 
                name="content" 
                value="<p>Contenu riche avec <strong>formatage</strong> et <em>styles</em>.</p>" 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.wysiwyg 
    name="content" 
    value="<p>Contenu riche avec <strong>formatage</strong> et <em>styles</em>.</p>" 
/>';
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
