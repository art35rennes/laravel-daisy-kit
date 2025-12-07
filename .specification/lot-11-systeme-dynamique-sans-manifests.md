# Lot 11 · Plan de spécification – Système dynamique sans manifests JSON

## 1. Objectifs produit

- **Éliminer la dépendance aux manifests JSON statiques** pour la documentation et la navigation, en faveur d'un système de scan dynamique avec cache intelligent.
- **Simplifier le workflow de développement** en automatisant la régénération des inventaires lors des modifications de fichiers.
- **Améliorer la maintenabilité** en centralisant la logique de scan dans une classe réutilisable, éliminant la duplication entre commandes Artisan et helpers.
- **Garantir les performances** grâce à un système de cache Laravel avec invalidation intelligente basée sur les timestamps des fichiers.

**Note importante** : Ce lot concerne uniquement les **outils de développement** du package. Les scanners, helpers et commandes sont dans `app/` car ils font partie de l'application de développement/test du package, **non publiés** avec le package final. Ils sont utilisés uniquement pour générer la documentation et les inventaires pendant le développement.

## 2. Périmètre fonctionnel

| Axe | Description synthétique | Valeur ajoutée |
|-----|-------------------------|----------------|
| Scanner dynamique centralisé | Classe `ComponentScanner` et `TemplateScanner` qui scannent les fichiers directement depuis le système de fichiers. | Source unique de vérité, pas de désynchronisation possible. |
| Cache Laravel avec invalidation | Utilisation du cache Laravel avec tags et invalidation basée sur les timestamps des fichiers. | Performances optimales sans régénération manuelle. |
| Intégration npm run dev | Watch automatique des fichiers Blade qui régénère les caches à la volée. | Workflow transparent pour le développeur. |
| Migration progressive | Support des deux systèmes (manifests JSON + cache) pendant la transition, avec fallback automatique. | Aucun breaking change, migration en douceur. |
| Refactor de DocsHelper | `DocsHelper` utilise désormais les scanners avec cache au lieu de lire les manifests JSON. | Code simplifié, logique centralisée. |

## 3. Exigences transverses

1. **Performance garantie**
   - Le scan ne doit jamais être exécuté à chaque requête HTTP.
   - Utilisation obligatoire du cache Laravel avec TTL approprié (1 heure par défaut).
   - Invalidation automatique basée sur les timestamps des fichiers modifiés.
   - Support des tags de cache pour invalidation sélective.

2. **Robustesse et résilience**
   - Fallback automatique vers les manifests JSON si le cache est vide et que les fichiers n'existent pas.
   - Gestion gracieuse des erreurs (fichiers manquants, permissions, etc.).
   - Logging des erreurs critiques sans bloquer l'application.

3. **Compatibilité et migration**
   - Support des deux systèmes pendant la transition (manifests JSON + cache dynamique).
   - Les commandes `inventory:*` continuent de fonctionner pour générer les manifests JSON (utiles pour les tests, CI/CD, etc.).
   - Migration progressive : le système détecte automatiquement la présence des manifests et les utilise en fallback.

4. **Intégration transparente**
   - Aucun changement dans l'API publique de `DocsHelper`.
   - Les vues de documentation continuent de fonctionner sans modification.
   - Les tests existants continuent de fonctionner (avec génération automatique des manifests si nécessaire).

5. **Développement local optimisé**
   - Watch automatique des fichiers Blade via `npm run dev`.
   - Régénération du cache uniquement quand nécessaire (fichiers modifiés).
   - Debounce pour éviter les scans multiples lors de sauvegardes rapides.

## 4. Spécifications détaillées

### 4.1. Architecture des scanners

#### 4.1.1 Classe `ComponentScanner`

**Localisation** : `app/Helpers/ComponentScanner.php`  
**Namespace** : `App\Helpers\`  
**Contexte** : Outil de développement uniquement, non publié avec le package

**Responsabilités** :
- Scanner récursivement `resources/views/components/ui/**/*.blade.php`
- Extraire les métadonnées (nom, catégorie, props, data-attributes, module JS)
- Générer la structure de données identique à celle des manifests JSON actuels
- Gérer le cache avec invalidation intelligente

**Signature principale** :

```php
class ComponentScanner
{
    /**
     * Scanne les composants et retourne les métadonnées.
     * Utilise le cache Laravel avec invalidation basée sur les timestamps.
     *
     * @return array{components: array<int, array<string, mixed>>, generated_at: string}
     */
    public static function scan(): array;
    
