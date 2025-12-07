@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'pagination';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Pagination" 
    category="navigation" 
    name="pagination"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Pagination" 
            subtitle="Pagination pour naviguer entre les pages."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="pagination">
        <x-slot:preview>
            <x-daisy::ui.navigation.pagination :total="42" :current="3" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.navigation.pagination :total="42" :current="3" />';
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

    <x-daisy::docs.sections.variants name="pagination">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Exemples avec différentes pages</p>
                    <div class="space-y-2">
                        <x-daisy::ui.navigation.pagination :total="10" :current="1" />
                        <x-daisy::ui.navigation.pagination :total="10" :current="5" />
                        <x-daisy::ui.navigation.pagination :total="10" :current="10" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Première page --}}
<x-daisy::ui.navigation.pagination :total="10" :current="1" />

{{-- Page du milieu --}}
<x-daisy::ui.navigation.pagination :total="10" :current="5" />

{{-- Dernière page --}}
<x-daisy::ui.navigation.pagination :total="10" :current="10" />';
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
