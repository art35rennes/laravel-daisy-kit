# Lot 7 : Conformité daisyUI et gestion des thèmes

## Vue d'ensemble
Corriger tous les composants pour qu'ils respectent strictement les classes daisyUI v5 et s'adaptent correctement aux différents thèmes. Les bordures, ombres, arrondis et autres attributs CSS doivent utiliser les classes daisyUI en priorité plutôt que des classes Tailwind spécifiques.

## Problèmes identifiés

### 1. Bordures non conformes à daisyUI
**Problème** : Utilisation de `border border-base-300` au lieu des classes daisyUI appropriées.

**Composants affectés** :
- `resources/views/components/ui/advanced/fieldset.blade.php` (ligne 14)
- `resources/views/components/ui/advanced/transfer.blade.php` (lignes 87, 109, 180, 202)
- `resources/views/components/ui/advanced/collapse.blade.php` (ligne 22)
- `resources/views/components/ui/advanced/code-editor.blade.php` (lignes 23, 34)
- `resources/views/components/ui/advanced/chart.blade.php` (ligne 18)
- `resources/views/components/ui/advanced/accordion.blade.php` (ligne 24)
- `resources/views/components/ui/changelog/changelog-version-item.blade.php` (lignes 73, 81, 126)
- `resources/views/components/ui/changelog/changelog-change-item.blade.php` (lignes 42, 126)
- `resources/views/components/ui/changelog/changelog-toolbar.blade.php` (ligne 30)
- `resources/views/components/ui/communication/notification-bell.blade.php` (lignes 61, 80, 103)
- `resources/views/components/ui/communication/chat-input.blade.php` (ligne 35)
- `resources/views/components/ui/data-display/file-preview.blade.php` (lignes 64, 94, 120, 135)
- `resources/views/components/ui/utilities/mockup-window.blade.php` (lignes 17, 24)
- `resources/views/components/ui/overlay/popover.blade.php` (lignes 40, 43, 77)
- `resources/views/components/ui/overlay/popconfirm.blade.php` (ligne 84)
- `resources/views/components/ui/inputs/file-input.blade.php` (ligne 11 : `border-2 border-dashed border-base-300`)

**Solution** :
- Pour les cards : utiliser `card-border` (classe daisyUI) au lieu de `border border-base-300`
- Pour les fieldset : utiliser les classes daisyUI appropriées si disponibles
- Pour les autres composants : vérifier si daisyUI fournit une classe spécifique, sinon utiliser `border` seul (daisyUI gère la couleur via les variables CSS de thème)

### 2. Arrondis non conformes à daisyUI
**Problème** : Utilisation de `rounded-2xl`, `rounded-3xl`, `rounded-full` au lieu de `rounded-box` (classe daisyUI standard).

**Composants affectés** :
- `resources/views/components/ui/changelog/changelog-version-item.blade.php` (lignes 73, 74, 81)
- `resources/views/components/ui/changelog/changelog-change-item.blade.php` (ligne 42)
- `resources/views/components/ui/changelog/changelog-toolbar.blade.php` (ligne 30, 52)
- `resources/views/components/ui/communication/notification-bell.blade.php` (lignes 61, 80, 103)
- `resources/views/components/ui/communication/chat-bubble.blade.php` (ligne 19)
- `resources/views/components/ui/data-display/file-preview.blade.php` (ligne 64)

**Solution** :
- Remplacer `rounded-2xl`, `rounded-3xl` par `rounded-box` (classe daisyUI standard)
- Pour les avatars/circles : `rounded-full` peut être conservé si c'est pour un avatar (daisyUI utilise aussi `rounded-full` pour les avatars)
- Vérifier la documentation daisyUI pour les cas spécifiques

### 3. Ombres non conformes à daisyUI
**Problème** : Utilisation de `shadow-md`, `shadow-lg`, `shadow-sm`, `shadow-2xl` au lieu de la classe `shadow` de daisyUI ou des classes de composants appropriées.

**Composants affectés** :
- `resources/views/components/ui/layout/card.blade.php` (ligne 39 : utilise `shadow` - OK mais vérifier)
- `resources/views/components/ui/changelog/changelog-version-item.blade.php` (lignes 81 : `shadow-md`, `hover:shadow-lg`)
- `resources/views/components/ui/changelog/changelog-change-item.blade.php` (ligne 42 : `shadow-sm`)
- `resources/views/components/ui/changelog/changelog-toolbar.blade.php` (ligne 30 : `shadow-sm`)
- `resources/views/components/ui/communication/chat-widget.blade.php` (lignes 62, 70 : `shadow-lg`, `shadow-2xl`)
- `resources/views/components/ui/communication/notification-bell.blade.php` (ligne 38 : `shadow` - OK)
- `resources/views/components/ui/advanced/theme-controller.blade.php` (ligne 34 : `shadow-2xl`)

