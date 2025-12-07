@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'feedback';
    $name = 'alert';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Alerte" 
    category="feedback" 
    name="alert"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Alerte" 
            subtitle="Alerte pour informer l'utilisateur."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="alert">
        <x-slot:preview>
            <x-daisy::ui.feedback.alert color="success" title="Succès">
                Votre demande a été traitée avec succès.
            </x-daisy::ui.feedback.alert>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.feedback.alert color="success" title="Succès">
    Votre demande a été traitée avec succès.
</x-daisy::ui.feedback.alert>';
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

    <x-daisy::docs.sections.variants name="alert">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.feedback.alert color="info" title="Information">Message informatif</x-daisy::ui.feedback.alert>
                        <x-daisy::ui.feedback.alert color="success" title="Succès">Opération réussie</x-daisy::ui.feedback.alert>
                        <x-daisy::ui.feedback.alert color="warning" title="Attention">Action requise</x-daisy::ui.feedback.alert>
                        <x-daisy::ui.feedback.alert color="error" title="Erreur">Une erreur est survenue</x-daisy::ui.feedback.alert>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Variantes</p>
                    <div class="space-y-2">
                        <x-daisy::ui.feedback.alert variant="outline" color="primary" title="Outline">Style avec bordure</x-daisy::ui.feedback.alert>
                        <x-daisy::ui.feedback.alert variant="soft" color="primary" title="Soft">Style doux</x-daisy::ui.feedback.alert>
                        <x-daisy::ui.feedback.alert variant="dash" color="primary" title="Dash">Style avec tirets</x-daisy::ui.feedback.alert>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.feedback.alert color="info" title="Information">Message</x-daisy::ui.feedback.alert>
<x-daisy::ui.feedback.alert color="success" title="Succès">Message</x-daisy::ui.feedback.alert>
<x-daisy::ui.feedback.alert color="error" title="Erreur">Message</x-daisy::ui.feedback.alert>

{{-- Variantes --}}
<x-daisy::ui.feedback.alert variant="outline" color="primary">Message</x-daisy::ui.feedback.alert>
<x-daisy::ui.feedback.alert variant="soft" color="primary">Message</x-daisy::ui.feedback.alert>';
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
