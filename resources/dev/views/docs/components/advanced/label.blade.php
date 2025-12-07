@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'label';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Étiquette" 
    category="advanced" 
    name="label"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Étiquette" 
            subtitle="Étiquette pour les champs de formulaire."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="label">
        <x-slot:preview>
            <div class="space-y-2">
                <x-daisy::ui.advanced.label for="email">Adresse email</x-daisy::ui.advanced.label>
                <x-daisy::ui.inputs.input type="email" id="email" placeholder="exemple@email.com" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.label for="email">Adresse email</x-daisy::ui.advanced.label>
<x-daisy::ui.inputs.input type="email" id="email" placeholder="exemple@email.com" />';
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
