@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'scroll-status';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Statut de défilement" 
    category="advanced" 
    name="scroll-status"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Statut de défilement" 
            subtitle="Indicateur de progression du défilement de la page."
            jsModule="scroll-status"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="scroll-status">
        <x-slot:preview>
            <div class="p-4 bg-base-200 rounded-box">
                <p class="text-sm mb-2">L'indicateur de défilement apparaît en haut de la page lors du scroll.</p>
                <x-daisy::ui.advanced.scroll-status />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.scroll-status />';
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

    <x-daisy::docs.sections.variants name="scroll-status">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap gap-2">
                        <x-daisy::ui.advanced.scroll-status color="primary" />
                        <x-daisy::ui.advanced.scroll-status color="secondary" />
                        <x-daisy::ui.advanced.scroll-status color="accent" />
                        <x-daisy::ui.advanced.scroll-status color="success" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.advanced.scroll-status color="primary" />
<x-daisy::ui.advanced.scroll-status color="secondary" />
<x-daisy::ui.advanced.scroll-status color="accent" />';
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
                height="200px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
