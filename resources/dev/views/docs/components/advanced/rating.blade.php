@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'rating';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Note" 
    category="advanced" 
    name="rating"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Note" 
            subtitle="Système de notation avec étoiles."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="rating">
        <x-slot:preview>
            <x-daisy::ui.advanced.rating name="rating" :count="5" :value="4" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.rating name="rating" :count="5" :value="4" />';
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

    <x-daisy::docs.sections.variants name="rating">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.rating name="r1" :count="5" :value="3" color="primary" />
                        <x-daisy::ui.advanced.rating name="r2" :count="5" :value="3" color="secondary" />
                        <x-daisy::ui.advanced.rating name="r3" :count="5" :value="3" color="accent" />
                        <x-daisy::ui.advanced.rating name="r4" :count="5" :value="3" color="warning" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.rating name="r5" :count="5" :value="3" size="xs" />
                        <x-daisy::ui.advanced.rating name="r6" :count="5" :value="3" size="sm" />
                        <x-daisy::ui.advanced.rating name="r7" :count="5" :value="3" size="md" />
                        <x-daisy::ui.advanced.rating name="r8" :count="5" :value="3" size="lg" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Demi-étoiles</p>
                    <x-daisy::ui.advanced.rating name="r9" :count="5" :value="3.5" half />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.advanced.rating name="rating1" :count="5" :value="3" color="primary" />
<x-daisy::ui.advanced.rating name="rating2" :count="5" :value="3" color="accent" />

{{-- Tailles --}}
<x-daisy::ui.advanced.rating name="rating3" :count="5" :value="3" size="sm" />
<x-daisy::ui.advanced.rating name="rating4" :count="5" :value="3" size="lg" />

{{-- Demi-étoiles --}}
<x-daisy::ui.advanced.rating name="rating5" :count="5" :value="3.5" half />';
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
