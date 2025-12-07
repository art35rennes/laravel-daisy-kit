@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'list';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Liste" 
    category="layout" 
    name="list"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Liste" 
            subtitle="Liste d'éléments avec styles daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="list">
        <x-slot:preview>
            <x-daisy::ui.layout.list>
                <li class="list-row">
                    <span>Jean Dupont</span>
                    <span>jean@example.com</span>
                </li>
                <li class="list-row">
                    <span>Marie Martin</span>
                    <span>marie@example.com</span>
                </li>
                <li class="list-row">
                    <span>Pierre Durand</span>
                    <span>pierre@example.com</span>
                </li>
            </x-daisy::ui.layout.list>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.list>
    <li class="list-row">
        <span>Jean Dupont</span>
        <span>jean@example.com</span>
    </li>
    <li class="list-row">
        <span>Marie Martin</span>
        <span>marie@example.com</span>
    </li>
</x-daisy::ui.layout.list>';
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
