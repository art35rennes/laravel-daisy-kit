@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'formats', 'label' => 'Formats de données'],
        ['id' => 'features', 'label' => 'Fonctionnalités'],
        ['id' => 'api', 'label' => 'API'],
    ];
@endphp

<x-daisy::layout.docs title="Changelog" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Changelog</h1>
        <p>Template pour afficher l'historique des versions et modifications d'une application, organisé par versions avec filtres, recherche et navigation.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-changelog" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    @php
                        $exampleVersions = [
                            [
                                'version' => '2.0.0',
                                'date' => '2024-01-15',
                                'isCurrent' => true,
                                'items' => [
                                    [
                                        'type' => 'added',
                                        'description' => 'Nouvelle fonctionnalité de recherche avancée',
                                    ],
                                    [
                                        'type' => 'fixed',
                                        'description' => 'Correction du bug de connexion',
                                    ],
                                ],
                            ],
                            [
                                'version' => '1.5.0',
                                'date' => '2023-12-01',
                                'isCurrent' => false,
                                'items' => [
                                    [
                                        'type' => 'added',
                                        'description' => 'Nouvelle page de profil',
                                    ],
                                ],
                            ],
                        ];
                    @endphp
                    <x-daisy::templates.changelog
                        title="Historique des versions"
                        :versions="$exampleVersions"
                        :currentVersion="'2.0.0'"
                        :showFilters="true"
                        :showSearch="true"
                        :expandLatest="true"
                    />
                </div>
            </div>
            <input type="radio" name="base-example-changelog" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '@php
$versions = [
    [
        \'version\' => \'2.0.0\',
        \'date\' => \'2024-01-15\',
        \'isCurrent\' => true,
        \'items\' => [
            [
                \'type\' => \'added\',
                \'description\' => \'Nouvelle fonctionnalité de recherche avancée\',
            ],
            [
                \'type\' => \'fixed\',
                \'description\' => \'Correction du bug de connexion\',
            ],
        ],
    ],
];
@endphp

