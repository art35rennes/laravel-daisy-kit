@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Double authentification" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Double authentification</h1>
        <p class="text-base-content/70">Saisie OTP 6 chiffres avec alertes et liens récupération / déconnexion.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.auth.two-factor')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.two-factor /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.auth.two-factor
    action="{{ route('two-factor.login') }}"
    recoveryUrl="{{ route('two-factor.recovery') }}"
    logoutUrl="{{ route('logout') }}"
    :showRecovery="true"
    :showLogout="true"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.two_factor')</code></td><td>Titre.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td>route('two-factor.login') ou <code>#</code></td><td>URL de vérification.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode.</td></tr>
                    <tr><td><code>recoveryUrl</code></td><td>string</td><td>route('two-factor.recovery') ou <code>#</code></td><td>Lien codes secours.</td></tr>
                    <tr><td><code>logoutUrl</code></td><td>string</td><td>route('logout') ou <code>#</code></td><td>Lien déconnexion.</td></tr>
                    <tr><td><code>showRecovery</code></td><td>bool</td><td><code>true</code></td><td>Affiche lien récupération.</td></tr>
                    <tr><td><code>showLogout</code></td><td>bool</td><td><code>true</code></td><td>Affiche bouton déconnexion.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>logo</code> : bloc de marque.</li>
            </ul>
        </div>
    </section>
</x-daisy::layout.docs>


