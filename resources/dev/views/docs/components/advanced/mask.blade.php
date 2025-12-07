@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'mask';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Masque" 
    category="advanced" 
    name="mask"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Masque" 
            subtitle="Masque pour appliquer des formes aux images."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="mask">
        <x-slot:preview>
            <x-daisy::ui.advanced.mask shape="squircle" src="https://picsum.photos/200/200" alt="Image" class="w-32 h-32" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.mask shape="squircle" src="https://picsum.photos/200/200" alt="Image" class="w-32 h-32" />';
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

    <x-daisy::docs.sections.variants name="mask">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Formes</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.mask shape="squircle" src="https://picsum.photos/100/100" alt="Squircle" class="w-20 h-20" />
                        <x-daisy::ui.advanced.mask shape="heart" src="https://picsum.photos/100/100" alt="Heart" class="w-20 h-20" />
                        <x-daisy::ui.advanced.mask shape="hexagon" src="https://picsum.photos/100/100" alt="Hexagon" class="w-20 h-20" />
                        <x-daisy::ui.advanced.mask shape="star" src="https://picsum.photos/100/100" alt="Star" class="w-20 h-20" />
                        <x-daisy::ui.advanced.mask shape="triangle" src="https://picsum.photos/100/100" alt="Triangle" class="w-20 h-20" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Demi-masques</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.mask shape="squircle" src="https://picsum.photos/100/100" alt="Half 1" class="w-20 h-20" half="1" />
                        <x-daisy::ui.advanced.mask shape="squircle" src="https://picsum.photos/100/100" alt="Half 2" class="w-20 h-20" half="2" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Formes --}}
<x-daisy::ui.advanced.mask shape="squircle" src="..." alt="Squircle" class="w-20 h-20" />
<x-daisy::ui.advanced.mask shape="heart" src="..." alt="Heart" class="w-20 h-20" />
<x-daisy::ui.advanced.mask shape="hexagon" src="..." alt="Hexagon" class="w-20 h-20" />
<x-daisy::ui.advanced.mask shape="star" src="..." alt="Star" class="w-20 h-20" />

{{-- Demi-masques --}}
<x-daisy::ui.advanced.mask shape="squircle" src="..." alt="Half 1" class="w-20 h-20" half="1" />
<x-daisy::ui.advanced.mask shape="squircle" src="..." alt="Half 2" class="w-20 h-20" half="2" />';
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
