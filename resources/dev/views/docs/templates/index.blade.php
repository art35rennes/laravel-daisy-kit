@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
        ['id' => 'templates', 'label' => 'Templates'],
    ];
@endphp

<x-daisy::layout.docs title="Templates" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section id="templates">
        <h1>Templates</h1>
        <p>Accédez rapidement à des structures de pages prêtes à l’emploi.</p>

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Navbar Layout</h3>
                    <p class="text-sm">Barre de navigation en haut de page avec menu horizontal et actions.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('layouts.navbar') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Sidebar Layout</h3>
                    <p class="text-sm">Barre latérale de navigation avec menu vertical et sous-menus.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('layouts.sidebar') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Navbar + Sidebar</h3>
                    <p class="text-sm">Combinaison navbar et sidebar pour applications complexes.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('layouts.navbar-sidebar') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>

            {{-- Auth Templates --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Auth • Login Split</h3>
                    <p class="text-sm">Page de connexion en deux colonnes avec illustration/témoignage.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('templates.auth.login-split') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Auth • Login Simple</h3>
                    <p class="text-sm">Page de connexion simple et centrée.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('templates.auth.login-simple') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Auth • Reset Password</h3>
                    <p class="text-sm">Formulaire d’envoi de lien de réinitialisation.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('templates.auth.reset-password') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Auth • Verify Email</h3>
                    <p class="text-sm">Confirmation d’email avec bouton de renvoi.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('templates.auth.verify-email') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Auth • Resend Verification</h3>
                    <p class="text-sm">Formulaire pour renvoyer l’email de vérification.</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('templates.auth.resend-verification') }}" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-daisy::layout.docs>


