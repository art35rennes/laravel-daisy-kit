@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'stack';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Empilement" 
    category="layout" 
    name="stack"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Empilement" 
            subtitle="Empilement d'éléments superposés."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="stack">
        <x-slot:preview>
            <x-daisy::ui.layout.stack class="w-64">
                <div class="bg-primary text-primary-content p-4 rounded-box">Carte 1</div>
                <div class="bg-secondary text-secondary-content p-4 rounded-box">Carte 2</div>
                <div class="bg-accent text-accent-content p-4 rounded-box">Carte 3</div>
            </x-daisy::ui.layout.stack>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.stack class="w-64">
    <div class="bg-primary text-primary-content p-4 rounded-box">Carte 1</div>
    <div class="bg-secondary text-secondary-content p-4 rounded-box">Carte 2</div>
    <div class="bg-accent text-accent-content p-4 rounded-box">Carte 3</div>
</x-daisy::ui.layout.stack>';
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

    <x-daisy::docs.sections.variants name="stack">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Alignements verticaux</p>
                    <div class="flex gap-4">
                        <x-daisy::ui.layout.stack alignV="top" class="w-32">
                            <div class="bg-primary text-primary-content p-2 rounded-box text-xs">Top</div>
                            <div class="bg-secondary text-secondary-content p-2 rounded-box text-xs">Top</div>
                        </x-daisy::ui.layout.stack>
                        <x-daisy::ui.layout.stack alignV="bottom" class="w-32">
                            <div class="bg-primary text-primary-content p-2 rounded-box text-xs">Bottom</div>
                            <div class="bg-secondary text-secondary-content p-2 rounded-box text-xs">Bottom</div>
                        </x-daisy::ui.layout.stack>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Alignements horizontaux</p>
                    <div class="flex gap-4">
                        <x-daisy::ui.layout.stack alignH="start" class="w-32">
                            <div class="bg-primary text-primary-content p-2 rounded-box text-xs">Start</div>
                            <div class="bg-secondary text-secondary-content p-2 rounded-box text-xs">Start</div>
                        </x-daisy::ui.layout.stack>
                        <x-daisy::ui.layout.stack alignH="end" class="w-32">
                            <div class="bg-primary text-primary-content p-2 rounded-box text-xs">End</div>
                            <div class="bg-secondary text-secondary-content p-2 rounded-box text-xs">End</div>
                        </x-daisy::ui.layout.stack>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Alignements verticaux --}}
<x-daisy::ui.layout.stack alignV="top">
    <div class="bg-primary p-2">Top</div>
</x-daisy::ui.layout.stack>

<x-daisy::ui.layout.stack alignV="bottom">
    <div class="bg-primary p-2">Bottom</div>
</x-daisy::ui.layout.stack>

{{-- Alignements horizontaux --}}
<x-daisy::ui.layout.stack alignH="start">
    <div class="bg-primary p-2">Start</div>
</x-daisy::ui.layout.stack>

<x-daisy::ui.layout.stack alignH="end">
    <div class="bg-primary p-2">End</div>
</x-daisy::ui.layout.stack>';
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
