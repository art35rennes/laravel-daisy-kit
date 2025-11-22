# Lot 5 : Template de documentation et changelog

## Vue d'ensemble
Créer un template de changelog pour afficher l'historique des versions et modifications d'une application, en respectant les principes Atomic Design.

## Architecture Atomic Design

### Niveau TEMPLATE
- `changelog.blade.php` : Structure de page composant des organismes et molécules

### Niveau ORGANISME
- `changelog-toolbar.blade.php` : Barre d'outils avec recherche et filtres (compose filter + input)

### Niveau MOLECULE
- `changelog-version-item.blade.php` : Élément de version avec en-tête et changements (compose collapse/accordion + badges + list)
- `changelog-change-item.blade.php` : Élément de changement individuel (compose badge + link + text)
- `changelog-header.blade.php` : En-tête du changelog (compose badge + link)

### Niveau ATOME
- Aucun nouveau composant atomique requis (utilise les atomes existants : badge, input, link, etc.)

## Composants à créer

### 1. changelog.blade.php (TEMPLATE)
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
- `x-daisy::ui.changelog.changelog-header` (en-tête du changelog)
- `x-daisy::ui.changelog.changelog-toolbar` (barre d'outils avec recherche/filtres)
- `x-daisy::ui.changelog.changelog-version-item` (élément de version)
- `x-daisy::ui.navigation.breadcrumbs` (navigation)
- `x-daisy::ui.navigation.pagination` (pagination si activée)
- `x-daisy::ui.feedback.empty-state` (aucun résultat)

**Structure** :
- `<x-daisy::ui.changelog.changelog-header />` : En-tête avec titre, version actuelle, liens RSS/Atom
- `<x-daisy::ui.changelog.changelog-toolbar />` : Barre d'outils avec recherche et filtres
- Liste des versions :
  - `<x-daisy::ui.changelog.changelog-version-item />` pour chaque version
- `<x-daisy::ui.navigation.pagination />` en bas (si activée)

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

**Format recommandé (plus flexible et enrichi)** :

```php
$versions = [
    [
        'version' => '2.0.0',
        'date' => '2024-01-15',
        'isCurrent' => true,
        'yanked' => false, // Version retirée
        'tagUrl' => 'https://github.com/user/repo/releases/tag/2.0.0', // Lien vers le tag Git
        'compareUrl' => 'https://github.com/user/repo/compare/1.9.0...2.0.0', // Lien de comparaison
        'items' => [
            [
                'type' => 'added',
                'category' => 'Features',
                'description' => 'Nouvelle fonctionnalité de recherche avancée',
                'breaking' => false,
                'issues' => [123, 456], // Numéros d'issues/PR
                'contributors' => ['user1', 'user2'], // Contributeurs
                'image' => '/images/changelog/search-feature.jpg', // Screenshot optionnel
                'migration' => false, // Nécessite une migration
            ],
            [
                'type' => 'changed',
                'category' => 'Performance',
                'description' => 'Amélioration des performances de chargement',
                'breaking' => false,
                'issues' => [789],
            ],
            [
                'type' => 'fixed',
                'category' => 'Bugfixes',
                'description' => 'Correction du bug de connexion',
                'breaking' => false,
                'issues' => [101],
            ],
            [
                'type' => 'removed',
                'category' => 'Deprecations',
                'description' => 'Suppression de l\'ancien système de cache',
                'breaking' => true, // Breaking change
                'migration' => true, // Nécessite une migration
                'migrationGuide' => 'https://docs.example.com/migrations/cache', // Guide de migration
            ],
            [
                'type' => 'security',
                'category' => 'Security',
                'description' => 'Mise à jour de sécurité critique',
                'breaking' => false,
                'cve' => 'CVE-2024-1234', // Numéro CVE si applicable
                'severity' => 'high', // high|medium|low
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
- **Contributeurs** : Afficher les contributeurs par changement
- **Screenshots** : Afficher des images pour les nouvelles fonctionnalités
- **Migrations** : Badge et lien vers le guide de migration si nécessaire
- **CVE** : Afficher les numéros CVE pour les failles de sécurité
- **Comparaison** : Lien vers la comparaison Git entre versions
- **Export** : Export PDF/JSON du changelog (optionnel)

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

**Traductions nécessaires** (à créer `resources/lang/fr/changelog.php` et `resources/lang/en/changelog.php`) :

```php
// Structure de base
'changelog' => 'Historique des versions',
'version' => 'Version',
'current_version' => 'Version actuelle',
'released_on' => 'Publié le',
'yanked' => 'Retirée',
'breaking_change' => 'Changement majeur',

// Types de changements
'added' => 'Ajouté',
'changed' => 'Modifié',
'fixed' => 'Corrigé',
'removed' => 'Supprimé',
'security' => 'Sécurité',
'all_types' => 'Tous les types',

// Interface
'search_placeholder' => 'Rechercher dans le changelog...',
'no_results' => 'Aucun résultat',
'no_versions' => 'Aucune version',
'view_full_changelog' => 'Voir le changelog complet',
'rss_feed' => 'Flux RSS',
'atom_feed' => 'Flux Atom',

// Métadonnées enrichies
'contributors' => 'Contributeurs',
'issues' => 'Issues',
'pull_requests' => 'Pull Requests',
'migration_required' => 'Migration requise',
'migration_guide' => 'Guide de migration',
'view_migration_guide' => 'Voir le guide de migration',
'cve' => 'CVE',
'severity' => 'Sévérité',
'severity_high' => 'Élevée',
'severity_medium' => 'Moyenne',
'severity_low' => 'Faible',
'screenshot' => 'Capture d\'écran',
'view_screenshot' => 'Voir la capture d\'écran',

// Liens Git
'view_tag' => 'Voir le tag',
'compare_versions' => 'Comparer les versions',
'view_release' => 'Voir la release',

// Catégories (optionnelles)
'category_features' => 'Fonctionnalités',
'category_performance' => 'Performance',
'category_bugfixes' => 'Corrections de bugs',
'category_deprecations' => 'Dépréciations',
'category_security' => 'Sécurité',
'category_ui' => 'Interface utilisateur',
'category_api' => 'API',
'category_database' => 'Base de données',
```

---

## Composants/Wrappers nécessaires

### 2. changelog-header.blade.php (MOLECULE)
**Fichier** : `resources/views/components/ui/changelog/changelog-header.blade.php`

**Description** : En-tête du changelog avec titre, version actuelle et liens RSS/Atom.

**Props** :
```php
@props([
    'title' => __('changelog.changelog'),
    'currentVersion' => null,
    'rssUrl' => null,
    'atomUrl' => null,
    'showVersionBadge' => true,
])
```

**Composants utilisés** :
- `x-daisy::ui.data-display.badge` (version actuelle)
- `x-daisy::ui.advanced.link` (liens RSS/Atom)

---

### 3. changelog-toolbar.blade.php (ORGANISME)
**Fichier** : `resources/views/components/ui/changelog/changelog-toolbar.blade.php`

**Description** : Barre d'outils avec recherche et filtres par type de changement.

**Props** :
```php
@props([
    'showSearch' => true,
    'showFilters' => true,
    'searchPlaceholder' => __('changelog.search_placeholder'),
    'filterName' => 'changelog-filter',
    'filterItems' => [], // ['added', 'changed', 'fixed', 'removed', 'security']
])
```

**Composants utilisés** :
- `x-daisy::ui.inputs.input` (recherche)
- `x-daisy::ui.advanced.filter` (filtres)

---

### 4. changelog-version-item.blade.php (MOLECULE)
**Fichier** : `resources/views/components/ui/changelog/changelog-version-item.blade.php`

**Description** : Élément de version avec en-tête (version, date, badges) et liste des changements.

**Props** :
```php
@props([
    'version' => null, // Version string
    'date' => null, // Date string
    'isCurrent' => false,
    'yanked' => false,
    'tagUrl' => null, // Lien vers le tag Git
    'compareUrl' => null, // Lien de comparaison
    'items' => [], // Array de changements
    'expandByDefault' => false,
    'highlightCurrent' => true,
])
```

**Composants utilisés** :
- `x-daisy::ui.advanced.collapse` ou `x-daisy::ui.advanced.accordion` (version repliable)
- `x-daisy::ui.data-display.badge` (version, current, yanked)
- `x-daisy::ui.changelog.changelog-change-item` (chaque changement)
- `x-daisy::ui.advanced.link` (liens tag/compare)

---

### 5. changelog-change-item.blade.php (MOLECULE)
**Fichier** : `resources/views/components/ui/changelog/changelog-change-item.blade.php`

**Description** : Élément de changement individuel avec badge de type, description, liens et métadonnées.

**Props** :
```php
@props([
    'type' => 'added', // added|changed|fixed|removed|security
    'category' => null, // Catégorie optionnelle
    'description' => '',
    'breaking' => false,
    'issues' => [], // [123, 456] ou [['number' => 123, 'url' => '...']]
    'contributors' => [], // ['username1', 'username2']
    'image' => null, // URL de l'image
    'migration' => false,
    'migrationGuide' => null,
    'cve' => null, // Numéro CVE
    'severity' => null, // high|medium|low
    'issueBaseUrl' => 'https://github.com/user/repo/issues', // Base URL pour les issues
])
```

**Composants utilisés** :
- `x-daisy::ui.data-display.badge` (type, breaking, migration, CVE)
- `x-daisy::ui.advanced.link` (issues, migration guide)
- `x-daisy::ui.data-display.avatar` (contributeurs optionnel)
- `x-daisy::ui.media.lightbox` (image optionnelle)

---

## Tests à prévoir

### Tests de rendu (Feature Tests)

#### Tests des MOLECULES
1. **changelog-change-item** :
   - Rendu avec tous les types (added, changed, fixed, removed, security)
   - Affichage des badges (breaking, migration, CVE)
   - Affichage des liens issues/PR
   - Affichage des contributeurs
   - Affichage des images (si fournies)

2. **changelog-header** :
   - Rendu avec/sans version actuelle
   - Affichage des liens RSS/Atom
   - Masquage du badge version si `showVersionBadge=false`

#### Tests de l'ORGANISME
3. **changelog-toolbar** :
   - Rendu avec recherche et filtres
   - Masquage de la recherche si `showSearch=false`
   - Masquage des filtres si `showFilters=false`
   - Filtres par type fonctionnels

#### Tests de la MOLECULE (niveau supérieur)
4. **changelog-version-item** :
   - Rendu avec toutes les métadonnées (version, date, badges)
   - Expansion/repli par défaut
   - Mise en évidence de la version actuelle
   - Affichage du badge "Yanked"
   - Liens tag/compare

#### Tests du TEMPLATE
5. **changelog** :
   - Rendu avec les props par défaut
   - Rendu avec données complètes
   - Rendu avec format simple (changes array)
   - Rendu avec format enrichi (items array)
   - Pagination (si activée)
   - État vide (aucune version)
   - Responsive (mobile/desktop)

### Tests d'interaction (Browser Tests)

6. **Recherche** : Vérifier le filtrage en temps réel
7. **Filtres** : Vérifier le filtrage par type de changement
8. **Expansion** : Vérifier le dépliage/repliage des versions
9. **Liens** : Vérifier que tous les liens sont cliquables et corrects
10. **Images** : Vérifier l'ouverture de la lightbox pour les screenshots
11. **Pagination** : Vérifier la navigation entre les pages
12. **Accessibilité** : Navigation clavier, ARIA labels, lecteurs d'écran

---

## Ordre d'implémentation recommandé (Atomic Design)

1. **MOLECULES** (composants de base réutilisables) :
   - `changelog-change-item.blade.php` (élément de changement)
   - `changelog-header.blade.php` (en-tête)

2. **ORGANISME** (composant complexe) :
   - `changelog-toolbar.blade.php` (barre d'outils)

3. **MOLECULE** (utilise les molécules précédentes) :
   - `changelog-version-item.blade.php` (élément de version)

4. **TEMPLATE** (compose tous les composants) :
   - `changelog.blade.php` (template principal)

---

## Enrichissements suggérés

### 1. Support des métadonnées enrichies
- **Contributeurs** : Afficher les avatars et noms des contributeurs par changement
- **Issues/PR** : Liens cliquables vers les issues GitHub/GitLab avec numéros
- **Screenshots** : Galerie d'images pour les nouvelles fonctionnalités (avec lightbox)
- **Vidéos** : Support des vidéos de démonstration (embed YouTube/Vimeo)

### 2. Support des migrations et dépréciations
- **Badge "Migration"** : Indiquer les changements nécessitant une migration
- **Lien vers le guide** : Lien direct vers la documentation de migration
- **Dépréciations** : Section dédiée aux fonctionnalités dépréciées avec date de suppression

### 3. Support de la sécurité
- **CVE** : Affichage des numéros CVE avec liens vers la base de données
- **Niveau de sévérité** : Badge de sévérité (high/medium/low)
- **Alerte de sécurité** : Mise en évidence spéciale pour les mises à jour critiques

### 4. Intégration Git
- **Tags** : Lien vers les tags Git (GitHub/GitLab releases)
- **Comparaison** : Lien vers la comparaison entre versions
- **Changelog automatique** : Génération depuis les commits Git (optionnel, via helper)

### 5. Fonctionnalités d'affichage
- **Groupement par catégorie** : Grouper les changements par catégorie dans chaque version
- **Timeline visuelle** : Option d'affichage en timeline (utilise le composant timeline existant)
- **Vue compacte/étendue** : Toggle entre vue compacte et vue détaillée
- **Filtres avancés** : Filtres par catégorie, contributeur, période

### 6. Export et partage
- **Export PDF** : Génération d'un PDF du changelog (optionnel, via package externe)
- **Export JSON** : API endpoint pour récupérer le changelog en JSON
- **RSS/Atom** : Flux RSS et Atom pour suivre les nouvelles versions
- **Webhooks** : Notifications webhook lors de nouvelles versions (optionnel)

### 7. Accessibilité et SEO
- **Structured Data** : Schema.org pour le SEO
- **ARIA labels** : Labels appropriés pour les lecteurs d'écran
- **Navigation clavier** : Support complet de la navigation au clavier

### 8. Performance
- **Lazy loading** : Chargement paresseux des versions anciennes
- **Pagination** : Pagination côté serveur pour les grands changelogs
- **Cache** : Mise en cache des données de changelog

## Notes importantes

- **Atomic Design** : Respecter strictement la hiérarchie (atomes → molécules → organismes → templates)
- **Réutilisabilité** : Les composants partiels doivent être réutilisables dans d'autres contextes
- **Flexibilité** : Le template doit accepter différents formats de données (array simple, array avec items, objets)
- **Dates** : Utiliser `Carbon` pour le formatage (ex: `Carbon::parse($version['date'])->format('d/m/Y')`)
- **Helper Laravel** : Créer un helper pour charger les données depuis JSON/YAML/DB/Markdown
- **Traductions** : Ajouter les traductions dans `resources/lang/fr/changelog.php` et `resources/lang/en/changelog.php`
- **Markdown** : Pour un changelog basé sur CHANGELOG.md, utiliser `league/commonmark`
- **Standalone** : Le template peut être utilisé dans une page de documentation ou comme page standalone
- **RSS/Atom** : Les liens RSS/Atom sont optionnels mais recommandés pour le référencement
- **Tests** : Tester chaque niveau (molécule, organisme, template) indépendamment

