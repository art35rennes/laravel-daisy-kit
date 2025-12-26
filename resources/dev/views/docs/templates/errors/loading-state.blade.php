@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="État de chargement" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>État de chargement</h1>
        <p class="text-base-content/70">Page d’attente avec spinner, skeleton ou progress, option plein écran.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.errors.loading-state')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.errors.loading-state /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.errors.loading-state
    type="spinner"
    message="{{ __('common.loading') }}"
    size="lg"
    :fullScreen="true"
    :skeletonCount="3"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>type</code></td><td>string</td><td><code>spinner</code></td><td><code>spinner</code>|<code>skeleton</code>|<code>progress</code>.</td></tr>
                    <tr><td><code>message</code></td><td>string</td><td><code>__('common.loading')</code></td><td>Texte affiché.</td></tr>
                    <tr><td><code>size</code></td><td>string</td><td><code>lg</code></td><td>Taille du loader.</td></tr>
                    <tr><td><code>fullScreen</code></td><td>bool</td><td><code>false</code></td><td>Min-height écran.</td></tr>
                    <tr><td><code>skeletonCount</code></td><td>int</td><td><code>3</code></td><td>Nombre de blocs skeleton.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

