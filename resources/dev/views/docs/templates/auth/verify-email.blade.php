@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Vérification d'email" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Vérification d'email</h1>
        <p class="text-base-content/70">Écran de confirmation d’adresse avec CTA de renvoi et bouton de déconnexion.</p>
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.auth.verify-email')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.auth.verify-email /&gt;</code></p>
        </div>
    </section>

    <section id="example" class="mt-10">
        <h2>Exemple</h2>
        <div class="mockup-code mt-4">
<pre data-prefix=""><code>&lt;x-daisy::templates.auth.verify-email
    resendUrl="{{ route('verification.send') }}"
    logoutUrl="{{ route('logout') }}"
    :showLogout="true"
    resendButtonText="{{ __('auth.resend_verification') }}"
/&gt;</code></pre>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>Props</h2>
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string</td><td><code>__('auth.verify_email')</code></td><td>Titre.</td></tr>
                    <tr><td><code>theme</code></td><td>string|null</td><td><code>null</code></td><td>Thème daisyUI.</td></tr>
                    <tr><td><code>message</code></td><td>string</td><td><code>__('auth.verification_sent')</code></td><td>Texte principal.</td></tr>
                    <tr><td><code>resendUrl</code></td><td>string</td><td>route('verification.send') ou <code>#</code></td><td>URL de renvoi.</td></tr>
                    <tr><td><code>logoutUrl</code></td><td>string</td><td>route('logout') ou <code>#</code></td><td>URL de déconnexion.</td></tr>
                    <tr><td><code>showLogout</code></td><td>bool</td><td><code>true</code></td><td>Affiche le bouton « Déconnexion ».</td></tr>
                    <tr><td><code>resendButtonText</code></td><td>string</td><td><code>__('auth.resend_verification')</code></td><td>Libellé du bouton principal.</td></tr>
                    <tr><td><code>logoutText</code></td><td>string</td><td><code>__('auth.logout')</code></td><td>Libellé du bouton secondaire.</td></tr>
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

