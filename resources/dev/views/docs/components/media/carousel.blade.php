@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'media';
    $name = 'carousel';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Carrousel" 
    category="media" 
    name="carousel"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Carrousel" 
            subtitle="Carrousel d'images ou de contenu."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="carousel">
        <x-slot:preview>
            <x-daisy::ui.media.carousel class="w-full max-w-xl">
                <div class="carousel-item">
                    <img src="https://picsum.photos/800/400?random=1" alt="Slide 1" class="w-full" />
                </div>
                <div class="carousel-item">
                    <img src="https://picsum.photos/800/400?random=2" alt="Slide 2" class="w-full" />
                </div>
                <div class="carousel-item">
                    <img src="https://picsum.photos/800/400?random=3" alt="Slide 3" class="w-full" />
                </div>
            </x-daisy::ui.media.carousel>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.media.carousel class="w-full max-w-xl">
    <div class="carousel-item">
        <img src="https://picsum.photos/800/400?random=1" alt="Slide 1" class="w-full" />
    </div>
    <div class="carousel-item">
        <img src="https://picsum.photos/800/400?random=2" alt="Slide 2" class="w-full" />
    </div>
    <div class="carousel-item">
        <img src="https://picsum.photos/800/400?random=3" alt="Slide 3" class="w-full" />
    </div>
</x-daisy::ui.media.carousel>';
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="carousel">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Vertical</p>
                    <x-daisy::ui.media.carousel vertical class="h-64 w-48">
                        <div class="carousel-item">
                            <img src="https://picsum.photos/200/300?random=1" alt="Slide 1" class="w-full h-full object-cover" />
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/200/300?random=2" alt="Slide 2" class="w-full h-full object-cover" />
                        </div>
                        <div class="carousel-item">
                            <img src="https://picsum.photos/200/300?random=3" alt="Slide 3" class="w-full h-full object-cover" />
                        </div>
                    </x-daisy::ui.media.carousel>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec contenu personnalisé</p>
                    <x-daisy::ui.media.carousel class="w-full max-w-xl">
                        <div class="carousel-item bg-primary text-primary-content flex items-center justify-center">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold">Slide 1</h3>
                                <p>Contenu personnalisé</p>
                            </div>
                        </div>
                        <div class="carousel-item bg-secondary text-secondary-content flex items-center justify-center">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold">Slide 2</h3>
                                <p>Contenu personnalisé</p>
                            </div>
                        </div>
                    </x-daisy::ui.media.carousel>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Vertical --}}
<x-daisy::ui.media.carousel vertical class="h-64 w-48">
    <div class="carousel-item">
        <img src="..." alt="Slide 1" class="w-full h-full object-cover" />
    </div>
</x-daisy::ui.media.carousel>

{{-- Avec contenu personnalisé --}}
<x-daisy::ui.media.carousel class="w-full max-w-xl">
    <div class="carousel-item bg-primary text-primary-content flex items-center justify-center">
        <div class="text-center">
            <h3 class="text-2xl font-bold">Slide 1</h3>
            <p>Contenu personnalisé</p>
        </div>
    </div>
</x-daisy::ui.media.carousel>';
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
