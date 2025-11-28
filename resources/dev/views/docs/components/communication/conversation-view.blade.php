@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
            ['id' => 'intro', 'label' => 'Introduction'],
            ['id' => 'base', 'label' => 'Exemple de base'],
            ['id' => 'api', 'label' => 'API'],
        ];
    $props = DocsHelper::getComponentProps('communication', 'conversation-view');
@endphp

<x-daisy::layout.docs title="Conversation View" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Conversation View</h1>
        <p>Composant compatible daisyUI v5 et Tailwind CSS v4.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-conversation-view" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose">
                    @php
$conversation = ["id" => 1, "name" => "Alice", "avatar" => "https://i.pravatar.cc/150?img=12", "isOnline" => true];
$messages = [["id" => 1, "user_id" => 2, "content" => "Bonjour !", "created_at" => "2024-01-15 14:30:00", "user_name" => "Alice"]];
@endphp
<x-daisy::ui.communication.conversation-view :conversation="$conversation" :messages="$messages" currentUserId="1" />
                </div>
            </div>
            <input type="radio" name="base-example-conversation-view" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $baseCode = '@php
$conversation = ["id" => 1, "name" => "Alice", "avatar" => "https://i.pravatar.cc/150?img=12", "isOnline" => true];
$messages = [["id" => 1, "user_id" => 2, "content" => "Bonjour !", "created_at" => "2024-01-15 14:30:00", "user_name" => "Alice"]];
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
                    height="200px"
                />
            </div>
        </div>
    </section>

    @if(!empty($props))
    <section id="api" class="mt-10">
        <h2>API</h2>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($props as $prop)
                        <tr>
                            <td><code>{{ $prop }}</code></td>
                            <td class="opacity-70">Voir les commentaires dans le composant Blade pour les valeurs et défauts.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</x-daisy::layout.docs>
