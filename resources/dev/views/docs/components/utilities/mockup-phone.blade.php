@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'mockup-phone';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Maquette téléphone" 
    category="utilities" 
    name="mockup-phone"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Maquette téléphone" 
            subtitle="Maquette de téléphone mobile."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="mockup-phone">
        <x-slot:preview>
            <x-daisy::ui.utilities.mockup-phone>
                <div class="mockup-phone-display">
                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2">Application</h3>
                        <p class="text-sm">Contenu de l'application mobile</p>
                    </div>
                </div>
            </x-daisy::ui.utilities.mockup-phone>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.mockup-phone>
    <div class="mockup-phone-display">
        <div class="p-4">
            <h3 class="text-lg font-bold mb-2">Application</h3>
            <p class="text-sm">Contenu de l\'application mobile</p>
        </div>
    </div>
</x-daisy::ui.utilities.mockup-phone>';
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
