@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'onboarding';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Assistant de démarrage" 
    category="advanced" 
    name="onboarding"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Assistant de démarrage" 
            subtitle="Assistant interactif pour guider les utilisateurs dans leur première utilisation."
            jsModule="onboarding"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="onboarding">
        <x-slot:preview>
            @php
                $steps = [
                    ['title' => 'Bienvenue', 'content' => 'Bienvenue dans notre application !'],
                    ['title' => 'Première étape', 'content' => 'Voici comment commencer.'],
                    ['title' => 'Terminé', 'content' => 'Vous êtes prêt à utiliser l\'application.'],
                ];
            @endphp
            <div class="p-4 bg-base-200 rounded-box">
                <x-daisy::ui.advanced.onboarding :steps="$steps" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$steps = [
    ["title" => "Bienvenue", "content" => "Bienvenue dans notre application !"],
    ["title" => "Première étape", "content" => "Voici comment commencer."],
    ["title" => "Terminé", "content" => "Vous êtes prêt à utiliser l\'application."]
];
@endphp
<x-daisy::ui.advanced.onboarding :steps="$steps" />';
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
