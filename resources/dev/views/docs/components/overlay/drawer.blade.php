@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'overlay';
    $name = 'drawer';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Tiroir" 
    category="overlay" 
    name="drawer"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Tiroir" 
            subtitle="Tiroir latéral pour la navigation."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="drawer">
        <x-slot:preview>
            <x-daisy::ui.overlay.drawer id="nav-drawer">
                <x-slot:content>
                    <label for="nav-drawer" class="btn btn-primary drawer-button">Ouvrir le tiroir</label>
                    <h1 class="mt-4">Contenu principal</h1>
                </x-slot:content>
                <x-slot:side>
                    <ul class="menu p-4 w-80 min-h-full bg-base-200">
                        <li><a>Accueil</a></li>
                        <li><a>Profil</a></li>
                        <li><a>Paramètres</a></li>
                    </ul>
                </x-slot:side>
            </x-daisy::ui.overlay.drawer>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.overlay.drawer id="nav-drawer">
    <x-slot:content>
        <label for="nav-drawer" class="btn btn-primary drawer-button">
            Ouvrir le tiroir
        </label>
        <h1>Contenu principal</h1>
    </x-slot:content>
    <x-slot:side>
        <ul class="menu p-4 w-80 min-h-full bg-base-200">
            <li><a>Accueil</a></li>
            <li><a>Profil</a></li>
        </ul>
    </x-slot:side>
</x-daisy::ui.overlay.drawer>';
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
