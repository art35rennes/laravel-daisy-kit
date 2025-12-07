@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'errors';
    $name = 'loading-state-content';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Loading State Content" 
    category="errors" 
    name="loading-state-content"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Loading State Content" 
            subtitle="Contenu d'état de chargement pour les pages en cours de chargement."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="loading-state-content">
        <x-slot:preview>
            <x-daisy::ui.errors.loading-state-content type="spinner" message="Chargement en cours..." />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.errors.loading-state-content type="spinner" message="Chargement en cours..." />';
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

    <x-daisy::docs.sections.variants name="loading-state-content">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Types d'indicateur</p>
                    <div class="space-y-2">
                        <x-daisy::ui.errors.loading-state-content type="spinner" message="Spinner" />
                        <x-daisy::ui.errors.loading-state-content type="dots" message="Dots" />
                        <x-daisy::ui.errors.loading-state-content type="ring" message="Ring" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.errors.loading-state-content type="spinner" message="Small" size="sm" />
                        <x-daisy::ui.errors.loading-state-content type="spinner" message="Medium" size="md" />
                        <x-daisy::ui.errors.loading-state-content type="spinner" message="Large" size="lg" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Types d\'indicateur --}}
<x-daisy::ui.errors.loading-state-content type="spinner" message="Spinner" />
<x-daisy::ui.errors.loading-state-content type="dots" message="Dots" />
<x-daisy::ui.errors.loading-state-content type="ring" message="Ring" />

{{-- Tailles --}}
<x-daisy::ui.errors.loading-state-content type="spinner" message="Small" size="sm" />
<x-daisy::ui.errors.loading-state-content type="spinner" message="Large" size="lg" />';
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
