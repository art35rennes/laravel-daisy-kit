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
    $props = DocsHelper::getComponentProps('errors', 'error-actions');
@endphp

<x-daisy::layout.docs title="Error Actions" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Error Actions</h1>
        <p>Composant molécule pour afficher les boutons d'action sur une page d'erreur (Accueil, Retour).</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-error-actions" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-actions />
                </div>
            </div>
            <input type="radio" name="base-example-error-actions" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.errors.error-actions />';
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
        
        <h3 class="mt-6">Sans bouton retour</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-no-back-error-actions" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-actions :showBack="false" />
                </div>
            </div>
            <input type="radio" name="variants-no-back-error-actions" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $codeNoBack = '<x-daisy::ui.errors.error-actions :showBack="false" />';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$codeNoBack"
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

        <h3 class="mt-6">Sans bouton accueil</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-no-home-error-actions" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.error-actions :showHome="false" />
                </div>
            </div>
            <input type="radio" name="variants-no-home-error-actions" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $codeNoHome = '<x-daisy::ui.errors.error-actions :showHome="false" />';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$codeNoHome"
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
                        <td><code>showBack</code></td>
                        <td><code>bool</code></td>
                        <td><code>true</code></td>
                        <td>Afficher le bouton "Retour".</td>
                    </tr>
                    <tr>
                        <td><code>showHome</code></td>
                        <td><code>bool</code></td>
                        <td><code>true</code></td>
                        <td>Afficher le bouton "Accueil".</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="mt-6">Composants utilisés</h3>
        <p class="text-sm text-base-content/70 mb-4">
            Ce composant utilise les composants suivants (hiérarchie Atomic Design) :
        </p>
        
        <ul class="list-disc list-inside space-y-2 text-sm">
            <li><code>x-daisy::ui.inputs.button</code> - Boutons d'action</li>
        </ul>
    </section>
</x-daisy::layout.docs>

