@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Navbar + grille + footer" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Navbar + grille + footer</h1>
        <p class="text-base-content/70">Layout combinant navbar, grille centrale et footer configurable.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.layout.navbar-grid-footer')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.layout.navbar-grid-footer /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.layout.navbar-grid-footer
    title="Dashboard"
    :navbarFixed="true"
    gap="6"
    align="start"
    container="container mx-auto p-6"
    :footerColumns="[['title' =&gt; 'Support', 'links' =&gt; [['label' =&gt; 'FAQ', 'href' =&gt; '/faq']]]]"
/&gt;
&lt;x-slot:navbarStart&gt;Logo&lt;/x-slot:navbarStart&gt;
&lt;x-slot:navbarCenter&gt;Menu&lt;/x-slot:navbarCenter&gt;
&lt;x-slot:navbarEnd&gt;Actions&lt;/x-slot:navbarEnd&gt;
&lt;x-slot&gt;
    &lt;div class="card bg-base-100 p-4"&gt;Widget 1&lt;/div&gt;
    &lt;div class="card bg-base-100 p-4"&gt;Widget 2&lt;/div&gt;
&lt;/x-slot&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props principales</h2>
        <p class="text-sm text-base-content/70 mt-2">Combine les props navbar (<code>navbar*</code>), celles de la grille (<code>gap</code>, <code>align</code>, <code>container</code>) et toutes les props footer (colonnes, marque, newsletter, copyright, etc.).</p>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>navbarStart</code>, <code>navbarCenter</code>, <code>navbarEnd</code> (ou <code>brand</code>/<code>nav</code>/<code>actions</code>).</li>
                <li><code>default</code> : items de la grille.</li>
                <li><code>columns</code>, <code>copyright</code>, <code>footerBottom</code> pour le footer.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

