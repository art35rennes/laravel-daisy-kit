@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Profil - vue" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Vue profil</h1>
        <p class="text-base-content/70">Page lecture seule avec avatar, bio, stats, badges et timeline d’activités.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.profile.profile-view')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.profile.profile-view /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.profile.profile-view
    :profile="$user"
    :stats="[['label' =&gt; 'Projects', 'value' =&gt; 12, 'icon' =&gt; 'bi-kanban']]"
    :badges="[['label' =&gt; 'Pro', 'color' =&gt; 'primary', 'icon' =&gt; 'bi-award']]"
    :timeline="[['date' =&gt; now(), 'title' =&gt; 'Inscription', 'icon' =&gt; 'bi-check']]]"
    profileEditUrl="@{{ route('profile.edit') }}"
    profileSettingsUrl="@{{ route('profile.settings') }}"
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
                        <li><code>profile</code> auto-détecté via <code>auth()</code> sinon passé.</li>
                        <li>Clés : <code>nameKey</code>, <code>emailKey</code>, <code>avatarKey</code>, <code>bioKey</code>, <code>locationKey</code>, <code>websiteKey</code>, <code>createdAtKey</code>, <code>lastActiveKey</code>.</li>
                        <li>Routes : <code>profileEditUrl</code>, <code>profileSettingsUrl</code>.</li>
                        <li><code>isOwnProfile</code> / <code>compareProfile</code> pour ajuster les actions.</li>
                    </ul>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Affichage</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><code>stats</code> (cartes stats), <code>badges</code> (étiquettes), <code>timeline</code> (événements).</li>
                        <li>Visibilité : <code>showStats</code>, <code>showBadges</code>, <code>showTimeline</code>, <code>showBio</code>, <code>showContact</code>.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>header</code> : remplace le breadcrumb.</li>
                <li><code>actions</code> : CTA personnalisés.</li>
                <li><code>sidebar</code> : contenu additionnel.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

