@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'media';
    $name = 'leaflet';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Carte" 
    category="media" 
    name="leaflet"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Carte" 
            subtitle="Carte interactive avec Leaflet.js pour afficher des emplacements géographiques."
            jsModule="leaflet"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="leaflet">
        <x-slot:preview>
            <div class="h-64 w-full">
                <x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="13" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="13" />';
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

    <x-daisy::docs.sections.variants name="leaflet">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Différentes villes</p>
                    <div class="h-48 w-full">
                        <x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="12" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Niveaux de zoom</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="h-32">
                            <x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="10" />
                        </div>
                        <div class="h-32">
                            <x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="15" />
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Différentes villes --}}
<x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="12" />

{{-- Niveaux de zoom --}}
<x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="10" />
<x-daisy::ui.media.leaflet :lat="48.8566" :lng="2.3522" zoom="15" />';
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
