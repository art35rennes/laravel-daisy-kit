@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Login simple" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Login simple</h1>
        <p class="text-base-content/70">
            Formulaire d’authentification compact avec rappel du mot de passe, case « se souvenir » et CTA d’inscription.
        </p>
        <div class="mt-4 space-y-2 text-sm">
            <p><strong>Vue Blade :</strong> <code>{{ "view('daisy::templates.auth.login-simple')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.login-simple /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple minimal</h2>
        <div class="mockup-code mt-4">
            <pre data-prefix=""><code>&lt;x-daisy::templates.auth.login-simple
    action="@{{ route('login') }}"
    :rememberMe="true"
    :rememberMeDays="30"
    forgotPasswordUrl="@{{ route('password.request') }}"
    signupUrl="@{{ route('register') }}"
/&gt;</code></pre>
        </div>
        <p class="mt-3 text-sm text-base-content/70">Slots disponibles : <code>logo</code> pour le bloc marque, <code>socialLogin</code> pour des boutons SSO.</p>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Type</th>
                        <th>Défaut</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.welcome')</code></td><td>Titre affiché.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI à appliquer.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td>route('login') ou <code>#</code></td><td>URL de soumission.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode HTML.</td></tr>
                    <tr><td><code>rememberMe</code></td><td>bool</td><td><code>true</code></td><td>Affiche la case « se souvenir ».</td></tr>
                    <tr><td><code>rememberMeDays</code></td><td>int</td><td><code>30</code></td><td>Durée affichée dans le texte.</td></tr>
                    <tr><td><code>forgotPasswordUrl</code></td><td>string</td><td>route('password.request') ou <code>#</code></td><td>Lien de récupération.</td></tr>
                    <tr><td><code>signupUrl</code></td><td>string</td><td>route('register') ou <code>#</code></td><td>Lien d’inscription.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>logo</code> : zone logo/branding.</li>
                <li><code>socialLogin</code> : boutons de connexion sociale affichés au-dessus du formulaire.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

