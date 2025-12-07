@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'copyable';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Copiable" 
    category="utilities" 
    name="copyable"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Copiable" 
            subtitle="Texte copiable dans le presse-papier au clic."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="copyable">
        <x-slot:preview>
            <p>Vous pouvez copier cette <x-daisy::ui.utilities.copyable>valeur</x-daisy::ui.utilities.copyable> directement dans votre presse-papier en cliquant dessus.</p>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<p>Vous pouvez copier cette <x-daisy::ui.utilities.copyable>valeur</x-daisy::ui.utilities.copyable> directement dans votre presse-papier en cliquant dessus.</p>';
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
