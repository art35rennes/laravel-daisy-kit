@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'overlay';
    $name = 'popconfirm';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Confirmation" 
    category="overlay" 
    name="popconfirm"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Confirmation" 
            subtitle="Confirmation via popover."
            jsModule="popconfirm"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="popconfirm">
        <x-slot:preview>
            <x-daisy::ui.overlay.popconfirm message="Voulez-vous vraiment supprimer cet élément ?">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button color="error">Supprimer</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.overlay.popconfirm message="Voulez-vous vraiment supprimer cet élément ?">
    <x-slot:trigger>
        <x-daisy::ui.inputs.button color="error">Supprimer</x-daisy::ui.inputs.button>
    </x-slot:trigger>
</x-daisy::ui.overlay.popconfirm>';
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
