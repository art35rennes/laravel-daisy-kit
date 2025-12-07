@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'overlay';
    $name = 'dropdown';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Menu déroulant" 
    category="overlay" 
    name="dropdown"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Menu déroulant" 
            subtitle="Menu déroulant."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="dropdown">
        <x-slot:preview>
            <x-daisy::ui.overlay.dropdown label="Menu">
                <ul class="menu bg-base-200 rounded-box w-52">
                    <li><a>Profil</a></li>
                    <li><a>Paramètres</a></li>
                    <li><a>Déconnexion</a></li>
                </ul>
            </x-daisy::ui.overlay.dropdown>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.overlay.dropdown label="Menu">
    <ul class="menu bg-base-200 rounded-box w-52">
        <li><a>Profil</a></li>
        <li><a>Paramètres</a></li>
        <li><a>Déconnexion</a></li>
    </ul>
</x-daisy::ui.overlay.dropdown>';
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