    /**
     * Force la régénération du cache (ignore le cache existant).
     *
     * @return array{components: array<int, array<string, mixed>>, generated_at: string}
     */
    public static function scanFresh(): array;
    
    /**
     * Vérifie si le cache est valide en comparant les timestamps des fichiers.
     *
     * @return bool
     */
    public static function isCacheValid(): bool;
    
    /**
     * Invalide le cache (utile pour les tests ou après modifications manuelles).
     *
     * @return void
     */
    public static function clearCache(): void;
}
```

**Stratégie de cache** :
- Clé de cache : `daisy.components.manifest`
- Tags : `['daisy', 'components', 'manifest']`
- TTL : 3600 secondes (1 heure)
- Invalidation : Comparaison des timestamps des fichiers avec le timestamp stocké dans le cache

**Algorithme d'invalidation** :
1. Stocker dans le cache : `['data' => [...], 'files_hash' => md5(serialize($fileTimestamps))]`
2. À chaque lecture, comparer le hash actuel avec celui du cache
3. Si différent, régénérer automatiquement

#### 4.1.2 Classe `TemplateScanner`

**Localisation** : `app/Helpers/TemplateScanner.php`  
**Namespace** : `App\Helpers\`  
**Contexte** : Outil de développement uniquement, non publié avec le package

**Responsabilités** :
- Scanner récursivement `resources/views/templates/**/*.blade.php`
- Extraire les métadonnées (nom, catégorie, annotations, type, route)
- Générer la structure de données identique à celle des manifests JSON actuels
- Gérer le cache avec invalidation intelligente

**Signature principale** :

```php
class TemplateScanner
{
    /**
     * Scanne les templates et retourne les métadonnées.
     * Utilise le cache Laravel avec invalidation basée sur les timestamps.
     *
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string}
     */
    public static function scan(): array;
    
    /**
     * Force la régénération du cache (ignore le cache existant).
     *
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string}
     */
    public static function scanFresh(): array;
    
    /**
     * Vérifie si le cache est valide en comparant les timestamps des fichiers.
     *
     * @return bool
     */
    public static function isCacheValid(): bool;
    
    /**
     * Invalide le cache (utile pour les tests ou après modifications manuelles).
     *
     * @return void
     */
    public static function clearCache(): void;
}
```

**Stratégie de cache** :
- Clé de cache : `daisy.templates.manifest`
- Tags : `['daisy', 'templates', 'manifest']`
- TTL : 3600 secondes (1 heure)
- Invalidation : Identique à `ComponentScanner`

#### 4.1.3 Logique de scan partagée

**Classe abstraite** : `app/Helpers/AbstractScanner.php` (optionnel, pour éviter la duplication)  
**Namespace** : `App\Helpers\`  
**Contexte** : Outil de développement uniquement, non publié avec le package

**Méthodes communes** :
- `getFiles(string $pattern): array` - Récupère tous les fichiers correspondant au pattern
- `getFilesHash(array $files): string` - Génère un hash basé sur les timestamps des fichiers
- `shouldRegenerate(string $cachedHash, string $currentHash): bool` - Détermine si le cache doit être régénéré

**Note sur les chemins** : Les scanners utilisent `resource_path()` qui fonctionne dans le contexte de l'application de développement du package. Les chemins pointent vers `resources/views/components/ui/` et `resources/views/templates/` qui sont les ressources du package.

### 4.2. Refactor de `DocsHelper`

#### 4.2.1 Migration progressive

**Phase 1** : Support des deux systèmes (manifests JSON + cache)
- `DocsHelper` essaie d'abord le cache dynamique
- Si le cache est vide, fallback vers les manifests JSON
- Si les manifests n'existent pas, scan à la volée et mise en cache

**Phase 2** : Migration complète vers le cache
- Suppression du support des manifests JSON (après validation en production)
- `DocsHelper` utilise uniquement les scanners avec cache

**Méthodes modifiées** :

```php
class DocsHelper
{
    /**
     * Lit le manifeste des composants (cache dynamique ou fallback JSON).
     *
     * @return array<string, mixed>
     */
    private static function readManifest(): array
    {
        // Essayer le cache dynamique
        $cached = ComponentScanner::scan();
        if (!empty($cached['components'])) {
            return $cached;
        }
        
        // Fallback vers manifests JSON (compatibilité)
        $path = resource_path('dev/data/components.json');
        if (File::exists($path)) {
            $json = File::get($path);
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data['components'])) {
                return $data;
            }
        }
        
        // Dernier recours : scan à la volée
        return ComponentScanner::scanFresh();
    }
    
    /**
     * Lit le manifeste des templates (cache dynamique ou fallback JSON).
     *
     * @return array<string, mixed>
     */
    private static function readTemplatesManifest(): array
    {
        // Essayer le cache dynamique
        $cached = TemplateScanner::scan();
        if (!empty($cached['templates'])) {
            return $cached;
        }
        
        // Fallback vers manifests JSON (compatibilité)
        $path = resource_path('dev/data/templates.json');
        if (File::exists($path)) {
            $json = File::get($path);
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data['templates'])) {
                return $data;
            }
        }
        
        // Dernier recours : scan à la volée
        return TemplateScanner::scanFresh();
    }
}
```

### 4.3. Intégration avec `npm run dev`

#### 4.3.1 Script de watch Node.js

**Fichier** : `scripts/watch-inventory.js`

**Fonctionnalités** :
- Watch des fichiers Blade dans `resources/views/components/ui/**/*.blade.php`
- Watch des fichiers Blade dans `resources/views/templates/**/*.blade.php`
- Debounce de 1 seconde pour éviter les scans multiples
- Exécution de `php artisan inventory:cache:refresh` (nouvelle commande)
- Logging clair des actions (start, change, success, error)

**Implémentation** :

```javascript
import { watch } from 'chokidar';
import { spawn } from 'child_process';
import { debounce } from 'lodash-es';

