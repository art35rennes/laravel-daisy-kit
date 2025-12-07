@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'feedback';
    $name = 'loading';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chargement" 
    category="feedback" 
    name="loading"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chargement" 
            subtitle="Indicateur de chargement."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="loading">
        <x-slot:preview>
            <x-daisy::ui.feedback.loading shape="spinner" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.feedback.loading shape="spinner" />';
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

    <x-daisy::docs.sections.variants name="loading">
        <x-slot:preview>
            <div class="flex flex-wrap items-center gap-3">
                <x-daisy::ui.feedback.loading shape="spinner" color="primary" />
                <x-daisy::ui.feedback.loading shape="spinner" color="secondary" />
                <x-daisy::ui.feedback.loading shape="spinner" size="sm" />
                <x-daisy::ui.feedback.loading shape="spinner" size="lg" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '<x-daisy::ui.feedback.loading shape="spinner" color="primary" />
<x-daisy::ui.feedback.loading shape="spinner" color="secondary" />
<x-daisy::ui.feedback.loading shape="spinner" size="sm" />
<x-daisy::ui.feedback.loading shape="spinner" size="lg" />';
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
