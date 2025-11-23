# Lot 2 : Templates de profil utilisateur

## Vue d'ensemble
Créer trois templates pour la gestion du profil utilisateur : édition, paramètres et affichage. Ces templates sont **agnostiques des modèles** : ils acceptent des données sous forme de tableaux ou d'objets génériques, permettant leur utilisation avec n'importe quel modèle (User, Customer, Member, etc.) ou même sans modèle.

## Templates à créer

### 1. profile-edit.blade.php
**Fichier** : `resources/views/templates/profile/profile-edit.blade.php`

**Description** : Page d'édition du profil utilisateur avec upload d'avatar, formulaire de modification des informations personnelles.

**Props** :
```php
@props([
    'title' => __('profile.edit_profile'),
    'theme' => null,
    // Profile data (agnostic: array, object, or model)
    // Accepts: ['name' => '', 'email' => '', 'avatar' => '', ...] or Model instance
    'profile' => null, // Auto-detect: auth()->user() if available, or passed explicitly
    // Data accessors (for model-agnostic access)
    'nameKey' => 'name', // Key to access name (profile->name, profile['name'], etc.)
    'emailKey' => 'email',
    'avatarKey' => 'avatar',
    'phoneKey' => 'phone',
    'bioKey' => 'bio',
    'locationKey' => 'location',
    'websiteKey' => 'website',
    // Form
    'action' => Route::has('profile.update') ? route('profile.update') : '#',
    'method' => 'POST',
    'enctype' => 'multipart/form-data', // For file upload
    // Routes
    'profileViewUrl' => Route::has('profile.show') ? route('profile.show') : '#',
    'profileSettingsUrl' => Route::has('profile.settings') ? route('profile.settings') : '#',
    // Options
    'showAvatar' => true,
    'showName' => true,
    'showEmail' => true,
    'showPhone' => false,
    'showBio' => true,
    'showLocation' => false,
    'showWebsite' => false,
    'avatarMaxSize' => 2048, // KB
    'avatarAcceptedTypes' => ['image/jpeg', 'image/png', 'image/webp'],
])
```

**Fonctionnalités Laravel** :
- **Agnostique des modèles** : Accepte des tableaux, objets ou modèles Eloquent
- Utilise des accesseurs génériques pour accéder aux données (`data_get()` ou accesseurs personnalisés)
- Utilise `Route::has()` et `route()` pour les URLs
- Gère l'upload de fichier avec `enctype="multipart/form-data"`
- Utilise `old()` pour pré-remplir les champs
- Gère les erreurs de validation via `$errors`
- Utilise `@method('PUT')` ou `@method('PATCH')` pour les routes RESTful
- Utilise `Storage::url()` pour afficher l'avatar existant (si stocké dans storage)

**Accès aux données** :
Le template utilise `data_get($profile, $key, $default)` pour accéder aux données de manière agnostique :
- Si `$profile` est un modèle : `data_get($profile, 'name')` → `$profile->name`
- Si `$profile` est un tableau : `data_get($profile, 'name')` → `$profile['name']`
- Si `$profile` est un objet : `data_get($profile, 'name')` → `$profile->name`

