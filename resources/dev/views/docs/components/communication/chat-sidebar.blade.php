@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'chat-sidebar';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chat Sidebar" 
    category="communication" 
    name="chat-sidebar"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chat Sidebar" 
            subtitle="Barre latérale listant les conversations avec statut en ligne et compteur de messages non lus."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="chat-sidebar">
        <x-slot:preview>
            @php
                $conversations = [
                    [
                        'id' => 1, 
                        'name' => 'Alice Martin', 
                        'avatar' => 'https://i.pravatar.cc/150?img=12', 
                        'lastMessage' => 'Dernier message', 
                        'unreadCount' => 2, 
                        'isOnline' => true
                    ],
                    [
                        'id' => 2, 
                        'name' => 'Bob Dupont', 
                        'avatar' => 'https://i.pravatar.cc/150?img=13', 
                        'lastMessage' => 'Salut !', 
                        'unreadCount' => 0, 
                        'isOnline' => false
                    ]
                ];
            @endphp
            <div class="w-64">
                <x-daisy::ui.communication.chat-sidebar :conversations="$conversations" />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.communication.chat-sidebar :conversations="[
    [
        'id' => 1,
        'name' => 'Alice Martin',
        'avatar' => 'https://i.pravatar.cc/150?img=12',
        'lastMessage' => 'Dernier message',
        'unreadCount' => 2,
        'isOnline' => true,
    ],
    [
        'id' => 2,
        'name' => 'Bob Dupont',
        'avatar' => 'https://i.pravatar.cc/150?img=13',
        'lastMessage' => 'Salut !',
        'unreadCount' => 0,
        'isOnline' => false,
    ],
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
