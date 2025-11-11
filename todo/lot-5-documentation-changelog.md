# Lot 5 : Template de documentation et changelog

## Vue d'ensemble
Créer un template de changelog pour afficher l'historique des versions et modifications d'une application.

## Template à créer

### 1. changelog.blade.php
**Fichier** : `resources/views/templates/changelog.blade.php`

**Description** : Page de changelog organisée par versions avec filtres, recherche et navigation.

**Props** :
```php
@props([
    'title' => __('changelog.changelog'),
    'theme' => null,
    // Changelog data
    'versions' => [], // Array of version data
    'currentVersion' => null, // Current app version
    // Routes
    'rssUrl' => null, // RSS feed URL (optional)
    'atomUrl' => null, // Atom feed URL (optional)
    // Options
    'showFilters' => true,
    'showSearch' => true,
    'showVersionBadge' => true,
    'groupByMonth' => false, // Group versions by month
    'highlightCurrent' => true, // Highlight current version
    'expandLatest' => true, // Expand latest version by default
    'itemsPerPage' => 20, // If pagination enabled
    'pagination' => false, // Enable pagination
])
```

**Fonctionnalités Laravel** :
- Utilise `config('app.version')` pour la version actuelle (si disponible)
- Peut utiliser un fichier JSON/YAML pour les données de changelog
- Utilise `Route::has()` et `route()` pour les URLs
- Utilise la pagination Laravel si activée
- Utilise `Carbon` pour formater les dates
- Peut utiliser des fichiers de traduction pour les catégories

**Composants UI utilisés** :
- `x-daisy::layout.app` ou `x-daisy::layout.docs` (layout de documentation)
- `x-daisy::ui.data-display.timeline` (affichage des versions)
- `x-daisy::ui.data-display.badge` (badge de version, type de changement)
- `x-daisy::ui.navigation.breadcrumbs` (navigation)
- `x-daisy::ui.advanced.filter` (filtres par type : Added, Changed, Fixed, Removed)
- `x-daisy::ui.inputs.input` (recherche)
- `x-daisy::ui.advanced.collapse` (versions repliables)
- `x-daisy::ui.navigation.pagination` (pagination si activée)
- `x-daisy::ui.feedback.empty-state` (aucun résultat)
- `x-daisy::ui.advanced.accordion` (alternative au collapse pour les versions)

**Structure** :
- En-tête avec :
  - Titre "Changelog"
  - Version actuelle (badge)
  - Liens RSS/Atom (si fournis)
- Barre d'outils :
  - Recherche (si `showSearch`)
  - Filtres par type (si `showFilters`) : Tous, Added, Changed, Fixed, Removed, Security
- Liste des versions :
  - Chaque version dans un `collapse` ou `accordion` :
    - En-tête : Version, date, badge "Current" si version actuelle
    - Contenu : Liste des changements groupés par catégorie
- Pagination en bas (si activée)

**Exemple de structure de données** :

```php
$versions = [
    [
        'version' => '2.0.0',
        'date' => '2024-01-15',
        'isCurrent' => true,
        'changes' => [
            'added' => [
                'Nouvelle fonctionnalité de recherche avancée',
                'Support des thèmes personnalisés',
            ],
            'changed' => [
                'Amélioration des performances de chargement',
                'Refonte de l\'interface utilisateur',
            ],
            'fixed' => [
                'Correction du bug de connexion',
                'Correction de l\'affichage sur mobile',
            ],
            'removed' => [
                'Suppression de l\'ancien système de cache',
            ],
            'security' => [
                'Mise à jour de sécurité critique',
            ],
        ],
    ],
    [
        'version' => '1.5.0',
        'date' => '2023-12-01',
        'isCurrent' => false,
        'changes' => [
            'added' => [
                'Nouvelle page de profil',
            ],
            'fixed' => [
                'Correction de plusieurs bugs mineurs',
            ],
        ],
    ],
];
```

**Format alternatif (plus flexible)** :

```php
$versions = [
    [
        'version' => '2.0.0',
        'date' => '2024-01-15',
        'isCurrent' => true,
        'yanked' => false, // Version retirée
        'items' => [
            [
                'type' => 'added',
                'category' => 'Features',
                'description' => 'Nouvelle fonctionnalité de recherche avancée',
                'breaking' => false,
            ],
            [
                'type' => 'changed',
                'category' => 'Performance',
                'description' => 'Amélioration des performances de chargement',
                'breaking' => false,
            ],
            [
                'type' => 'fixed',
                'category' => 'Bugfixes',
                'description' => 'Correction du bug de connexion',
                'breaking' => false,
            ],
            [
                'type' => 'removed',
                'category' => 'Deprecations',
                'description' => 'Suppression de l\'ancien système de cache',
                'breaking' => true, // Breaking change
            ],
            [
                'type' => 'security',
                'category' => 'Security',
                'description' => 'Mise à jour de sécurité critique',
                'breaking' => false,
            ],
        ],
    ],
];
```

