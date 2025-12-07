@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'mockup-window';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Maquette fenêtre" 
    category="utilities" 
    name="mockup-window"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Maquette fenêtre" 
            subtitle="Maquette de fenêtre système."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="mockup-window">
        <x-slot:preview>
            <x-daisy::ui.utilities.mockup-window>
                <div class="p-4">
                    <h3 class="text-lg font-bold mb-2">Application</h3>
                    <p>Contenu de la fenêtre système</p>
                </div>
            </x-daisy::ui.utilities.mockup-window>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.mockup-window>
    <div class="p-4">
        <h3 class="text-lg font-bold mb-2">Application</h3>
        <p>Contenu de la fenêtre système</p>
    </div>
</x-daisy::ui.utilities.mockup-window>';
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
