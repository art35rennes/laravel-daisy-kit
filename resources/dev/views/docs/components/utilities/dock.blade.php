@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'dock';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Barre d'ancrage" 
    category="utilities" 
    name="dock"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Barre d'ancrage" 
            subtitle="Barre de navigation fixée en bas de l'écran."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="dock">
        <x-slot:preview>
            <x-daisy::ui.utilities.dock>
                <button class="btn btn-ghost">
                    <x-daisy::ui.advanced.icon name="house" />
                    <span class="dock-label">Accueil</span>
                </button>
                <button class="btn btn-ghost">
                    <x-daisy::ui.advanced.icon name="search" />
                    <span class="dock-label">Recherche</span>
                </button>
                <button class="btn btn-ghost">
                    <x-daisy::ui.advanced.icon name="heart" />
                    <span class="dock-label">Favoris</span>
                </button>
            </x-daisy::ui.utilities.dock>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.dock>
    <button class="btn btn-ghost">
        <x-daisy::ui.advanced.icon name="house" />
        <span class="dock-label">Accueil</span>
    </button>
    <button class="btn btn-ghost">
        <x-daisy::ui.advanced.icon name="search" />
        <span class="dock-label">Recherche</span>
    </button>
</x-daisy::ui.utilities.dock>';
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

    <x-daisy::docs.sections.variants name="dock">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.utilities.dock size="xs">
                            <button class="btn btn-ghost btn-sm">XS</button>
                            <button class="btn btn-ghost btn-sm">XS</button>
                        </x-daisy::ui.utilities.dock>
                        <x-daisy::ui.utilities.dock size="sm">
                            <button class="btn btn-ghost btn-sm">SM</button>
                            <button class="btn btn-ghost btn-sm">SM</button>
                        </x-daisy::ui.utilities.dock>
                        <x-daisy::ui.utilities.dock size="lg">
                            <button class="btn btn-ghost">LG</button>
                            <button class="btn btn-ghost">LG</button>
                        </x-daisy::ui.utilities.dock>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec état actif</p>
                    <x-daisy::ui.utilities.dock>
                        <button class="btn btn-ghost">Inactif</button>
                        <button class="btn btn-ghost dock-active">Actif</button>
                        <button class="btn btn-ghost">Inactif</button>
                    </x-daisy::ui.utilities.dock>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Tailles --}}
<x-daisy::ui.utilities.dock size="sm">
    <button class="btn btn-ghost btn-sm">SM</button>
</x-daisy::ui.utilities.dock>

<x-daisy::ui.utilities.dock size="lg">
    <button class="btn btn-ghost">LG</button>
</x-daisy::ui.utilities.dock>

{{-- Avec état actif --}}
<x-daisy::ui.utilities.dock>
    <button class="btn btn-ghost">Inactif</button>
    <button class="btn btn-ghost dock-active">Actif</button>
</x-daisy::ui.utilities.dock>';
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
