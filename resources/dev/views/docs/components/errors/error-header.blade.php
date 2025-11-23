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
    $props = DocsHelper::getComponentProps('errors', 'error-header');
@endphp

<x-daisy::layout.docs title="Error Header" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Error Header</h1>
        <p>Composant molécule pour afficher l'en-tête d'une erreur avec le code d'erreur et le titre.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-error-header" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-header 
                        statusCode="404" 
                        title="Page non trouvée"
                    />
                </div>
            </div>
            <input type="radio" name="base-example-error-header" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.errors.error-header 
    statusCode="404" 
    title="Page non trouvée"
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
            <input type="radio" name="variants-500-error-header" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-header 
                        statusCode="500" 
                        title="Erreur serveur"
                    />
                </div>
            </div>
            <input type="radio" name="variants-500-error-header" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $code500 = '<x-daisy::ui.errors.error-header 
    statusCode="500" 
    title="Erreur serveur"
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

        <h3 class="mt-6">Erreur 403</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-403-error-header" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-header 
                        statusCode="403" 
                        title="Accès refusé"
                    />
                </div>
            </div>
            <input type="radio" name="variants-403-error-header" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $code403 = '<x-daisy::ui.errors.error-header 
    statusCode="403" 
    title="Accès refusé"
/>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$code403"
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
                </tbody>
            </table>
        </div>

        <h3 class="mt-6">Composants utilisés</h3>
        <p class="text-sm text-base-content/70 mb-4">
            Ce composant utilise les composants suivants (hiérarchie Atomic Design) :
        </p>
        
        <ul class="list-disc list-inside space-y-2 text-sm">
            <li><code>x-daisy::ui.data-display.badge</code> - Badge pour le code d'erreur</li>
        </ul>
    </section>
</x-daisy::layout.docs>

