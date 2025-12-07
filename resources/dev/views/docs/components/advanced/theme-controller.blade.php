@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'theme-controller';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Contrôleur de thème" 
    category="advanced" 
    name="theme-controller"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Contrôleur de thème" 
            subtitle="Contrôleur pour changer le thème daisyUI."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="theme-controller">
        <x-slot:preview>
            <x-daisy::ui.advanced.theme-controller :themes="['light', 'dark']" value="light" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$themes = ["light", "dark"];
@endphp
<x-daisy::ui.advanced.theme-controller :themes="$themes" value="light" />';
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

    <x-daisy::docs.sections.variants name="theme-controller">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Plusieurs thèmes</p>
                    @php
                        $themes = ['light', 'dark', 'cupcake', 'synthwave'];
                    @endphp
                    <x-daisy::ui.advanced.theme-controller :themes="$themes" value="light" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec label</p>
                    @php
                        $themes = ['light', 'dark'];
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-sm">Thème :</span>
                        <x-daisy::ui.advanced.theme-controller :themes="$themes" value="light" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '@php
$themes = ["light", "dark", "cupcake", "synthwave"];
@endphp

{{-- Plusieurs thèmes --}}
<x-daisy::ui.advanced.theme-controller :themes="$themes" value="light" />

{{-- Avec label --}}
<div class="flex items-center gap-2">
    <span class="text-sm">Thème :</span>
    <x-daisy::ui.advanced.theme-controller :themes="$themes" value="light" />
</div>';
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
