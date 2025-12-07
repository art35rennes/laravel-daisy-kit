@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'communication';
    $name = 'chat-bubble';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Chat Bubble" 
    category="communication" 
    name="chat-bubble"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Chat Bubble" 
            subtitle="Bulle de conversation."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="chat-bubble">
        <x-slot:preview>
            <x-daisy::ui.communication.chat-bubble name="Alice" time="12:45">
                Salut ! Comment ça va ?
            </x-daisy::ui.communication.chat-bubble>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.communication.chat-bubble name="Alice" time="12:45">
    Salut ! Comment ça va ?
</x-daisy::ui.communication.chat-bubble>';
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

    <x-daisy::docs.sections.variants name="chat-bubble">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Alignement</p>
                    <div class="space-y-2">
                        <x-daisy::ui.communication.chat-bubble align="start" name="Alice" time="12:45">
                            Message à gauche
                        </x-daisy::ui.communication.chat-bubble>
                        <x-daisy::ui.communication.chat-bubble align="end" name="Bob" time="12:46">
                            Message à droite
                        </x-daisy::ui.communication.chat-bubble>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="space-y-2">
                        <x-daisy::ui.communication.chat-bubble color="primary" name="Alice">Message primary</x-daisy::ui.communication.chat-bubble>
                        <x-daisy::ui.communication.chat-bubble color="success" name="Bob">Message success</x-daisy::ui.communication.chat-bubble>
                        <x-daisy::ui.communication.chat-bubble color="warning" name="Charlie">Message warning</x-daisy::ui.communication.chat-bubble>
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Alignement --}}
<x-daisy::ui.communication.chat-bubble align="start" name="Alice" time="12:45">
    Message à gauche
</x-daisy::ui.communication.chat-bubble>

<x-daisy::ui.communication.chat-bubble align="end" name="Bob" time="12:46">
    Message à droite
</x-daisy::ui.communication.chat-bubble>

{{-- Couleurs --}}
<x-daisy::ui.communication.chat-bubble color="primary" name="Alice">
    Message primary
</x-daisy::ui.communication.chat-bubble>

<x-daisy::ui.communication.chat-bubble color="success" name="Bob">
    Message success
</x-daisy::ui.communication.chat-bubble>';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$variantsCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
