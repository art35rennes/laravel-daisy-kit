@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'link';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Lien" 
    category="advanced" 
    name="link"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Lien" 
            subtitle="Lien stylisé avec underline."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="link">
        <x-slot:preview>
            <x-daisy::ui.advanced.link href="/about">En savoir plus</x-daisy::ui.advanced.link>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.link href="/about">En savoir plus</x-daisy::ui.advanced.link>';
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

    <x-daisy::docs.sections.variants name="link">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.link href="#" color="primary">Primary</x-daisy::ui.advanced.link>
                        <x-daisy::ui.advanced.link href="#" color="secondary">Secondary</x-daisy::ui.advanced.link>
                        <x-daisy::ui.advanced.link href="#" color="accent">Accent</x-daisy::ui.advanced.link>
                        <x-daisy::ui.advanced.link href="#" color="success">Success</x-daisy::ui.advanced.link>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Hover</p>
                    <x-daisy::ui.advanced.link href="#" hover>Lien avec effet hover</x-daisy::ui.advanced.link>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.advanced.link href="#" color="primary">Primary</x-daisy::ui.advanced.link>
<x-daisy::ui.advanced.link href="#" color="accent">Accent</x-daisy::ui.advanced.link>

{{-- Hover --}}
<x-daisy::ui.advanced.link href="#" hover>Lien avec effet hover</x-daisy::ui.advanced.link>';
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
