@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'conversation-view';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Conversation View" 
    category="communication" 
    name="conversation-view"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Conversation View" 
            subtitle="Vue complète de conversation avec en-tête, messages et champ de saisie."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="conversation-view">
        <x-slot:preview>
            @php
                $conversation = [
                    'id' => 1, 
                    'name' => 'Alice', 
                    'avatar' => 'https://i.pravatar.cc/150?img=12', 
                    'isOnline' => true
                ];
                $messages = [
                    [
                        'id' => 1, 
                        'user_id' => 2, 
                        'content' => 'Bonjour !', 
                        'created_at' => '2024-01-15 14:30:00', 
                        'user_name' => 'Alice'
                    ]
                ];
            @endphp
            <div class="h-96 border border-base-300 rounded-box">
                <x-daisy::ui.communication.conversation-view :conversation="$conversation" :messages="$messages" currentUserId="1" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '@php
$conversation = [
    "id" => 1, 
    "name" => "Alice", 
    "avatar" => "https://i.pravatar.cc/150?img=12", 
    "isOnline" => true
];
$messages = [
    [
        "id" => 1, 
        "user_id" => 2, 
        "content" => "Bonjour !", 
        "created_at" => "2024-01-15 14:30:00", 
        "user_name" => "Alice"
    ]
];
@endphp
<x-daisy::ui.communication.conversation-view :conversation="$conversation" :messages="$messages" currentUserId="1" />';
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
