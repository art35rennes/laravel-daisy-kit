@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Inscription simple" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Inscription simple</h1>
        <p class="text-base-content/70">Formulaire d’inscription centré avec confirmation de mot de passe et acceptation des CGU.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.auth.register-simple')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.register-simple /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.auth.register-simple
    action="@{{ route('register') }}"
    loginUrl="@{{ route('login') }}"
    :passwordConfirmation="true"
    termsUrl="/terms"
    privacyUrl="/privacy"
/&gt;</code></pre>
        </div>
        <p class="mt-3 text-sm text-base-content/70">Slots : <code>logo</code>, <code>socialLogin</code>.</p>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.register')</code></td><td>Titre de la page.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td>route('register') ou <code>#</code></td><td>URL de soumission.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode du formulaire.</td></tr>
                    <tr><td><code>loginUrl</code></td><td>string</td><td>route('login') ou <code>#</code></td><td>Lien vers la connexion.</td></tr>
                    <tr><td><code>passwordConfirmation</code></td><td>bool</td><td><code>true</code></td><td>Affiche le champ de confirmation.</td></tr>
                    <tr><td><code>termsUrl</code></td><td>string|null</td><td><code>null</code></td><td>Lien vers les CGU.</td></tr>
                    <tr><td><code>privacyUrl</code></td><td>string|null</td><td><code>null</code></td><td>Lien vers la politique de confidentialité.</td></tr>
                    <tr><td><code>acceptTerms</code></td><td>bool</td><td><code>true</code></td><td>Affiche la case d’acceptation.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>logo</code> : zone de marque.</li>
                <li><code>socialLogin</code> : boutons SSO avant le formulaire.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

