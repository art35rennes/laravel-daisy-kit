@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'utilities';
    $name = 'mockup-code';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Maquette code" 
    category="utilities" 
    name="mockup-code"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Maquette code" 
            subtitle="Maquette d'éditeur de code."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="mockup-code">
        <x-slot:preview>
            <x-daisy::ui.utilities.mockup-code>
                <pre data-prefix="$"><code>php artisan serve</code></pre>
                <pre data-prefix=">" class="text-success"><code>Server started on http://127.0.0.1:8000</code></pre>
            </x-daisy::ui.utilities.mockup-code>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.utilities.mockup-code>
    <pre data-prefix="$"><code>php artisan serve</code></pre>
    <pre data-prefix=">" class="text-success"><code>Server started</code></pre>
</x-daisy::ui.utilities.mockup-code>';
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

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
