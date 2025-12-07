@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'mockup-browser';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Maquette navigateur" 
    category="utilities" 
    name="mockup-browser"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Maquette navigateur" 
            subtitle="Maquette de fenêtre de navigateur."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="mockup-browser">
        <x-slot:preview>
            <x-daisy::ui.utilities.mockup-browser url="https://example.com">
                <div class="p-4">
                    <h2 class="text-xl font-bold mb-2">Bienvenue</h2>
                    <p>Contenu de la page dans la maquette.</p>
                </div>
            </x-daisy::ui.utilities.mockup-browser>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.mockup-browser url="https://example.com">
    <div class="p-4">
        <h2 class="text-xl font-bold mb-2">Bienvenue</h2>
        <p>Contenu de la page dans la maquette.</p>
    </div>
</x-daisy::ui.utilities.mockup-browser>';
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
