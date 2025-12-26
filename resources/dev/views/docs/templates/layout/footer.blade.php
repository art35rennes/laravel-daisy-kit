@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Layout avec footer" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Layout avec footer</h1>
        <p class="text-base-content/70">Page avec contenu libre et footer riche (colonnes, newsletter, réseaux sociaux).</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.layout.footer')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.layout.footer /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.layout.footer
    title="Ma page"
    footerBg="base-200"
    :footerColumns="[['title' =&gt; 'Produit', 'links' =&gt; [['label' =&gt; 'Docs', 'href' =&gt; '/docs']]]]"
    footerBrandText="Daisy Kit"
    footerBrandDescription="UI Laravel + daisyUI"
    footerCopyright="Acme"
    footerSocialLinks="[['icon' =&gt; 'bi-github', 'href' =&gt; 'https://github.com']]"
/&gt;
&lt;x-slot&gt;
    &lt;div class="prose"&gt;Contenu principal&lt;/div&gt;
&lt;/x-slot&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props principales</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string|null</td><td><code>null</code></td><td>Titre de la page.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>container</code></td><td>string</td><td><code>container mx-auto p-6</code></td><td>Classes du contenu.</td></tr>
                    <tr><td><code>footerBg</code>, <code>footerText</code></td><td>string</td><td><code>base-200</code>/<code>base-content</code></td><td>Couleurs.</td></tr>
                    <tr><td><code>footerPadding</code></td><td>string</td><td><code>p-10</code></td><td>Padding du footer.</td></tr>
                    <tr><td><code>footerCenter</code></td><td>bool</td><td><code>false</code></td><td>Active <code>footer-center</code>.</td></tr>
                    <tr><td><code>footerHorizontal</code></td><td>bool</td><td><code>false</code></td><td>Active <code>footer-horizontal</code>.</td></tr>
                    <tr><td><code>footerHorizontalAt</code></td><td>string|null</td><td><code>null</code></td><td>Breakpoint responsive.</td></tr>
                    <tr><td><code>footerColumns</code></td><td>array</td><td><code>[]</code></td><td>Colonnes (titre, liens).</td></tr>
                    <tr><td><code>footerLogo</code>, <code>footerBrandText</code>, <code>footerBrandDescription</code></td><td>mixte</td><td><code>null</code></td><td>Zone marque.</td></tr>
                    <tr><td><code>footerSocialLinks</code></td><td>array</td><td><code>[]</code></td><td>Liens sociaux (icon, href).</td></tr>
                    <tr><td><code>footerNewsletter</code></td><td>bool</td><td><code>false</code></td><td>Affiche le bloc newsletter.</td></tr>
                    <tr><td><code>footerNewsletterTitle</code>, <code>footerNewsletterDescription</code>, <code>footerNewsletterAction</code>, <code>footerNewsletterMethod</code></td><td>mixte</td><td>-</td><td>Configuration newsletter.</td></tr>
                    <tr><td><code>footerShowDivider</code>, <code>footerDividerColor</code></td><td>mixte</td><td><code>true</code>/<code>null</code></td><td>Affichage du séparateur.</td></tr>
                    <tr><td><code>footerCopyright</code>, <code>footerCopyrightYear</code>, <code>footerCopyrightText</code></td><td>mixte</td><td><code>null</code></td><td>Mentions légales.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>default</code> : contenu principal.</li>
                <li><code>columns</code> : colonnes personnalisées.</li>
                <li><code>copyright</code> : mention personnalisée.</li>
                <li><code>footerBottom</code> : barre basse optionnelle.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

