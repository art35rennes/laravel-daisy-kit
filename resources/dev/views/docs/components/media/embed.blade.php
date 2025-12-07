@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'media';
    $name = 'embed';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Intégration" 
    category="media" 
    name="embed"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Intégration" 
            subtitle="Intégration de contenu externe (vidéo, carte, etc.)."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="embed">
        <x-slot:preview>
            <x-daisy::ui.media.embed src="https://www.youtube.com/embed/dQw4w9WgXcQ" class="aspect-video w-full max-w-2xl" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.media.embed src="https://www.youtube.com/embed/dQw4w9WgXcQ" class="aspect-video w-full max-w-2xl" />';
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

    <x-daisy::docs.sections.variants name="embed">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Vidéo YouTube</p>
                    <x-daisy::ui.media.embed src="https://www.youtube.com/embed/dQw4w9WgXcQ" class="aspect-video w-full max-w-xl" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Carte Google Maps</p>
                    <x-daisy::ui.media.embed src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.184132576894!2d-73.98811768459399!3d40.758895979327!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus" class="aspect-video w-full max-w-xl" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Vidéo YouTube --}}
<x-daisy::ui.media.embed src="https://www.youtube.com/embed/..." class="aspect-video w-full max-w-xl" />

{{-- Carte Google Maps --}}
<x-daisy::ui.media.embed src="https://www.google.com/maps/embed?pb=..." class="aspect-video w-full max-w-xl" />';
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
