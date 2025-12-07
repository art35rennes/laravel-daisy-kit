@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'notification-bell';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Notification Bell" 
    category="communication" 
    name="notification-bell"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Notification Bell" 
            subtitle="Icône de cloche avec compteur de notifications non lues et liste déroulante."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="notification-bell">
        <x-slot:preview>
            @php
                $notifications = [
                    [
                        'id' => 1, 
                        'type' => 'info', 
                        'data' => [
                            'message' => 'Nouveau message', 
                            'priority' => 'normal', 
                            'user' => ['name' => 'Alice']
                        ], 
                        'read_at' => null, 
                        'created_at' => '2024-01-15 10:00:00'
                    ]
                ];
            @endphp
            <x-daisy::ui.communication.notification-bell :notifications="$notifications" unreadCount="1" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$notifications = [
    [
        "id" => 1, 
        "type" => "info", 
        "data" => [
            "message" => "Nouveau message", 
            "priority" => "normal", 
            "user" => ["name" => "Alice"]
        ], 
        "read_at" => null, 
        "created_at" => "2024-01-15 10:00:00"
    ]
];
@endphp
<x-daisy::ui.communication.notification-bell :notifications="$notifications" unreadCount="1" />';
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
