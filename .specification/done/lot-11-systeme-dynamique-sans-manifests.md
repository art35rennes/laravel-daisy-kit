# Lot 11 · Plan de spécification – Inventaire dynamique sans manifests JSON (cache fichier, sans DB)

## 1. Objectifs produit

- **Éliminer la dépendance aux manifests JSON statiques** pour la documentation et la navigation, en faveur d'un système de scan dynamique avec cache intelligent.
- **Simplifier le workflow de développement** en automatisant la régénération des inventaires lors des modifications de fichiers.
- **Améliorer la maintenabilité** en centralisant la logique de scan dans une classe réutilisable, éliminant la duplication entre commandes Artisan et helpers.
- **Garantir les performances** grâce à un cache **fichier** (PHP) avec invalidation intelligente basée sur les timestamps des fichiers, **sans jamais dépendre d'un driver de cache DB**.

**Note importante** : Ce lot concerne uniquement les **outils de développement** du package. Les scanners, helpers et commandes sont dans `app/` car ils font partie de l'application de développement/test du package, **non publiés** avec le package final. Ils sont utilisés uniquement pour générer la documentation et les inventaires pendant le développement.

## 2. Périmètre fonctionnel

| Axe | Description synthétique | Valeur ajoutée |
|-----|-------------------------|----------------|
| Scanner dynamique centralisé | Classe `ComponentScanner` et `TemplateScanner` qui scannent les fichiers directement depuis le système de fichiers. | Source unique de vérité, pas de désynchronisation possible. |
| Cache fichier avec invalidation | Utilisation d'un cache **fichier PHP** (chargé via `require`) avec invalidation basée sur les `filemtime()`. | Performances optimales sans régénération manuelle. |
| Intégration npm run dev | Watch automatique des fichiers Blade qui régénère les caches à la volée. | Workflow transparent pour le développeur. |
| Refactor de DocsHelper | `DocsHelper` utilise désormais les scanners avec cache au lieu de lire les manifests JSON. | Code simplifié, logique centralisée. |

## 3. Exigences transverses (phase dev, sans rétrocompatibilité)

1. **Performance garantie**
   - Le scan ne doit jamais être exécuté à chaque requête HTTP.
   - Utilisation obligatoire d'un cache **fichier** (PHP) lisible via `require`, sans TTL (invalidation par changement des fichiers).
   - Invalidation automatique basée sur les timestamps des fichiers modifiés.
   - Pas de tags de cache Laravel (les tags dépendent des stores et peuvent impliquer Redis / DB).
   - Ne pas utiliser `Cache::` / `cache()` pour stocker l'inventaire (risque de driver `database`). Le seul cache Laravel toléré ici est `php artisan view:cache` (cache des vues compilées).

2. **Robustesse et résilience**
   - Pas de fallback vers des manifests JSON (suppression du mode legacy).
   - Gestion gracieuse des erreurs (fichiers manquants, permissions, etc.).
   - Logging des erreurs critiques sans bloquer l'application.

3. **Pas de rétrocompatibilité**
   - Les manifests JSON `resources/dev/data/*.json` sont supprimés du flux “source de vérité”.
   - Les points d'entrée (Docs, navigation) lisent uniquement l'inventaire via le cache fichier (ou un scan forcé en commande).

4. **Intégration transparente**
   - L'API publique de `DocsHelper` peut évoluer si nécessaire (phase dev), mais le résultat fonctionnel (navigation/docs) doit rester identique.
   - Les vues de documentation continuent de fonctionner sans dépendre de JSON.

5. **Développement local optimisé**
   - Watch automatique des fichiers Blade via `npm run dev`.
   - Régénération du cache uniquement quand nécessaire (fichiers modifiés) via une commande Artisan.
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
- Générer une structure de données stable (tableaux PHP) consommée par `DocsHelper`
- Gérer un **cache fichier PHP** avec invalidation intelligente (hash des mtimes)

**Signature principale** :

