@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'navbar';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Barre de navigation" 
    category="navigation" 
    name="navbar"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Barre de navigation" 
            subtitle="Barre de navigation en haut de page."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="navbar">
        <x-slot:preview>
            <x-daisy::ui.navigation.navbar>
                <x-slot:start>
                    <a href="/" class="text-xl font-bold">Mon Site</a>
                </x-slot:start>
                <x-slot:end>
                    <x-daisy::ui.inputs.button>Connexion</x-daisy::ui.inputs.button>
                </x-slot:end>
            </x-daisy::ui.navigation.navbar>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.navigation.navbar>
    <x-slot:start>
        <a href="/" class="text-xl font-bold">Mon Site</a>
    </x-slot:start>
    <x-slot:end>
        <x-daisy::ui.inputs.button>Connexion</x-daisy::ui.inputs.button>
    </x-slot:end>
</x-daisy::ui.navigation.navbar>';
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
