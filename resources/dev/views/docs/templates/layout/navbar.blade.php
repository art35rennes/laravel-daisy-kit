@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Layout avec navbar" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Layout avec navbar</h1>
        <p class="text-base-content/70">Barre de navigation fixe ou non, avec slots start/center/end et contenu principal conteneur.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.layout.navbar')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.layout.navbar /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.layout.navbar
    title="Page"
    navbarBg="base-100"
    :navbarFixed="true"
    navbarFixedPosition="top"
    container="container mx-auto p-6"
/&gt;
&lt;x-slot:navbarStart&gt;Logo&lt;/x-slot:navbarStart&gt;
&lt;x-slot:navbarCenter&gt;Menu&lt;/x-slot:navbarCenter&gt;
&lt;x-slot:navbarEnd&gt;&lt;x-daisy::ui.inputs.button size="sm"&gt;Action&lt;/x-daisy::ui.inputs.button&gt;&lt;/x-slot:navbarEnd&gt;
&lt;x-slot&gt;Contenu page&lt;/x-slot&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string|null</td><td><code>null</code></td><td>Titre de page.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>navbarBg</code></td><td>string</td><td><code>base-100</code></td><td>Couleur de fond.</td></tr>
                    <tr><td><code>navbarText</code></td><td>string|null</td><td><code>null</code></td><td>Couleur texte (optionnelle).</td></tr>
                    <tr><td><code>navbarShadow</code></td><td>string</td><td><code>sm</code></td><td>Niveau d’ombre.</td></tr>
                    <tr><td><code>navbarFixed</code></td><td>bool</td><td><code>true</code></td><td>Fixe la navbar.</td></tr>
                    <tr><td><code>navbarFixedPosition</code></td><td>string</td><td><code>top</code></td><td>Position (top|bottom).</td></tr>
                    <tr><td><code>container</code></td><td>string</td><td><code>container mx-auto p-6</code></td><td>Classes du contenu.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>navbarStart</code>, <code>navbarCenter</code>, <code>navbarEnd</code> (ou <code>brand</code>/<code>nav</code>/<code>actions</code>).</li>
                <li><code>default</code> : contenu principal.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

