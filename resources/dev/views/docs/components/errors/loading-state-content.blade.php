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
    $props = DocsHelper::getComponentProps('errors', 'loading-state-content');
@endphp

<x-daisy::layout.docs title="Loading State Content" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Loading State Content</h1>
        <p>Composant organisme pour afficher un état de chargement avec différents types d'indicateurs visuels.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-loading-state-content" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.loading-state-content 
                        type="spinner"
                        message="Chargement en cours..."
                    />
                </div>
            </div>
            <input type="radio" name="base-example-loading-state-content" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.errors.loading-state-content 
    type="spinner"
    message="Chargement en cours..."
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
        
        <h3 class="mt-6">Type skeleton</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-skeleton-loading-state-content" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.loading-state-content 
                        type="skeleton"
                        message="Chargement des données..."
                        :skeletonCount="3"
                    />
                </div>
            </div>
            <input type="radio" name="variants-skeleton-loading-state-content" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $codeSkeleton = '<x-daisy::ui.errors.loading-state-content 
    type="skeleton"
    message="Chargement des données..."
    :skeletonCount="3"
/>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$codeSkeleton"
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

        <h3 class="mt-6">Type progress</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-progress-loading-state-content" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.errors.loading-state-content 
                        type="progress"
                        message="Téléchargement en cours..."
                    />
                </div>
            </div>
            <input type="radio" name="variants-progress-loading-state-content" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $codeProgress = '<x-daisy::ui.errors.loading-state-content 
    type="progress"
    message="Téléchargement en cours..."
/>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$codeProgress"
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
                        <td><code>type</code></td>
                        <td><code>string</code></td>
                        <td><code>'spinner'</code></td>
                        <td>Type d'indicateur : <code>'spinner'</code>, <code>'skeleton'</code>, <code>'progress'</code></td>
                    </tr>
                    <tr>
                        <td><code>message</code></td>
                        <td><code>string|null</code></td>
                        <td><code>null</code></td>
                        <td>Message à afficher pendant le chargement.</td>
                    </tr>
                    <tr>
                        <td><code>size</code></td>
                        <td><code>string</code></td>
                        <td><code>'md'</code></td>
                        <td>Taille de l'indicateur : <code>'xs'</code>, <code>'sm'</code>, <code>'md'</code>, <code>'lg'</code>, <code>'xl'</code></td>
                    </tr>
                    <tr>
                        <td><code>skeletonCount</code></td>
                        <td><code>int</code></td>
                        <td><code>3</code></td>
                        <td>Nombre de skeletons à afficher (uniquement pour type <code>'skeleton'</code>).</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="mt-6">Composants utilisés</h3>
        <p class="text-sm text-base-content/70 mb-4">
            Ce composant utilise les composants suivants (hiérarchie Atomic Design) :
        </p>
        
        <ul class="list-disc list-inside space-y-2 text-sm">
            <li><code>x-daisy::ui.feedback.loading-message</code> - Message avec indicateur de chargement</li>
            <li><code>x-daisy::ui.feedback.skeleton</code> - Skeleton (si type='skeleton')</li>
            <li><code>x-daisy::ui.data-display.progress</code> - Barre de progression (si type='progress')</li>
        </ul>
    </section>
</x-daisy::layout.docs>

