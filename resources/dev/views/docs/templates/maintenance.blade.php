@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Template Maintenance" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section>
        <h1>Template Maintenance</h1>
        <p class="text-base-content/70">
            Page de maintenance affichée quand l'application est en mode maintenance (<code>php artisan down</code>).
        </p>
    </section>

    <section class="mt-8">
        <h2>Utilisation</h2>
        
        <div class="mt-4">
            <h3>Configuration Laravel</h3>
            <p class="text-sm text-base-content/70 mb-4">
                Pour utiliser ce template comme vue de maintenance par défaut, créez <code>resources/views/errors/503.blade.php</code> :
            </p>
            
            <div class="mockup-code">
                <pre data-prefix="$"><code>resources/views/errors/503.blade.php</code></pre>
                <pre data-prefix=""><code>&lt;x-daisy::templates.maintenance /&gt;</code></pre>
            </div>
            
            <p class="text-sm text-base-content/70 mt-4">
                Ou utilisez-le directement dans votre handler d'exception pour le mode maintenance.
            </p>
        </div>
    </section>

    <section class="mt-8">
        <h2>Props disponibles</h2>
        
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Type</th>
                        <th>Défaut</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>title</code></td>
                        <td>string</td>
                        <td><code>__('maintenance.maintenance')</code></td>
                        <td>Titre de la page de maintenance</td>
                    </tr>
                    <tr>
                        <td><code>theme</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Thème daisyUI à appliquer</td>
                    </tr>
                    <tr>
                        <td><code>message</code></td>
                        <td>string|null</td>
                        <td><code>__('maintenance.message')</code></td>
                        <td>Message de maintenance personnalisé</td>
                    </tr>
                    <tr>
                        <td><code>retryAfter</code></td>
                        <td>int|null</td>
                        <td><code>null</code></td>
                        <td>Nombre de secondes avant retour estimé (Retry-After header)</td>
                    </tr>
                    <tr>
                        <td><code>allowedIps</code></td>
                        <td>array</td>
                        <td><code>[]</code></td>
                        <td>Liste des adresses IP autorisées pendant la maintenance</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8">
        <h2>Traductions</h2>
        
        <p class="text-sm text-base-content/70 mb-4">
            Les messages sont traduits depuis <code>resources/lang/{locale}/maintenance.php</code> :
        </p>
        
        <div class="mockup-code">
            <pre data-prefix=""><code>maintenance => "Maintenance en cours"</code></pre>
            <pre data-prefix=""><code>message => "Nous effectuons actuellement une maintenance..."</code></pre>
            <pre data-prefix=""><code>estimated_return => "Retour estimé : :time"</code></pre>
        </div>
    </section>
</x-daisy::layout.docs>

