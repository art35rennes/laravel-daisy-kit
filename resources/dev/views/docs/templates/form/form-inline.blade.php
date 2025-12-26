@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Formulaire inline" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Formulaire inline / filtres</h1>
        <p class="text-base-content/70">Barre de filtres responsive avec tokens actifs, panneau avancé via drawer et refresh CSRF optionnel.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.form.form-inline')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.form.form-inline /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.form.form-inline
    action="/search"
    method="GET"
    size="sm"
    :activeFilters="[['label' =&gt; 'Statut', 'value' =&gt; 'Actif', 'param' =&gt; 'status']]"
    :showAdvanced="true"
    advancedTitle="Filtres avancés"
/&gt;
&lt;x-slot:filters&gt;
    &lt;x-daisy::ui.inputs.input name="q" placeholder="Rechercher..." /&gt;
&lt;/x-slot:filters&gt;
&lt;x-slot:advanced&gt;
    &lt;x-daisy::ui.inputs.select name="status"&gt;...&lt;/x-daisy::ui.inputs.select&gt;
&lt;/x-slot:advanced&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>action</code></td><td>string</td><td><code>#</code></td><td>URL du formulaire.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>GET</code></td><td>Méthode HTTP.</td></tr>
                    <tr><td><code>size</code></td><td>string</td><td><code>sm</code></td><td>Taille des champs (xs|sm|md).</td></tr>
                    <tr><td><code>collapseBelow</code></td><td>string</td><td><code>md</code></td><td>Breakpoint pour passer en colonne.</td></tr>
                    <tr><td><code>showReset</code></td><td>bool</td><td><code>true</code></td><td>Affiche le bouton Reset.</td></tr>
                    <tr><td><code>submitText</code></td><td>string</td><td><code>__('daisy::form.search')</code></td><td>Texte du submit.</td></tr>
                    <tr><td><code>resetText</code></td><td>string</td><td><code>__('daisy::form.reset')</code></td><td>Texte du reset.</td></tr>
                    <tr><td><code>activeFilters</code></td><td>array</td><td><code>[]</code></td><td>Badges de filtres actifs.</td></tr>
                    <tr><td><code>showAdvanced</code></td><td>bool</td><td><code>false</code></td><td>Affiche le drawer de filtres avancés.</td></tr>
                    <tr><td><code>advancedTitle</code></td><td>string</td><td><code>__('daisy::form.advanced_filters')</code></td><td>Titre du drawer.</td></tr>
                    <tr><td><code>autoRefreshCsrf</code></td><td>bool</td><td><code>true</code></td><td>Insère <code>csrf-keeper</code> pour POST.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>filters</code> : champs principaux.</li>
                <li><code>actions</code> : actions à droite (remplace boutons par défaut).</li>
                <li><code>advanced</code> : contenu du drawer avancé.</li>
            </ul>
        </div>
        <p class="mt-4 text-sm text-base-content/70">JS : <code>data-module="inline"</code> pour gérer le reset des tokens et le panneau avancé.</p>
    </section>
</x-daisy::layout.docs>

