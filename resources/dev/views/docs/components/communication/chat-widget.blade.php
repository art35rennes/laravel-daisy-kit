@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'chat-widget';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chat Widget" 
    category="communication" 
    name="chat-widget"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chat Widget" 
            subtitle="Widget de chat flottant pour le support client."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="chat-widget">
        <x-slot:preview>
            @php
                $conversation = [
                    'id' => 1, 
                    'name' => 'Support', 
                    'avatar' => 'https://i.pravatar.cc/150?img=12', 
                    'isOnline' => true
                ];
                $messages = [
                    [
                        'id' => 1, 
                        'user_id' => 2, 
                        'content' => 'Bonjour, comment puis-je vous aider ?', 
                        'created_at' => '2024-01-15 14:30:00', 
                        'user_name' => 'Support'
                    ]
                ];
            @endphp
            <div class="relative h-96 bg-base-200 rounded-box p-4">
                <x-daisy::ui.communication.chat-widget 
                    :conversation="$conversation" 
                    :messages="$messages" 
                    currentUserId="1" 
                    position="bottom-right" 
                />
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.communication.chat-widget
    :conversation="[
        'id' => 1,
        'name' => 'Support',
        'avatar' => 'https://i.pravatar.cc/150?img=12',
        'isOnline' => true,
    ]"
    :messages="[
        [
            'id' => 1,
            'user_id' => 2,
            'content' => 'Bonjour, comment puis-je vous aider ?',
            'created_at' => '2024-01-15 14:30:00',
            'user_name' => 'Support',
        ],
    ]"
    currentUserId="1"
    position="bottom-right"
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
