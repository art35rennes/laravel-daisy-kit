@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'calendar-full';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Calendrier complet" 
    category="advanced" 
    name="calendar-full"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Calendrier complet" 
            subtitle="Calendrier complet avec gestion d'événements et affichage mensuel."
            jsModule="calendar-full"
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="calendar-full">
        <x-slot:preview>
            @php
                $events = [
                    ['title' => 'Réunion', 'start' => '2024-01-15 10:00'],
                    ['title' => 'Déjeuner', 'start' => '2024-01-15 12:30'],
                    ['title' => 'Conférence', 'start' => '2024-01-20 14:00'],
                ];
            @endphp
            <x-daisy::ui.advanced.calendar-full :events="$events" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.advanced.calendar-full :events="[
    ['title' => 'Réunion', 'start' => '2024-01-15 10:00'],
    ['title' => 'Déjeuner', 'start' => '2024-01-15 12:30'],
    ['title' => 'Conférence', 'start' => '2024-01-20 14:00'],
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
