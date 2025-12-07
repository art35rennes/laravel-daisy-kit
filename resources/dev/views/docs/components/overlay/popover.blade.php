@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'overlay';
    $name = 'popover';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Popover" 
    category="overlay" 
    name="popover"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Popover" 
            subtitle="Popover pour afficher des informations contextuelles."
            jsModule="popover"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="popover">
        <x-slot:preview>
            <x-daisy::ui.overlay.popover title="Informations">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button>Plus d'infos</x-daisy::ui.inputs.button>
                </x-slot:trigger>
                <p>Contenu informatif affiché dans le popover.</p>
            </x-daisy::ui.overlay.popover>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.overlay.popover title="Informations">
    <x-slot:trigger>
        <x-daisy::ui.inputs.button>Plus d\'infos</x-daisy::ui.inputs.button>
    </x-slot:trigger>
    <p>Contenu informatif affiché dans le popover.</p>
</x-daisy::ui.overlay.popover>';
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
