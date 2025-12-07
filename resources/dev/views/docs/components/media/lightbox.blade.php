@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'media';
    $name = 'lightbox';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Visionneuse" 
    category="media" 
    name="lightbox"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Visionneuse" 
            subtitle="Galerie d'images avec lightbox."
            jsModule="lightbox"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="lightbox">
        <x-slot:preview>
            <div class="grid grid-cols-3 gap-2 max-w-md">
                <x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=1" alt="Photo 1" class="w-full h-24 object-cover rounded-box" />
                <x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=2" alt="Photo 2" class="w-full h-24 object-cover rounded-box" />
                <x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=3" alt="Photo 3" class="w-full h-24 object-cover rounded-box" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=1" alt="Photo 1" class="w-full h-24 object-cover rounded-box" />';
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

    <x-daisy::docs.sections.variants name="lightbox">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Galerie</p>
                    <div class="grid grid-cols-3 gap-2 max-w-md">
                        <x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=1" alt="Photo 1" gallery="gallery1" class="w-full h-24 object-cover rounded-box" />
                        <x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=2" alt="Photo 2" gallery="gallery1" class="w-full h-24 object-cover rounded-box" />
                        <x-daisy::ui.media.lightbox src="https://picsum.photos/400/300?random=3" alt="Photo 3" gallery="gallery1" class="w-full h-24 object-cover rounded-box" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Galerie --}}
<div class="grid grid-cols-3 gap-2">
    <x-daisy::ui.media.lightbox src="..." alt="Photo 1" gallery="gallery1" class="w-full h-24 object-cover rounded-box" />
    <x-daisy::ui.media.lightbox src="..." alt="Photo 2" gallery="gallery1" class="w-full h-24 object-cover rounded-box" />
    <x-daisy::ui.media.lightbox src="..." alt="Photo 3" gallery="gallery1" class="w-full h-24 object-cover rounded-box" />
</div>';
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
