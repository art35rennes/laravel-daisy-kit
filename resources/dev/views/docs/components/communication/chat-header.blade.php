@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'chat-header';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chat Header" 
    category="communication" 
    name="chat-header"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chat Header" 
            subtitle="En-tête de conversation avec informations du contact et statut en ligne."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="chat-header">
        <x-slot:preview>
            @php
                $conversation = [
                    'id' => 1,
                    'name' => 'Alice Martin',
                    'avatar' => 'https://i.pravatar.cc/150?img=12',
                    'isOnline' => true
                ];
            @endphp
            <x-daisy::ui.communication.chat-header :conversation="$conversation" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.communication.chat-header :conversation="[
    'id' => 1,
    'name' => 'Alice Martin',
    'avatar' => 'https://i.pravatar.cc/150?img=12',
    'isOnline' => true,
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