**Solution** :
- Utiliser uniquement `shadow` (classe daisyUI) qui s'adapte automatiquement aux thèmes
- Pour les effets hover, utiliser `hover:shadow` si nécessaire
- Éviter les variantes `shadow-*` de Tailwind qui sont fixes et ne s'adaptent pas aux thèmes

### 4. Classes de couleur de texte non conformes
**Problème** : Utilisation de `text-base-content/70`, `text-base-content/60`, etc. avec opacité au lieu des classes daisyUI appropriées.

**Composants affectés** :
- `resources/views/components/ui/changelog/changelog-version-item.blade.php` (lignes 70, 84, 85, 87, 135)
- `resources/views/components/ui/changelog/changelog-change-item.blade.php` (lignes 85, 90, 96, 99, 110, 119)
- `resources/views/components/ui/communication/chat-messages.blade.php` (ligne 129)
- `resources/views/components/ui/feedback/empty-state.blade.php` (lignes 40, 46, 52)

**Solution** :
- Vérifier si daisyUI fournit des classes pour les opacités de texte
- Si non, utiliser les classes Tailwind mais s'assurer qu'elles fonctionnent avec tous les thèmes
- Préférer les classes sémantiques daisyUI quand disponibles

### 5. Couleurs Tailwind fixes (non adaptatives aux thèmes)
**Problème** : Utilisation de couleurs Tailwind fixes (`bg-yellow-400`, etc.) qui ne s'adaptent pas aux thèmes daisyUI.

**Composants affectés** :
- `resources/views/components/ui/advanced/rating.blade.php` (ligne 18 : `bg-yellow-400`)

**Solution** :
- Remplacer `bg-yellow-400` par une couleur daisyUI adaptative (`bg-warning` ou `bg-primary` selon le contexte)
- Utiliser les couleurs sémantiques daisyUI qui s'adaptent automatiquement aux thèmes

