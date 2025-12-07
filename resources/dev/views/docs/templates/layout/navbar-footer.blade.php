@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Navbar + footer" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Layout navbar + footer</h1>
        <p class="text-base-content/70">Structure classique : navbar configurable, contenu avec padding, footer riche.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.layout.navbar-footer')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.layout.navbar-footer /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.layout.navbar-footer
    title="Page"
    :navbarFixed="true"
    navbarBg="base-100"
    container="container mx-auto p-6"
    :footerColumns="[['title' =&gt; 'Liens', 'links' =&gt; [['label' =&gt; 'Accueil', 'href' =&gt; '/']]]]"
    footerBrandText="Daisy Kit"
/&gt;
&lt;x-slot:navbarStart&gt;Logo&lt;/x-slot:navbarStart&gt;
&lt;x-slot:navbarCenter&gt;Menu&lt;/x-slot:navbarCenter&gt;
&lt;x-slot:navbarEnd&gt;Actions&lt;/x-slot:navbarEnd&gt;
&lt;x-slot&gt;Contenu page&lt;/x-slot&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props principales</h2>
        <p class="text-sm text-base-content/70 mt-2">Combine les props navbar (<code>navbarBg</code>, <code>navbarShadow</code>, <code>navbarFixed</code>, <code>navbarFixedPosition</code>, <code>container</code>) et toutes les props footer (<code>footerColumns</code>, marque, newsletter, copyright, etc.).</p>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>navbarStart</code>, <code>navbarCenter</code>, <code>navbarEnd</code> (ou <code>brand</code>/<code>nav</code>/<code>actions</code>).</li>
                <li><code>default</code> : contenu principal.</li>
                <li><code>columns</code>, <code>copyright</code>, <code>footerBottom</code> pour personnaliser le footer.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

