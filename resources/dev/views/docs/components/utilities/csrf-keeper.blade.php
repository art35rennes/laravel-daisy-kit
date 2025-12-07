@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'csrf-keeper';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="CSRF Keeper" 
    category="utilities" 
    name="csrf-keeper"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="CSRF Keeper" 
            subtitle="Maintient le token CSRF à jour pour les requêtes AJAX."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="csrf-keeper">
        <x-slot:preview>
            <div class="p-4 bg-base-200 rounded-box">
                <p class="text-sm mb-2">Le composant CSRF Keeper maintient automatiquement le token CSRF à jour.</p>
                <x-daisy::ui.utilities.csrf-keeper />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.csrf-keeper />';
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
