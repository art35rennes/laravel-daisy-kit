@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'file-preview';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="File Preview" 
    category="data-display" 
    name="file-preview"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="File Preview" 
            subtitle="Aperçu de fichier avec support pour images, documents et autres types de fichiers."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="file-preview">
        <x-slot:preview>
            <x-daisy::ui.data-display.file-preview 
                url="https://picsum.photos/400/300" 
                name="image.jpg" 
                type="image" 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.file-preview 
    url="https://picsum.photos/400/300" 
    name="image.jpg" 
    type="image" 
/>';
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

    <x-daisy::docs.sections.variants name="file-preview">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Types de fichiers</p>
                    <div class="flex flex-wrap gap-3">
                        <x-daisy::ui.data-display.file-preview 
                            url="https://picsum.photos/200/200" 
                            name="photo.jpg" 
                            type="image" 
                        />
                        <x-daisy::ui.data-display.file-preview 
                            url="#" 
                            name="document.pdf" 
                            type="pdf" 
                        />
                        <x-daisy::ui.data-display.file-preview 
                            url="#" 
                            name="video.mp4" 
                            type="video" 
                        />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.data-display.file-preview 
                            url="https://picsum.photos/150/150" 
                            name="small.jpg" 
                            type="image"
                            size="sm"
                        />
                        <x-daisy::ui.data-display.file-preview 
                            url="https://picsum.photos/200/200" 
                            name="medium.jpg" 
                            type="image"
                            size="md"
                        />
                        <x-daisy::ui.data-display.file-preview 
                            url="https://picsum.photos/300/300" 
                            name="large.jpg" 
                            type="image"
                            size="lg"
                        />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Types de fichiers --}}
<x-daisy::ui.data-display.file-preview 
    url="image.jpg" 
    name="photo.jpg" 
    type="image" 
/>
<x-daisy::ui.data-display.file-preview 
    url="document.pdf" 
    name="document.pdf" 
    type="pdf" 
/>

{{-- Tailles --}}
<x-daisy::ui.data-display.file-preview 
    url="image.jpg" 
    name="small.jpg" 
    type="image"
    size="sm"
/>
<x-daisy::ui.data-display.file-preview 
    url="image.jpg" 
    name="large.jpg" 
    type="image"
    size="lg"
/>';
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