```php
class ComponentScanner
{
    /**
     * Retourne l'inventaire des composants depuis un cache fichier PHP.
     * Si le cache est manquant ou invalide, une exception est levée (pas de scan implicite en HTTP).
     *
     * @return array{components: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function readCached(): array;
    
    /**
     * Reconstruit le cache fichier PHP (scan + écriture), en ignorants les caches existants.
     *
     * @return array{components: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function rebuildCache(): array;
    
    /**
     * Vérifie si le cache fichier est valide en comparant le hash des mtimes.
     *
     * @return bool
     */
    public static function isCacheValid(): bool;
    
    /**
     * Supprime le cache fichier (utile pour les tests ou après modifications manuelles).
     *
     * @return void
     */
    public static function clearCache(): void;
}
```

**Stratégie de cache (sans DB)** :
- Supporté uniquement via **fichier PHP** (ex: `bootstrap/cache/daisy-components.php` ou `storage/framework/cache/daisy-components.php`)
- Chargement par `require $path` (OPcache-friendly)
- Pas de TTL : on régénère uniquement si le hash des mtimes change

**Algorithme d'invalidation** :
1. Construire la liste de fichiers et leurs `filemtime()`
2. Calculer `files_hash = md5(json_encode($fileMtimes))`
3. Écrire un fichier PHP qui retourne `['components' => [...], 'generated_at' => ..., 'files_hash' => ...]`
4. Lors d'un rebuild, comparer le `files_hash` courant avec celui du cache existant et ne réécrire que si différent

#### 4.1.2 Classe `TemplateScanner`

**Localisation** : `app/Helpers/TemplateScanner.php`  
**Namespace** : `App\Helpers\`  
**Contexte** : Outil de développement uniquement, non publié avec le package

**Responsabilités** :
- Scanner récursivement `resources/views/templates/**/*.blade.php`
- Extraire les métadonnées (nom, catégorie, annotations, type, route)
- Générer une structure de données stable (tableaux PHP) consommée par `DocsHelper`
- Gérer un **cache fichier PHP** avec invalidation intelligente (hash des mtimes)

**Signature principale** :

```php
class TemplateScanner
{
    /**
     * Retourne l'inventaire des templates depuis un cache fichier PHP.
     * Si le cache est manquant ou invalide, une exception est levée (pas de scan implicite en HTTP).
     *
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function readCached(): array;
    
    /**
     * Reconstruit le cache fichier PHP (scan + écriture), en ignorant les caches existants.
     *
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function rebuildCache(): array;
    
    /**
     * Vérifie si le cache fichier est valide en comparant le hash des mtimes.
     *
     * @return bool
     */
    public static function isCacheValid(): bool;
    
    /**
     * Supprime le cache fichier (utile pour les tests ou après modifications manuelles).
     *
     * @return void
     */
    public static function clearCache(): void;
}
```

**Stratégie de cache (sans DB)** :
- Supporté uniquement via **fichier PHP** (ex: `bootstrap/cache/daisy-templates.php` ou `storage/framework/cache/daisy-templates.php`)
- Même logique d'invalidation que `ComponentScanner`

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

#### 4.2.1 Simplification (sans rétrocompatibilité)

- `DocsHelper` lit uniquement l'inventaire depuis les caches fichiers construits par les commandes (pas de JSON).
- En contexte HTTP, **pas de scan implicite** : si le cache est absent, on affiche une erreur actionnable (“lancez la commande X”).
- En contexte CLI (commandes), les scanners reconstruisent le cache.

**Méthodes modifiées** :

```php
class DocsHelper
{
    /**
     * Lit l'inventaire des composants depuis le cache fichier.
     *
     * @return array<string, mixed>
     */
    private static function readManifest(): array
    {
        return ComponentScanner::readCached();
    }
    
