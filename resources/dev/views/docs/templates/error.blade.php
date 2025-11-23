@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Template Error" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section>
        <h1>Template Error</h1>
        <p class="text-base-content/70">
            Template généralisé pour toutes les pages d'erreur HTTP (404, 403, 500, 503, etc.), compatible avec le système de gestion des erreurs Laravel.
        </p>
    </section>

    <section class="mt-8">
        <h2>Utilisation</h2>
        
        <div class="mt-4">
            <h3>Dans les vues d'erreur Laravel</h3>
            <p class="text-sm text-base-content/70 mb-4">
                Placez ce template dans <code>resources/views/errors/</code> pour remplacer les vues d'erreur par défaut :
            </p>
            
            <div class="mockup-code">
                <pre data-prefix="$"><code>resources/views/errors/404.blade.php</code></pre>
                <pre data-prefix=""><code>&lt;x-daisy::templates.error statusCode="404" /&gt;</code></pre>
            </div>
            
            <div class="mockup-code mt-4">
                <pre data-prefix="$"><code>resources/views/errors/500.blade.php</code></pre>
                <pre data-prefix=""><code>&lt;x-daisy::templates.error statusCode="500" /&gt;</code></pre>
            </div>
            
            <div class="mockup-code mt-4">
                <pre data-prefix="$"><code>resources/views/errors/403.blade.php</code></pre>
                <pre data-prefix=""><code>&lt;x-daisy::templates.error statusCode="403" /&gt;</code></pre>
            </div>
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
                        <td><code>statusCode</code></td>
                        <td>int</td>
                        <td><code>500</code></td>
                        <td>Code d'erreur HTTP (404, 403, 500, 503, etc.)</td>
                    </tr>
                    <tr>
                        <td><code>title</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Titre de l'erreur (auto-généré si null)</td>
                    </tr>
                    <tr>
                        <td><code>message</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Message d'erreur (auto-généré si null)</td>
                    </tr>
                    <tr>
                        <td><code>theme</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Thème daisyUI à appliquer</td>
                    </tr>
                    <tr>
                        <td><code>homeUrl</code></td>
                        <td>string</td>
                        <td><code>route('home')</code></td>
                        <td>URL de la page d'accueil</td>
                    </tr>
                    <tr>
                        <td><code>backUrl</code></td>
                        <td>string</td>
                        <td><code>url()->previous()</code></td>
                        <td>URL de retour (page précédente)</td>
                    </tr>
                    <tr>
                        <td><code>showIllustration</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Afficher l'illustration d'erreur</td>
                    </tr>
                    <tr>
                        <td><code>showActions</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Afficher les boutons d'action (retour/accueil)</td>
                    </tr>
                    <tr>
                        <td><code>showDetails</code></td>
                        <td>bool|null</td>
                        <td><code>null</code></td>
                        <td>Afficher les détails d'erreur (auto-détecté depuis <code>config('app.debug')</code>)</td>
                    </tr>
                    <tr>
                        <td><code>exception</code></td>
                        <td>Exception|null</td>
                        <td><code>null</code></td>
                        <td>Exception Laravel (injectée automatiquement)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8">
        <h2>🔒 Sécurité</h2>
        
        <x-daisy::ui.feedback.alert color="warning" variant="soft" class="mt-4">
            <p class="font-semibold mb-2">Protection automatique des informations sensibles</p>
            <p class="text-sm">
                Le template <strong>ne dévoilera jamais</strong> les détails d'erreur (message d'exception, stack trace) en production.
            </p>
            <ul class="list-disc list-inside text-sm mt-2 space-y-1">
                <li>Les détails d'erreur ne sont affichés que si <code>config('app.debug')</code> est <code>true</code></li>
                <li>Même si <code>showDetails=true</code> est passé manuellement, il sera ignoré en production</li>
                <li>En production, seuls le code d'erreur et un message générique sont affichés</li>
            </ul>
        </x-daisy::ui.feedback.alert>
    </section>

    <section class="mt-8">
        <h2>Traductions</h2>
        
        <p class="text-sm text-base-content/70 mb-4">
            Les messages sont traduits automatiquement depuis <code>resources/lang/{locale}/errors.php</code> :
        </p>
        
        <div class="mockup-code">
            <pre data-prefix=""><code>404_title => "Page non trouvée"</code></pre>
            <pre data-prefix=""><code>404_message => "La page que vous recherchez n'existe pas."</code></pre>
            <pre data-prefix=""><code>500_title => "Erreur serveur"</code></pre>
            <pre data-prefix=""><code>500_message => "Une erreur s'est produite. Veuillez réessayer plus tard."</code></pre>
        </div>
    </section>

    <section class="mt-8">
        <h2>Composants utilisés</h2>
        
        <p class="text-sm text-base-content/70 mb-4">
            Ce template utilise les composants suivants (hiérarchie Atomic Design) :
        </p>
        
        <ul class="list-disc list-inside space-y-2 text-sm">
            <li><code>x-daisy::layout.app</code> - Layout de base</li>
            <li><code>x-daisy::ui.layout.hero</code> - Illustration d'erreur</li>
            <li><code>x-daisy::ui.errors.error-content</code> - Organisme contenant :
                <ul class="list-disc list-inside ml-4 mt-1">
                    <li><code>x-daisy::ui.errors.error-header</code> - Badge + titre</li>
                    <li><code>x-daisy::ui.errors.error-actions</code> - Boutons d'action</li>
                    <li><code>x-daisy::ui.feedback.alert</code> - Détails debug (si activé)</li>
                </ul>
            </li>
        </ul>
    </section>
</x-daisy::layout.docs>

