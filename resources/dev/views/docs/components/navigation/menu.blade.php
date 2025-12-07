@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'menu';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Menu" 
    category="navigation" 
    name="menu"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Menu" 
            subtitle="Menu de navigation vertical ou horizontal."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="menu">
        <x-slot:preview>
            <x-daisy::ui.navigation.menu>
                <li><a href="/dashboard">Tableau de bord</a></li>
                <li><a href="/users">Utilisateurs</a></li>
                <li><a href="/settings">Paramètres</a></li>
            </x-daisy::ui.navigation.menu>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.navigation.menu>
    <li><a href="/dashboard">Tableau de bord</a></li>
    <li><a href="/users">Utilisateurs</a></li>
    <li><a href="/settings">Paramètres</a></li>
</x-daisy::ui.navigation.menu>';
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

    <x-daisy::docs.sections.variants name="menu">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Vertical (par défaut)</p>
                    <x-daisy::ui.navigation.menu class="bg-base-200 rounded-box w-56">
                        <li><a>Item 1</a></li>
                        <li><a>Item 2</a></li>
                        <li><a>Item 3</a></li>
                    </x-daisy::ui.navigation.menu>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Horizontal</p>
                    <x-daisy::ui.navigation.menu horizontal class="bg-base-200 rounded-box">
                        <li><a>Item 1</a></li>
                        <li><a>Item 2</a></li>
                        <li><a>Item 3</a></li>
                    </x-daisy::ui.navigation.menu>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="space-y-2">
                        <x-daisy::ui.navigation.menu size="xs" class="bg-base-200 rounded-box w-56">
                            <li><a>Extra Small</a></li>
                        </x-daisy::ui.navigation.menu>
                        <x-daisy::ui.navigation.menu size="sm" class="bg-base-200 rounded-box w-56">
                            <li><a>Small</a></li>
                        </x-daisy::ui.navigation.menu>
                        <x-daisy::ui.navigation.menu size="lg" class="bg-base-200 rounded-box w-56">
                            <li><a>Large</a></li>
                        </x-daisy::ui.navigation.menu>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Vertical (par défaut) --}}
<x-daisy::ui.navigation.menu>
    <li><a>Item 1</a></li>
    <li><a>Item 2</a></li>
</x-daisy::ui.navigation.menu>

{{-- Horizontal --}}
<x-daisy::ui.navigation.menu horizontal>
    <li><a>Item 1</a></li>
    <li><a>Item 2</a></li>
</x-daisy::ui.navigation.menu>

{{-- Tailles --}}
<x-daisy::ui.navigation.menu size="sm">
    <li><a>Small</a></li>
</x-daisy::ui.navigation.menu>
<x-daisy::ui.navigation.menu size="lg">
    <li><a>Large</a></li>
</x-daisy::ui.navigation.menu>';
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
                height="350px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
