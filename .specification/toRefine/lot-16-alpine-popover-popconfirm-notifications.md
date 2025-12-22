# Lot 16 : Popover / Popconfirm / Notifications vers Alpine (accessibilité)

Ce lot migre des interactions sensibles à l’accessibilité et déjà couvertes par des Browser tests.

## Objectifs
- Migrer popover et popconfirm vers Alpine en conservant:
  - ouverture/fermeture fiable
  - keyboard (Escape)
  - focus management (focus trap si modal, retour focus trigger)
  - `aria-*` cohérent
- Migrer le centre de notifications vers Alpine (state + fetching) si applicable.

## Périmètre
- Popover:
  - `resources/views/components/ui/overlay/popover.blade.php`
  - `resources/js/modules/popover.js` (si utilisé par `data-module`)
  - `resources/js/popover.js` (si utilisé par l’entrée `app.js`)
  - `tests/Browser/PopoverTest.php`
- Popconfirm:
  - `resources/views/components/ui/overlay/popconfirm.blade.php`
  - `resources/js/popconfirm.js`
  - `tests/Browser/PopconfirmTest.php`
- Notifications:
  - `resources/views/templates/communication/notification-center.blade.php`
  - `resources/js/modules/notifications.js`
  - tests existants dans `tests/Browser/CommunicationComponentsTest.php` (notifications)

## Comportement attendu (UX + A11y)
- Popover:
  - toggle au clic
  - fermeture sur click outside
  - Escape ferme
- Popconfirm:
  - open panel inline OU open modal (selon variante)
  - actions confirm/cancel
  - focus: ouverture = focus panel, fermeture = focus trigger
- Notifications:
  - chargement sans erreurs console
  - état empty/loading/error clair

## Architecture
### Alpine
- Composants “headless”:
  - `x-data="{ open: false, ... }"`
  - `@click`, `@keydown.escape.window`
  - `x-trap` seulement si plugin ajouté (à éviter si non nécessaire; sinon implémenter trap simple)
- Préserver des `data-*` hooks stables utilisés par Browser tests (ex: `.popover-trigger`, `.popover-panel`, `[data-popconfirm]`, etc.)

### Modules JS
- Conserver les modules en fallback pendant migration; suppression Lot 20.

## Plan de migration
1. Popover: migrer trigger/panel en Alpine, conserver sélecteurs tests.
2. Popconfirm: migrer inline + modal, conserver sélecteurs tests existants.
3. Notifications: migrer state + fetch, conserver endpoints/datasets.

## Tests
- `tests/Browser/PopoverTest.php` reste vert.
- `tests/Browser/PopconfirmTest.php` reste vert (inline + modal).
- Communication tests liés aux notifications restent verts.

## Critères d’acceptation
- [ ] Comportements clavier + focus corrects.
- [ ] Zéro erreurs console sur Browser tests.
- [ ] Les hooks tests (`data-*` / classes) restent stables.