**Composants UI utilisés** :
- `x-daisy::layout.app` ou `x-daisy::layout.sidebar-layout` (selon le contexte)
- `x-daisy::ui.partials.form-field` (champs de formulaire)
- `x-daisy::ui.inputs.input` (nom, email, téléphone, site web, localisation)
- `x-daisy::ui.inputs.textarea` (biographie)
- `x-daisy::ui.inputs.file-input` (upload d'avatar)
- `x-daisy::ui.data-display.avatar` (affichage de l'avatar actuel)
- `x-daisy::ui.inputs.button` (sauvegarder, annuler)
- `x-daisy::ui.feedback.alert` (messages de succès/erreur)
- `x-daisy::ui.navigation.breadcrumbs` (navigation)

**Structure** :
- En-tête avec titre et breadcrumbs
- Section avatar (upload + prévisualisation)
- Formulaire avec champs conditionnels selon les props
- Boutons d'action (sauvegarder, annuler)
- Messages de validation/erreur

**Slots disponibles** :
- `header` : Contenu personnalisé dans l'en-tête
- `sidebar` : Contenu dans une sidebar (si layout avec sidebar)
- `actions` : Actions supplémentaires (ex: supprimer le compte)

**Traductions nécessaires** (à créer `resources/lang/fr/profile.php`) :
- `edit_profile` : "Modifier mon profil"
- `profile_information" : "Informations du profil"
- `avatar" : "Photo de profil"
- `current_avatar" : "Photo actuelle"
- `upload_avatar" : "Télécharger une photo"
- `remove_avatar" : "Supprimer la photo"
- `name" : "Nom"
- `email" : "Email"
- `phone" : "Téléphone"
- `bio" : "Biographie"
- `location" : "Localisation"
- `website" : "Site web"
- `save" : "Enregistrer"
- `cancel" : "Annuler"
- `avatar_max_size" : "Taille maximale : :size KB"
- `avatar_accepted_types" : "Types acceptés : :types"

---

### 2. profile-settings.blade.php
**Fichier** : `resources/views/templates/profile/profile-settings.blade.php`

**Description** : Page de paramètres utilisateur (préférences, notifications, sécurité, etc.).

**Props** :
```php
@props([
    'title' => __('profile.settings'),
    'theme' => null,
    // Profile data (agnostic: array, object, or model)
    'profile' => null, // Auto-detect: auth()->user() if available, or passed explicitly
    // Data accessors (for model-agnostic access)
    'preferencesKey' => 'preferences', // Key to access preferences
    'languageKey' => 'language',
    'timezoneKey' => 'timezone',
    // Routes
    'action' => Route::has('profile.settings.update') ? route('profile.settings.update') : '#',
    'method' => 'POST',
    'profileEditUrl' => Route::has('profile.edit') ? route('profile.edit') : '#',
    'profileViewUrl' => Route::has('profile.show') ? route('profile.show') : '#',
    // Sections
    'showPreferences' => true,
    'showNotifications' => true,
    'showSecurity' => true,
    'showPrivacy' => false,
    'showLanguage' => true,
    'showTheme' => true,
    // Preferences data (can be passed separately or accessed from profile)
    'preferences' => null, // ['language' => 'fr', 'timezone' => 'Europe/Paris', ...]
])
```

**Fonctionnalités Laravel** :
- **Agnostique des modèles** : Accepte des tableaux, objets ou modèles Eloquent
- Utilise des accesseurs génériques pour accéder aux données (`data_get()` ou accesseurs personnalisés)
- Utilise les sessions Laravel pour les préférences (langue, thème)
- Utilise `config('app.locale')` pour la langue par défaut
- Gère les préférences via des champs de formulaire
- Utilise `@method('PUT')` ou `@method('PATCH')` pour les routes RESTful

**Composants UI utilisés** :
- `x-daisy::layout.app` ou `x-daisy::layout.sidebar-layout`
- `x-daisy::ui.navigation.tabs` (pour organiser les sections)
- `x-daisy::ui.partials.form-field`
- `x-daisy::ui.inputs.select` (langue, fuseau horaire)
- `x-daisy::ui.inputs.toggle` (notifications, préférences)
- `x-daisy::ui.inputs.checkbox` (options multiples)
- `x-daisy::ui.inputs.radio` (choix uniques)
- `x-daisy::ui.advanced.theme-controller` (sélecteur de thème)
- `x-daisy::ui.inputs.button`
- `x-daisy::ui.feedback.alert`
- `x-daisy::ui.navigation.breadcrumbs`

**Structure** :
- Tabs pour organiser les sections :
  - **Préférences** : langue, fuseau horaire, format de date
  - **Notifications** : email, push, SMS (toggles)
  - **Sécurité** : changement de mot de passe, 2FA
  - **Confidentialité** : visibilité du profil, données
  - **Apparence** : thème (light/dark)
- Formulaire avec validation
- Boutons de sauvegarde

**Sections détaillées** :

**Préférences** :
- Langue (select avec `config('app.locales')`)
- Fuseau horaire (select)
- Format de date (select)
- Format d'heure (12h/24h)

**Notifications** :
- Notifications par email (toggle)
- Notifications push (toggle)
- Notifications SMS (toggle)
- Types de notifications (checkboxes) :
  - Nouvelles fonctionnalités
  - Messages
  - Commentaires
  - Mentions

**Sécurité** :
- Section changement de mot de passe (collapsible)
- Authentification à deux facteurs (toggle + lien de configuration)
- Sessions actives (liste avec possibilité de déconnexion)

**Traductions nécessaires** :
- `settings" : "Paramètres"
- `preferences" : "Préférences"
- `notifications" : "Notifications"
- `security" : "Sécurité"
- `privacy" : "Confidentialité"
- `appearance" : "Apparence"
- `language" : "Langue"
- `timezone" : "Fuseau horaire"
- `date_format" : "Format de date"
- `time_format" : "Format d'heure"
- `change_password" : "Changer le mot de passe"
- `two_factor_auth" : "Authentification à deux facteurs"
- `active_sessions" : "Sessions actives"
- `theme" : "Thème"
- `theme_light" : "Clair"
- `theme_dark" : "Sombre"
- `theme_system" : "Système"

---

### 3. profile-view.blade.php
**Fichier** : `resources/views/templates/profile/profile-view.blade.php`

**Description** : Page d'affichage du profil utilisateur (lecture seule) avec timeline, badges, statistiques.

**Props** :
```php
@props([
    'title' => __('profile.profile'),
    'theme' => null,
    // Profile data (agnostic: array, object, or model)
    'profile' => null, // Auto-detect: auth()->user() if available, or passed explicitly
    // Data accessors (for model-agnostic access)
    'nameKey' => 'name',
    'emailKey' => 'email',
    'avatarKey' => 'avatar',
    'bioKey' => 'bio',
    'locationKey' => 'location',
    'websiteKey' => 'website',
    'createdAtKey' => 'created_at', // For "member since"
    'lastActiveKey' => 'last_active_at', // For "last active"
    // Routes
    'profileEditUrl' => Route::has('profile.edit') ? route('profile.edit') : '#',
    'profileSettingsUrl' => Route::has('profile.settings') ? route('profile.settings') : '#',
    // Data (can be passed or computed - agnostic format)
    'stats' => [], // ['label' => 'Posts', 'value' => 42, 'icon' => 'file-text']
    'badges' => [], // ['label' => 'Early Adopter', 'color' => 'primary', 'icon' => 'star']
    'timeline' => [], // Events/activities: ['date' => '2024-01-15', 'title' => '...', 'icon' => '...']
    'showStats' => true,
    'showBadges' => true,
    'showTimeline' => true,
    'showBio' => true,
    'showContact' => true,
    // Comparison function for isOwnProfile (agnostic)
    'isOwnProfile' => null, // Auto-detect: compare profile with auth()->user() or use custom function
    'compareProfile' => null, // Callable: function($profile) { return $profile->id === auth()->id(); }
])
```

**Fonctionnalités Laravel** :
- **Agnostique des modèles** : Accepte des tableaux, objets ou modèles Eloquent
- Utilise des accesseurs génériques pour accéder aux données (`data_get()` ou accesseurs personnalisés)
- Détecte automatiquement si c'est le profil de l'utilisateur connecté (ou utilise une fonction de comparaison personnalisée)
- Les stats, badges et timeline peuvent être passés en props (format agnostique) ou calculés dans le contrôleur
- Utilise `Route::has()` et `route()` pour les URLs
- Les dates peuvent être formatées avec `Carbon` si disponibles, sinon affichées telles quelles

**Composants UI utilisés** :
- `x-daisy::layout.app` ou `x-daisy::layout.sidebar-layout`
- `x-daisy::ui.data-display.avatar` (photo de profil)
- `x-daisy::ui.data-display.badge` (badges)
- `x-daisy::ui.data-display.stat` (statistiques)
- `x-daisy::ui.data-display.timeline` (timeline d'activités)
- `x-daisy::ui.layout.card` (sections)
- `x-daisy::ui.inputs.button` (actions : modifier, paramètres)
- `x-daisy::ui.navigation.breadcrumbs`

**Structure** :
- En-tête avec avatar, nom, bio
- Section statistiques (stats cards)
- Section badges (badges avec icônes)
- Section timeline (activités récentes)
- Section contact (email, site web, localisation)
- Actions (modifier, paramètres) si `isOwnProfile` est true

**Exemple de données** :

```php
$stats = [
    ['label' => 'Posts', 'value' => 42, 'icon' => 'file-text'],
    ['label' => 'Followers', 'value' => 1234, 'icon' => 'people'],
    ['label' => 'Following', 'value' => 567, 'icon' => 'person-plus'],
];

$badges = [
    ['label' => 'Early Adopter', 'color' => 'primary', 'icon' => 'star'],
    ['label' => 'Verified', 'color' => 'success', 'icon' => 'check-circle'],
];

$timeline = [
    ['date' => '2024-01-15', 'title' => 'A rejoint la plateforme', 'icon' => 'person-plus'],
    ['date' => '2024-01-20', 'title' => 'Premier post publié', 'icon' => 'file-text'],
];
```

**Traductions nécessaires** :
- `profile" : "Profil"
- `edit_profile" : "Modifier le profil"
- `settings" : "Paramètres"
- `stats" : "Statistiques"
- `badges" : "Badges"
- `timeline" : "Activités"
- `contact" : "Contact"
- `bio" : "Biographie"
- `no_bio" : "Aucune biographie"
- `member_since" : "Membre depuis"
- `last_active" : "Dernière activité"

---

## Composants/Wrappers nécessaires

### Aucun nouveau composant requis
Tous les templates utilisent exclusivement les composants UI existants.

**Note** : Le composant `x-daisy::ui.data-display.timeline` existe déjà et peut être utilisé pour afficher la timeline d'activités.

---

## Tests à prévoir

Pour chaque template :
1. **Test de rendu** : Vérifier le rendu avec les props par défaut
2. **Test avec utilisateur authentifié** : Vérifier que `auth()->user()` fonctionne
3. **Test de validation** : Vérifier l'affichage des erreurs
4. **Test d'upload** (profile-edit) : Vérifier l'upload d'avatar
5. **Test de tabs** (profile-settings) : Vérifier la navigation entre les sections
6. **Test de données** (profile-view) : Vérifier l'affichage des stats, badges, timeline
7. **Test responsive** : Vérifier l'affichage sur tous les écrans

---

## Ordre d'implémentation recommandé

1. `profile/profile-view.blade.php` (lecture seule, le plus simple)
2. `profile/profile-edit.blade.php` (formulaire classique)
3. `profile/profile-settings.blade.php` (le plus complexe avec tabs)

---

## Notes importantes

- **Agnosticité des modèles** : Les templates acceptent des données sous forme de tableaux, objets ou modèles Eloquent
- Utilisation de `data_get()` pour accéder aux données de manière agnostique : `data_get($profile, 'name', '')`
- Les templates peuvent fonctionner sans modèle : passer directement un tableau de données
- Les templates peuvent fonctionner avec n'importe quel modèle : User, Customer, Member, etc.
- Les templates doivent gérer le cas où le profil n'est pas fourni (affichage vide ou message)
- Respecter les conventions Laravel pour les routes RESTful (`profile.edit`, `profile.update`, `profile.show`, etc.)
- Les traductions doivent être ajoutées dans `resources/lang/fr/profile.php` et `resources/lang/en/profile.php`
- Pour l'upload d'avatar, utiliser `Storage::disk('public')->put()` et `Storage::url()` pour l'affichage
- Les stats, badges et timeline doivent être passés en props dans un format agnostique (tableaux)
- Exemple d'utilisation avec un modèle : `<x-daisy::templates.profile.profile-edit :profile="$user" />`
- Exemple d'utilisation sans modèle : `<x-daisy::templates.profile.profile-edit :profile="['name' => 'John', 'email' => 'john@example.com']" />`

