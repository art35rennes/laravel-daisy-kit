@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="État vide" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>État vide</h1>
        <p class="text-base-content/70">Carte d’état vide avec illustration optionnelle et CTA.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.errors.empty-state')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.errors.empty-state /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.errors.empty-state
    icon="bi-inbox"
    title="Aucune donnée"
    message="Créez votre premier élément."
    actionLabel="Créer"
    actionUrl="/items/create"
    size="md"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>icon</code></td><td>string</td><td><code>bi-inbox</code></td><td>Icône Blade Icons.</td></tr>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('common.empty')</code></td><td>Titre.</td></tr>
                    <tr><td><code>message</code></td><td>string|null</td><td><code>null</code></td><td>Texte descriptif.</td></tr>
                    <tr><td><code>actionLabel</code></td><td>string|null</td><td><code>null</code></td><td>Libellé du CTA.</td></tr>
                    <tr><td><code>actionUrl</code></td><td>string|null</td><td><code>null</code></td><td>Lien du CTA.</td></tr>
                    <tr><td><code>actionVariant</code></td><td>string</td><td><code>primary</code></td><td>Variante du bouton.</td></tr>
                    <tr><td><code>size</code></td><td>string</td><td><code>md</code></td><td>Taille (xs|sm|md|lg).</td></tr>
                    <tr><td><code>illustration</code></td><td>string|null</td><td><code>null</code></td><td>URL d’illustration.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

