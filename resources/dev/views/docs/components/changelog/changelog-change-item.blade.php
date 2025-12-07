@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'changelog';
    $name = 'changelog-change-item';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Changelog Change Item" 
    category="changelog" 
    name="changelog-change-item"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Changelog Change Item" 
            subtitle="Élément de changement individuel dans un changelog avec type et description."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="changelog-change-item">
        <x-slot:preview>
            <x-daisy::ui.changelog.changelog-change-item 
                type="added" 
                description="Nouvelle fonctionnalité permettant de filtrer les résultats" 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.changelog.changelog-change-item 
    type="added" 
    description="Nouvelle fonctionnalité permettant de filtrer les résultats" 
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

    <x-daisy::docs.sections.variants name="changelog-change-item">
        <x-slot:preview>
            <div class="space-y-2">
                <x-daisy::ui.changelog.changelog-change-item type="added" description="Nouvelle fonctionnalité ajoutée" />
                <x-daisy::ui.changelog.changelog-change-item type="fixed" description="Correction d'un bug critique" />
                <x-daisy::ui.changelog.changelog-change-item type="changed" description="Amélioration des performances" />
                <x-daisy::ui.changelog.changelog-change-item type="deprecated" description="Fonctionnalité dépréciée" />
                <x-daisy::ui.changelog.changelog-change-item type="removed" description="Fonctionnalité supprimée" />
                <x-daisy::ui.changelog.changelog-change-item type="security" description="Correction de sécurité" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '<x-daisy::ui.changelog.changelog-change-item type="added" description="Nouvelle fonctionnalité" />
<x-daisy::ui.changelog.changelog-change-item type="fixed" description="Correction d\'un bug" />
<x-daisy::ui.changelog.changelog-change-item type="changed" description="Amélioration" />
<x-daisy::ui.changelog.changelog-change-item type="deprecated" description="Déprécié" />
<x-daisy::ui.changelog.changelog-change-item type="removed" description="Supprimé" />
<x-daisy::ui.changelog.changelog-change-item type="security" description="Sécurité" />';
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
