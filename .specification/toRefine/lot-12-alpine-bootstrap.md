# Lot 12 : Fondations Alpine (bootstrap, helpers, conventions)

Ce lot met en place Alpine.js comme **moteur d’interactions principal** pour les composants Blade du package.

## Objectifs
- Rendre Alpine disponible et fiable partout où le package injecte ses assets (`@include('daisy::components.partials.assets')`).
- Standardiser des helpers Alpine (persist, debounce, utils DOM) pour éviter la duplication de logique.
- Préparer la migration des modules `data-module` → Alpine sans casser les tests Browser existants.

## Non-objectifs
- Migrer des composants spécifiques (c’est l’objet des Lots 13+).
- Optimiser/perf fine des gros composants (Lot 18).
- Mettre en place le lazyloading “core” pour les apps hôtes (Lot 19 : lazyloading dev-only).

## Périmètre
- Assets:
  - `resources/views/components/partials/assets.blade.php`
  - `resources/js/app.js`
  - `resources/js/bootstrap.js`
  - `config/daisy-kit.php` (contrat `auto_assets`, `use_vite`, fallback bundle)
- Conventions:
  - `.cursor/rules/javascript.mdc` (déjà mis à jour)

## Comportement attendu
- Les directives Alpine (`x-data`, `x-on`, `x-show`, etc.) fonctionnent sur:
  - les vues du package (`daisy::`)
  - les vues de démo/docs (`daisy-dev::`) sans configuration additionnelle.
- Alpine ne doit pas dépendre du système `data-module` pour fonctionner.

## Architecture proposée
### Alpine (core)
- Charger Alpine dans l’entrypoint (ou dans un entrypoint “core” si on scinde plus tard au Lot 19).
- Convention:
  - `window.Alpine = Alpine`
  - `Alpine.start()`
  - éventuels plugins (uniquement si nécessaires, sinon éviter).

### Contrat d’assets (futureproof)
À expliciter dès ce lot pour éviter des migrations coûteuses plus tard:
- **Décision**: **Option B + core**.
  - L’app hôte doit installer `alpinejs` et builder les sources publiées via `daisy-src`.
  - Le package fournit un **entrypoint core** (prévu en `resources/js/core.js` au Lot 19) qui bootstrap Alpine + helpers + interactions nécessaires.
- **Pages / entrypoints**
  - **Core** (publié): `core.js` + `app.css` (sans lazyloading scheduler).
  - **Dev (démo uniquement)**: `dev.js` + scheduler/lazy imports (Lot 19).

### Intégration attendue côté app hôte (Option B)
- Publier les sources du package:
  - `php artisan vendor:publish --tag=daisy-src`
- Installer Alpine:
  - `npm i alpinejs`
- Configurer Vite dans l’app hôte pour builder les entrées du package (au minimum):
  - `resources/vendor/daisy-kit/css/app.css`
  - `resources/vendor/daisy-kit/js/core.js` (après Lot 19; temporairement `app.js` avant découpage)
  - et utiliser le `buildDirectory` du package (voir `config('daisy-kit.vite_build_directory')`) afin que `@vite(..., buildDirectory)` résolve correctement le manifest du package.

### Helpers Alpine (à créer)
- `persistLocal(key, defaultValue)`
- `persistSession(key, defaultValue)`
- `debounce(fn, wait)`
- `normalizeText(text)` (utile pour la recherche sidebar)

> Note: en phase dev, pas besoin de backward compatibility, mais il faut maintenir la cohérence interne (mêmes patterns).

## Plan de migration
1. Ajouter l’initialisation Alpine dans l’entrypoint **core** chargé par `assets.blade.php` (avant Lot 19: `resources/js/app.js`, après Lot 19: `resources/js/core.js`).
2. Exposer un petit namespace utilitaire (ex: `window.DaisyAlpine` ou module importable) pour partager persist/debounce.
3. Vérifier qu’un composant de démo existant utilisant déjà Alpine (ex: `resources/dev/views/demo/ui/partials/test-callout.blade.php`) fonctionne.

## Tests
### Browser tests
- Ajouter (ou étendre) un smoke test minimal “Alpine loaded” sur une page de démo (si besoin) :
  - assert qu’un élément `x-data` change d’état via `click`.

### Critères d’acceptation
- [ ] Alpine est chargé et actif sur les pages utilisant `@include('daisy::components.partials.assets')`.
- [ ] Aucun Browser test existant ne réagit négativement (pas d’erreurs console).
- [ ] Helpers Alpine définis (persist/debounce) et documentés dans le code.

## Risques & mitigations
- Risque: double initialisation Alpine → mitigation: s’assurer d’un seul `Alpine.start()`.
- Risque: conflits avec modules existants → mitigation: lots suivants migrent progressivement, garder hooks `data-*` pour tests.


