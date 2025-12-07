@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'errors';
    $name = 'error-header';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Error Header" 
    category="errors" 
    name="error-header"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Error Header" 
            subtitle="En-tête de page d'erreur affichant le code de statut HTTP."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="error-header">
        <x-slot:preview>
            <x-daisy::ui.errors.error-header statusCode="500" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.errors.error-header statusCode="500" />';
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

    <x-daisy::docs.sections.variants name="error-header">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Codes de statut</p>
                    <div class="space-y-2">
                        <x-daisy::ui.errors.error-header statusCode="404" />
                        <x-daisy::ui.errors.error-header statusCode="500" />
                        <x-daisy::ui.errors.error-header statusCode="403" />
                        <x-daisy::ui.errors.error-header statusCode="503" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Codes de statut --}}
<x-daisy::ui.errors.error-header statusCode="404" />
<x-daisy::ui.errors.error-header statusCode="500" />
<x-daisy::ui.errors.error-header statusCode="403" />
<x-daisy::ui.errors.error-header statusCode="503" />';
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
