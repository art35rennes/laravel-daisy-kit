@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'badge';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Badge" 
    category="data-display" 
    name="badge"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Badge" 
            subtitle="Badge pour afficher des informations."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="badge">
        <x-slot:preview>
            <x-daisy::ui.data-display.badge color="success">Nouveau</x-daisy::ui.data-display.badge>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.data-display.badge color="success">Nouveau</x-daisy::ui.data-display.badge>';
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

    <x-daisy::docs.sections.variants name="badge">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-daisy::ui.data-display.badge color="primary">Primary</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge color="secondary">Secondary</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge color="accent">Accent</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge color="info">Info</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge color="success">Success</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge color="warning">Warning</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge color="error">Error</x-daisy::ui.data-display.badge>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-daisy::ui.data-display.badge variant="outline" color="primary">Outline</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge variant="ghost" color="primary">Ghost</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge variant="soft" color="primary">Soft</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge variant="dash" color="primary">Dash</x-daisy::ui.data-display.badge>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-daisy::ui.data-display.badge size="xs">XS</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge size="sm">SM</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge size="md">MD</x-daisy::ui.data-display.badge>
                        <x-daisy::ui.data-display.badge size="lg">LG</x-daisy::ui.data-display.badge>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.data-display.badge color="primary">Primary</x-daisy::ui.data-display.badge>
<x-daisy::ui.data-display.badge color="success">Success</x-daisy::ui.data-display.badge>
<x-daisy::ui.data-display.badge color="error">Error</x-daisy::ui.data-display.badge>

{{-- Styles --}}
<x-daisy::ui.data-display.badge variant="outline" color="primary">Outline</x-daisy::ui.data-display.badge>
<x-daisy::ui.data-display.badge variant="ghost" color="primary">Ghost</x-daisy::ui.data-display.badge>

{{-- Tailles --}}
<x-daisy::ui.data-display.badge size="sm">Small</x-daisy::ui.data-display.badge>
<x-daisy::ui.data-display.badge size="lg">Large</x-daisy::ui.data-display.badge>';
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
