@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Page d'erreur" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Page d'erreur</h1>
        <p class="text-base-content/70">Template générique pour erreurs HTTP (404, 403, 500, 503…).</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.errors.error')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.errors.error /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.errors.error
    statusCode="404"
    title="Page non trouvée"
    message="La ressource demandée est introuvable."
    homeUrl="{{ url('/') }}"
    backUrl="{{ url()->previous() }}"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>statusCode</code></td><td>int</td><td><code>500</code></td><td>Code HTTP à afficher.</td></tr>
                    <tr><td><code>title</code></td><td>string|null</td><td><code>null</code></td><td>Titre (auto-généré si null).</td></tr>
                    <tr><td><code>message</code></td><td>string|null</td><td><code>null</code></td><td>Message (auto-généré).</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>homeUrl</code></td><td>string</td><td><code>route('home')</code></td><td>Lien vers l’accueil.</td></tr>
                    <tr><td><code>backUrl</code></td><td>string</td><td><code>url()->previous()</code></td><td>Lien retour.</td></tr>
                    <tr><td><code>showIllustration</code></td><td>bool</td><td><code>true</code></td><td>Affiche l’illustration.</td></tr>
                    <tr><td><code>showActions</code></td><td>bool</td><td><code>true</code></td><td>Affiche les boutons.</td></tr>
                    <tr><td><code>showDetails</code></td><td>bool|null</td><td><code>null</code></td><td>Détails debug (uniquement si <code>app.debug</code> true).</td></tr>
                    <tr><td><code>exception</code></td><td>Exception|null</td><td><code>null</code></td><td>Exception transmise par Laravel.</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

