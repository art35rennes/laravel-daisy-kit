# Lot 19 : Lazyloading JS dev-only (exclu du package publié)

Ce lot répond à l’exigence: **le lazyloading JS reste un outil de dev/démo**, et ne fait pas partie de l’expérience “core” publiée pour les apps hôtes.

## Décision amont (Lot 12)
- **Option B + core**: l’app hôte installe `alpinejs` et build les sources publiées via `daisy-src`.

## Objectifs
- Garder le lazyloading (scheduler `importWhenIdle` / `importWhenNearViewport` + imports conditionnels) **uniquement** pour:
  - les pages de démo (`daisy-dev::demo/*`)
- Exclure cette logique du tag de publication `daisy-src`.
- Clarifier les entrypoints JS:
  - **core**: Alpine + helpers + interactions nécessaires au package publié
  - **dev**: lazyloading + modules de démonstration + outillage

## Périmètre
- Publication:
  - `src/DaisyKitServiceProvider.php` (tags `daisy-src`, `daisy-dev-views`)
- Injection assets:
  - `resources/views/components/partials/assets.blade.php`
- JS:
  - `resources/js/app.js` (contient actuellement scheduler + imports lazy)
  - `resources/js/kit/index.js` (router `data-module`)
  - `resources/js/utils/scheduler.js`
- Vues de démo:
  - `resources/dev/views/**` (chargées via namespace `daisy-dev`)

## Comportement attendu
### App hôte (package publié)
- Si elle publie `daisy-src`, elle ne reçoit pas le lazyloading de démo.
- Le core doit rester fonctionnel:
  - Alpine disponible (fourni par le build Vite de l’app hôte)
  - interactions nécessaires aux composants (migrées dans lots 13–18)

Attendu côté app hôte (Option B):
- `npm i alpinejs`
- build Vite incluant les entrées du package (core + css) dans le `buildDirectory` configuré (`config('daisy-kit.vite_build_directory')`).

### Démo (dev)
- Les pages de démo continuent de:
  - lazy load les modules via scheduler (idle/near viewport)
  - afficher la page “Modules JS” (`resources/dev/views/demo/ui/partials/test-js-modules.blade.php`)

### Docs (optionnelles)
- Les pages docs ne doivent pas dépendre du lazyloading scheduler pour fonctionner.
- Si des exemples “lourds” existent dans les docs, ils doivent:
  - soit fonctionner avec le core (Alpine-first, modules/adapters init immédiat)
  - soit être explicitement marqués “demo-only” et déplacés vers `demo/`.

## Définition exacte de ce qu’on exclut (dev-only)
- À exclure du package publié:
  - `importWhenIdle`, `importWhenNearViewport`, file `resources/js/utils/scheduler.js`
  - la logique de concurrence/planification et les imports conditionnels de `resources/js/app.js`
- Ce qui peut rester “core” si nécessaire:
  - le router `data-module` (init à la présence) **sans** scheduler viewport/idle
  - les modules/adapters requis pour les libs tierces (Lot 18)

## Architecture / stratégie de découpage
Option recommandée:
- Scinder `resources/js/app.js` en 2 entrypoints:
  - `resources/js/core.js` (publié)
  - `resources/js/dev.js` (dev only)
- Faire pointer `assets.blade.php` vers:
  - `core.js` pour les vues `daisy::`
  - `core.js` pour les pages docs (même si elles vivent sous `daisy-dev::docs/*`)
  - `dev.js` uniquement pour les pages démo (ex: `daisy-dev::demo/*`)

Implémentation suggérée:
- Garder `daisy::components.partials.assets` pour le core.
- Ajouter un partial dev dédié (ex: `daisy-dev::partials.assets-dev`) que seules les vues `demo/*` incluent.

## Publication / tags
### `daisy-src` (core uniquement)
- Publier uniquement:
  - `resources/js/core.js` (+ modules nécessaires au package publié)
  - `resources/css/*` (inchangé)
- Ne pas publier:
  - `resources/js/dev.js`
  - le code scheduler/lazy imports de démo

### Option: `daisy-dev-src` (si on veut publier la démo)
- Publier:
  - `resources/js/dev.js`
  - éventuellement les dépendances dev spécifiques (si présentes)

## Plan de migration
1. Identifier ce qui est “core” vs “dev-only” dans `resources/js/app.js`.
2. Extraire la partie lazyloading en `dev.js`.
3. S’assurer que:
   - les vues `daisy::` chargent `core.js`
   - les pages docs chargent `core.js`
   - les pages démo chargent `dev.js`
4. Ajuster `DaisyKitServiceProvider` pour que `daisy-src` n’embarque pas `dev.js`.
5. Vérifier les Browser tests (la plupart visitent des pages de démo).

## Tests
- Browser tests: doivent rester verts (ils s’appuient sur démos).
- Ajouter un test de “publication contract” si pertinent (optionnel): vérifier que `daisy-src` publie bien le core attendu.

## Critères d’acceptation
- [ ] Lazyloading présent uniquement sur `daisy-dev::`.
- [ ] `daisy-src` ne publie pas les assets dev-only.
- [ ] Tous les Browser tests existants restent verts.


