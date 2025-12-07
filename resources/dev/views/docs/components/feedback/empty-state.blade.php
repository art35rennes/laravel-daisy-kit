@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'feedback';
    $name = 'empty-state';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="État vide" 
    category="feedback" 
    name="empty-state"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="État vide" 
            subtitle="Affichage d'un état vide avec message et action."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="empty-state">
        <x-slot:preview>
            <x-daisy::ui.feedback.empty-state 
                icon="bi-inbox" 
                title="Aucun élément" 
                message="Il n'y a rien à afficher pour le moment." 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.feedback.empty-state 
    icon="bi-inbox" 
    title="Aucun élément" 
    message="Il n\'y a rien à afficher pour le moment." 
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

    <x-daisy::docs.sections.variants name="empty-state">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Avec action</p>
                    <x-daisy::ui.feedback.empty-state 
                        icon="bi-folder" 
                        title="Aucun fichier" 
                        message="Commencez par ajouter votre premier fichier."
                    >
                        <x-slot:action>
                            <x-daisy::ui.inputs.button>Ajouter un fichier</x-daisy::ui.inputs.button>
                        </x-slot:action>
                    </x-daisy::ui.feedback.empty-state>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Sans icône</p>
                    <x-daisy::ui.feedback.empty-state 
                        title="Aucun résultat" 
                        message="Essayez de modifier vos critères de recherche."
                    />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Avec action --}}
<x-daisy::ui.feedback.empty-state 
    icon="bi-folder" 
    title="Aucun fichier" 
    message="Commencez par ajouter votre premier fichier."
>
    <x-slot:action>
        <x-daisy::ui.inputs.button>Ajouter un fichier</x-daisy::ui.inputs.button>
    </x-slot:action>
</x-daisy::ui.feedback.empty-state>

{{-- Sans icône --}}
<x-daisy::ui.feedback.empty-state 
    title="Aucun résultat" 
    message="Essayez de modifier vos critères de recherche."
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
                height="350px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
