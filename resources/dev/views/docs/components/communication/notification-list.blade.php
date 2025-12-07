@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'notification-list';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Notification List" 
    category="communication" 
    name="notification-list"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Notification List" 
            subtitle="Liste de notifications avec regroupement par date et support des différents types."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="notification-list">
        <x-slot:preview>
            @php
                $notifications = [
                    [
                        'id' => 1, 
                        'type' => 'info', 
                        'data' => ['message' => 'Message 1'], 
                        'read_at' => null, 
                        'created_at' => '2024-01-15 10:00:00'
                    ],
                    [
                        'id' => 2, 
                        'type' => 'warning', 
                        'data' => ['message' => 'Message 2'], 
                        'read_at' => '2024-01-15 09:00:00', 
                        'created_at' => '2024-01-15 08:00:00'
                    ]
                ];
            @endphp
            <x-daisy::ui.communication.notification-list :notifications="$notifications" :groupByDate="true" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$notifications = [
    [
        "id" => 1, 
        "type" => "info", 
        "data" => ["message" => "Message 1"], 
        "read_at" => null, 
        "created_at" => "2024-01-15 10:00:00"
    ],
    [
        "id" => 2, 
        "type" => "warning", 
        "data" => ["message" => "Message 2"], 
        "read_at" => "2024-01-15 09:00:00", 
        "created_at" => "2024-01-15 08:00:00"
    ]
];
@endphp
<x-daisy::ui.communication.notification-list :notifications="$notifications" :groupByDate="true" />';
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
