# Lot 1 : Templates d'authentification complémentaires

## Vue d'ensemble
Compléter la collection de templates d'authentification existants (login-simple, login-split, reset-password, verify-email, resend-verification) avec les pages d'inscription et d'authentification à deux facteurs.

## Templates à créer

### 1. register-simple.blade.php
**Fichier** : `resources/views/templates/auth/register-simple.blade.php`

**Description** : Page d'inscription simple, complémentaire de `login-simple.blade.php`.

**Props** :
```php
@props([
    'title' => __('auth.register'),
    'theme' => null,
    // Form
    'action' => Route::has('register') ? route('register') : '#',
    'method' => 'POST',
    'loginUrl' => Route::has('login') ? route('login') : '#',
    // Validation
    'passwordConfirmation' => true,
    'termsUrl' => null,
    'privacyUrl' => null,
    'acceptTerms' => true,
])
```

**Fonctionnalités Laravel** :
- Utilise `Route::has()` pour détecter les routes disponibles
- Utilise `route()` pour générer les URLs
- Gère les erreurs de validation via `$errors`
- Utilise `old()` pour pré-remplir les champs
- Supporte les traductions via `__()`

**Composants UI utilisés** :
- `x-daisy::layout.app` (layout de base)
- `x-daisy::ui.partials.theme-selector` (sélecteur de thème)
- `x-daisy::ui.partials.form-field` (champ de formulaire)
- `x-daisy::ui.inputs.input` (champs texte, email, password)
- `x-daisy::ui.inputs.checkbox` (case à cocher pour les conditions)
- `x-daisy::ui.inputs.button` (bouton de soumission)
- `x-daisy::ui.advanced.validator` (messages d'erreur)

**Champs du formulaire** :
- Nom (name) - optionnel
- Prénom (first_name) - optionnel
- Email (email) - requis
- Mot de passe (password) - requis
- Confirmation mot de passe (password_confirmation) - si `passwordConfirmation` est true
- Case à cocher "J'accepte les conditions" (terms) - si `acceptTerms` est true

**Slots disponibles** :
- `logo` : Logo/branding en haut de la page
- `socialLogin` : Boutons de connexion sociale (Google, Facebook, etc.)

**Traductions nécessaires** (à ajouter dans `resources/lang/fr/auth.php`) :
- `register` : "Créer un compte"
- `already_have_account` : "Vous avez déjà un compte ?"
- `sign_in` : "Se connecter"
- `accept_terms` : "J'accepte les conditions d'utilisation"
- `accept_privacy` : "J'accepte la politique de confidentialité"
- `terms_link` : "conditions d'utilisation"
- `privacy_link` : "politique de confidentialité"

---

### 2. register-split.blade.php
**Fichier** : `resources/views/templates/auth/register-split.blade.php`

**Description** : Page d'inscription avec image latérale, complémentaire de `login-split.blade.php`.

**Props** :
```php
@props([
    'title' => __('auth.register'),
    'theme' => null,
    // Form (identique à register-simple)
    'action' => Route::has('register') ? route('register') : '#',
    'method' => 'POST',
    'loginUrl' => Route::has('login') ? route('login') : '#',
    'passwordConfirmation' => true,
    'termsUrl' => null,
    'privacyUrl' => null,
    'acceptTerms' => true,
    // UI
    'backgroundImage' => null,
    'showTestimonial' => false,
    'testimonial' => null, // ['quote' => '', 'author' => '', 'role' => '', 'avatar' => '', 'rating' => 5]
])
```

**Fonctionnalités Laravel** : Identiques à `register-simple.blade.php`

**Composants UI utilisés** :
- Tous ceux de `register-simple.blade.php`
- `x-daisy::ui.layout.hero` (pour l'image de fond et le témoignage)
- `x-daisy::ui.advanced.rating` (pour l'évaluation du témoignage)

**Structure** :
- Layout en 2 colonnes (grid lg:grid-cols-2)
- Colonne gauche : formulaire (identique à register-simple)
- Colonne droite : hero avec image de fond et témoignage optionnel

---

### 3. forgot-password.blade.php
**Fichier** : `resources/views/templates/auth/forgot-password.blade.php`

**Description** : Page de demande de réinitialisation de mot de passe.

**Props** :
```php
@props([
    'title' => __('auth.forgot_password'),
    'theme' => null,
    // Form
    'action' => Route::has('password.email') ? route('password.email') : '#',
    'method' => 'POST',
    'loginUrl' => Route::has('login') ? route('login') : '#',
    // Messages
    'status' => session('status'), // Laravel password reset status
])
```

**Fonctionnalités Laravel** :
- Utilise `session('status')` pour afficher le message de confirmation Laravel
- Utilise `Route::has('password.email')` pour la route de réinitialisation
- Gère les erreurs de validation

**Composants UI utilisés** :
- `x-daisy::layout.app`
- `x-daisy::ui.partials.theme-selector`
- `x-daisy::ui.partials.form-field`
- `x-daisy::ui.inputs.input`
- `x-daisy::ui.inputs.button`
- `x-daisy::ui.feedback.alert` (pour afficher le status)

**Structure** :
- Formulaire simple avec un seul champ email
- Message de confirmation si `status` est présent
- Lien de retour vers la page de connexion

**Traductions nécessaires** :
- `forgot_password` : "Mot de passe oublié"
- `forgot_password_description` : "Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe."
- `send_reset_link" : "Envoyer le lien de réinitialisation"
- `remember_password" : "Vous vous souvenez de votre mot de passe ?"

---

### 4. two-factor.blade.php
**Fichier** : `resources/views/templates/auth/two-factor.blade.php`

**Description** : Page d'authentification à deux facteurs (2FA).

**Props** :
```php
@props([
    'title' => __('auth.two_factor'),
    'theme' => null,
    // Form
    'action' => Route::has('two-factor.login') ? route('two-factor.login') : '#',
    'method' => 'POST',
    'recoveryUrl' => Route::has('two-factor.recovery') ? route('two-factor.recovery') : '#',
    'logoutUrl' => Route::has('logout') ? route('logout') : '#',
    // Options
    'showRecovery' => true,
    'showLogout' => true,
])
```

**Fonctionnalités Laravel** :
- Compatible avec Laravel Fortify ou Laravel Breeze pour la 2FA
- Utilise les routes standards de 2FA
- Gère les erreurs de validation

**Composants UI utilisés** :
- `x-daisy::layout.app`
- `x-daisy::ui.partials.theme-selector`
- `x-daisy::ui.partials.form-field`
- `x-daisy::ui.inputs.input` (type="text" avec pattern pour code 6 chiffres)
- `x-daisy::ui.inputs.button`
- `x-daisy::ui.feedback.alert` (instructions)

**Structure** :
- Champ de saisie pour le code à 6 chiffres
- Instructions claires pour l'utilisateur
- Lien optionnel vers la récupération de code
- Bouton optionnel de déconnexion

**Traductions nécessaires** :
- `two_factor` : "Authentification à deux facteurs"
- `two_factor_description" : "Entrez le code à 6 chiffres généré par votre application d'authentification."
- `two_factor_code" : "Code d'authentification"
- `two_factor_recovery" : "Utiliser un code de récupération"
- `two_factor_logout" : "Se déconnecter"

**Composants/Wrappers nécessaires** :
- Aucun nouveau composant requis, utilisation des composants existants

---

## Composants/Wrappers nécessaires

### Aucun nouveau composant requis
Tous les templates utilisent exclusivement les composants UI existants dans `resources/views/components/ui/`.

---

## Tests à prévoir

Pour chaque template :
1. **Test de rendu** : Vérifier que le template se rend sans erreur avec les props par défaut
2. **Test avec routes Laravel** : Vérifier que les URLs sont générées correctement quand les routes existent
3. **Test sans routes Laravel** : Vérifier le fallback vers '#' quand les routes n'existent pas
4. **Test de validation** : Vérifier l'affichage des erreurs de validation
5. **Test de traduction** : Vérifier que toutes les traductions sont présentes
6. **Test responsive** : Vérifier l'affichage sur mobile/tablette/desktop (pour register-split)

---

## Ordre d'implémentation recommandé

1. `forgot-password.blade.php` (le plus simple)
2. `register-simple.blade.php` (base pour register-split)
3. `register-split.blade.php` (variante de register-simple)
4. `two-factor.blade.php` (le plus spécifique)

---

## Notes importantes

- Tous les templates doivent respecter la philosophie Laravel : utilisation de `Route::has()`, `route()`, `old()`, `$errors`, `session()`
- Les traductions doivent être ajoutées dans `resources/lang/fr/auth.php` et `resources/lang/en/auth.php`
- Les templates doivent être testés avec et sans les routes Laravel correspondantes
- Respecter la structure et le style des templates existants (`login-simple.blade.php`, `login-split.blade.php`)