const paths = [
  'resources/views/components/ui/**/*.blade.php',
  'resources/views/templates/**/*.blade.php',
];

const debounceMs = 1000;
let isRunning = false;

function runInventoryRefresh() {
  if (isRunning) {
    return;
  }
  
  isRunning = true;
  console.log('🔄 Mise à jour du cache des inventaires...');
  
  const proc = spawn('php', ['artisan', 'inventory:cache:refresh'], {
    stdio: 'inherit',
    shell: true,
  });
  
  proc.on('close', (code) => {
    isRunning = false;
    if (code === 0) {
      console.log('✅ Cache des inventaires mis à jour');
    } else {
      console.error('❌ Erreur lors de la mise à jour du cache');
    }
  });
}

const debouncedRefresh = debounce(runInventoryRefresh, debounceMs);

const watcher = watch(paths, {
  ignored: /(^|[\/\\])\../,
  persistent: true,
  ignoreInitial: true,
});

watcher.on('change', (path) => {
  console.log(`📝 Fichier modifié: ${path}`);
  debouncedRefresh();
});

watcher.on('add', (path) => {
  console.log(`➕ Fichier ajouté: ${path}`);
  debouncedRefresh();
});

watcher.on('unlink', (path) => {
  console.log(`🗑️  Fichier supprimé: ${path}`);
  debouncedRefresh();
});

console.log('👀 Surveillance des composants et templates activée...');
```

#### 4.3.2 Modification de `package.json`

**Ajout des dépendances** :

```json
{
  "devDependencies": {
    "chokidar": "^3.6.0",
    "lodash-es": "^4.17.21"
  }
}
```

**Modification des scripts** :

```json
{
  "scripts": {
    "build": "vite build",
    "dev": "concurrently \"vite\" \"npm run watch:inventory\"",
    "watch:inventory": "node scripts/watch-inventory.js"
  }
}
```

#### 4.3.3 Nouvelle commande Artisan `inventory:cache:refresh`

**Localisation** : `app/Console/Commands/InventoryCacheRefresh.php`  
**Namespace** : `App\Console\Commands\`  
**Contexte** : Commande Artisan de développement uniquement, non publiée avec le package. Les commandes dans `app/Console/Commands/` sont automatiquement découvertes par Laravel dans le contexte de l'application de développement.

**Responsabilités** :
- Invalider les caches des composants et templates
- Forcer la régénération immédiate
- Logging clair des actions

**Signature** :

```php
class InventoryCacheRefresh extends Command
{
    protected $signature = 'inventory:cache:refresh {--components : Refresh only components cache} {--templates : Refresh only templates cache}';
    
    protected $description = 'Rafraîchit le cache des inventaires (composants et/ou templates)';
    
