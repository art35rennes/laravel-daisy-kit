# Lot 13 : Remplacer les comportements globaux par Alpine (quick wins)

Ce lot migre 3 comportements aujourd’hui implémentés dans `resources/js/app.js` vers Alpine directement dans les composants Blade.

## Objectifs
- Éliminer les listeners globaux pour:
  - checkbox indéterminées (`data-indeterminate="true"`)
  - radios “décochables” (`data-uncheckable="1"`)
  - sidebar collapse/expand + persistance localStorage (`[data-sidebar-root] .sidebar-toggle`)
- Recentrer la logique au plus près du markup (composants Blade) via Alpine.

## Non-objectifs
- Refaire la recherche sidebar (Lot 15).
- Refaire les modules lourds (Lot 18).
- Changer le style/markup daisyUI (sauf attributs Alpine).

## Périmètre
- Checkbox indeterminate:
  - `resources/views/components/ui/inputs/checkbox.blade.php`
- Radio uncheckable:
  - `resources/views/components/ui/inputs/radio.blade.php`
- Sidebar collapse:
  - `resources/views/components/ui/navigation/sidebar.blade.php`
- JS global actuel:
  - `resources/js/app.js` (puis `resources/js/core.js` après Lot 19) (bloc indeterminate + sidebar toggle + uncheckable radios)

## Comportement attendu (UX)
### Checkbox indeterminate
- Si `indeterminate=true`, la checkbox démarre en état “mixed”.
- Au premier changement utilisateur, l’état indeterminate disparaît et `aria-checked` passe à `true/false`.

### Radio uncheckable
- Si `uncheckable=true`, un radio déjà coché peut être décoché:
  - au clic (sur input/label)
  - au clavier (Space) quand l’input est focused
- Émettre un `change` bubble quand l’état est modifié.

### Sidebar collapse
- Le bouton `.sidebar-toggle` replie/déplie la sidebar.
- Persistance via `localStorage` (clé `storageKey` existante).
- Mise à jour:
  - dataset `data-collapsed`
  - classes de largeur (`w-20`, `w-64`, `w-fit min/max`, etc.)
  - labels `.sidebar-label` et texte `.sidebar-label-toggle`

## API Blade
- Ne pas changer les props publiques existantes; uniquement ajouter des attributs Alpine.
- `checkbox`: prop `indeterminate` existe déjà.
- `radio`: prop `uncheckable` existe déjà.
- `sidebar`: props `collapsed`, `storageKey`, `variant`, etc. existent déjà.

## Architecture (Alpine)
### Checkbox
- `x-data` minimal (ou `x-init`) pour appliquer `el.indeterminate = true` et gérer la transition vers checked.

### Radio
- Gestion `mousedown`/`click`/`keydown` portée dans Alpine sur l’input (ou wrapper label) pour détecter “wasChecked” sans globals.

### Sidebar
- `x-data` sur le `<aside>` racine:
  - état `collapsed` initialisé depuis dataset + localStorage
  - méthode `setCollapsed(bool)` responsable des classes et labels
  - méthode `toggle()` sur bouton

## Plan de migration
1. Introduire Alpine sur les 3 composants ciblés en conservant les `data-*` actuels.
2. Désactiver/supprimer progressivement dans `resources/js/app.js`:
   - le bloc `data-indeterminate`
   - le bloc radios `data-uncheckable`
   - le bloc sidebar `[data-sidebar-root] .sidebar-toggle`
3. Vérifier la compat sur pages démo.

## Plan de tests
### Feature tests
- Ajouter des assertions stables de présence des hooks Alpine (ex: `x-data`/`x-init`) plutôt que `data-module`.

### Browser tests
- Ajouter un Browser test “sidebar collapse persists” (il n’existe pas actuellement).

## Critères d’acceptation
- [ ] Checkbox indeterminate fonctionne sans le code global.
- [ ] Radio uncheckable fonctionne au clic et au clavier.
- [ ] Sidebar collapse/persist fonctionne sans listeners globaux.
- [ ] Tests Browser/Feature concernés verts.

## Risques & mitigations
- Risque: le composant `checkbox` inclut aujourd’hui `assets` conditionnellement (`@include(...)` en cas d’indeterminate).
  - Mitigation: après Lot 19, l’injection d’assets doit être “core” et ne pas dépendre d’un comportement demo.
