@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
            ['id' => 'intro', 'label' => 'Introduction'],
            ['id' => 'base', 'label' => 'Exemple de base'],
            ['id' => 'variants', 'label' => 'Variantes'],
            ['id' => 'api', 'label' => 'API'],
        ];
    $props = DocsHelper::getComponentProps('errors', 'error-content');
@endphp

<x-daisy::layout.docs title="Error Content" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Error Content</h1>
        <p>Composant organisme pour afficher le contenu d'une page d'erreur HTTP (404, 500, 403, etc.).</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-error-content" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-content 
                        statusCode="404" 
                        title="Page non trouvée"
                        message="La page que vous recherchez n'existe pas ou a été déplacée."
                    />
                </div>
            </div>
            <input type="radio" name="base-example-error-content" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.errors.error-content 
    statusCode="404" 
    title="Page non trouvée"
    message="La page que vous recherchez n\'existe pas ou a été déplacée."
/>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$baseCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="200px"
                />
            </div>
        </div>
    </section>

    <section id="variants" class="mt-10">
        <h2>Variantes</h2>
        
        <h3 class="mt-6">Erreur 500</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-500-error-content" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-content 
                        statusCode="500" 
                        title="Erreur serveur"
                        message="Une erreur interne s'est produite. Veuillez réessayer plus tard."
                    />
                </div>
            </div>
            <input type="radio" name="variants-500-error-content" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $code500 = '<x-daisy::ui.errors.error-content 
    statusCode="500" 
    title="Erreur serveur"
    message="Une erreur interne s\'est produite. Veuillez réessayer plus tard."
/>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$code500"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="200px"
                />
            </div>
        </div>

        <h3 class="mt-6">Sans actions</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-no-actions-error-content" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-content 
                        statusCode="403" 
                        title="Accès refusé"
                        message="Vous n'avez pas les permissions nécessaires pour accéder à cette ressource."
                        :showActions="false"
                    />
                </div>
            </div>
            <input type="radio" name="variants-no-actions-error-content" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $codeNoActions = '<x-daisy::ui.errors.error-content 
    statusCode="403" 
    title="Accès refusé"
    message="Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource."
    :showActions="false"
/>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$codeNoActions"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="200px"
                />
            </div>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>API</h2>
        
        <h3 class="mt-6">Props disponibles</h3>
        <div class="overflow-x-auto">
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
                        <td><code>int</code></td>
                        <td><code>500</code></td>
                        <td>Code d'erreur HTTP (404, 500, 403, etc.)</td>
                    </tr>
                    <tr>
                        <td><code>title</code></td>
                        <td><code>string|null</code></td>
                        <td><code>null</code></td>
                        <td>Titre de l'erreur. Si null, généré automatiquement selon le code.</td>
                    </tr>
                    <tr>
                        <td><code>message</code></td>
                        <td><code>string|null</code></td>
                        <td><code>null</code></td>
                        <td>Message d'erreur. Si null, généré automatiquement selon le code.</td>
                    </tr>
                    <tr>
                        <td><code>homeUrl</code></td>
                        <td><code>string</code></td>
                        <td><code>route('home') ou '/'</code></td>
                        <td>URL de la page d'accueil pour le bouton "Accueil".</td>
                    </tr>
                    <tr>
                        <td><code>backUrl</code></td>
                        <td><code>string</code></td>
                        <td><code>url()->previous()</code></td>
                        <td>URL de retour pour le bouton "Retour".</td>
                    </tr>
                    <tr>
                        <td><code>showActions</code></td>
                        <td><code>bool</code></td>
                        <td><code>true</code></td>
                        <td>Afficher les boutons d'action (Accueil, Retour).</td>
                    </tr>
                    <tr>
                        <td><code>showDetails</code></td>
                        <td><code>bool|null</code></td>
                        <td><code>null</code></td>
                        <td>Afficher les détails de debug. Auto-détecté depuis <code>config('app.debug')</code> en production.</td>
                    </tr>
                    <tr>
                        <td><code>exception</code></td>
                        <td><code>\Throwable|null</code></td>
                        <td><code>null</code></td>
                        <td>Exception Laravel pour afficher les détails de debug (uniquement si <code>app.debug=true</code>).</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="mt-6">Composants utilisés</h3>
        <p class="text-sm text-base-content/70 mb-4">
            Ce composant utilise les composants suivants (hiérarchie Atomic Design) :
        </p>
        
        <ul class="list-disc list-inside space-y-2 text-sm">
            <li><code>x-daisy::ui.layout.card</code> - Conteneur principal</li>
            <li><code>x-daisy::ui.errors.error-header</code> - En-tête avec badge et titre</li>
            <li><code>x-daisy::ui.errors.error-actions</code> - Boutons d'action</li>
            <li><code>x-daisy::ui.feedback.alert</code> - Détails de debug (si activé)</li>
        </ul>
    </section>
</x-daisy::layout.docs>

