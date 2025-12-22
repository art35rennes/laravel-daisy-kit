# Lot 20 : Nettoyage final (suppression code mort, réduction `data-module`)

Ce lot finalise la migration: une fois Alpine dominant et les composants migrés, on nettoie les restes.

## Objectifs
- Supprimer le code mort:
  - modules JS devenus inutiles
  - wiring `data-module` obsolète dans Blade
  - handlers globaux devenus inutiles
- Stabiliser le contrat “core” du package:
  - Alpine (core)
  - modules/adapters uniquement là où nécessaire (libs)

## Périmètre
- `resources/js/kit/index.js` (router `data-module`)
- `resources/js/app.js` / `resources/js/core.js` (selon Lot 19)
- `resources/js/modules/**` (suppression/cleanup)
- Composants Blade qui gardent des `data-module` uniquement par héritage
- Docs/démos qui décrivent les anciens modules (déplacer vers “legacy” ou mettre à jour)
- Scripts inline dans les vues Blade (à auditer et migrer):
  - templates: `resources/views/templates/communication/chat.blade.php`, `resources/views/templates/changelog.blade.php`
  - layouts/partials: `resources/views/components/layout/docs.blade.php`, `resources/views/components/layout/*`, `resources/views/templates/layout/*`
  - composants UI: `resources/views/components/ui/overlay/modal.blade.php`, `resources/views/components/ui/utilities/mockup-code.blade.php`, `resources/views/components/ui/partials/theme-selector.blade.php`, etc.

## Plan de migration
1. Inventorier les `data-module="..."` restants dans `resources/views/**`.
2. Pour chaque occurrence:
   - confirmer qu’Alpine remplace bien le comportement
   - supprimer `data-module` et le module associé si devenu inutile
3. Nettoyer `kit/index.js` si plus nécessaire (ou le garder minimal si encore utile pour quelques adapters libs).
4. Auditer les `<script>` inline dans `resources/views/**`:
   - Conserver les scripts de **données** (`type="application/json"`) utilisés comme config (ex: chart/leaflet/code-editor).
   - Migrer les scripts de **comportement** vers Alpine ou modules/adapters (selon Lot 18/19).
   - Supprimer les scripts inline restants après migration.
5. Mettre à jour les docs/démos pour refléter la réalité (Alpine-first).

## Tests
- Lancer la suite ciblée des Browser tests pour s’assurer qu’aucune interaction ne dépend encore d’un module supprimé.
- Adapter les tests Feature qui recherchaient `data-module`.
- Ajouter/adapter des Browser tests si un script inline migre un comportement non couvert.

## Critères d’acceptation
- [ ] Aucun `data-module` inutile dans les composants “UI pure”.
- [ ] Router `data-module` minimal ou supprimé si inutile.
- [ ] Browser tests et Feature tests verts.


