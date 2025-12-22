# Lot 14 : Migration Forms vers Alpine (wizard / tabs / inline)

Ce lot remplace les modules JS `resources/js/modules/forms/*` par Alpine dans les templates de formulaires avancés.

## Objectifs
- Remplacer `data-module="wizard|tabs|inline"` par une implémentation Alpine.
- Conserver la persistance d’état:
  - wizard: étape courante + persistance des champs
  - tabs: onglet actif (et persistance si configurée)
  - inline: comportement “advanced panel / reset tokens” si présent
- Mettre à jour les tests Feature qui assertent actuellement `data-module`.

## Non-objectifs
- Refaire le composant stepper lui-même (seulement l’orchestration).
- Ajouter du lazyloading “core” (Lot 19 : dev-only).

## Périmètre
- Templates:
  - `resources/views/templates/form/form-wizard.blade.php`
  - `resources/views/templates/form/form-with-tabs.blade.php`
  - `resources/views/templates/form/form-inline.blade.php`
- Modules actuels:
  - `resources/js/modules/forms/wizard.js`
  - `resources/js/modules/forms/tabs.js`
  - `resources/js/modules/forms/inline.js`
- Tests Feature:
  - `tests/Feature/FormWizardRenderingTest.php`
  - `tests/Feature/FormTabsRenderingTest.php`
  - `tests/Feature/FormInlineRenderingTest.php`

## Comportement attendu (UX)
### Wizard
- Navigation prev/next, step courant, validation éventuelle en mode linéaire.
- Persistance des champs via `sessionStorage` (équivalent à `wizard.js`).
- Émission d’un event (ex: `wizard:step-change`) pour permettre aux composants dépendants de se synchroniser.

### Tabs
- Changement d’onglet au clic/keyboard.
- Persistance optionnelle si la template le supporte (actuellement mentionnée en docs dev).

### Inline
- Toggle panneau avancé (si présent).
- Reset tokens / état (si présent), sans dépendre d’un module global.

## API Blade
- Ne pas changer le contrat public des templates sans nécessité.
- Autoriser une migration “interne” en gardant certains `data-*` comme hooks de tests.

## Architecture
### Alpine
- `x-data` au niveau du formulaire (wizard/inline) ou du conteneur tabs.
- Stores Alpine autorisés si utile (ex: `Alpine.store('wizard', ...)`) mais préférer composant local pour éviter collisions multi-instances.
- Persistance:
  - clé stable dérivée des props/dataset (ex: `wizardKey` + `form.id`), comme dans `wizard.js`.

### Modules JS
- Option: garder temporairement les modules forms comme fallback, puis les supprimer au Lot 20.

## Plan de migration
1. Porter les comportements dans les templates:
   - wizard: état, persistance, nav
   - tabs: onglet actif, persistance
   - inline: panneau advanced / reset
2. Retirer l’attribut `data-module` des templates (ou le laisser sans effet pendant une phase transitoire).
3. Mettre à jour les tests Feature: remplacer les asserts `data-module="..."` par asserts stables (présence `x-data` + marqueurs `data-*`).

## Plan de tests
### Feature tests
- Adapter `tests/Feature/FormWizardRenderingTest.php`, `FormTabsRenderingTest.php`, `FormInlineRenderingTest.php`.
- Assertions recommandées:
  - présence de `x-data`
  - présence des champs hidden (`_wizard_step`) / marqueurs `data-wizard-prev` etc.

### Browser tests
- Ajouter un Browser test wizard minimal (si pas déjà couvert indirectement):
  - passer une étape, recharger page, vérifier persistance.

## Critères d’acceptation
- [ ] Les 3 templates fonctionnent sans dépendance aux modules forms.
- [ ] Les tests Feature existants sont mis à jour et verts.
- [ ] Un test Browser critique pour wizard/tabs passe sans erreurs console.

## Risques & mitigations
- Risque: multi-instances wizard sur une page → mitigation: clé d’instance stable (id ou dataset) + state local.
- Risque: divergence avec `WizardPersistence` côté PHP → mitigation: aligner la persistance JS avec le helper existant si applicable.


