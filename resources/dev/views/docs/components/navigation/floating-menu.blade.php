@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'navigation';
    $name = 'floating-menu';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Floating Menu" 
    category="navigation" 
    name="floating-menu"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Floating Menu" 
            subtitle="Menu flottant avec groupes de boutons, positionnable sur les bords de l'écran."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="floating-menu">
        <x-slot:preview>
            <div class="relative h-96 border border-base-300 rounded-box overflow-hidden bg-base-200">
                @php
                    $groups = [
                        [
                            'items' => [
                                ['icon' => 'pencil', 'label' => 'Edit', 'active' => false],
                                ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
                                ['icon' => 'code-slash', 'label' => 'Code', 'active' => false],
                            ]
                        ],
                        [
                            'items' => [
                                ['icon' => 'phone', 'label' => 'Mobile', 'active' => false],
                                ['icon' => 'tablet', 'label' => 'Tablet', 'active' => false],
                                ['icon' => 'laptop', 'label' => 'Desktop', 'active' => false],
                            ]
                        ],
                        [
                            'items' => [
                                ['icon' => 'sun', 'label' => 'Light', 'active' => false],
                                ['icon' => 'moon', 'label' => 'Dark', 'active' => false],
                            ]
                        ],
                    ];
                @endphp
                <x-daisy::ui.navigation.floating-menu 
                    position="left"
                    :groups="$groups"
                />
                <div class="p-8">
                    <p class="text-base-content/70">Le menu flottant est positionné à gauche de cette zone.</p>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.navigation.floating-menu
    position="left"
    :groups="[
        [
            'items' => [
                ['icon' => 'pencil', 'label' => 'Edit', 'active' => false],
                ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
                ['icon' => 'code-slash', 'label' => 'Code', 'active' => false],
            ],
        ],
        [
            'items' => [
                ['icon' => 'phone', 'label' => 'Mobile', 'active' => false],
                ['icon' => 'tablet', 'label' => 'Tablet', 'active' => false],
                ['icon' => 'laptop', 'label' => 'Desktop', 'active' => false],
            ],
        ],
        [
            'items' => [
                ['icon' => 'sun', 'label' => 'Light', 'active' => false],
                ['icon' => 'moon', 'label' => 'Dark', 'active' => false],
            ],
        ],
    ]"
/>
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
                height="400px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="floating-menu">
        <x-slot:preview>
            <div class="space-y-8">
                <div>
                    <p class="text-sm font-semibold mb-2">Positions</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200">
                            <x-daisy::ui.navigation.floating-menu 
                                position="left"
                                :groups="[['items' => [['icon' => 'pencil', 'label' => 'Edit']]]]"
                            />
                            <div class="p-4 text-xs text-base-content/70">Gauche</div>
                        </div>
                        <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200">
                            <x-daisy::ui.navigation.floating-menu 
                                position="right"
                                :groups="[['items' => [['icon' => 'pencil', 'label' => 'Edit']]]]"
                            />
                            <div class="p-4 text-xs text-base-content/70">Droite</div>
                        </div>
                        <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200">
                            <x-daisy::ui.navigation.floating-menu 
                                position="top"
                                orientation="horizontal"
                                :groups="[['items' => [['icon' => 'pencil', 'label' => 'Edit']]]]"
                            />
                            <div class="p-4 text-xs text-base-content/70">Haut</div>
                        </div>
                        <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200">
                            <x-daisy::ui.navigation.floating-menu 
                                position="bottom"
                                orientation="horizontal"
                                :groups="[['items' => [['icon' => 'pencil', 'label' => 'Edit']]]]"
                            />
                            <div class="p-4 text-xs text-base-content/70">Bas</div>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Variants de boutons</p>
                    <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200">
                        <x-daisy::ui.navigation.floating-menu 
                            position="left"
                            buttonVariant="outline"
                            :groups="[['items' => [
                                ['icon' => 'pencil', 'label' => 'Edit', 'active' => false],
                                ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
                            ]]]"
                        />
                        <div class="p-4 text-xs text-base-content/70">Outline avec bouton actif</div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex gap-4">
                        <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200 flex-1">
                            <x-daisy::ui.navigation.floating-menu 
                                position="left"
                                buttonSize="sm"
                                :groups="[['items' => [['icon' => 'pencil', 'label' => 'Small']]]]"
                            />
                            <div class="p-4 text-xs text-base-content/70">Small</div>
                        </div>
                        <div class="relative h-48 border border-base-300 rounded-box overflow-hidden bg-base-200 flex-1">
                            <x-daisy::ui.navigation.floating-menu 
                                position="left"
                                buttonSize="lg"
                                :groups="[['items' => [['icon' => 'pencil', 'label' => 'Large']]]]"
                            />
                            <div class="p-4 text-xs text-base-content/70">Large</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Position gauche --}}
<x-daisy::ui.navigation.floating-menu position="left" :groups="$groups" />

{{-- Position droite --}}
<x-daisy::ui.navigation.floating-menu position="right" :groups="$groups" />

{{-- Position haut (horizontal) --}}
<x-daisy::ui.navigation.floating-menu 
    position="top" 
    orientation="horizontal"
    :groups="$groups" 
/>

{{-- Variant outline --}}
<x-daisy::ui.navigation.floating-menu 
    position="left"
    buttonVariant="outline"
    :groups="$groups" 
/>

{{-- Tailles --}}
<x-daisy::ui.navigation.floating-menu 
    position="left"
    buttonSize="sm"
    :groups="$groups" 
/>
<x-daisy::ui.navigation.floating-menu 
    position="left"
    buttonSize="lg"
    :groups="$groups" 
/>';
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
                height="400px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>

