@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'countdown';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Compte à rebours" 
    category="advanced" 
    name="countdown"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Compte à rebours" 
            subtitle="Compte à rebours animé avec transitions."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="countdown">
        <x-slot:preview>
            @php
                $values = ['days' => 5, 'hours' => 12, 'min' => 30, 'sec' => 45];
            @endphp
            <x-daisy::ui.advanced.countdown :values="$values" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.advanced.countdown :values="[
    'days' => 5,
    'hours' => 12,
    'min' => 30,
    'sec' => 45,
]" />
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

    <x-daisy::docs.sections.variants name="countdown">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Format simple</p>
                    @php
                        $values = ['hours' => 12, 'min' => 30];
                    @endphp
                    <x-daisy::ui.advanced.countdown :values="$values" />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Seulement secondes</p>
                    @php
                        $values = ['sec' => 59];
                    @endphp
                    <x-daisy::ui.advanced.countdown :values="$values" />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = <<<'CODE'
<x-daisy::ui.advanced.countdown :values="[
    'hours' => 12,
    'min' => 30,
]" />

<x-daisy::ui.advanced.countdown :values="[
    'sec' => 59,
]" />
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