**Types de changements** :
- `added` : Nouvelles fonctionnalités (couleur: success)
- `changed` : Modifications (couleur: info)
- `fixed` : Corrections de bugs (couleur: warning)
- `removed` : Suppressions (couleur: error)
- `security` : Sécurité (couleur: error)

**Fonctionnalités avancées** :
- **Recherche** : Filtrer les versions/changements par mot-clé
- **Filtres** : Filtrer par type de changement
- **Groupement** : Grouper les versions par mois/année
- **Expansion** : Déplier/replier les versions
- **Badge "Breaking"** : Mettre en évidence les changements breaking
- **Badge "Yanked"** : Afficher les versions retirées
- **Liens** : Liens vers les issues GitHub/GitLab (si fournis)

**Exemple d'utilisation** :

```blade
@php
$versions = [
    [
        'version' => config('app.version', '1.0.0'),
        'date' => '2024-01-15',
        'isCurrent' => true,
        'items' => [
            ['type' => 'added', 'description' => 'Nouvelle fonctionnalité'],
            ['type' => 'fixed', 'description' => 'Correction de bug'],
        ],
    ],
];
@endphp

<x-daisy::templates.changelog
    title="Historique des versions"
    :versions="$versions"
    :currentVersion="config('app.version')"
    :showFilters="true"
    :showSearch="true"
    :expandLatest="true"
/>
```

**Source de données recommandée** :
- Fichier JSON : `storage/app/changelog.json`
- Fichier YAML : `storage/app/changelog.yaml`
- Base de données : Table `changelog_versions` et `changelog_items`
- Fichier Markdown : `CHANGELOG.md` (parser Markdown)

**Helper Laravel recommandé** :

```php
// app/Helpers/ChangelogHelper.php
class ChangelogHelper
{
    public static function getVersions(): array
    {
        // Charger depuis JSON/YAML/DB
        $file = storage_path('app/changelog.json');
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }
        return [];
    }

    public static function getCurrentVersion(): ?string
    {
        return config('app.version');
    }
}
```

**Traductions nécessaires** (à créer `resources/lang/fr/changelog.php`) :
- `changelog" : "Historique des versions"
- `version" : "Version"
- `current_version" : "Version actuelle"
- `released_on" : "Publié le"
- `yanked" : "Retirée"
- `breaking_change" : "Changement majeur"
- `added" : "Ajouté"
- `changed" : "Modifié"
- `fixed" : "Corrigé"
- `removed" : "Supprimé"
- `security" : "Sécurité"
- `all_types" : "Tous les types"
- `search_placeholder" : "Rechercher dans le changelog..."
- `no_results" : "Aucun résultat"
- `no_versions" : "Aucune version"
- `view_full_changelog" : "Voir le changelog complet"
- `rss_feed" : "Flux RSS"
- `atom_feed" : "Flux Atom"

---

## Composants/Wrappers nécessaires

### Aucun nouveau composant requis
Tous les composants nécessaires existent déjà :
- `timeline` pour l'affichage chronologique
- `collapse` ou `accordion` pour les versions repliables
- `filter` pour les filtres
- `badge` pour les types de changements
- `empty-state` pour l'état vide

**Note** : Le composant `timeline` peut être utilisé pour afficher les versions de manière chronologique, mais un affichage avec `collapse`/`accordion` est plus adapté pour un changelog détaillé.

---

## Tests à prévoir

1. **Test de rendu** : Vérifier le rendu avec les props par défaut
2. **Test avec données** : Vérifier l'affichage des versions et changements
3. **Test de filtres** : Vérifier le filtrage par type
4. **Test de recherche** : Vérifier la recherche dans les changements
5. **Test d'expansion** : Vérifier le dépliage/repliage des versions
6. **Test de pagination** : Vérifier la pagination (si activée)
7. **Test de badge "Current"** : Vérifier la mise en évidence de la version actuelle
8. **Test d'état vide** : Vérifier l'affichage quand il n'y a pas de versions
9. **Test responsive** : Vérifier l'affichage sur mobile

---

## Ordre d'implémentation recommandé

1. `changelog.blade.php` (template unique de ce lot)

---

## Notes importantes

- Le template doit être flexible pour accepter différents formats de données (array simple, array avec items, etc.)
- Les dates doivent être formatées avec `Carbon` (ex: `Carbon::parse($version['date'])->format('d/m/Y')`)
- Le template peut utiliser un helper Laravel pour charger les données depuis un fichier JSON/YAML ou la base de données
- Les traductions doivent être ajoutées dans `resources/lang/fr/changelog.php` et `resources/lang/en/changelog.php`
- Pour un changelog basé sur un fichier Markdown (CHANGELOG.md), utiliser un parser Markdown comme `league/commonmark`
- Le template peut être utilisé dans une page de documentation ou comme page standalone
- Les liens RSS/Atom sont optionnels mais recommandés pour le référencement

