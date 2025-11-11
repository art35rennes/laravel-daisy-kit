# Lot 6 : Templates de gestion des erreurs et états

## Vue d'ensemble
Créer des templates pour gérer les erreurs HTTP (compatible avec Laravel) et les états vides/chargement de l'application.

## Templates à créer

### 1. error.blade.php (généralisé pour 404, 500, 403, etc.)
**Fichier** : `resources/views/templates/error.blade.php`

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

**Composants UI utilisés** :
- `x-daisy::layout.app` (layout minimal)
- `x-daisy::ui.layout.hero` (illustration)
- `x-daisy::ui.data-display.badge` (code d'erreur)
- `x-daisy::ui.inputs.button` (retour, accueil)
- `x-daisy::ui.feedback.alert` (détails de l'erreur en debug)
- `x-daisy::ui.layout.card` (contenu)

**Structure** :
- Hero avec illustration (optionnel)
- Code d'erreur (badge)
- Titre et message
- Actions (retour, accueil)
- Détails de l'erreur (si debug mode)

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
- `404_title" : "Page non trouvée"
- `404_message" : "La page que vous recherchez n'existe pas."
- `403_title" : "Accès refusé"
- `403_message" : "Vous n'avez pas l'autorisation d'accéder à cette page."
- `500_title" : "Erreur serveur"
- `500_message" : "Une erreur s'est produite. Veuillez réessayer plus tard."
- `503_title" : "Service indisponible"
- `503_message" : "Le service est temporairement indisponible."
- `go_home" : "Retour à l'accueil"
- `go_back" : "Retour"

---

### 2. maintenance.blade.php
**Fichier** : `resources/views/templates/maintenance.blade.php`

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

**Composants UI utilisés** :
- `x-daisy::layout.app` (layout minimal)
- `x-daisy::ui.layout.hero` (illustration)
- `x-daisy::ui.feedback.alert` (message)
- `x-daisy::ui.feedback.loading` (indicateur)

**Traductions nécessaires** (à créer `resources/lang/fr/maintenance.php`) :
- `maintenance" : "Maintenance en cours"
- `message" : "Nous effectuons actuellement une maintenance. Veuillez réessayer dans quelques instants."
- `estimated_return" : "Retour estimé : :time"

---

### 3. empty-state.blade.php
**Fichier** : `resources/views/templates/empty-state.blade.php`

**Description** : Template pour afficher un état vide (aucune donnée) avec message et action.

**Note** : Ce template peut aussi servir de composant UI réutilisable.

**Props** :
```php
@props([
    'icon' => 'inbox',
    'title' => __('common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'actionVariant' => 'primary',
    'size' => 'md',
    'illustration' => null, // Custom illustration image
])
```

**Composants UI utilisés** :
- `x-daisy::ui.layout.card` (conteneur)
- `x-daisy::ui.advanced.icon` (icône)
- `x-daisy::ui.inputs.button` (action)
- `x-daisy::ui.layout.hero` (si illustration)

**Traductions nécessaires** (à créer `resources/lang/fr/common.php`) :
- `empty" : "Aucune donnée"
- `no_results" : "Aucun résultat"
- `create_first" : "Créer le premier"

---

### 4. loading-state.blade.php
**Fichier** : `resources/views/templates/loading-state.blade.php`

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

**Composants UI utilisés** :
- `x-daisy::ui.feedback.loading` (spinner)
- `x-daisy::ui.feedback.skeleton` (skeleton)
- `x-daisy::ui.data-display.progress` (progress bar)

**Traductions nécessaires** :
- `loading" : "Chargement..."
- `please_wait" : "Veuillez patienter"

---

## Composants/Wrappers nécessaires

### 1. empty-state.blade.php (composant UI)
**Fichier** : `resources/views/components/ui/feedback/empty-state.blade.php`

Créer ce composant réutilisable (déjà mentionné dans le lot 4).

---

## Tests à prévoir

1. **Test error** : Vérifier chaque code d'erreur (404, 403, 500, 503)
2. **Test maintenance** : Vérifier l'affichage en mode maintenance
3. **Test empty-state** : Vérifier avec/sans action
4. **Test loading-state** : Vérifier chaque type (spinner, skeleton, progress)
5. **Test responsive** : Vérifier sur tous les écrans

---

## Ordre d'implémentation recommandé

1. `empty-state.blade.php` (composant UI + template)
2. `loading-state.blade.php`
3. `error.blade.php`
4. `maintenance.blade.php`

---

## Notes importantes

- **error.blade.php** : Doit être compatible avec le système d'erreurs Laravel. Placer dans `resources/views/errors/` ou utiliser via `view()->exists()`.
- **maintenance.blade.php** : Compatible avec `php artisan down`. Peut être utilisé comme vue par défaut de maintenance.
- Tous les templates doivent respecter les traductions Laravel.
- Les illustrations peuvent être des images ou des SVG inline.

