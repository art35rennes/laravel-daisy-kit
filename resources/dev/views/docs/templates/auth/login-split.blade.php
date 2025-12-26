@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Login split" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <x-daisy::ui.overlay.dropdown label="Templates" buttonClass="btn btn-sm btn-ghost" end>
            <li><a href="/{{ $prefix }}">Docs</a></li>
            <li><a href="{{ route('demo') }}">Démo</a></li>
            <li><a href="/{{ $prefix }}/templates" class="menu-active">Templates</a></li>
        </x-daisy::ui.overlay.dropdown>
    </x-slot:navbar>

    <section id="intro">
        <h1>Login split</h1>
        <p class="text-base-content/70">
            Page d’authentification en deux colonnes avec illustration pleine hauteur et témoignage optionnel.
        </p>
        <div class="mt-4 space-y-2 text-sm">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.auth.login-split')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.login-split /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.auth.login-split
    action="@{{ route('login') }}"
    forgotPasswordUrl="@{{ route('password.request') }}"
    signupUrl="@{{ route('register') }}"
    backgroundImage="https://picsum.photos/1200/900"
    :showTestimonial="true"
    :testimonial="['quote' => 'Support réactif', 'author' => 'Jane', 'role' => 'CTO', 'rating' => 5]"
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
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.welcome')</code></td><td>Titre principal.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td>route('login') ou <code>#</code></td><td>URL de soumission.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode du formulaire.</td></tr>
                    <tr><td><code>rememberMe</code></td><td>bool</td><td><code>true</code></td><td>Affiche la case « se souvenir ».</td></tr>
                    <tr><td><code>rememberMeDays</code></td><td>int</td><td><code>30</code></td><td>Durée affichée.</td></tr>
                    <tr><td><code>forgotPasswordUrl</code></td><td>string</td><td>route('password.request') ou <code>#</code></td><td>Lien d’oubli.</td></tr>
                    <tr><td><code>signupUrl</code></td><td>string</td><td>route('register') ou <code>#</code></td><td>Lien d’inscription.</td></tr>
                    <tr><td><code>backgroundImage</code></td><td>string|null</td><td><code>null</code></td><td>Image de fond colonne droite.</td></tr>
                    <tr><td><code>showTestimonial</code></td><td>bool</td><td><code>false</code></td><td>Affiche un bloc témoignage.</td></tr>
                    <tr><td><code>testimonial</code></td><td>array|null</td><td><code>null</code></td><td>Données du témoignage (quote, author, role, avatar, rating).</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>logo</code> : bloc marque.</li>
                <li><code>socialLogin</code> : boutons sociaux avant le formulaire.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>

