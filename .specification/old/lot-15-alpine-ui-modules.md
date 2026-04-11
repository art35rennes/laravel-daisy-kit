# Lot 15 : Migration des modules “UI pure” vers Alpine

Ce lot cible les modules dont la logique est principalement UI (sans grosse lib tierce) et qui profitent fortement d’Alpine.

## Objectifs
- Migrer vers Alpine:
  - Sidebar search (`resources/js/modules/sidebar.js`)
  - OTP code (`resources/js/modules/otp-code.js`)
  - Select (search/autocomplete) (`resources/js/modules/select.js`)
  - Copyable (`resources/js/modules/copyable.js`)
  - CSRF keeper (`resources/js/modules/csrf-keeper.js`)
- Réduire l’usage de `data-module` dans les composants concernés.

## Périmètre
- Sidebar:
  - `resources/views/components/ui/navigation/sidebar.blade.php`
  - `resources/views/components/ui/navigation/sidebar-navigation.blade.php`
  - `resources/js/modules/sidebar.js`
- Select (search/autocomplete):
  - `resources/views/components/ui/inputs/select.blade.php`
  - `resources/js/modules/select.js`
  - `tests/Browser/SelectTest.php`
- Two-factor / OTP:
  - `resources/views/templates/auth/two-factor.blade.php`
  - `resources/views/components/templates/auth/two-factor.blade.php`
  - `resources/js/modules/otp-code.js`
- Copyable:
  - `resources/views/components/ui/utilities/copyable.blade.php`
  - `resources/js/modules/copyable.js`
  - `tests/Browser/CopyableTest.php`
- CSRF keeper:
  - `resources/views/components/ui/utilities/csrf-keeper.blade.php`
  - `resources/js/modules/csrf-keeper.js`

## Comportement attendu (UX)
### Sidebar search
- Filtre temps réel sur input search.
- Conserve l’ouverture des `<details>` qui matchent un descendant.
- Pas de régression sur navigation (items actifs, sections, labels).

### Select (search/autocomplete)
- Mode `search` (local): filtre local des `<option>` existantes.
- Mode `autocomplete` (remote): fetch sur `endpoint` avec `param`, `minChars`, `debounce`, `fetchOnEmpty`.
- Accessibilité:
  - `role="listbox"` + options focusables
  - navigation clavier (ArrowUp/ArrowDown/Enter/Escape)
  - fermeture au click outside

### OTP code
- Focus management (auto-advance, backspace).
- Numeric-only si configuré.
- Synchronisation vers un input hidden (champ `code`) pour submit.

### Copyable
- Copie via `navigator.clipboard.writeText` si dispo.
- Fallback: sélection + `document.execCommand('copy')` si nécessaire.
- Feedback UI “copié” (state Alpine, durée courte).

### CSRF keeper
- Poll/refresh du token uniquement si le composant est présent.
- Ne pas spammer le réseau; throttle/debounce.

## Architecture
### Alpine
- Sidebar: `x-data` sur `<aside>` (ou wrapper) avec `query`, `filter()` et `normalizeText()` (helper Lot 12).
- Select: `x-data` sur le wrapper `.dropdown`:
  - état `open`, `query`, `activeIndex`, `options[]`
  - stratégie: local filter vs remote fetch
  - synchronisation vers le `<select data-role="native">`
- OTP: `x-data` sur wrapper de l’OTP; gérer inputs et hidden.
- Copyable: `x-data` sur wrapper, `copied` state.
- CSRF keeper: `x-data` + `x-init` pour scheduling (setInterval) avec guard.

### Modules JS
- Garder les modules en fallback pendant migration si utile, puis suppression Lot 20.

## Plan de migration
1. Implémenter Alpine dans chaque composant/template, en gardant les `data-*` hooks utiles aux tests.
2. Désactiver `data-module` ou rendre le module no-op si Alpine actif.
3. Mettre à jour les pages de démo/docs qui décrivent les modules (si nécessaire).

## Tests
### Feature tests (rendu)
- Ajouter/adapter tests de rendu sur sidebar et otp (présence hooks Alpine + markup stable).

### Browser tests
- `tests/Browser/CopyableTest.php` doit rester vert.
- `tests/Browser/SelectTest.php` doit rester vert.
- Ajouter un Browser test OTP (si pas existant) pour valider focus + saisie.

## Critères d’acceptation
- [ ] Les 5 fonctionnalités marchent sans module JS obligatoire.
- [ ] Tests Browser/Feature concernés verts.
- [ ] Aucun warning console récurrent.