### 6. Bordures avec épaisseur spécifique
**Problème** : Utilisation de `border-2` au lieu de `border` (daisyUI gère l'épaisseur via les variables CSS de thème).

**Composants affectés** :
- `resources/views/components/ui/changelog/changelog-version-item.blade.php` (ligne 73 : `border-2`)
- `resources/views/components/ui/inputs/file-input.blade.php` (ligne 11 : `border-2 border-dashed`)

**Solution** :
- Utiliser `border` seul pour les bordures normales (daisyUI gère l'épaisseur)
- Pour les bordures en pointillés, vérifier si daisyUI fournit une classe équivalente
- Si nécessaire, utiliser `border-dashed` mais avec `border` seul (pas `border-2`)

### 7. Card avec bordures manuelles
**Problème** : Le composant `card.blade.php` ajoute `shadow` manuellement au lieu d'utiliser uniquement les classes daisyUI.

**Fichier** : `resources/views/components/ui/layout/card.blade.php` (ligne 39)

**Solution** :
- Vérifier si `card-border` ou d'autres classes daisyUI gèrent déjà les ombres
- Si `shadow` est nécessaire, s'assurer qu'il s'adapte aux thèmes
- Utiliser `card-border` pour les bordures au lieu de classes manuelles

### 8. Bordures avec base-200 vs base-300
**Problème** : Incohérence dans l'utilisation de `border-base-200` vs `border-base-300` dans les composants overlay.

**Composants affectés** :
- `resources/views/components/ui/overlay/popover.blade.php` (lignes 40, 43, 77 : `border-base-200`)
- `resources/views/components/ui/overlay/popconfirm.blade.php` (ligne 84 : `border-base-200`)

**Solution** :
- Vérifier la documentation daisyUI pour déterminer quelle couleur de bordure est recommandée pour les overlays
- Standardiser l'utilisation de `border-base-200` ou `border-base-300` selon les recommandations daisyUI
- Préférer les classes de composants daisyUI si disponibles (comme `dropdown-content` qui gère déjà les bordures)

### 9. Bordures directionnelles non conformes
**Problème** : Utilisation de bordures directionnelles (`border-t`, `border-b`, `border-l`, `border-r`) avec des couleurs spécifiques au lieu de classes daisyUI.

**Composants affectés** :
- `resources/views/components/ui/communication/chat-header.blade.php` (ligne 23 : `border-b border-base-300`)
- `resources/views/components/ui/communication/chat-input.blade.php` (ligne 23 : `border-t border-base-300`)
- `resources/views/components/ui/communication/chat-sidebar.blade.php` (lignes 20, 22, 90 : `border-r`, `border-b`, `border-t border-base-300`)
- `resources/views/components/ui/communication/notification-item.blade.php` (ligne 93 : `border-b border-base-200`)
- `resources/views/components/ui/layout/crud-section.blade.php` (ligne 78 : `border-t border-base-200`)
- `resources/views/components/ui/navigation/sidebar.blade.php` (lignes 123, 133, 192 : `border-b`, `border-t border-base-content/10`)
- `resources/views/components/ui/navigation/sidebar-navigation.blade.php` (ligne 61 : `border-b border-base-content/10`)
- `resources/views/components/ui/overlay/drawer.blade.php` (lignes 33, 37 : `border-r border-base-content/10`)
- `resources/views/components/ui/overlay/popover.blade.php` (lignes 46, 48, 50, 52, 77 : bordures directionnelles pour la flèche et séparateur)
- `resources/views/components/ui/advanced/code-editor.blade.php` (ligne 34 : `border-b border-base-300`)
- `resources/views/components/ui/partials/tree-node.blade.php` (ligne 55 : `border-l border-base-300`)
- `resources/views/components/layout/docs.blade.php` (ligne 15 : `border-b border-base-200`)
- `resources/views/components/ui/navigation/tabs.blade.php` (ligne 13 : `border-base-300` dans contentClass)

**Solution** :
- Vérifier si daisyUI fournit des classes pour les bordures directionnelles
- Pour les séparateurs, utiliser `divider` (composant daisyUI) si approprié
- Standardiser l'utilisation de `border-base-200` ou `border-base-300` selon les recommandations daisyUI
- Pour `border-base-content/10`, vérifier si c'est conforme ou si une alternative daisyUI existe

### 10. Navbar avec shadow non conforme
**Problème** : Le composant `navbar.blade.php` génère des classes `shadow-sm`, `shadow-md`, `shadow-lg` au lieu de `shadow` (classe daisyUI).

**Fichier** : `resources/views/components/ui/navigation/navbar.blade.php` (ligne 17)

**Solution** :
- Remplacer la logique de génération de shadow par `shadow` uniquement (classe daisyUI)
- Supprimer les variantes `shadow-sm`, `shadow-md`, `shadow-lg`
- Vérifier les usages dans `docs.blade.php` (ligne 15 : `shadow="sm"`) et les corriger

### 11. Composants avec rounded-md/lg/xl comme props
**Problème** : Les composants `skeleton.blade.php` et `avatar.blade.php` acceptent `rounded-md`, `rounded-lg`, `rounded-xl` comme valeurs de props.

**Composants affectés** :
- `resources/views/components/ui/feedback/skeleton.blade.php` (lignes 15-17)
- `resources/views/components/ui/data-display/avatar.blade.php` (lignes 24-26)

**Solution** :
- Conserver la flexibilité des props (les utilisateurs peuvent vouloir des arrondis spécifiques)
- Mais suggérer `rounded-box` comme valeur par défaut ou option recommandée
- Documenter que `rounded-box` est la classe daisyUI standard pour les conteneurs

### 12. JavaScript générant des classes non conformes
**Problème** : Les fichiers JavaScript génèrent dynamiquement des classes CSS qui ne sont pas conformes à daisyUI.

**Fichiers affectés** :
- `resources/js/modules/chat.js` (ligne 283 : `border border-base-300`, `shadow-sm`, ligne 293 : `rounded` au lieu de `rounded-box`)
- `resources/js/file-input.js` (ligne 32 : `border border-base-300`)
- `resources/js/onboarding.js` (ligne 51 : `border border-base-200`, `shadow`)
- `resources/js/treeview.js` (ligne 748 : `border-l border-base-300`, ligne 284 : `rounded` au lieu de `rounded-box`)

**Solution** :
- Remplacer toutes les classes générées dynamiquement par des classes daisyUI appropriées
- Utiliser `rounded-box` au lieu de `rounded` pour les conteneurs
- Utiliser `shadow` au lieu de `shadow-sm` pour les ombres
- Vérifier si `border border-base-300` peut être remplacé par des classes daisyUI spécifiques

### 13. Commandes Artisan générant du HTML non conforme
**Problème** : Les commandes Artisan génèrent du HTML avec des classes non conformes à daisyUI.

**Fichiers affectés** :
- `app/Console/Commands/GenerateDocsPages.php` (lignes 137, 143, 387, 393 : `border-base-300`, ligne 225 : `rounded` au lieu de `rounded-box`)

**Solution** :
- Remplacer `border-base-300` par des classes daisyUI appropriées
- Remplacer `rounded` par `rounded-box` pour les conteneurs
- Vérifier tous les autres templates générés par les commandes Artisan

## Liste des corrections à réaliser

### Priorité 1 : Corrections critiques (affectent tous les thèmes)

1. **Corriger les bordures dans les composants de communication**
   - `notification-bell.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées
   - `chat-input.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées
   - `chat-widget.blade.php` : Vérifier et corriger les classes d'ombres

2. **Corriger les bordures dans les composants de changelog**
   - `changelog-version-item.blade.php` : Remplacer `border border-base-300` et `rounded-3xl` par `rounded-box`
   - `changelog-change-item.blade.php` : Remplacer `border border-base-300` et `rounded-2xl` par `rounded-box`
   - `changelog-toolbar.blade.php` : Remplacer `border border-base-300` et `rounded-3xl` par `rounded-box`

3. **Corriger le composant transfer**
   - `transfer.blade.php` : Remplacer toutes les occurrences de `border border-base-300` par des classes daisyUI appropriées (utiliser `card-border` si dans une card)

4. **Corriger le composant fieldset**
   - `fieldset.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées

5. **Corriger les composants overlay**
   - `popover.blade.php` : Remplacer `border border-base-200` par des classes daisyUI appropriées
   - `popconfirm.blade.php` : Remplacer `border border-base-200` par des classes daisyUI appropriées

6. **Corriger les composants advanced**
   - `collapse.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées
   - `code-editor.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées
   - `chart.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées
   - `accordion.blade.php` : Remplacer `border border-base-300` par des classes daisyUI appropriées
   - `rating.blade.php` : Remplacer `bg-yellow-400` par une couleur daisyUI adaptative (`bg-warning` ou `bg-primary`)
   - `theme-controller.blade.php` : Remplacer `shadow-2xl` par `shadow`

7. **Corriger le composant file-input**
   - `file-input.blade.php` : Remplacer `border-2 border-dashed border-base-300` par des classes daisyUI appropriées

8. **Corriger le composant file-preview**
   - `file-preview.blade.php` : Remplacer toutes les occurrences de `border border-base-300` par des classes daisyUI appropriées

9. **Corriger le composant navbar**
   - `navbar.blade.php` : Remplacer la génération de `shadow-sm/md/lg` par `shadow` uniquement
   - `docs.blade.php` : Corriger l'utilisation de `shadow="sm"` dans navbar

10. **Corriger les bordures directionnelles**
    - `chat-header.blade.php` : Remplacer `border-b border-base-300` par des classes daisyUI appropriées
    - `chat-input.blade.php` : Remplacer `border-t border-base-300` par des classes daisyUI appropriées
    - `chat-sidebar.blade.php` : Remplacer toutes les bordures directionnelles par des classes daisyUI appropriées
    - `notification-item.blade.php` : Remplacer `border-b border-base-200` par des classes daisyUI appropriées
    - `crud-section.blade.php` : Remplacer `border-t border-base-200` par des classes daisyUI appropriées
    - `sidebar.blade.php` : Remplacer `border-base-content/10` par des classes daisyUI appropriées
    - `sidebar-navigation.blade.php` : Remplacer `border-base-content/10` par des classes daisyUI appropriées
    - `drawer.blade.php` : Remplacer `border-base-content/10` par des classes daisyUI appropriées
    - `code-editor.blade.php` : Remplacer `border-b border-base-300` par des classes daisyUI appropriées
    - `tree-node.blade.php` : Remplacer `border-l border-base-300` par des classes daisyUI appropriées
    - `docs.blade.php` : Remplacer `border-b border-base-200` par des classes daisyUI appropriées

11. **Corriger le composant card**
    - `card.blade.php` : Vérifier l'utilisation de `shadow` et s'assurer qu'elle est conforme à daisyUI

### Priorité 2 : Corrections importantes (améliorent la cohérence)

10. **Corriger les ombres dans tous les composants**
    - Remplacer `shadow-md`, `shadow-lg`, `shadow-sm`, `shadow-2xl` par `shadow` (classe daisyUI)
    - Vérifier les effets hover et les adapter si nécessaire

11. **Corriger les arrondis dans tous les composants**
    - Remplacer `rounded-2xl`, `rounded-3xl` par `rounded-box` (classe daisyUI standard)
    - Conserver `rounded-full` uniquement pour les avatars/circles si approprié
    - Remplacer `rounded-xl` par `rounded-box` si approprié

12. **Corriger les bordures avec épaisseur spécifique**
    - Remplacer `border-2` par `border` (daisyUI gère l'épaisseur via les variables CSS)
    - Vérifier les cas où `border-2` est nécessaire (comme les indicateurs visuels)

13. **Corriger les classes de couleur de texte**
    - Vérifier l'utilisation de `text-base-content/*` et s'assurer qu'elle fonctionne avec tous les thèmes
    - Préférer les classes sémantiques daisyUI quand disponibles

14. **Standardiser les couleurs de bordure**
    - Déterminer si `border-base-200` ou `border-base-300` est recommandé par daisyUI
    - Standardiser l'utilisation dans tous les composants
    - Vérifier l'utilisation de `border-base-content/10` et déterminer si c'est conforme ou à remplacer

15. **Documenter les props rounded dans skeleton et avatar**
    - Ajouter `rounded-box` comme valeur recommandée dans la documentation
    - Conserver la flexibilité mais guider vers les classes daisyUI

16. **Corriger les fichiers JavaScript**
    - `chat.js` : Remplacer `border border-base-300`, `shadow-sm`, `rounded` par des classes daisyUI appropriées
    - `file-input.js` : Remplacer `border border-base-300` par des classes daisyUI appropriées
    - `onboarding.js` : Remplacer `border border-base-200`, `shadow` par des classes daisyUI appropriées
    - `treeview.js` : Remplacer `border-l border-base-300`, `rounded` par des classes daisyUI appropriées

17. **Corriger les commandes Artisan**
    - `GenerateDocsPages.php` : Remplacer `border-base-300`, `rounded` par des classes daisyUI appropriées
    - Vérifier toutes les autres commandes qui génèrent du HTML

### Priorité 3 : Corrections mineures (polish)

12. **Corriger les templates**
    - `notification-center.blade.php` : Remplacer `shadow-lg`, `shadow-sm`, `border border-base-300/60`, `border border-error/30` par des classes daisyUI appropriées
    - `changelog.blade.php` : Remplacer `rounded-4xl`, `rounded-3xl`, `shadow-sm`, `border border-base-200` par des classes daisyUI appropriées
    - `two-factor.blade.php` : Remplacer `shadow-sm` dans alert par `shadow` ou supprimer
    - `forgot-password.blade.php` : Remplacer `shadow-sm` dans alert par `shadow` ou supprimer
    - `reset-password.blade.php` : Remplacer `shadow-sm` dans alert par `shadow` ou supprimer
    - `resend-verification.blade.php` : Remplacer `shadow-sm` dans alert par `shadow` ou supprimer

13. **Corriger les vues de démo/docs**
    - `docs/templates/changelog.blade.php` : Remplacer `shadow-sm` dans les cards par `shadow` ou supprimer
    - `docs/templates/index.blade.php` : Remplacer `shadow-sm` dans card par `shadow` ou supprimer
    - `demo/ui/index.blade.php` : Remplacer `rounded-lg`, `shadow-lg`, `border border-base-300` par des classes daisyUI appropriées
    - `demo/ui/partials/test-layouts.blade.php` : Remplacer `shadow-sm` dans les cards par `shadow` ou supprimer
    - `demo/ui/partials/test-*.blade.php` : Vérifier tous les autres fichiers de test pour des problèmes similaires

14. **Vérifier tous les autres composants**
    - Parcourir tous les composants pour identifier d'autres utilisations non conformes
    - Vérifier les composants de layout, navigation, data-display, etc.

15. **Tests de conformité**
    - Créer des tests pour vérifier que tous les composants s'adaptent correctement aux différents thèmes
    - Tester avec plusieurs thèmes daisyUI (light, dark, synthwave, etc.)

## Statistiques finales

- **Total de composants affectés** : 35+ composants UI + 5+ templates + 5+ vues de démo/docs
- **Fichiers JavaScript affectés** : 4+ fichiers générant des classes dynamiquement
- **Commandes Artisan affectées** : 1+ commande générant du HTML
- **Problèmes de bordures** : 55+ occurrences (border, border-t, border-b, border-l, border-r)
- **Problèmes d'ombres** : 25+ occurrences (shadow-sm, shadow-md, shadow-lg, shadow-2xl)
- **Problèmes d'arrondis** : 20+ occurrences (rounded, rounded-2xl, rounded-3xl, rounded-4xl, rounded-xl)
- **Couleurs fixes** : 1 occurrence critique (`rating.blade.php` : `bg-yellow-400`)
- **Bordures directionnelles** : 20+ occurrences nécessitant vérification
- **Navbar shadow** : 1 composant générant des classes non conformes
- **JavaScript dynamique** : Classes générées en runtime qui ne sont pas conformes

## Règles à respecter

### Règle 1 : Priorité aux classes daisyUI
**TOUJOURS** utiliser les classes daisyUI en priorité avant les classes Tailwind spécifiques :
- ✅ `card-border` au lieu de `border border-base-300` pour les cards
- ✅ `rounded-box` au lieu de `rounded-2xl`, `rounded-3xl`
- ✅ `shadow` au lieu de `shadow-md`, `shadow-lg`, etc.
- ✅ Classes de composants daisyUI (`card`, `alert`, `btn`, etc.) qui gèrent automatiquement les styles

### Règle 2 : Adaptation aux thèmes
Toutes les classes utilisées doivent s'adapter automatiquement aux différents thèmes daisyUI :
- Les couleurs daisyUI (`base-100`, `base-200`, `base-300`, `primary`, etc.) s'adaptent automatiquement
- Les classes Tailwind fixes (`red-500`, `gray-800`, etc.) ne s'adaptent pas et doivent être évitées

### Règle 3 : Vérification de la documentation
Avant d'ajouter une classe CSS :
1. Vérifier si daisyUI fournit une classe équivalente
2. Consulter la documentation daisyUI pour le composant concerné
3. Utiliser la classe daisyUI si elle existe
4. Sinon, utiliser les classes Tailwind mais s'assurer qu'elles fonctionnent avec tous les thèmes

### Règle 4 : Cohérence visuelle
- Tous les composants similaires doivent utiliser les mêmes classes
- Les bordures doivent être cohérentes (utiliser `card-border` pour toutes les cards)
- Les arrondis doivent être cohérents (utiliser `rounded-box` pour les conteneurs)

## Tests à réaliser

### Tests visuels
1. Tester chaque composant corrigé avec plusieurs thèmes daisyUI :
   - `light` (thème par défaut)
   - `dark` (thème sombre)
   - `synthwave` (thème coloré)
   - `corporate` (thème professionnel)
   - `dracula` (thème sombre alternatif)

2. Vérifier que :
   - Les bordures sont visibles et cohérentes sur tous les thèmes
   - Les ombres sont appropriées sur tous les thèmes
   - Les arrondis sont cohérents
   - Les couleurs de texte sont lisibles sur tous les thèmes

### Tests automatisés
1. Créer des tests Pest pour vérifier la présence des classes daisyUI appropriées
2. Créer des tests de rendu pour vérifier que les classes sont correctement appliquées
3. Créer des tests browser pour vérifier l'apparence visuelle sur différents thèmes

## Documentation à mettre à jour

1. **Mettre à jour les règles daisyUI** (`.cursor/rules/daisyui.mdc`)
   - Ajouter la contrainte de priorité aux classes daisyUI
   - Documenter les bonnes pratiques pour les bordures, ombres, arrondis

2. **Mettre à jour la documentation des composants**
   - Documenter les classes daisyUI utilisées par chaque composant
   - Ajouter des exemples avec différents thèmes

## Ordre d'implémentation recommandé

1. **Phase 1** : Corrections critiques (Priorité 1)
   - Commencer par les composants les plus utilisés (card, fieldset, transfer)
   - Tester après chaque correction

2. **Phase 2** : Corrections importantes (Priorité 2)
   - Corriger les ombres et arrondis dans tous les composants
   - Tester avec plusieurs thèmes

3. **Phase 3** : Polish et tests (Priorité 3)
   - Vérifier tous les autres composants
   - Créer les tests automatisés
   - Mettre à jour la documentation

## Notes importantes

- **Ne pas casser la compatibilité** : Les corrections doivent maintenir la compatibilité avec le code existant
- **Tests réguliers** : Tester après chaque correction pour éviter les régressions
- **Documentation** : Documenter les changements importants
- **Communication** : Informer l'équipe des changements de classes si nécessaire

