@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Formulaire à onglets" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Formulaire à onglets</h1>
        <p class="text-base-content/70">Organisation du formulaire en onglets avec badges d’erreurs, persistance de l’onglet actif et mapping champ → onglet.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.form.form-with-tabs')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.form.form-with-tabs /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.form.form-with-tabs
    action="/profile"
    method="POST"
    :tabs="[['id' =&gt; 'general', 'label' =&gt; 'Général'], ['id' =&gt; 'advanced', 'label' =&gt; 'Avancé']]"
    tabsStyle="box"
    :showErrorBadges="true"
    :fieldToTabMap="['email' =&gt; 'general', 'api_key' =&gt; 'advanced']"
/&gt;
&lt;x-slot:tab_general&gt;...&lt;/x-slot:tab_general&gt;
&lt;x-slot:tab_advanced&gt;...&lt;/x-slot:tab_advanced&gt;
&lt;x-slot:actions&gt;
    &lt;x-daisy::ui.inputs.button type="submit"&gt;Enregistrer&lt;/x-daisy::ui.inputs.button&gt;
&lt;/x-slot:actions&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('daisy::form.tabs.title')</code></td><td>Titre optionnel.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td><code>#</code></td><td>URL du formulaire.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode HTTP.</td></tr>
                    <tr><td><code>tabs</code></td><td>array</td><td><code>[]</code></td><td>Onglets <code>[id, label]</code>.</td></tr>
                    <tr><td><code>activeTab</code></td><td>string|null</td><td>Premier onglet</td><td>Onglet actif par défaut.</td></tr>
                    <tr><td><code>tabsStyle</code></td><td>string</td><td><code>box</code></td><td>Style (box|border|lift).</td></tr>
                    <tr><td><code>tabsPlacement</code></td><td>string</td><td><code>top</code></td><td>Position des onglets.</td></tr>
                    <tr><td><code>highlightErrors</code></td><td>bool</td><td><code>true</code></td><td>Marque les tab en erreur.</td></tr>
                    <tr><td><code>showErrorBadges</code></td><td>bool</td><td><code>true</code></td><td>Affiche le compteur d’erreurs.</td></tr>
                    <tr><td><code>persistActiveTabField</code></td><td>string</td><td><code>_active_tab</code></td><td>Nom du champ caché de persistance.</td></tr>
                    <tr><td><code>fieldToTabMap</code></td><td>array</td><td><code>[]</code></td><td>Associe champs ↔ onglets pour compter les erreurs.</td></tr>
                    <tr><td><code>autoRefreshCsrf</code></td><td>bool</td><td><code>true</code></td><td>Insère <code>csrf-keeper</code> (POST).</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>tab_{id}</code> : contenu de chaque onglet.</li>
                <li><code>actions</code> : barre d’actions (submit, secondaire).</li>
            </ul>
        </div>
        <p class="mt-4 text-sm text-base-content/70">JS : <code>data-module="tabs"</code> et persistance d’onglet via <code>persistActiveTabField</code>.</p>
    </section>
</x-daisy::layout.docs>

