@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Templates de formulaires avancés" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost">Templates</a>
        </div>
    </x-slot:navbar>

    <section id="overview">
        <h1>Templates de formulaires avancés</h1>
        <p class="text-lg text-base-content/70 mb-6">
            Des templates de formulaires complets et prêts à l'emploi pour gérer des cas d'usage complexes : 
            formulaires multi-étapes, formulaires à onglets, filtres inline, et gestion automatique du token CSRF.
        </p>
    </section>

    <section id="csrf-keeper" class="mt-10">
        <h2>CSRF Keeper</h2>
        <p class="mb-4">
            Le composant CSRF Keeper rafraîchit automatiquement le token CSRF pour éviter les échecs de soumission 
            après une longue période d'inactivité ou lorsque le navigateur met la page en veille.
        </p>
        
        <div class="tabs tabs-box">
            <input type="radio" name="csrf-example" class="tab" aria-label="Usage" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    value='<x-daisy::ui.utilities.csrf-keeper />'
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="100px"
                />
            </div>
            <input type="radio" name="csrf-example" class="tab" aria-label="Props" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <ul class="list space-y-2">
                    <li><code>refreshInterval</code> : Intervalle en ms (calculé automatiquement si non fourni)</li>
                    <li><code>refreshRatio</code> : Ratio de sécurité (défaut: 0.8)</li>
                    <li><code>endpoint</code> : Route pour rafraîchir le token</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="form-tabs" class="mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2>Formulaire à onglets</h2>
            <a href="{{ route('templates.forms.tabs') }}" class="btn btn-sm btn-primary" target="_blank">
                Voir la démo
                <x-bi-box-arrow-up-right class="w-4 h-4" />
            </a>
        </div>
        <p class="mb-4">
            Structure un formulaire en sections via des onglets, avec badges d'erreurs et restauration automatique 
            de l'onglet actif après validation.
        </p>
        
        <div class="tabs tabs-box">
            <input type="radio" name="tabs-example" class="tab" aria-label="Usage" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    value='@view("daisy::templates.form-with-tabs", [
    "tabs" => [
        ["id" => "general", "label" => "Général"],
        ["id" => "advanced", "label" => "Avancé"],
    ],
])
    <x-slot:tab_general>
        <!-- Contenu de l'\''onglet Général -->
    </x-slot:tab_general>
    <x-slot:tab_advanced>
        <!-- Contenu de l'\''onglet Avancé -->
    </x-slot:tab_advanced>
    <x-slot:actions>
        <x-daisy::ui.inputs.button type="submit">Enregistrer</x-daisy::ui.inputs.button>
    </x-slot:actions>
@endview'
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="300px"
                />
            </div>
            <input type="radio" name="tabs-example" class="tab" aria-label="Props" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <ul class="list space-y-2">
                    <li><code>tabs</code> : Tableau d'onglets <code>[['id' => 'general', 'label' => 'Général']]</code></li>
                    <li><code>activeTab</code> : ID de l'onglet actif par défaut</li>
                    <li><code>tabsStyle</code> : Style des onglets (box|border|lift)</li>
                    <li><code>showErrorBadges</code> : Afficher les badges d'erreurs sur les onglets</li>
                    <li><code>fieldToTabMap</code> : Mapping des champs vers les onglets pour le comptage d'erreurs</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="form-inline" class="mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2>Formulaire inline / Filtres</h2>
            <a href="{{ route('templates.forms.inline') }}" class="btn btn-sm btn-primary" target="_blank">
                Voir la démo
                <x-bi-box-arrow-up-right class="w-4 h-4" />
            </a>
        </div>
        <p class="mb-4">
            Barre de filtres réactive avec tokens actifs, actions condensées et panel avancé optionnel via drawer.
        </p>
        
        <div class="tabs tabs-box">
            <input type="radio" name="inline-example" class="tab" aria-label="Usage" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    value='@view("daisy::templates.form-inline", [
    "action" => "/search",
    "method" => "GET",
    "activeFilters" => [
        ["label" => "Statut", "value" => "Actif", "param" => "status"],
    ],
    "showAdvanced" => true,
])
    <x-slot:filters>
        <x-daisy::ui.inputs.input name="search" placeholder="Rechercher..." />
    </x-slot:filters>
    <x-slot:advanced>
        <!-- Filtres avancés -->
    </x-slot:advanced>
