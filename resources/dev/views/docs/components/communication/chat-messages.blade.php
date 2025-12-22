@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'chat-messages';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chat Messages" 
    category="communication" 
    name="chat-messages"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chat Messages" 
            subtitle="Liste de messages de conversation avec distinction entre messages envoyés et reçus."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="chat-messages">
        <x-slot:preview>
            @php
                $messages = [
                    [
                        'id' => 1, 
                        'user_id' => 2, 
                        'content' => 'Bonjour !', 
                        'created_at' => '2024-01-15 14:30:00', 
                        'user_name' => 'Alice', 
                        'user_avatar' => 'https://i.pravatar.cc/150?img=12'
                    ],
                    [
                        'id' => 2, 
                        'user_id' => 1, 
                        'content' => 'Salut, comment ça va ?', 
                        'created_at' => '2024-01-15 14:31:00', 
                        'user_name' => 'Vous'
                    ]
                ];
            @endphp
            <x-daisy::ui.communication.chat-messages :messages="$messages" currentUserId="1" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.communication.chat-messages
    :messages="[
        [
            'id' => 1,
            'user_id' => 2,
            'content' => 'Bonjour !',
            'created_at' => '2024-01-15 14:30:00',
            'user_name' => 'Alice',
            'user_avatar' => 'https://i.pravatar.cc/150?img=12',
        ],
        [
            'id' => 2,
            'user_id' => 1,
            'content' => 'Salut, comment ça va ?',
            'created_at' => '2024-01-15 14:31:00',
            'user_name' => 'Vous',
        ],
    ]"
    currentUserId="1"
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
