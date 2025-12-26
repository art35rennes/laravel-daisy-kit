@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Centre de messagerie" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Centre de messagerie</h1>
        <p class="text-base-content/70">Mise en page complète chat + sidebar de conversations, compatible REST ou WebSockets.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.communication.chat')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.communication.chat /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.communication.chat
    :conversation="$conversation"
    :conversations="$conversations"
    :messages="$messages"
    :currentUserId="$userId"
    sendMessageUrl="/api/messages"
    loadMessagesUrl="/api/messages"
    conversationsUrl="/api/conversations"
    :useWebSockets="false"
/&gt;</code></pre>
        </div>
        <p class="mt-3 text-sm text-base-content/70">Le template instancie <code>x-daisy::ui.communication.conversation-view</code> et <code>chat-sidebar</code> et inclut un drawer responsive.</p>
    </section>

    <section id="api" class="mt-10">
        <h2>Props clés</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Données et affichage</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>title</code> (string, défaut <code>__('chat.messages')</code>).</li>
                        <li><code>conversation</code>, <code>conversations</code>, <code>messages</code>, <code>currentUser</code>, <code>currentUserId</code>.</li>
                        <li><code>showSidebar</code> (bool), <code>showUserList</code> (bool).</li>
                        <li><code>typingUsers</code> (array), <code>showTypingIndicator</code> (bool).</li>
                        <li>Upload : <code>enableFileUpload</code>, <code>maxFileSize</code>, <code>multipleFiles</code>, <code>showFilePreview</code>, <code>acceptedFileTypes</code>.</li>
                    </ul>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Routes & temps réel</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>sendMessageUrl</code>, <code>typingUrl</code>, <code>loadMessagesUrl</code>, <code>conversationsUrl</code>.</li>
                        <li>Temps réel : <code>useWebSockets</code> (bool), <code>pollingInterval</code> (ms), <code>autoReconnect</code>, <code>reconnectDelay</code>.</li>
                        <li>Clés d’accès : <code>conversationIdKey</code>, <code>conversationNameKey</code>, <code>conversationAvatarKey</code>, <code>conversationIsOnlineKey</code>, <code>conversationLastMessageKey</code>, <code>conversationUnreadCountKey</code>, <code>message*</code>.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-6 text-sm text-base-content/70">
            <p>JS : le drawer ferme automatiquement en mobile après choix de conversation (script inline). Le composant enfant peut consommer vos modules WebSocket ou requêtes fetch.</p>
        </div>
    </section>
</x-daisy::layout.docs>

