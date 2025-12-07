@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'list-row';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="List Row" 
    category="layout" 
    name="list-row"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="List Row" 
            subtitle="Ligne de liste avec distribution automatique des colonnes."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="list-row">
        <x-slot:preview>
            <ul class="list">
                <x-daisy::ui.layout.list-row>
                    <span>Nom</span>
                    <span>Valeur</span>
                </x-daisy::ui.layout.list-row>
                <x-daisy::ui.layout.list-row>
                    <span>Email</span>
                    <span>user@example.com</span>
                </x-daisy::ui.layout.list-row>
            </ul>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<ul class="list">
    <x-daisy::ui.layout.list-row>
        <span>Nom</span>
        <span>Valeur</span>
    </x-daisy::ui.layout.list-row>
    <x-daisy::ui.layout.list-row>
        <span>Email</span>
        <span>user@example.com</span>
    </x-daisy::ui.layout.list-row>
</ul>';
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

    <x-daisy::docs.sections.variants name="list-row">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Liste simple</p>
                    <ul class="list">
                        <x-daisy::ui.layout.list-row>
                            <span>Propriété 1</span>
                            <span>Valeur 1</span>
                        </x-daisy::ui.layout.list-row>
                        <x-daisy::ui.layout.list-row>
                            <span>Propriété 2</span>
                            <span>Valeur 2</span>
                        </x-daisy::ui.layout.list-row>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec actions</p>
                    <ul class="list">
                        <x-daisy::ui.layout.list-row>
                            <span>Item 1</span>
                            <x-daisy::ui.inputs.button size="sm">Action</x-daisy::ui.inputs.button>
                        </x-daisy::ui.layout.list-row>
                        <x-daisy::ui.layout.list-row>
                            <span>Item 2</span>
                            <x-daisy::ui.inputs.button size="sm">Action</x-daisy::ui.inputs.button>
                        </x-daisy::ui.layout.list-row>
                    </ul>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Liste simple --}}
<ul class="list">
    <x-daisy::ui.layout.list-row>
        <span>Propriété</span>
        <span>Valeur</span>
    </x-daisy::ui.layout.list-row>
</ul>

{{-- Avec actions --}}
<ul class="list">
    <x-daisy::ui.layout.list-row>
        <span>Item</span>
        <x-daisy::ui.inputs.button size="sm">Action</x-daisy::ui.inputs.button>
    </x-daisy::ui.layout.list-row>
</ul>';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$variantsCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