    public function handle(): int
    {
        $refreshComponents = $this->option('components') || !$this->option('templates');
        $refreshTemplates = $this->option('templates') || !$this->option('components');
        
        if ($refreshComponents) {
            $this->info('Rafraîchissement du cache des composants...');
            ComponentScanner::clearCache();
            ComponentScanner::scanFresh();
            $this->info('✓ Cache des composants rafraîchi');
        }
        
        if ($refreshTemplates) {
            $this->info('Rafraîchissement du cache des templates...');
            TemplateScanner::clearCache();
            TemplateScanner::scanFresh();
            $this->info('✓ Cache des templates rafraîchi');
        }
        
        return Command::SUCCESS;
    }
}
```

### 4.4. Compatibilité avec les commandes existantes

#### 4.4.1 Commandes `inventory:*` conservées

Les commandes `inventory:components`, `inventory:templates` et `inventory:update` sont **conservées** pour :
- Génération des manifests JSON pour les tests
- CI/CD et scripts automatisés
- Debug et inspection manuelle
- Compatibilité avec les outils existants

**Modification** : Ces commandes peuvent optionnellement rafraîchir le cache après génération des manifests.

#### 4.4.2 Nouvelle commande `inventory:cache:clear`

**Localisation** : `app/Console/Commands/InventoryCacheClear.php`  
**Namespace** : `App\Console\Commands\`  
**Contexte** : Commande Artisan de développement uniquement, non publiée avec le package.

**Responsabilités** :
- Nettoyer uniquement les caches (sans régénération)
- Utile pour forcer un scan frais au prochain accès

### 4.5. Gestion des erreurs et logging

#### 4.5.1 Stratégie de gestion d'erreurs

- **Fichiers manquants** : Retourner un tableau vide avec logging warning
- **Permissions insuffisantes** : Logging error + fallback vers manifests JSON
- **Erreurs de parsing** : Logging error + continuer avec les autres fichiers
- **Cache corrompu** : Détection automatique + régénération silencieuse

#### 4.5.2 Logging

- **Niveau INFO** : Scan initié, cache régénéré, fichiers détectés
- **Niveau WARNING** : Fichiers ignorés, fallback vers manifests
- **Niveau ERROR** : Erreurs critiques (permissions, corruption)

## 5. Livrables techniques

### 5.1. Nouvelles classes (outils de développement)

**Important** : Toutes ces classes sont dans `app/` car elles font partie de l'application de développement/test du package. Elles ne sont **pas publiées** avec le package final et sont utilisées uniquement pour générer la documentation pendant le développement.

1. **`app/Helpers/ComponentScanner.php`** (`App\Helpers\ComponentScanner`)
   - Scanner des composants UI
   - Gestion du cache avec invalidation intelligente
   - Extraction des métadonnées (props, data-attributes, modules JS)
   - Utilise `resource_path()` pour accéder aux ressources du package

2. **`app/Helpers/TemplateScanner.php`** (`App\Helpers\TemplateScanner`)
   - Scanner des templates
   - Gestion du cache avec invalidation intelligente
   - Extraction des annotations et métadonnées
   - Utilise `resource_path()` pour accéder aux ressources du package

3. **`app/Helpers/AbstractScanner.php`** (`App\Helpers\AbstractScanner`) (optionnel)
   - Classe abstraite pour partager la logique commune
   - Méthodes utilitaires (getFiles, getFilesHash, etc.)

### 5.2. Modifications des classes existantes

1. **`app/Helpers/DocsHelper.php`**
   - Migration vers les scanners avec cache
   - Support du fallback vers manifests JSON
   - Aucun changement dans l'API publique

2. **`app/Console/Commands/InventoryComponents.php`**
   - Option `--refresh-cache` pour rafraîchir le cache après génération
   - Conservation de la génération des manifests JSON

3. **`app/Console/Commands/InventoryTemplates.php`**
   - Option `--refresh-cache` pour rafraîchir le cache après génération
   - Conservation de la génération des manifests JSON

### 5.3. Nouvelles commandes Artisan (outils de développement)

**Important** : Ces commandes sont dans `app/Console/Commands/` car elles font partie de l'application de développement/test du package. Elles ne sont **pas publiées** avec le package final et sont utilisées uniquement pendant le développement.

1. **`app/Console/Commands/InventoryCacheRefresh.php`** (`App\Console\Commands\InventoryCacheRefresh`)
   - Rafraîchissement du cache des inventaires
   - Signature : `inventory:cache:refresh`

2. **`app/Console/Commands/InventoryCacheClear.php`** (`App\Console\Commands\InventoryCacheClear`)
   - Nettoyage des caches (sans régénération)
   - Signature : `inventory:cache:clear`

### 5.4. Scripts et configuration

1. **`scripts/watch-inventory.js`**
   - Watch automatique des fichiers Blade
   - Intégration avec `npm run dev`

2. **`package.json`**
   - Ajout des dépendances `chokidar` et `lodash-es`
   - Modification du script `dev` pour inclure le watch

### 5.5. Tests

1. **`tests/Unit/ComponentScannerTest.php`**
   - Test du scan des composants
   - Test du cache et de l'invalidation
   - Test du fallback vers manifests JSON

2. **`tests/Unit/TemplateScannerTest.php`**
   - Test du scan des templates
   - Test du cache et de l'invalidation
   - Test du fallback vers manifests JSON

3. **`tests/Feature/InventoryCacheTest.php`**
   - Test des commandes de cache
   - Test de l'intégration avec les scanners

4. **Mise à jour des tests existants**
   - `tests/Feature/ComponentsManifestTest.php` : Support du cache
   - `tests/Feature/Commands/InventoryUpdateTest.php` : Test du refresh cache

## 6. Plan de tests

| Suite | Cible | Fichiers | Points vérifiés |
|-------|-------|----------|-----------------|
| Unit | ComponentScanner | `tests/Unit/ComponentScannerTest.php` | Scan correct, cache valide, invalidation, extraction métadonnées |
| Unit | TemplateScanner | `tests/Unit/TemplateScannerTest.php` | Scan correct, cache valide, invalidation, extraction annotations |
| Feature | DocsHelper | `tests/Feature/DocsHelperTest.php` | Fallback manifests, utilisation cache, API inchangée |
| Feature | Commandes cache | `tests/Feature/InventoryCacheTest.php` | Refresh, clear, intégration scanners |
| Browser | Documentation | `tests/Browser/DocsNavigationTest.php` | Navigation fonctionne avec cache, pas de régression |
| Integration | npm run dev | Tests manuels | Watch fonctionne, cache régénéré automatiquement |

## 7. Roadmap d'implémentation

### Phase 1 : Infrastructure (Semaine 1)

1. **Créer les classes scanners**
   - `ComponentScanner` avec cache et invalidation
   - `TemplateScanner` avec cache et invalidation
   - Tests unitaires complets

2. **Créer les commandes Artisan**
   - `inventory:cache:refresh`
   - `inventory:cache:clear`
   - Tests des commandes

### Phase 2 : Migration DocsHelper (Semaine 1-2)

3. **Refactor de `DocsHelper`**
   - Migration vers les scanners avec fallback
   - Tests de compatibilité
   - Validation que l'API publique reste inchangée

4. **Tests d'intégration**
   - Vérifier que toutes les pages de documentation fonctionnent
   - Vérifier la navigation
   - Vérifier les performances

### Phase 3 : Intégration npm run dev (Semaine 2)

5. **Créer le script de watch**
   - `scripts/watch-inventory.js`
   - Tests manuels du watch
   - Validation du debounce

6. **Modifier `package.json`**
   - Ajout des dépendances
   - Modification du script `dev`
   - Documentation

### Phase 4 : Validation et documentation (Semaine 2-3)

7. **Tests complets**
   - Suite complète de tests
   - Tests de performance
   - Tests de compatibilité

8. **Documentation**
   - Mise à jour du README
   - Documentation des nouvelles commandes
   - Guide de migration (si nécessaire)

### Phase 5 : Déploiement progressif (Semaine 3)

9. **Déploiement**
   - Activation progressive
   - Monitoring des performances
   - Collecte de feedback

10. **Optimisations finales**
    - Ajustements basés sur les retours
    - Optimisations de performance si nécessaire

## 8. Points de vigilance

### 8.0. Contexte package vs application

- **Outils de développement uniquement** : Tous les scanners, helpers et commandes sont des outils de développement qui ne sont **pas publiés** avec le package.
- **Namespace `App\`** : Acceptable pour les outils de dev dans le contexte d'une application de développement/test du package.
- **Chemins relatifs au package** : Utiliser `resource_path()` qui fonctionne dans le contexte de l'application de développement et pointe vers les ressources du package.
- **Non publié** : Aucun de ces fichiers ne doit être inclus dans les tags de publication du package (`daisy-views`, `daisy-templates`, etc.).

### 8.1. Performance

- **Cache obligatoire** : Ne jamais scanner sans cache en production
- **TTL approprié** : 1 heure par défaut, ajustable via config
- **Invalidation intelligente** : Basée sur les timestamps, pas sur le temps écoulé
- **Tags de cache** : Utiliser les tags Laravel pour invalidation sélective

### 8.2. Compatibilité

- **Fallback automatique** : Toujours supporter les manifests JSON en fallback
- **API publique inchangée** : `DocsHelper` doit conserver la même API
- **Tests existants** : Tous les tests doivent continuer de fonctionner
- **Migration progressive** : Support des deux systèmes pendant la transition

### 8.3. Robustesse

- **Gestion d'erreurs** : Toutes les erreurs doivent être gérées gracieusement
- **Logging approprié** : Logging clair sans spam
- **Validation des données** : Valider les données avant mise en cache
- **Détection de corruption** : Détecter et corriger automatiquement les caches corrompus

### 8.4. Développement

- **Watch fiable** : Le watch doit fonctionner sur tous les OS (Windows, Linux, macOS)
- **Debounce efficace** : Éviter les scans multiples lors de sauvegardes rapides
- **Feedback utilisateur** : Logging clair dans la console lors du watch
- **Documentation** : Documentation complète des nouvelles fonctionnalités

## 9. Métriques de succès

1. **Performance** : Temps de réponse des pages de documentation < 100ms (avec cache)
2. **Fiabilité** : 100% des tests existants passent sans modification
3. **Transparence** : Aucun changement visible pour l'utilisateur final
4. **Maintenabilité** : Réduction de 50% du code lié aux manifests JSON
5. **Développement** : Workflow transparent avec watch automatique

## 10. Évolutions futures possibles

1. **Cache distribué** : Support de Redis/Memcached pour les environnements multi-serveurs
2. **Webhooks** : Invalidation du cache via webhooks lors de déploiements
3. **Métriques** : Dashboard de monitoring des performances du cache
4. **Optimisations** : Cache partiel (seulement les métadonnées nécessaires)
5. **API REST** : Exposition des inventaires via API REST pour intégrations externes

## 11. Notes techniques

### 11.1. Format du cache

Le cache stocke la structure suivante :

```php
[
    'data' => [
        'components' => [...], // ou 'templates' => [...], 'categories' => [...]
        'generated_at' => '2024-01-01T00:00:00Z',
    ],
    'files_hash' => 'abc123...', // Hash des timestamps des fichiers
    'cached_at' => 1704067200, // Timestamp Unix
]
```

### 11.2. Algorithme d'invalidation

1. Calculer le hash actuel des timestamps des fichiers
2. Comparer avec le hash stocké dans le cache
3. Si différent, régénérer automatiquement
4. Stocker le nouveau hash dans le cache

### 11.3. Gestion des fichiers partiels

- Ignorer les fichiers dans `partials/`
- Ignorer les fichiers cachés (commençant par `.`)
- Gérer les erreurs de lecture gracieusement
- Continuer le scan même si un fichier est corrompu

### 11.4. Structure du package

**Ressources du package** (publiées) :
- `resources/views/components/` → Composants UI du package
- `resources/views/templates/` → Templates du package
- `resources/lang/` → Traductions du package
- `src/` → Code source du package (ServiceProvider, etc.)

**Outils de développement** (non publiés) :
- `app/` → Application de développement/test du package
  - `app/Helpers/` → Helpers de développement (scanners, DocsHelper)
  - `app/Console/Commands/` → Commandes Artisan de développement
  - `app/Http/Controllers/` → Contrôleurs pour la documentation/démo
- `resources/dev/` → Ressources de développement
  - `resources/dev/data/` → Données générées (manifests JSON, cache)
  - `resources/dev/views/` → Pages de documentation/démo
- `scripts/` → Scripts Node.js de développement (watch, etc.)

**Important** : Les utilisateurs finaux du package n'ont pas accès à `app/`, `resources/dev/` ni `scripts/`. Ces éléments sont uniquement pour le développement du package lui-même.

Ce lot 11 transforme le système d'inventaire en une solution moderne, performante et maintenable, tout en conservant la compatibilité avec l'existant et en améliorant significativement l'expérience de développement. **Tous les outils créés restent dans le contexte de développement du package et ne sont pas publiés avec le package final.**

