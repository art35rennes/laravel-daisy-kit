@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'icon';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Icône" 
    category="advanced" 
    name="icon"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Icône" 
            subtitle="Icône depuis blade-icons."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="icon">
        <x-slot:preview>
            <div class="flex items-center gap-2">
                <x-daisy::ui.advanced.icon name="heart" />
                <span>Icône cœur</span>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.icon name="heart" />';
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

    <x-daisy::docs.sections.variants name="icon">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.icon name="star" size="xs" />
                        <x-daisy::ui.advanced.icon name="star" size="sm" />
                        <x-daisy::ui.advanced.icon name="star" size="md" />
                        <x-daisy::ui.advanced.icon name="star" size="lg" />
                        <x-daisy::ui.advanced.icon name="star" size="xl" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.advanced.icon name="heart" class="text-primary" />
                        <x-daisy::ui.advanced.icon name="heart" class="text-secondary" />
                        <x-daisy::ui.advanced.icon name="heart" class="text-accent" />
                        <x-daisy::ui.advanced.icon name="heart" class="text-success" />
                        <x-daisy::ui.advanced.icon name="heart" class="text-warning" />
                        <x-daisy::ui.advanced.icon name="heart" class="text-error" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Tailles --}}
<x-daisy::ui.advanced.icon name="star" size="xs" />
<x-daisy::ui.advanced.icon name="star" size="sm" />
<x-daisy::ui.advanced.icon name="star" size="lg" />

{{-- Couleurs --}}
<x-daisy::ui.advanced.icon name="heart" class="text-primary" />
<x-daisy::ui.advanced.icon name="heart" class="text-accent" />
<x-daisy::ui.advanced.icon name="heart" class="text-success" />';
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
