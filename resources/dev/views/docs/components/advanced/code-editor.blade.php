@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'code-editor';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);

    // Never embed raw PHP open/close tags in docs Blade files (can crash parsers).
    $phpHelloWorld = '<' . "?php echo 'Hello World'; ?" . '>';
    $phpHello = '<' . "?php echo 'Hello'; ?" . '>';
@endphp

<x-daisy::docs.page 
    title="Éditeur de code" 
    category="advanced" 
    name="code-editor"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Éditeur de code" 
            subtitle="Éditeur de code avec coloration syntaxique."
            jsModule="code-editor"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="code-editor">
        <x-slot:preview>
            <x-daisy::ui.advanced.code-editor 
                language="php" 
                :value="$phpHelloWorld"
                height="200px"
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.code-editor 
    language="php" 
    value="<' . "?php echo \\'Hello World\\'; ?" . '>"
    height="200px"
/>';
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

    <x-daisy::docs.sections.variants name="code-editor">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Langages supportés</p>
                    <div class="space-y-2">
                        <x-daisy::ui.advanced.code-editor 
                            language="javascript" 
                            value="const hello = 'World';" 
                            height="150px"
                            :showToolbar="false"
                        />
                        <x-daisy::ui.advanced.code-editor 
                            language="php" 
                            :value="$phpHello"
                            height="150px"
                            :showToolbar="false"
                        />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Avec barre d'outils</p>
                    <x-daisy::ui.advanced.code-editor 
                        language="blade" 
                        value="<x-component />" 
                        height="150px"
                        :showToolbar="true"
                    />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Langages supportés --}}
<x-daisy::ui.advanced.code-editor 
    language="javascript" 
    value="const hello = \'World\';" 
    height="150px"
/>

{{-- Avec barre d\'outils --}}
<x-daisy::ui.advanced.code-editor 
    language="blade" 
    value="<x-component />" 
    height="150px"
    :showToolbar="true"
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
