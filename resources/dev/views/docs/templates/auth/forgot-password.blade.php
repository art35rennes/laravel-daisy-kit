@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Mot de passe oublié" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Mot de passe oublié</h1>
        <p class="text-base-content/70">Formulaire d’envoi de lien de réinitialisation avec message de statut Laravel.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.auth.forgot-password')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.forgot-password /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.auth.forgot-password
    action="{{ route('password.email') }}"
    loginUrl="{{ route('login') }}"
    :status="session('status')"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.forgot_password')</code></td><td>Titre.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td>route('password.email') ou <code>#</code></td><td>URL de soumission.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode HTTP.</td></tr>
                    <tr><td><code>loginUrl</code></td><td>string</td><td>route('login') ou <code>#</code></td><td>Lien retour connexion.</td></tr>
                    <tr><td><code>status</code></td><td>mixed</td><td><code>session('status')</code></td><td>Message de confirmation.</td></tr>
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

