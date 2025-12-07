@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'collapse';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Repliable" 
    category="advanced" 
    name="collapse"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Repliable" 
            subtitle="Contenu repliable."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="collapse">
        <x-slot:preview>
            <x-daisy::ui.advanced.collapse title="Cliquez pour développer">
                <p>Contenu masqué qui s'affiche au clic. Vous pouvez mettre n'importe quel contenu ici.</p>
            </x-daisy::ui.advanced.collapse>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.collapse title="Cliquez pour développer">
    <p>Contenu masqué qui s\'affiche au clic.</p>
</x-daisy::ui.advanced.collapse>';
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

    <x-daisy::docs.sections.variants name="collapse">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Avec icône flèche</p>
                    <x-daisy::ui.advanced.collapse title="Section avec flèche" icon="arrow">
                        <p>Contenu avec icône flèche.</p>
                    </x-daisy::ui.advanced.collapse>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec icône plus</p>
                    <x-daisy::ui.advanced.collapse title="Section avec plus" icon="plus">
                        <p>Contenu avec icône plus.</p>
                    </x-daisy::ui.advanced.collapse>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Ouvert par défaut</p>
                    <x-daisy::ui.advanced.collapse title="Section ouverte" open>
                        <p>Cette section est ouverte par défaut.</p>
                    </x-daisy::ui.advanced.collapse>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Avec icône flèche --}}
<x-daisy::ui.advanced.collapse title="Section avec flèche" icon="arrow">
    <p>Contenu avec icône flèche.</p>
</x-daisy::ui.advanced.collapse>

{{-- Avec icône plus --}}
<x-daisy::ui.advanced.collapse title="Section avec plus" icon="plus">
    <p>Contenu avec icône plus.</p>
</x-daisy::ui.advanced.collapse>

{{-- Ouvert par défaut --}}
<x-daisy::ui.advanced.collapse title="Section ouverte" open>
    <p>Cette section est ouverte par défaut.</p>
</x-daisy::ui.advanced.collapse>';
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
