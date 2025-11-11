# Spécifications des templates à implémenter

Ce dossier contient les spécifications détaillées pour l'implémentation des nouveaux templates du package Laravel Daisy Kit.

## Organisation

Les spécifications sont organisées en 6 lots cohérents :

### Lot 1 : Authentification complémentaire
**Fichier** : `lot-1-authentification.md`

Templates d'authentification pour compléter la collection existante :
- `register-simple.blade.php`
- `register-split.blade.php`
- `forgot-password.blade.php`
- `two-factor.blade.php`

### Lot 2 : Profil utilisateur
**Fichier** : `lot-2-profil-utilisateur.md`

Templates pour la gestion du profil utilisateur :
- `profile-edit.blade.php`
- `profile-settings.blade.php`
- `profile-view.blade.php`

### Lot 3 : Formulaires avancés
**Fichier** : `lot-3-formulaires-avances.md`

Templates de formulaires avec expériences utilisateur améliorées :
- `form-wizard.blade.php`
- `form-inline.blade.php`
- `form-with-tabs.blade.php`

### Lot 4 : Communication
**Fichier** : `lot-4-communication.md`

Templates pour les fonctionnalités de communication :
- `chat.blade.php`
- `notification-center.blade.php`

### Lot 5 : Documentation et changelog
**Fichier** : `lot-5-documentation-changelog.md`

Template de documentation :
- `changelog.blade.php`

### Lot 6 : Gestion des erreurs et états
**Fichier** : `lot-6-erreurs-etats.md`

Templates pour les erreurs et états de l'application :
- `error.blade.php` (généralisé pour 404, 500, 403, etc.)
- `maintenance.blade.php`
- `empty-state.blade.php` (template + composant UI)
- `loading-state.blade.php`

## Principes généraux

Tous les templates doivent :

1. **Respecter la philosophie Laravel** :
   - Utiliser `Route::has()` et `route()` pour les URLs
   - Utiliser `auth()->user()` pour l'utilisateur connecté
   - Utiliser `old()` pour pré-remplir les champs
   - Gérer les erreurs via `$errors`
   - Utiliser les sessions Laravel quand nécessaire
   - Utiliser `@csrf` pour la protection CSRF

2. **Utiliser exclusivement les composants UI existants** :
   - Tous les composants doivent être dans `resources/views/components/ui/`
   - Aucun CSS personnalisé, uniquement Tailwind v4 + daisyUI v5
   - Réutiliser les composants existants au maximum

3. **Respecter les conventions du package** :
   - Namespace Blade : `daisy::`
   - Structure : `resources/views/templates/`
   - Traductions dans `resources/lang/fr/` et `resources/lang/en/`

4. **Être testables** :
   - Tests de rendu avec Pest
   - Tests de validation
   - Tests d'interactions (browser tests si nécessaire)

## Composants/Wrappers nécessaires

### Composants UI à créer

1. **empty-state.blade.php**
   - Fichier : `resources/views/components/ui/feedback/empty-state.blade.php`
   - Utilisé par : notification-center, changelog, et comme template standalone
   - Voir spécifications dans `lot-4-communication.md` et `lot-6-erreurs-etats.md`

### Aucun autre composant requis

Tous les autres composants nécessaires existent déjà dans le package.

## Traductions nécessaires

Les fichiers de traduction suivants doivent être créés ou complétés :

- `resources/lang/fr/auth.php` (compléter)
- `resources/lang/fr/profile.php` (créer)
- `resources/lang/fr/form.php` (créer)
- `resources/lang/fr/chat.php` (créer)
- `resources/lang/fr/notifications.php` (créer)
- `resources/lang/fr/changelog.php` (créer)
- `resources/lang/fr/errors.php` (créer)
- `resources/lang/fr/maintenance.php` (créer)
- `resources/lang/fr/common.php` (créer)

Et leurs équivalents anglais dans `resources/lang/en/`.

## Ordre d'implémentation recommandé

1. **Lot 1** : Authentification (compléter la collection existante)
2. **Lot 6** : Erreurs et états (composants de base réutilisables)
3. **Lot 2** : Profil utilisateur (fonctionnalités courantes)
4. **Lot 3** : Formulaires avancés (amélioration UX)
5. **Lot 5** : Changelog (documentation)
6. **Lot 4** : Communication (le plus complexe, nécessite WebSocket/polling)

## Tests

Chaque template doit avoir :

1. **Tests de rendu** (Feature tests) :
   - Rendu avec props par défaut
   - Rendu avec props personnalisées
   - Gestion des erreurs de validation
   - Affichage des traductions

2. **Tests d'interactions** (Browser tests si nécessaire) :
   - Navigation (wizard, tabs)
   - Soumission de formulaires
   - Actions (chat, notifications)
   - Filtres et recherche

3. **Tests de compatibilité Laravel** :
   - Routes existantes/inexistantes
   - Utilisateur authentifié/non authentifié
   - Mode debug/production

## Notes importantes

- Tous les templates doivent être **Laravel way** : utiliser les helpers, facades, et conventions Laravel
- Aucun CSS personnalisé : uniquement Tailwind v4 + daisyUI v5
- Les templates doivent être **flexibles** : accepter différents formats de données
- Les templates doivent être **accessibles** : respecter les standards d'accessibilité
- Les templates doivent être **responsives** : fonctionner sur tous les écrans

## Questions ou clarifications

Si des clarifications sont nécessaires sur une spécification, consulter :
1. Les templates existants dans `resources/views/templates/`
2. Les composants UI dans `resources/views/components/ui/`
3. Les règles du package dans `.cursor/rules/`

