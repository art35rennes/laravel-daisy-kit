@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'notification-filters';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Notification Filters" 
    category="communication" 
    name="notification-filters"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Notification Filters" 
            subtitle="Filtres pour filtrer les notifications par type (info, warning, error, etc.)."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="notification-filters">
        <x-slot:preview>
            @php
                $types = ['info', 'warning', 'error', 'success'];
            @endphp
            <x-daisy::ui.communication.notification-filters :types="$types" currentFilter="all" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$types = ["info", "warning", "error", "success"];
@endphp
<x-daisy::ui.communication.notification-filters :types="$types" currentFilter="all" />';
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

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
