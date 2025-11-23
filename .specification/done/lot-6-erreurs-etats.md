# Lot 6 : Templates de gestion des erreurs et états

## Vue d'ensemble
Créer des templates pour gérer les erreurs HTTP (compatible avec Laravel) et les états vides/chargement de l'application.

**Architecture Atomic Design** : Ce lot suit les principes Atomic Design avec une hiérarchie stricte :
- **ATOMS** : Éléments de base (déjà existants : badge, button, icon, alert, loading, skeleton, progress)
- **MOLECULES** : Combinaisons simples d'atomes (error-header, error-actions, loading-message)
- **ORGANISMS** : Combinaisons complexes (error-content, loading-state-content)
- **TEMPLATES** : Structures de page utilisant des organisms (error, maintenance, empty-state, loading-state)

## Hiérarchie Atomic Design - Vue d'ensemble

```
TEMPLATES (pages complètes)
├── error.blade.php
│   └── error-content (ORGANISM)
│       ├── error-header (MOLECULE)
│       │   ├── badge (ATOM)
│       │   └── titre (texte)
│       ├── error-actions (MOLECULE)
│       │   └── button (ATOM) × 2
│       └── alert (ATOM) - détails debug
│
├── maintenance.blade.php
│   ├── hero (ATOM)
│   ├── alert (ATOM)
│   └── loading (ATOM)
│
├── empty-state.blade.php
│   └── empty-state (MOLECULE existant)
│       ├── icon (ATOM)
│       ├── button (ATOM)
│       └── texte
│
└── loading-state.blade.php
    └── loading-state-content (ORGANISM)
        ├── loading-message (MOLECULE)
        │   ├── loading (ATOM)
        │   └── texte
        ├── skeleton (ATOM) - si type skeleton
        └── progress (ATOM) - si type progress
```

## Composants UI à créer (selon Atomic Design)

### Niveau MOLECULE

#### 1. error-header.blade.php
**Fichier** : `resources/views/components/ui/feedback/error-header.blade.php`