    /**
     * Lit l'inventaire des templates depuis le cache fichier.
     *
     * @return array<string, mixed>
     */
    private static function readTemplatesManifest(): array
    {
        return TemplateScanner::readCached();
    }
}
```

### 4.3. Intégration avec `npm run dev`

#### 4.3.1 Script de watch Node.js (dev)

**Fichier** : `scripts/watch-inventory.js`

**Fonctionnalités** :
- Watch des fichiers Blade dans `resources/views/components/ui/**/*.blade.php`
- Watch des fichiers Blade dans `resources/views/templates/**/*.blade.php`
- Debounce de 1 seconde pour éviter les scans multiples
- Exécution de `php artisan inventory:cache:rebuild` (nouvelle commande)
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
  
  const proc = spawn('php', ['artisan', 'inventory:cache:rebuild'], {
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

#### 4.3.3 Nouvelle commande Artisan `inventory:cache:rebuild`

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
    protected $signature = 'inventory:cache:rebuild {--components : Rebuild only components cache} {--templates : Rebuild only templates cache}';
    
    protected $description = 'Reconstruit le cache fichier des inventaires (composants et/ou templates)';
    
    public function handle(): int
    {
        $refreshComponents = $this->option('components') || !$this->option('templates');
        $refreshTemplates = $this->option('templates') || !$this->option('components');
        
        if ($refreshComponents) {
            $this->info('Rafraîchissement du cache des composants...');
            ComponentScanner::clearCache();
            ComponentScanner::rebuildCache();
            $this->info('✓ Cache des composants rafraîchi');
        }
        
        if ($refreshTemplates) {
            $this->info('Rafraîchissement du cache des templates...');
            TemplateScanner::clearCache();
            TemplateScanner::rebuildCache();
            $this->info('✓ Cache des templates rafraîchi');
        }
        
        return Command::SUCCESS;
    }
}
```

### 4.4. Commandes et flux (sans JSON)

- Les commandes `inventory:*` sont réorientées pour **générer/reconstruire le cache fichier PHP** (plus de JSON comme livrable).
- La commande dédiée `inventory:cache:rebuild` devient le point d'entrée standard pour le watch.

#### 4.4.1 Nouvelle commande `inventory:cache:clear`

