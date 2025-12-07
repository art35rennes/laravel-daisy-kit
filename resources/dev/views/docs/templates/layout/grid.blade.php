@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Layout grille" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Layout grille</h1>
        <p class="text-base-content/70">Container grille réutilisable (gap, alignement, option conteneur).</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.layout.grid')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.layout.grid /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.layout.grid gap="6" align="start" :container="true"&gt;
    &lt;div class="card bg-base-100 p-4"&gt;Bloc 1&lt;/div&gt;
    &lt;div class="card bg-base-100 p-4"&gt;Bloc 2&lt;/div&gt;
&lt;/x-daisy::templates.layout.grid&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string|null</td><td><code>null</code></td><td>Titre page.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>gap</code></td><td>int</td><td><code>4</code></td><td>Gap tailwind (ex : 4 → <code>gap-4</code>).</td></tr>
                    <tr><td><code>align</code></td><td>string</td><td><code>start</code></td><td>Alignement (start|center|end).</td></tr>
                    <tr><td><code>container</code></td><td>bool</td><td><code>true</code></td><td>Enveloppe dans un container.</td></tr>
                    <tr><td><code>containerClass</code></td><td>string</td><td><code>container mx-auto p-6</code></td><td>Classes du container.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>default</code> : items de la grille.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