**Description** : Molécule combinant badge (code d'erreur) et titre.

**Niveau Atomic** : MOLECULE (combine badge + titre)

**Dépendances** : 
- `x-daisy::ui.data-display.badge` (ATOM)
- Titre (texte simple)

**Props** :
```php
@props([
    'statusCode' => 500,
    'title' => null, // Auto-generated if null
])
```

**Structure** :
- Badge avec code d'erreur
- Titre (h1 ou h2)

---

#### 2. error-actions.blade.php
**Fichier** : `resources/views/components/ui/feedback/error-actions.blade.php`

**Description** : Molécule combinant les boutons d'action (retour, accueil).

**Niveau Atomic** : MOLECULE (combine plusieurs boutons)

**Dépendances** :
- `x-daisy::ui.inputs.button` (ATOM)

**Props** :
```php
@props([
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    'showBack' => true,
    'showHome' => true,
])
```

**Structure** :
- Bouton retour (si showBack)
- Bouton accueil (si showHome)

---

#### 3. loading-message.blade.php
**Fichier** : `resources/views/components/ui/feedback/loading-message.blade.php`

**Description** : Molécule combinant loading spinner et message.

**Niveau Atomic** : MOLECULE (combine loading + texte)

**Dépendances** :
- `x-daisy::ui.feedback.loading` (ATOM)

**Props** :
```php
@props([
    'message' => __('common.loading'),
    'shape' => 'spinner',
    'size' => 'lg',
])
```

**Structure** :
- Loading spinner
- Message texte

---

### Niveau ORGANISM

#### 4. error-content.blade.php
**Fichier** : `resources/views/components/ui/feedback/error-content.blade.php`

**Description** : Organisme combinant header, message, actions et détails d'erreur.

**Niveau Atomic** : ORGANISM (combine plusieurs molécules/atomes)

**Dépendances** :
- `x-daisy::ui.feedback.error-header` (MOLECULE)
- `x-daisy::ui.feedback.error-actions` (MOLECULE)
- `x-daisy::ui.feedback.alert` (ATOM)
- `x-daisy::ui.layout.card` (ATOM)

**Props** :
```php
@props([
    'statusCode' => 500,
    'title' => null,
    'message' => null,
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    'showActions' => true,
    'showDetails' => config('app.debug'),
    'exception' => null, // $exception from Laravel
])
```

**Structure** :
- Card container
- Error header (molecule)
- Message
- Error actions (molecule)
- Alert avec détails (si debug mode)

---

#### 5. loading-state-content.blade.php
**Fichier** : `resources/views/components/ui/feedback/loading-state-content.blade.php`

**Description** : Organisme pour afficher un état de chargement complet (spinner, skeleton ou progress).

**Niveau Atomic** : ORGANISM (combine loading + skeleton + message)

**Dépendances** :
- `x-daisy::ui.feedback.loading` (ATOM)
- `x-daisy::ui.feedback.skeleton` (ATOM)
- `x-daisy::ui.data-display.progress` (ATOM)
- `x-daisy::ui.feedback.loading-message` (MOLECULE)

**Props** :
```php
@props([
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('common.loading'),
    'size' => 'lg',
    'skeletonCount' => 3,
])
```

**Structure** :
- Selon type : spinner avec message, skeleton répété, ou progress bar

---

## Templates à créer

### 1. error.blade.php (généralisé pour 404, 500, 403, etc.)
**Fichier** : `resources/views/templates/error.blade.php`

**Niveau Atomic** : TEMPLATE (utilise des organisms/molecules/atomes)

**Description** : Template généralisé pour toutes les pages d'erreur HTTP, compatible avec le système de gestion des erreurs Laravel.

**Props** :
```php
@props([
    'statusCode' => 500, // 404, 403, 500, 503, etc.
    'title' => null, // Auto-generated if null
    'message' => null, // Auto-generated if null
    'theme' => null,
    // Routes
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    // Options
    'showIllustration' => true,
    'showActions' => true,
    'showDetails' => config('app.debug'), // Show error details only in debug mode
])
```

**Fonctionnalités Laravel** :
- Compatible avec `resources/views/errors/404.blade.php`, `500.blade.php`, etc.
- Utilise `$exception` (injecté par Laravel) pour les détails
- Utilise `config('app.debug')` pour afficher les détails en mode debug
- Utilise `url()->previous()` pour le bouton retour
- Utilise `Route::has()` et `route()` pour les URLs
- Utilise les traductions Laravel pour les messages

**Composants utilisés (hiérarchie Atomic)** :
- `x-daisy::layout.app` (layout minimal - ATOM)
- `x-daisy::ui.layout.hero` (illustration - ATOM)
- `x-daisy::ui.feedback.error-content` (ORGANISM - contient header, message, actions, details)

**Structure** :
- Layout app (minimal)
- Hero avec illustration (optionnel)
- Error content (organisme complet)

**Utilisation dans Laravel** :
```blade
{{-- resources/views/errors/404.blade.php --}}
<x-daisy::templates.error statusCode="404" />

{{-- resources/views/errors/500.blade.php --}}
<x-daisy::templates.error statusCode="500" />

{{-- resources/views/errors/403.blade.php --}}
<x-daisy::templates.error statusCode="403" />
```

**Traductions nécessaires** (à créer `resources/lang/fr/errors.php`) :
- `404_title` : "Page non trouvée"
- `404_message` : "La page que vous recherchez n'existe pas."
- `403_title` : "Accès refusé"
- `403_message` : "Vous n'avez pas l'autorisation d'accéder à cette page."
- `500_title` : "Erreur serveur"
- `500_message` : "Une erreur s'est produite. Veuillez réessayer plus tard."
- `503_title` : "Service indisponible"
- `503_message` : "Le service est temporairement indisponible."
- `go_home` : "Retour à l'accueil"
- `go_back` : "Retour"

---

### 2. maintenance.blade.php
**Fichier** : `resources/views/templates/maintenance.blade.php`

**Niveau Atomic** : TEMPLATE (utilise des atomes)

**Description** : Page de maintenance affichée quand l'application est en mode maintenance (`php artisan down`).

**Props** :
```php
@props([
    'title' => __('maintenance.maintenance'),
    'theme' => null,
    'message' => null, // Custom message or use Laravel's
    'retryAfter' => null, // Retry-After header value
    'allowedIps' => [], // IPs allowed during maintenance
])
```

**Fonctionnalités Laravel** :
- Compatible avec `php artisan down`
- Utilise `app()->isDownForMaintenance()` pour détecter le mode maintenance
- Affiche le message de maintenance Laravel
- Utilise `Carbon` pour formater la date de retour estimée

**Composants utilisés (hiérarchie Atomic)** :
- `x-daisy::layout.app` (layout minimal - ATOM)
- `x-daisy::ui.layout.hero` (illustration - ATOM)
- `x-daisy::ui.feedback.alert` (message - ATOM)
- `x-daisy::ui.feedback.loading` (indicateur - ATOM)

**Traductions nécessaires** (à créer `resources/lang/fr/maintenance.php`) :
- `maintenance` : "Maintenance en cours"
- `message` : "Nous effectuons actuellement une maintenance. Veuillez réessayer dans quelques instants."
- `estimated_return` : "Retour estimé : :time"

---

### 3. empty-state.blade.php
**Fichier** : `resources/views/templates/empty-state.blade.php`

**Niveau Atomic** : TEMPLATE (utilise un composant UI existant)

**Description** : Template pour afficher un état vide (aucune donnée) avec message et action.

**Note** : Le composant UI `x-daisy::ui.feedback.empty-state` existe déjà. Ce template est un wrapper qui peut être utilisé dans des pages complètes.

**Props** :
```php
@props([
    'icon' => 'bi-inbox',
    'title' => __('common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'actionVariant' => 'primary',
    'size' => 'md',
    'illustration' => null, // Custom illustration image
])
```

**Composants utilisés (hiérarchie Atomic)** :
- `x-daisy::layout.app` (layout minimal - ATOM)
- `x-daisy::ui.feedback.empty-state` (composant UI existant - MOLECULE)
- `x-daisy::ui.layout.card` (conteneur optionnel - ATOM)

**Traductions nécessaires** (à créer `resources/lang/fr/common.php`) :
- `empty` : "Aucune donnée"
- `no_results` : "Aucun résultat"
- `create_first` : "Créer le premier"

---

### 4. loading-state.blade.php
**Fichier** : `resources/views/templates/loading-state.blade.php`

**Niveau Atomic** : TEMPLATE (utilise un organisme)

**Description** : Template pour afficher un état de chargement avec skeleton ou spinner.

**Props** :
```php
@props([
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('common.loading'),
    'size' => 'lg',
    'fullScreen' => false,
    'skeletonCount' => 3, // For skeleton type
])
```

**Composants utilisés (hiérarchie Atomic)** :
- `x-daisy::layout.app` (layout minimal - ATOM)
- `x-daisy::ui.feedback.loading-state-content` (ORGANISM - contient loading/skeleton/progress + message)

**Traductions nécessaires** :
- `loading` : "Chargement..."
- `please_wait` : "Veuillez patienter"

---

## Composants existants à réutiliser

### Composants UI déjà disponibles
- `x-daisy::ui.feedback.empty-state` : Composant UI existant (MOLECULE) - peut être utilisé directement
- `x-daisy::ui.feedback.loading` : Composant UI existant (ATOM)
- `x-daisy::ui.feedback.skeleton` : Composant UI existant (ATOM)
- `x-daisy::ui.data-display.progress` : Composant UI existant (ATOM)
- `x-daisy::ui.data-display.badge` : Composant UI existant (ATOM)
- `x-daisy::ui.inputs.button` : Composant UI existant (ATOM)
- `x-daisy::ui.feedback.alert` : Composant UI existant (ATOM)
- `x-daisy::ui.layout.card` : Composant UI existant (ATOM)
- `x-daisy::ui.layout.hero` : Composant UI existant (ATOM)

---

## Tests à prévoir

1. **Test error** : Vérifier chaque code d'erreur (404, 403, 500, 503)
2. **Test maintenance** : Vérifier l'affichage en mode maintenance
3. **Test empty-state** : Vérifier avec/sans action
4. **Test loading-state** : Vérifier chaque type (spinner, skeleton, progress)
5. **Test responsive** : Vérifier sur tous les écrans

---

## Ordre d'implémentation recommandé (selon Atomic Design)

### Phase 1 : Molécules
1. `error-header.blade.php` (MOLECULE)
2. `error-actions.blade.php` (MOLECULE)
3. `loading-message.blade.php` (MOLECULE)

### Phase 2 : Organisms
4. `error-content.blade.php` (ORGANISM)
5. `loading-state-content.blade.php` (ORGANISM)

### Phase 3 : Templates
6. `empty-state.blade.php` (TEMPLATE - utilise composant existant)
7. `loading-state.blade.php` (TEMPLATE)
8. `error.blade.php` (TEMPLATE)
9. `maintenance.blade.php` (TEMPLATE)

---

## Notes importantes

### Respect de la hiérarchie Atomic Design
- **Strict hierarchy** : Les templates ne doivent utiliser que des organisms/molecules/atomes
- **No duplication** : Réutiliser les composants existants avant d'en créer de nouveaux
- **Logical placement** : Tous les composants UI dans `resources/views/components/ui/feedback/` (catégorie fonctionnelle)
- **Maximum reuse** : Le composant `empty-state` existe déjà, le template est juste un wrapper

### Compatibilité Laravel
- **error.blade.php** : Doit être compatible avec le système d'erreurs Laravel. Placer dans `resources/views/errors/` ou utiliser via `view()->exists()`.
- **maintenance.blade.php** : Compatible avec `php artisan down`. Peut être utilisé comme vue par défaut de maintenance.
- Tous les templates doivent respecter les traductions Laravel.
- Les illustrations peuvent être des images ou des SVG inline.

### Structure des fichiers
- **Composants UI** : `resources/views/components/ui/feedback/`
- **Templates** : `resources/views/templates/`

