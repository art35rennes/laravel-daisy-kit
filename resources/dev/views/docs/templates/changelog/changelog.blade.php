@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Changelog" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Changelog</h1>
        <p class="text-base-content/70">Page de journal des versions avec filtres, recherche et mise en avant de la version courante.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.changelog')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.changelog /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.changelog
    :versions="$versions"
    currentVersion="2.1.0"
    rssUrl="/feed/rss"
    atomUrl="/feed/atom"
    :showFilters="true"
    :showSearch="true"
    :groupByMonth="false"
/&gt;</code></pre>
        </div>
        <p class="mt-3 text-sm text-base-content/70">Passez un tableau <code>versions</code> avec clés <code>version</code>, <code>title</code>, <code>date</code>, <code>highlights</code>, <code>changes</code>, etc.</p>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('changelog.changelog')</code></td><td>Titre affiché.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>versions</code></td><td>array</td><td><code>[]</code></td><td>Données de versions.</td></tr>
                    <tr><td><code>currentVersion</code></td><td>string|null</td><td><code>config('app.version')</code></td><td>Version courante à mettre en avant.</td></tr>
                    <tr><td><code>rssUrl</code></td><td>string|null</td><td><code>null</code></td><td>Flux RSS.</td></tr>
                    <tr><td><code>atomUrl</code></td><td>string|null</td><td><code>null</code></td><td>Flux Atom.</td></tr>
                    <tr><td><code>showFilters</code></td><td>bool</td><td><code>true</code></td><td>Affiche les filtres.</td></tr>
                    <tr><td><code>showSearch</code></td><td>bool</td><td><code>true</code></td><td>Affiche la recherche.</td></tr>
                    <tr><td><code>showVersionBadge</code></td><td>bool</td><td><code>true</code></td><td>Badge de version actuelle.</td></tr>
                    <tr><td><code>groupByMonth</code></td><td>bool</td><td><code>false</code></td><td>Regroupe les versions par mois.</td></tr>
                    <tr><td><code>highlightCurrent</code></td><td>bool</td><td><code>true</code></td><td>Accentue la version courante.</td></tr>
                    <tr><td><code>expandLatest</code></td><td>bool</td><td><code>true</code></td><td>Ouvre la dernière version.</td></tr>
                    <tr><td><code>itemsPerPage</code></td><td>int</td><td><code>20</code></td><td>Pagination si activée.</td></tr>
                    <tr><td><code>pagination</code></td><td>bool</td><td><code>false</code></td><td>Active la pagination.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>logo</code> (optionnel) : peut être injecté au-dessus du header.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