<x-daisy::templates.changelog
    title="Historique des versions"
    :versions="$versions"
    :currentVersion="\'2.0.0\'"
    :showFilters="true"
    :showSearch="true"
    :expandLatest="true"
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
                    height="400px"
                />
            </div>
        </div>
    </section>

    <section id="formats" class="mt-10">
        <h2>Formats de données</h2>
        <p class="text-base-content/70 mb-6">
            Le template accepte deux formats de données : un format simple avec un tableau <code>changes</code> ou un format enrichi avec un tableau <code>items</code>.
        </p>

        <h3 class="text-xl font-semibold mt-6 mb-4">Format simple</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="format-simple-changelog" class="tab" aria-label="Code" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $simpleFormatCode = '$versions = [
    [
        \'version\' => \'2.0.0\',
        \'date\' => \'2024-01-15\',
        \'changes\' => [
            \'added\' => [
                \'Nouvelle fonctionnalité de recherche avancée\',
                \'Support des thèmes personnalisés\',
            ],
            \'changed\' => [
                \'Amélioration des performances de chargement\',
            ],
            \'fixed\' => [
                \'Correction du bug de connexion\',
            ],
            \'removed\' => [
                \'Suppression de l\'ancien système de cache\',
            ],
            \'security\' => [
                \'Mise à jour de sécurité critique\',
            ],
        ],
    ],
];';
                @endphp
                <x-daisy::ui.advanced.code-editor
                    language="php"
                    :value="$simpleFormatCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="300px"
                />
            </div>
        </div>

        <h3 class="text-xl font-semibold mt-6 mb-4">Format enrichi</h3>
        <div class="tabs tabs-box">
            <input type="radio" name="format-enriched-changelog" class="tab" aria-label="Code" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $enrichedFormatCode = '$versions = [
    [
        \'version\' => \'2.0.0\',
        \'date\' => \'2024-01-15\',
        \'isCurrent\' => true,
        \'tagUrl\' => \'https://github.com/user/repo/releases/tag/2.0.0\',
        \'compareUrl\' => \'https://github.com/user/repo/compare/1.9.0...2.0.0\',
        \'items\' => [
            [
                \'type\' => \'added\',
                \'category\' => \'Features\',
                \'description\' => \'Nouvelle fonctionnalité de recherche avancée\',
                \'breaking\' => false,
                \'issues\' => [123, 456],
                \'contributors\' => [\'user1\', \'user2\'],
                \'image\' => \'/images/changelog/search-feature.jpg\',
            ],
            [
                \'type\' => \'removed\',
                \'category\' => \'Deprecations\',
                \'description\' => \'Suppression de l\'ancien système de cache\',
                \'breaking\' => true,
                \'migration\' => true,
                \'migrationGuide\' => \'https://docs.example.com/migrations/cache\',
            ],
            [
                \'type\' => \'security\',
                \'description\' => \'Mise à jour de sécurité critique\',
                \'cve\' => \'CVE-2024-1234\',
                \'severity\' => \'high\',
            ],
        ],
    ],
];';
                @endphp
                <x-daisy::ui.advanced.code-editor
                    language="php"
                    :value="$enrichedFormatCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="400px"
                />
            </div>
        </div>
    </section>

    <section id="features" class="mt-10">
        <h2>Fonctionnalités</h2>

        <div class="space-y-6 mt-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">Recherche et filtres</h3>
                    <p>Recherche en temps réel dans les changements et filtrage par type (Added, Changed, Fixed, Removed, Security).</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">Métadonnées enrichies</h3>
                    <p>Support des issues/PR GitHub/GitLab, contributeurs, screenshots, migrations, CVE et niveaux de sévérité.</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">Intégration Git</h3>
                    <p>Liens vers les tags Git et comparaisons entre versions.</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">Groupement par mois</h3>
                    <p>Option pour grouper les versions par mois pour une meilleure organisation.</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-lg">Expansion automatique</h3>
                    <p>La dernière version peut être automatiquement dépliée par défaut.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="api" class="mt-10">
        <h2>API</h2>

        <div class="overflow-x-auto mt-6">
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
                        <td><code>__('changelog.changelog')</code></td>
                        <td>Titre du changelog</td>
                    </tr>
                    <tr>
                        <td><code>theme</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Thème daisyUI à appliquer</td>
                    </tr>
                    <tr>
                        <td><code>versions</code></td>
                        <td>array</td>
                        <td><code>[]</code></td>
                        <td>Tableau des versions (format simple ou enrichi)</td>
                    </tr>
                    <tr>
                        <td><code>currentVersion</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Version actuelle (auto-détectée depuis <code>config('app.version')</code> si non fournie)</td>
                    </tr>
                    <tr>
                        <td><code>rssUrl</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>URL du flux RSS (optionnel)</td>
                    </tr>
                    <tr>
                        <td><code>atomUrl</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>URL du flux Atom (optionnel)</td>
                    </tr>
                    <tr>
                        <td><code>showFilters</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Afficher les filtres par type</td>
                    </tr>
                    <tr>
                        <td><code>showSearch</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Afficher le champ de recherche</td>
                    </tr>
                    <tr>
                        <td><code>showVersionBadge</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Afficher le badge de version actuelle</td>
                    </tr>
                    <tr>
                        <td><code>groupByMonth</code></td>
                        <td>bool</td>
                        <td><code>false</code></td>
                        <td>Grouper les versions par mois</td>
                    </tr>
                    <tr>
                        <td><code>highlightCurrent</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Mettre en évidence la version actuelle</td>
                    </tr>
                    <tr>
                        <td><code>expandLatest</code></td>
                        <td>bool</td>
                        <td><code>true</code></td>
                        <td>Déplier la dernière version par défaut</td>
                    </tr>
                    <tr>
                        <td><code>itemsPerPage</code></td>
                        <td>int</td>
                        <td><code>20</code></td>
                        <td>Nombre d'éléments par page (si pagination activée)</td>
                    </tr>
                    <tr>
                        <td><code>pagination</code></td>
                        <td>bool</td>
                        <td><code>false</code></td>
                        <td>Activer la pagination</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="text-xl font-semibold mt-8 mb-4">Types de changements</h3>
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.badge color="success" size="sm">added</x-daisy::ui.data-display.badge>
                <span>Nouvelles fonctionnalités</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.badge color="info" size="sm">changed</x-daisy::ui.data-display.badge>
                <span>Modifications</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.badge color="warning" size="sm">fixed</x-daisy::ui.data-display.badge>
                <span>Corrections de bugs</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.badge color="error" size="sm">removed</x-daisy::ui.data-display.badge>
                <span>Suppressions</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.data-display.badge color="error" size="sm">security</x-daisy::ui.data-display.badge>
                <span>Sécurité</span>
            </div>
        </div>
    </section>
</x-daisy::layout.docs>

