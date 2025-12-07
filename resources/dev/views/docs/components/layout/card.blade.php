@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'card';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'slots', 'label' => 'Slots'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Carte" 
    category="layout" 
    name="card"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Carte" 
            subtitle="Carte pour afficher du contenu groupé."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="card">
        <x-slot:preview>
            <x-daisy::ui.layout.card title="Produit Premium" imageUrl="https://picsum.photos/400/300">
                <p>Description du produit avec toutes ses caractéristiques.</p>
                <x-slot:actions>
                    <x-daisy::ui.inputs.button>Acheter</x-daisy::ui.inputs.button>
                </x-slot:actions>
            </x-daisy::ui.layout.card>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.card title="Produit Premium" imageUrl="https://picsum.photos/400/300">
    <p>Description du produit avec toutes ses caractéristiques.</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button>Acheter</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.layout.card>';
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

    <x-daisy::docs.sections.variants name="card">
        <x-slot:preview>
            <div class="space-y-6">
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="grid grid-cols-3 gap-4">
                        <x-daisy::ui.layout.card size="xs" title="Extra Small">Contenu compact</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card size="sm" title="Small">Petite carte</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card size="md" title="Medium">Carte standard</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card size="lg" title="Large">Grande carte</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card size="xl" title="Extra Large">Très grande carte</x-daisy::ui.layout.card>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    <div class="grid grid-cols-2 gap-4">
                        <x-daisy::ui.layout.card :bordered="true" title="Avec bordure">Card avec bordure visible</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card :dash="true" title="Style dash">Card avec style dash</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card :compact="true" title="Compact">Card compacte (moins d'espacement)</x-daisy::ui.layout.card>
                        <x-daisy::ui.layout.card :side="true" imageUrl="https://picsum.photos/200/300" title="Side layout">Card avec image sur le côté</x-daisy::ui.layout.card>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Tailles --}}
<x-daisy::ui.layout.card size="sm" title="Small">Contenu</x-daisy::ui.layout.card>
<x-daisy::ui.layout.card size="lg" title="Large">Contenu</x-daisy::ui.layout.card>

{{-- Styles --}}
<x-daisy::ui.layout.card :bordered="true" title="Avec bordure">Contenu</x-daisy::ui.layout.card>
<x-daisy::ui.layout.card :compact="true" title="Compact">Contenu</x-daisy::ui.layout.card>
<x-daisy::ui.layout.card :side="true" imageUrl="..." title="Side">Contenu</x-daisy::ui.layout.card>';
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

    <x-daisy::docs.sections.custom id="slots" title="Slots disponibles">
        <p class="text-sm text-base-content/70 mb-4">Le composant card supporte plusieurs slots pour personnaliser le contenu.</p>
        <div class="tabs tabs-box">
            <input type="radio" name="slots-example-card" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <x-daisy::ui.layout.card>
                    <x-slot:figure>
                        <img src="https://picsum.photos/400/200" alt="Image" />
                    </x-slot:figure>
                    <h2 class="card-title">Titre de la carte</h2>
                    <p>Contenu principal de la carte avec description détaillée.</p>
                    <x-slot:actions>
                        <x-daisy::ui.inputs.button color="primary">Action 1</x-daisy::ui.inputs.button>
                        <x-daisy::ui.inputs.button variant="ghost">Action 2</x-daisy::ui.inputs.button>
                    </x-slot:actions>
                </x-daisy::ui.layout.card>
            </div>
            <input type="radio" name="slots-example-card" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $slotsCode = '<x-daisy::ui.layout.card>
    <x-slot:figure>
        <img src="image.jpg" alt="Image" />
    </x-slot:figure>
    <h2 class="card-title">Titre</h2>
    <p>Contenu principal</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button>Action</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.layout.card>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$slotsCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="250px"
                />
            </div>
        </div>
    </x-daisy::docs.sections.custom>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
