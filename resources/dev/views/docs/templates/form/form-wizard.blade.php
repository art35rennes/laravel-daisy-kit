@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Wizard multi-étapes" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Wizard multi-étapes</h1>
        <p class="text-base-content/70">Assistant linéaire ou libre avec persistance session, navigation verrouillable et résumé final.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.form.form-wizard')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.form.form-wizard /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.form.form-wizard
    action="/wizard"
    :steps="[['key' =&gt; 'profile', 'label' =&gt; 'Profil'], ['key' =&gt; 'settings', 'label' =&gt; 'Paramètres']]"
    :linear="true"
    :allowClickNav="false"
    wizardKey="onboarding"
/&gt;
&lt;x-slot:step_profile&gt;...&lt;/x-slot:step_profile&gt;
&lt;x-slot:step_settings&gt;...&lt;/x-slot:step_settings&gt;
&lt;x-slot:summary&gt;Résumé final&lt;/x-slot:summary&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('daisy::form.wizard.title')</code></td><td>Titre (optionnel).</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td><code>#</code></td><td>URL du submit.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode.</td></tr>
                    <tr><td><code>steps</code></td><td>array</td><td><code>[]</code></td><td>Étapes <code>[key, label, icon]</code>.</td></tr>
                    <tr><td><code>currentStep</code></td><td>int</td><td><code>1</code></td><td>Étape initiale.</td></tr>
                    <tr><td><code>linear</code></td><td>bool</td><td><code>true</code></td><td>Interdit de sauter des étapes.</td></tr>
                    <tr><td><code>allowClickNav</code></td><td>bool</td><td><code>false</code></td><td>Autorise clic direct sur le stepper.</td></tr>
                    <tr><td><code>showSummary</code></td><td>bool</td><td><code>true</code></td><td>Affiche la section résumé.</td></tr>
                    <tr><td><code>prevText</code></td><td>string</td><td><code>__('daisy::form.previous')</code></td><td>Bouton précédent.</td></tr>
                    <tr><td><code>nextText</code></td><td>string</td><td><code>__('daisy::form.next')</code></td><td>Bouton suivant.</td></tr>
                    <tr><td><code>finishText</code></td><td>string</td><td><code>__('daisy::form.finish')</code></td><td>Bouton terminer.</td></tr>
                    <tr><td><code>resumeSlot</code></td><td>string</td><td><code>summary</code></td><td>Slot utilisé pour le résumé.</td></tr>
                    <tr><td><code>autoRefreshCsrf</code></td><td>bool</td><td><code>true</code></td><td>Insère <code>csrf-keeper</code> (POST).</td></tr>
                    <tr><td><code>wizardKey</code></td><td>string</td><td><code>wizard</code></td><td>Clé de persistance session.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>step_{key}</code> : contenu de chaque étape.</li>
                <li><code>summary</code> (ou valeur de <code>resumeSlot</code>) : résumé final.</li>
                <li><code>actions</code> : personnalisation des boutons.</li>
            </ul>
        </div>
        <p class="mt-4 text-sm text-base-content/70">JS : <code>data-module="wizard"</code> + persistance via <code>wizardKey</code> (<code>WizardPersistence</code>).</p>
    </section>
</x-daisy::layout.docs>

