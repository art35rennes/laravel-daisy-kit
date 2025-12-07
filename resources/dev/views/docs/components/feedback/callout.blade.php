@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'feedback';
    $name = 'callout';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Callout" 
    category="feedback" 
    name="callout"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Callout" 
            subtitle="Encadré d'information."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="callout">
        <x-slot:preview>
            <x-daisy::ui.feedback.callout 
                heading="Note importante" 
                text="Veuillez lire attentivement les instructions avant de continuer." 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.feedback.callout 
    heading="Note importante" 
    text="Veuillez lire attentivement les instructions avant de continuer." 
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

    <x-daisy::docs.sections.variants name="callout">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Variants</p>
                    <div class="space-y-2">
                        <x-daisy::ui.feedback.callout variant="secondary" heading="Info" text="Message informatif." />
                        <x-daisy::ui.feedback.callout variant="success" heading="Succès" text="Opération réussie." />
                        <x-daisy::ui.feedback.callout variant="warning" heading="Attention" text="Action requise." />
                        <x-daisy::ui.feedback.callout variant="danger" heading="Erreur" text="Une erreur est survenue." />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Variants --}}
<x-daisy::ui.feedback.callout variant="secondary" heading="Info" text="Message informatif." />
<x-daisy::ui.feedback.callout variant="success" heading="Succès" text="Opération réussie." />
<x-daisy::ui.feedback.callout variant="warning" heading="Attention" text="Action requise." />
<x-daisy::ui.feedback.callout variant="danger" heading="Erreur" text="Une erreur est survenue." />';
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
