@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'notification-item';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Notification Item" 
    category="communication" 
    name="notification-item"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Notification Item" 
            subtitle="Élément de notification individuel avec actions (marquer comme lu, supprimer)."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="notification-item">
        <x-slot:preview>
            @php
                $notification = [
                    'id' => 1,
                    'type' => 'info',
                    'data' => [
                        'message' => 'Vous avez reçu un nouveau message', 
                        'priority' => 'normal', 
                        'user' => [
                            'name' => 'Alice', 
                            'avatar' => 'https://i.pravatar.cc/150?img=12'
                        ]
                    ],
                    'read_at' => null,
                    'created_at' => '2024-01-15 10:00:00'
                ];
            @endphp
            <x-daisy::ui.communication.notification-item :notification="$notification" :showActions="true" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.communication.notification-item
    :notification="[
        'id' => 1,
        'type' => 'info',
        'data' => [
            'message' => 'Vous avez reçu un nouveau message',
            'priority' => 'normal',
            'user' => [
                'name' => 'Alice',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
            ],
        ],
        'read_at' => null,
        'created_at' => '2024-01-15 10:00:00',
    ]"
    :showActions="true"
/>
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
