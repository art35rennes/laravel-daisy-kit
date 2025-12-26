@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Maintenance" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Maintenance</h1>
        <p class="text-base-content/70">Page de maintenance avec message configurable, estimation de retour et liste d’IP autorisées.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.errors.maintenance')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.errors.maintenance /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.errors.maintenance
    title="Maintenance en cours"
    message="Nous revenons vite."
    :retryAfter="3600"
    :allowedIps="['127.0.0.1']"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('maintenance.maintenance')</code></td><td>Titre principal.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>message</code></td><td>string|null</td><td><code>__('maintenance.message')</code></td><td>Message affiché.</td></tr>
                    <tr><td><code>retryAfter</code></td><td>int|null</td><td><code>null</code></td><td>Secondes avant retour (affiche estimation si présent).</td></tr>
                    <tr><td><code>allowedIps</code></td><td>array</td><td><code>[]</code></td><td>IPs autorisées (affiche une alerte info).</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

