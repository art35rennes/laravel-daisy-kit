@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Profil - édition" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Édition du profil</h1>
        <p class="text-base-content/70">Formulaire complet (avatar, infos, bio) agnostique du modèle, avec accès rapides vers affichage et préférences.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.profile.profile-edit')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.profile.profile-edit /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.profile.profile-edit
    :profile="auth()->user()"
    action="{{ route('profile.update') }}"
    method="POST"
    profileViewUrl="{{ route('profile.show') }}"
    profileSettingsUrl="{{ route('profile.settings') }}"
    :showPhone="true"
    :showLocation="true"
    :showWebsite="true"
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
                        <li><code>profile</code> (mixed) auto-détecté via <code>auth()</code> si null.</li>
                        <li>Clés d’accès : <code>nameKey</code>, <code>emailKey</code>, <code>avatarKey</code>, <code>phoneKey</code>, <code>bioKey</code>, <code>locationKey</code>, <code>websiteKey</code>.</li>
                        <li>Formulaire : <code>action</code>, <code>method</code>, <code>enctype</code>.</li>
                        <li>Routes : <code>profileViewUrl</code>, <code>profileSettingsUrl</code>.</li>
                    </ul>
                </div>
            </div>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">Affichage</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li>Visibilité des champs : <code>showAvatar</code>, <code>showName</code>, <code>showEmail</code>, <code>showPhone</code>, <code>showBio</code>, <code>showLocation</code>, <code>showWebsite</code>.</li>
                        <li>Avatar : <code>avatarMaxSize</code> (Ko), <code>avatarAcceptedTypes</code> (MIME).</li>
                        <li><code>readonly</code> (bool) pour verrouiller les inputs.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>header</code> : remplace le breadcrumb.</li>
                <li><code>actions</code> : barre d’actions (submit, liens).</li>
                <li><code>aside</code> : contenu complémentaire à droite.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