@endview'
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="250px"
                />
            </div>
            <input type="radio" name="inline-example" class="tab" aria-label="Props" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <ul class="list space-y-2">
                    <li><code>method</code> : Méthode HTTP (GET|POST|PUT|PATCH|DELETE)</li>
                    <li><code>size</code> : Taille des champs (xs|sm|md)</li>
                    <li><code>activeFilters</code> : Tableau des filtres actifs avec tokens</li>
                    <li><code>showAdvanced</code> : Afficher le drawer de filtres avancés</li>
                    <li><code>collapseBelow</code> : Breakpoint pour le layout vertical (md|lg|xl)</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="form-wizard" class="mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2>Wizard multi-étapes</h2>
            <a href="{{ route('templates.forms.wizard') }}" class="btn btn-sm btn-primary" target="_blank">
                Voir la démo
                <x-bi-box-arrow-up-right class="w-4 h-4" />
            </a>
        </div>
        <p class="mb-4">
            Assistant multi-étapes linéaire ou libre avec persistance session et résumé final.
        </p>
        
        <div class="tabs tabs-box">
            <input type="radio" name="wizard-example" class="tab" aria-label="Usage" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    value='@view("daisy::templates.form-wizard", [
    "steps" => [
        ["key" => "profile", "label" => "Profil"],
        ["key" => "settings", "label" => "Paramètres"],
    ],
    "linear" => true,
])
    <x-slot:step_profile>
        <!-- Contenu de l'\''étape Profil -->
    </x-slot:step_profile>
    <x-slot:step_settings>
        <!-- Contenu de l'\''étape Paramètres -->
    </x-slot:step_settings>
    <x-slot:summary>
        <!-- Résumé final -->
    </x-slot:summary>
@endview'
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="300px"
                />
            </div>
            <input type="radio" name="wizard-example" class="tab" aria-label="Props" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <ul class="list space-y-2">
                    <li><code>steps</code> : Tableau des étapes <code>[['key' => 'profile', 'label' => 'Profil', 'icon' => 'person']]</code></li>
                    <li><code>currentStep</code> : Étape courante (défaut: 1)</li>
                    <li><code>linear</code> : Mode linéaire (bloque les étapes futures)</li>
                    <li><code>allowClickNav</code> : Permettre la navigation par clic sur les étapes</li>
                    <li><code>showSummary</code> : Afficher le résumé à la dernière étape</li>
                    <li><code>wizardKey</code> : Clé unique pour la persistance (permet plusieurs wizards)</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="features" class="mt-10">
        <h2>Fonctionnalités communes</h2>
        <div class="card card-border bg-base-200 mt-4">
            <div class="card-body">
                <h3 class="card-title">CSRF Keeper intégré</h3>
                <p class="text-sm text-base-content/70">
                    Tous les templates de formulaires incluent automatiquement le composant CSRF Keeper 
                    pour rafraîchir le token CSRF et éviter les échecs de soumission après inactivité.
                </p>
                <p class="text-sm text-base-content/70 mt-2">
                    Vous pouvez désactiver cette fonctionnalité via la prop <code>autoRefreshCsrf="false"</code>.
                </p>
            </div>
        </div>

        <div class="card card-border bg-base-200 mt-4">
            <div class="card-body">
                <h3 class="card-title">Gestion de plusieurs instances</h3>
                <p class="text-sm text-base-content/70">
                    Chaque template génère automatiquement un ID unique pour permettre plusieurs instances 
                    du même type de formulaire sur la même page sans conflit de persistance.
                </p>
            </div>
        </div>

        <div class="card card-border bg-base-200 mt-4">
            <div class="card-body">
                <h3 class="card-title">Persistance des données</h3>
                <p class="text-sm text-base-content/70">
                    Les données sont automatiquement sauvegardées dans le sessionStorage (côté client) 
                    et peuvent être persistées côté serveur via les helpers PHP fournis.
                </p>
            </div>
        </div>
    </section>
</x-daisy::layout.docs>

