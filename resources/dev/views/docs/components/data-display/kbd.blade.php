@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'data-display';
    $name = 'kbd';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Raccourci clavier" 
    category="data-display" 
    name="kbd"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Raccourci clavier" 
            subtitle="Affichage de raccourcis clavier."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="kbd">
        <x-slot:preview>
            <div class="space-y-2">
                @php $keys = ['Ctrl', 'K']; @endphp
                <x-daisy::ui.data-display.kbd :keys="$keys" />
                <x-daisy::ui.data-display.kbd>Esc</x-daisy::ui.data-display.kbd>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'K']" />

<x-daisy::ui.data-display.kbd>Esc</x-daisy::ui.data-display.kbd>
CODE;
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

    <x-daisy::docs.sections.variants name="kbd">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        @php $keys = ['Ctrl', 'K']; @endphp
                        <x-daisy::ui.data-display.kbd :keys="$keys" size="xs" />
                        <x-daisy::ui.data-display.kbd :keys="$keys" size="sm" />
                        <x-daisy::ui.data-display.kbd :keys="$keys" size="md" />
                        <x-daisy::ui.data-display.kbd :keys="$keys" size="lg" />
                        <x-daisy::ui.data-display.kbd :keys="$keys" size="xl" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Combinaisons</p>
                    <div class="flex flex-wrap items-center gap-3">
                        @php $keys1 = ['Ctrl', 'Shift', 'P']; @endphp
                        <x-daisy::ui.data-display.kbd :keys="$keys1" />
                        @php $keys2 = ['Alt', 'F4']; @endphp
                        <x-daisy::ui.data-display.kbd :keys="$keys2" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'K']" size="xs" />
<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'K']" size="sm" />
<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'K']" size="md" />
<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'K']" size="lg" />
<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'K']" size="xl" />

<x-daisy::ui.data-display.kbd :keys="['Ctrl', 'Shift', 'P']" />
<x-daisy::ui.data-display.kbd :keys="['Alt', 'F4']" />
CODE;
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
