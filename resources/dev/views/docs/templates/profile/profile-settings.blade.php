@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Profil - préférences" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Préférences du profil</h1>
        <p class="text-base-content/70">Page de réglages (langue, fuseau horaire, notifications, sécurité, thème), agnostique du modèle.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.profile.profile-settings')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.profile.profile-settings /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.profile.profile-settings
    :profile="auth()->user()"
    action="@{{ route('profile.settings.update') }}"
    :preferences="['language' =&gt; 'fr', 'timezone' =&gt; 'Europe/Paris', 'notify_email' =&gt; true]"
    :showPrivacy="true"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props clés</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Données & accès</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>profile</code> auto-détecté si absent.</li>
                        <li>Clés : <code>preferencesKey</code>, <code>languageKey</code>, <code>timezoneKey</code>.</li>
                        <li>Routes : <code>action</code>, <code>method</code>, <code>profileEditUrl</code>, <code>profileViewUrl</code>.</li>
                        <li><code>preferences</code> (array) pour surcharger les données stockées.</li>
                    </ul>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Sections & options</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li>Visibilité : <code>showPreferences</code>, <code>showNotifications</code>, <code>showSecurity</code>, <code>showPrivacy</code>, <code>showLanguage</code>, <code>showTheme</code>.</li>
                        <li>Notifications : <code>notify_email</code>, <code>notify_push</code>, <code>notify_sms</code>, etc. gérés via <code>preferences</code>.</li>
                        <li>Sécurité : <code>twoFactorEnabled</code> pris depuis <code>preferences</code>.</li>
                        <li>Thème : <code>currentTheme</code> (session ou préférence).</li>
                        <li><code>readonly</code> pour désactiver les champs.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>header</code> : remplace le breadcrumb.</li>
                <li><code>actions</code> : barre de boutons (sauvegarder, annuler).</li>
                <li><code>sidebar</code> : contenu latéral supplémentaire.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

