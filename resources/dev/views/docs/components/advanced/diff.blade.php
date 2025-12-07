@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'diff';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Différence" 
    category="advanced" 
    name="diff"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Différence" 
            subtitle="Comparaison côte à côte de deux éléments avec redimensionnement."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="diff">
        <x-slot:preview>
            <x-daisy::ui.advanced.diff class="aspect-video">
                <div class="diff-item-1 bg-primary text-primary-content flex items-center justify-center">
                    <p class="text-lg font-bold">Version avant</p>
                </div>
                <div class="diff-item-2 bg-secondary text-secondary-content flex items-center justify-center">
                    <p class="text-lg font-bold">Version après</p>
                </div>
                <div class="diff-resizer"></div>
            </x-daisy::ui.advanced.diff>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.diff class="aspect-video">
    <div class="diff-item-1 bg-primary text-primary-content flex items-center justify-center">
        <p class="text-lg font-bold">Version avant</p>
    </div>
    <div class="diff-item-2 bg-secondary text-secondary-content flex items-center justify-center">
        <p class="text-lg font-bold">Version après</p>
    </div>
    <div class="diff-resizer"></div>
</x-daisy::ui.advanced.diff>';
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

    <x-daisy::docs.sections.variants name="diff">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Avec images</p>
                    <x-daisy::ui.advanced.diff class="aspect-video">
                        <div class="diff-item-1">
                            <img src="https://picsum.photos/800/600?random=1" alt="Avant" class="w-full h-full object-cover" />
                        </div>
                        <div class="diff-item-2">
                            <img src="https://picsum.photos/800/600?random=2" alt="Après" class="w-full h-full object-cover" />
                        </div>
                        <div class="diff-resizer"></div>
                    </x-daisy::ui.advanced.diff>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '<x-daisy::ui.advanced.diff class="aspect-video">
    <div class="diff-item-1">
        <img src="..." alt="Avant" class="w-full h-full object-cover" />
    </div>
    <div class="diff-item-2">
        <img src="..." alt="Après" class="w-full h-full object-cover" />
    </div>
    <div class="diff-resizer"></div>
</x-daisy::ui.advanced.diff>';
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
