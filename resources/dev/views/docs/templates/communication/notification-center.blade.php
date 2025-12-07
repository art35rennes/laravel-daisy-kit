@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Centre de notifications" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Centre de notifications</h1>
        <p class="text-base-content/70">Dashboard notifications avec filtres, KPIs, grouping par date et actions (lire, supprimer, préférences).</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.communication.notification-center')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.communication.notification-center /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.communication.notification-center
    :notifications="$notifications"
    :types="[['id' => 'all', 'label' => 'Toutes'], ['id' => 'alerts', 'label' => 'Alertes']]"
    markAsReadUrl="/notifications/{id}/read"
    markAllAsReadUrl="/notifications/read-all"
    deleteUrl="/notifications/{id}"
    loadNotificationsUrl="/notifications"
    preferencesUrl="/notifications/preferences"
    :useWebSockets="false"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props clés</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Données & affichage</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>notifications</code> (array), <code>unreadCount</code> (int|null).</li>
                        <li><code>showFilters</code>, <code>showMarkAllRead</code>, <code>showDelete</code>, <code>groupByDate</code>, <code>pagination</code>.</li>
                        <li><code>types</code> (filtres), <code>currentFilter</code> (string).</li>
                        <li>Accès données : <code>notificationIdKey</code>, <code>notificationTypeKey</code>, <code>notificationDataKey</code>, <code>notificationReadAtKey</code>, <code>notificationCreatedAtKey</code>.</li>
                        <li>Stats additionnelles : <code>digestTime</code>, <code>userId</code>.</li>
                    </ul>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Actions & temps réel</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>markAsReadUrl</code>, <code>markAllAsReadUrl</code>, <code>deleteUrl</code>, <code>loadNotificationsUrl</code>, <code>preferencesUrl</code>.</li>
                        <li><code>useWebSockets</code> (bool), <code>pollingInterval</code> (ms), <code>autoReconnect</code>, <code>reconnectDelay</code>.</li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="mt-6 text-sm text-base-content/70">Les URLs marquées <code>{id}</code> doivent être interpolées côté application avant l’appel.</p>
    </section>
</x-daisy::layout.docs>

