@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'fieldset';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Groupe de champs" 
    category="advanced" 
    name="fieldset"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Groupe de champs" 
            subtitle="Groupe de champs de formulaire avec légende."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="fieldset">
        <x-slot:preview>
            <x-daisy::ui.advanced.fieldset legend="Informations personnelles">
                <div class="space-y-2">
                    <x-daisy::ui.inputs.input name="name" placeholder="Nom" />
                    <x-daisy::ui.inputs.input name="email" type="email" placeholder="Email" />
                </div>
                <p class="label mt-2">Remplissez vos informations personnelles</p>
            </x-daisy::ui.advanced.fieldset>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.fieldset legend="Informations personnelles">
    <div class="space-y-2">
        <x-daisy::ui.inputs.input name="name" placeholder="Nom" />
        <x-daisy::ui.inputs.input name="email" type="email" placeholder="Email" />
    </div>
    <p class="label mt-2">Remplissez vos informations personnelles</p>
</x-daisy::ui.advanced.fieldset>';
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
