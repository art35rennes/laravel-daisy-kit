# Lot 18 : Composants lourds (Alpine orchestrateur + wrappers libs)

Ce lot couvre les composants où Alpine apporte de la valeur sur l’UX et l’état, mais où il est parfois préférable de conserver un module JS (perf, libs tierces).

## Objectifs
- Utiliser Alpine pour:
  - state, contrôles UI, options, accessibilité
- Conserver des modules JS quand:
  - intégration lib tierce (Chart.js, Leaflet, CodeMirror, SignaturePad, etc.)
  - perf critique (treeview volumineux)
- Maintenir tous les Browser tests existants.

## Périmètre (cibles + tests existants)
- TreeView: `tests/Browser/TreeViewTest.php`
  - `resources/views/components/ui/advanced/tree-view.blade.php`
  - `resources/js/treeview.js`
- Transfer: `tests/Browser/TransferTest.php`
  - `resources/views/components/ui/advanced/transfer.blade.php`
  - `resources/js/transfer.js`
- Stepper: `tests/Browser/StepperTest.php`
  - `resources/views/components/ui/navigation/stepper.blade.php`
  - `resources/js/stepper.js`
- Table (sélection/UX): `tests/Browser/TableTest.php`
  - `resources/views/components/ui/data-display/table.blade.php`
  - `resources/js/table.js`
- Scrollspy: `tests/Browser/ScrollspyTest.php`
  - `resources/views/components/ui/advanced/scrollspy.blade.php`
  - `resources/js/scrollspy.js`
- Scroll status: `tests/Browser/ScrollStatusTest.php`
  - `resources/views/components/ui/advanced/scroll-status.blade.php`
  - `resources/js/scroll-status.js`
- Onboarding: `tests/Browser/OnboardingTest.php`
  - `resources/views/components/ui/advanced/onboarding.blade.php`
  - `resources/js/onboarding.js`
- Lightbox: `tests/Browser/LightboxTest.php`
  - `resources/views/components/ui/media/lightbox.blade.php`
  - `resources/js/lightbox.js`
- Media Gallery: `tests/Browser/MediaGalleryTest.php`
  - `resources/views/components/ui/media/media-gallery.blade.php`
  - `resources/js/media-gallery.js`
- Color Picker: `tests/Browser/ColorPickerTest.php`
  - `resources/views/components/ui/inputs/color-picker.blade.php`
  - `resources/js/color-picker.js`
- Sign: `tests/Browser/SignTest.php`
  - `resources/views/components/ui/inputs/sign.blade.php`
  - `resources/js/modules/sign.js` + `signature_pad`
- File input (upload/preview):
  - `resources/views/components/ui/inputs/file-input.blade.php`
  - `resources/js/file-input.js`
- Input mask:
  - `resources/js/input-mask.js`
- WYSIWYG / Code editor (lazy editors):
  - `resources/views/components/ui/advanced/wysiwyg.blade.php`
  - `resources/views/components/ui/advanced/code-editor.blade.php`
  - `resources/js/lazy-editors.js`
  - `resources/js/code-editor.js`
- Chart:
  - `resources/views/components/ui/advanced/chart.blade.php`
  - `resources/js/chart/index.js`
- Leaflet:
  - `resources/views/components/ui/media/leaflet.blade.php`
  - `resources/js/leaflet/index.js`
- Calendar-full / Cally:
  - `resources/views/components/ui/advanced/calendar-full.blade.php`
  - `resources/js/calendar-full/index.js`
  - `cally` (Web Components) chargé côté JS

## Stratégie
### Alpine comme orchestrateur
Pattern:
- `x-data="{ ...options, ...uiState }"` sur le wrapper Blade
- `x-init`:
  - décider si on doit initialiser une lib/module
  - déclencher l’init (idempotent)
- `x-on:*` pour gérer interactions locales et déclencher des events

Note importante (Lot 19):
- Ne pas dépendre du scheduler `importWhenIdle` / `importWhenNearViewport` pour que le **core** fonctionne. Le core doit pouvoir init via:
  - Alpine (`x-init`)
  - ou le router `data-module` (si conservé) sans “lazyloading démo”.

### Modules JS conservés
Le module JS devient un “adapter”:
- expose `init(root, options)` idempotent
- se contente d’instancier la lib et de relayer les events

## Plan de migration
1. Pour chaque composant lourd, définir:
   - quelle partie bascule en Alpine (state, open/close, options)
   - quelle partie reste en module (lib, perf)
2. Adapter les composants Blade pour poser `x-data` + dataset options.
3. Ajuster les modules JS pour devenir idempotents et facilement appelables depuis Alpine.

## Tests
- Tous les Browser tests listés doivent rester verts.
- Ajouter des assertions “no console errors” si manquantes.
- Ajouter des Browser tests si un composant lourd n’en a pas encore (ex: Leaflet/Chart/Calendar) ou couvrir via un smoke test de page démo.

## Critères d’acceptation
- [ ] Alpine pilote l’état UI (pas uniquement du DOM imperative JS).
- [ ] Les libs tierces restent encapsulées (modules/adapters), init idempotent.
- [ ] Tous les Browser tests concernés restent verts.


