@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Renvoyer l'email de vérification" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Renvoyer l'email de vérification</h1>
        <p class="text-base-content/70">Écran dédié à la relance du lien de vérification avec message de succès.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.auth.resend-verification')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.resend-verification /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.auth.resend-verification
    action="{{ route('verification.send') }}"
    :emailSent="session('status') !== null"
    submitButtonText="{{ __('auth.resend_verification') }}"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.resend_verification')</code></td><td>Titre.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>description</code></td><td>string</td><td><code>__('auth.resend_verification')</code></td><td>Sous-titre.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td>route('verification.send') ou <code>#</code></td><td>URL du POST.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode du formulaire.</td></tr>
                    <tr><td><code>submitButtonText</code></td><td>string</td><td><code>__('auth.resend_verification')</code></td><td>Libellé bouton.</td></tr>
                    <tr><td><code>emailSent</code></td><td>bool</td><td><code>(bool) session('status')</code></td><td>Affiche l’alerte succès.</td></tr>
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