**Localisation** : `app/Console/Commands/InventoryCacheClear.php`  
**Namespace** : `App\Console\Commands\`  
**Contexte** : Commande Artisan de développement uniquement, non publiée avec le package.

**Responsabilités** :
- Nettoyer uniquement les caches (sans régénération)
- Utile pour forcer un scan frais au prochain accès

### 4.5. Gestion des erreurs et logging

#### 4.5.1 Stratégie de gestion d'erreurs

- **Fichiers manquants** : Retourner un tableau vide avec logging warning
- **Permissions insuffisantes** : Logging error + message actionnable (“corrigez les droits / relancez la commande”)
- **Erreurs de parsing** : Logging error + continuer avec les autres fichiers
- **Cache corrompu** : Détection automatique + suppression + rebuild en CLI (pas en HTTP)

#### 4.5.2 Logging

- **Niveau INFO** : Scan initié, cache régénéré, fichiers détectés
- **Niveau WARNING** : Fichiers ignorés, cache absent/invalide, incohérences mineures
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
   - Lecture via caches fichiers générés par `ComponentScanner` / `TemplateScanner`
   - Suppression du support JSON

2. **`app/Console/Commands/InventoryComponents.php`**
   - Reconstruit le cache fichier des composants (et peut afficher un résumé)

3. **`app/Console/Commands/InventoryTemplates.php`**
   - Reconstruit le cache fichier des templates (et peut afficher un résumé)

### 5.3. Nouvelles commandes Artisan (outils de développement)

**Important** : Ces commandes sont dans `app/Console/Commands/` car elles font partie de l'application de développement/test du package. Elles ne sont **pas publiées** avec le package final et sont utilisées uniquement pendant le développement.

1. **`app/Console/Commands/InventoryCacheRebuild.php`** (`App\Console\Commands\InventoryCacheRebuild`)
   - Reconstruction du cache fichier des inventaires
   - Signature : `inventory:cache:rebuild`

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
   - Test de l'écriture/lecture du cache fichier PHP

2. **`tests/Unit/TemplateScannerTest.php`**
   - Test du scan des templates
   - Test du cache et de l'invalidation
   - Test de l'écriture/lecture du cache fichier PHP

3. **`tests/Feature/InventoryCacheTest.php`**
   - Test des commandes de cache
   - Test de l'intégration avec les scanners

4. **Mise à jour des tests existants**
   - `tests/Feature/ComponentsManifestTest.php` : remplacer le JSON par le cache fichier
   - `tests/Feature/Commands/InventoryUpdateTest.php` : tester le rebuild cache

## 6. Plan de tests

| Suite | Cible | Fichiers | Points vérifiés |
|-------|-------|----------|-----------------|
| Unit | ComponentScanner | `tests/Unit/ComponentScannerTest.php` | Scan correct, cache valide, invalidation, extraction métadonnées |
| Unit | TemplateScanner | `tests/Unit/TemplateScannerTest.php` | Scan correct, cache valide, invalidation, extraction annotations |
| Feature | DocsHelper | `tests/Feature/DocsHelperTest.php` | Lecture cache fichier, erreurs actionnables si cache absent, API stable côté docs |
| Feature | Commandes cache | `tests/Feature/InventoryCacheTest.php` | Rebuild, clear, intégration scanners |
| Browser | Documentation | `tests/Browser/DocsNavigationTest.php` | Navigation fonctionne avec cache, pas de régression |
| Integration | npm run dev | Tests manuels | Watch fonctionne, cache régénéré automatiquement |

## 7. Roadmap d'implémentation

### Phase 1 : Infrastructure (Semaine 1)

1. **Créer les classes scanners**
   - `ComponentScanner` avec cache et invalidation
   - `TemplateScanner` avec cache et invalidation
   - Tests unitaires complets

2. **Créer les commandes Artisan**
   - `inventory:cache:rebuild`
   - `inventory:cache:clear`
   - Tests des commandes

### Phase 2 : Refactor DocsHelper (Semaine 1-2)

3. **Refactor de `DocsHelper`**
   - Bascule vers lecture du cache fichier (suppression JSON)
   - Tests de rendu/docs
   - Validation que la navigation/docs restent identiques

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
   - Tests de non-régression docs/navigation

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

- **Cache obligatoire** : Ne jamais scanner sans cache en HTTP
- **Pas de TTL** : invalidation par changement de fichiers (hash/mtime)
- **Invalidation intelligente** : Basée sur les timestamps, pas sur le temps écoulé
- **Pas de Cache store** : éviter toute dépendance à un driver (notamment `database`)

### 8.2. Compatibilité

- **Pas de fallback** : suppression du JSON
- **Mises à jour associées** : tests et docs adaptés au nouveau flux

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

Le cache fichier (PHP) retourne la structure suivante :

```php
return [
    'components' => [...], // ou 'templates' => [...], 'categories' => [...]
    'generated_at' => '2024-01-01T00:00:00Z',
    'files_hash' => 'abc123...', // Hash des filemtime() des fichiers sources
];
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
- `bootstrap/cache/` (ou `storage/framework/cache/`) → Caches fichiers PHP des inventaires (dev uniquement)
  - `resources/dev/views/` → Pages de documentation/démo
- `scripts/` → Scripts Node.js de développement (watch, etc.)

**Important** : Les utilisateurs finaux du package n'ont pas accès à `app/`, `resources/dev/` ni `scripts/`. Ces éléments sont uniquement pour le développement du package lui-même.

Ce lot 11 transforme le système d'inventaire en une solution moderne, performante et maintenable, tout en conservant la compatibilité avec l'existant et en améliorant significativement l'expérience de développement. **Tous les outils créés restent dans le contexte de développement du package et ne sont pas publiés avec le package final.**

