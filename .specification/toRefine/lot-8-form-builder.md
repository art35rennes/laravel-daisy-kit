# Lot 8 : Form Builder Avancé (No-Code / Low-Code)

Ce lot vise à créer un constructeur de formulaires visuel (WYSIWYG) inspiré de SurveyJS/GrapeJS et des bonnes pratiques Vueform, pour générer des formulaires dynamiques stockés en JSON, avec validation côté serveur (Laravel) et logique conditionnelle (JSONata).

## Objectifs
- **Interface Drag & Drop** : Palette de composants (inputs, layouts, actions) vers un canvas hiérarchique.
- **Non-duplication** : Le builder “connaît” les composants DaisyKit existants sans dupliquer markup ou styles.
- **Interopérabilité** : Export JSON versionné + génération de règles Laravel (`FormRequest` / `Validator`).
- **Logique dynamique** : JSONata pour `onInit`, `onChange`, `onSubmit`, `visibleIf`, `computed`.
- **Patterns de formulaires** : `wizard | tabs | inline | simple`, avec fallback si les templates Lot 3 sont absents.
- **Accessibilité et theming** : 100% daisyUI v5 / Tailwind v4, focus/aria, readonly cohérent.
- **Canvas en grille** : utilisation d’un layout grid (12 colonnes par défaut, gaps configurables) pour un drag & drop précis (rendu type preview : étapes, arbre de sections, formulaire multi-colonnes).

## 0. Principes directeurs
- **Single source of truth** : le JSON schema porte props, logique, validation.
- **Registries contractuels** : `ComponentRegistry`, `TemplateRegistry`, `LogicRegistry` exposent métadonnées typées (props, defaults, contraintes, slots attendus) consommées par Builder, Renderer, ValidationGenerator.
- **Rendu déterministe** : `FormRenderer` centralise JSON → Blade (patterns + slots), sans duplication UI.
- **Extensibilité douce** : nouveaux patterns ou composants ajoutables via registries sans toucher le cœur.
- **Fallback garanti** : si Lot 3 indisponible, rendu “simple” cohérent et message de dégradation gracieux.

## 1. Schéma JSON (v1.0)
- Champs requis : `schemaVersion`, `template` (`wizard|tabs|inline|simple`), `settings` (title/action/method), `pattern` (structure dépendante du template), `elements` (par section), `validation`, `logic`.
- `logic` : `onInit`, `onChange[]`, `onSubmit`, `visibleIf`, `computed` (expressions JSONata stringifiées).
- Defaults/contraintes : fournis par registries (ex : `wizard.props.linear=false`, `tabs.props.tabsStyle="box"`).
- **Layout grid** :
  - Chaque élément peut porter `layout` : `colSpan`, `rowSpan`, `order`, `row`, `col`, `breakpoints` (ex: `{ sm:6, md:6, lg:4, xl:4 }` sur 12 colonnes).
  - Les sections (steps/tabs/simple) peuvent définir `grid` : `columns` (default 12), `gap` (ex: `4`), `rowGap`, `columnGap`, `align`, `justify`, `stackMobile` (true/false).
  - Le renderer mappe ces infos vers les classes daisyUI/TW4 (`grid`, `grid-cols-12`, `gap-*`, `col-span-*`, responsive).

## 2. Contrats & registries (PHP)
- `Contracts\ComponentDefinition` : `type`, `category`, `props` (nom, type, required, default, options, validation), `slots?`, `capabilities` (readonly static/disabled, supportsLogic?).
- `Contracts\TemplatePattern` : `type`, `props`, `structure` (ex : `steps[].elements[]`), `slots`, `supports` (summary, errors mapping), `bladeMapping` (Lot 3) + `fallback`.
- `Contracts\LogicHook` : points d’entrée (`onInit`, `onChange`, `onSubmit`, `visibleIf`, `computed`) et règles de validation d’expression.
- `ComponentRegistry` : recense inputs/layouts/actions, dérive props/slots via `BuilderAware`.
- `TemplateRegistry` : décrit wizard/tabs/inline/simple, props, slots, mapping Blade, fallback.
- `LogicRegistry` : référence des hooks disponibles et leurs attentes (payload, contexte).
- `FormSchemaToValidationRules` : transforme `validation` + contraintes des composants en règles Laravel (inclut required, types, min/max, patterns, optionnalité sous `visibleIf`).

## 3. Renderer (PHP)
- `FormRenderer` :
  - Sélection du template (override possible).
  - Instanciation Blade (Lot 3 si présent, sinon fallback simple).
  - Injection des slots/sections (`step_{key}`, `tab_{id}`…) et rendu via `ui/*`.
  - Gestion `mode` et `readonlyStrategy` (fieldset disabled ou rendu statique par type).
  - Export des meta-infos dataset pour le JS (module, mode, applyLogic).

## 4. Découpage Blade
- `resources/views/components/ui/advanced/form-viewer.blade.php` : délègue à `FormRenderer`, slots `header/footer/actions`, modes `edit|readonly`.
- `resources/views/components/ui/advanced/form-builder.blade.php` : layout 3 colonnes (palette/canvas/inspecteur), génère dataset (module, options, endpoints metadata).
- Utilisation exclusive des composants `ui/*` existants (cards, tabs, steps, alerts, badges, inputs, buttons).
- Canvas Blade : wrapper grid (`grid`, `grid-cols-12`, `gap-*`) avec dropzones par cellule (overlay) et rendu responsive (stack sur mobile).

## 5. Découpage JS
- `resources/js/modules/form-builder/index.js` :
  - Orchestration (palette, canvas hiérarchique, inspecteur).
  - State + undo/redo, sélection contextuelle (pattern/section/élément).
  - Appels metadata (registries) et persistance (save/load JSON).
  - Prévisualisation “Run” (readonly/interactive) via FormRenderer côté Blade.
  - Gestion du layout grid : calcul des positions/colSpan, validation des collisions, auto-stack sur mobile selon `stackMobile`.
- `resources/js/modules/form-builder/dnd.js` :
  - Abstraction SortableJS (init, reorder, cross-list), gestion handles, ghost, placeholders, events.
  - Réutilisable pour d’autres usages (cf. lot générique infra DnD).
  - Support drop sur cellules de grille (coordonnées, recalcul colSpan), highlighting des zones valides.
- `resources/js/modules/form-builder/inspector.js` :
  - Form rendering dynamique des props (types, defaults, validation immédiate).
  - Éditeur d’expressions JSONata (validation syntaxique basique).
  - Panneau “Layout” pour colSpan/rowSpan/ordre/breakpoints, avec aperçus rapides.
- `resources/js/kit/logic-engine.js` :
  - Wrapper JSONata sécurisé (try/catch, erreurs structurées).
  - API : `evaluateVisibleIf`, `evaluateComputed`, `runHook`.
  - Événements `form:*` (ready/change/submit/error), écoute `csrf-keeper:updated`.
- `resources/js/modules/form-viewer.js` :
  - Sync mode/readonly, applique logique si `applyLogic=true`.
  - Émet `form-viewer:change|submit|error`.

## 6. Infrastructure DnD générique (nouveau lot transversal)
Objectif : factoriser les composants réutilisables autour de SortableJS pour d’autres usages.
- **Blade utilitaires** (catégorie `ui/utilities/` ou `ui/advanced/` selon besoin) :
  - `x-daisy::ui.utilities.dnd-area` : zone droppable, expose slots header/body/empty, états hover/disabled.
  - `x-daisy::ui.utilities.dnd-item` : item draggable avec handle optionnel.
  - `x-daisy::ui.utilities.dnd-placeholder` : placeholder stylé (daisyUI) pour insertion.
- **JS** :
  - `resources/js/modules/dnd-core.js` : initialisation SortableJS, options par défaut (animations, handles, accessibility), hooks d’événements (start/end/add/update/remove), utilitaires pour sérialisation d’ordre.
  - Réutilisé par `form-builder/dnd.js` et tout autre module nécessitant du tri.
  - Extensions grid : mapping cell → index, prévention collisions, gestion auto-insertion avec colSpan minimal.
- **Props/slots génériques** : labels, icônes, disabled, messages vides, états “drop denied”.

## 7. UX du builder (inspirations Vueform)
- Palette avec recherche/filtre, catégories (templates, inputs, layout, actions, hidden).
- Canvas hiérarchique : pattern → sections → éléments, avec reorder drag & drop et “click to add”.
- Inspecteur contextuel : props dynamiques issues des registries, validation inline, defaults visibles.
- Logic : éditeur JSONata, surlignage des erreurs, vue des dépendances.
- Prévisualisation “Run” fidèle (reuse FormRenderer), modes edit/readonly.
- Accessibilité : focus clair, aria-live pour erreurs, dropzones visibles, clavier (reorder via touches si possible).
- Canvas “grid first” : aperçu multi-colonnes, highlighting des cellules, auto-alignement des champs, support des décors (icônes handle, badge de type) sans bloquer la grille.

## 8. Stack JS requise
- `sortablejs` (DnD)
- `jsonata` (logic engine)
- (Optionnel) `ajv` (validation JSON Schema côté UI)

## 9. Composant Form Viewer (Edit / Readonly)
- Dépend du `FormRenderer`.
- Modes :
  - `edit` : champs interactifs, événements `form-viewer:*`.
  - `readonly` : stratégie `disabled` (fieldset) ou `static` (texte par type).
- Props clés : `schema`, `data`, `mode`, `readonlyStrategy`, `applyLogic`, `action`, `method`, `submitText`, `showActions`, `templateOverride`, `module`.
- Slots : `header`, `footer`, `actions`.
- Sécurité/CSRF : écoute `csrf-keeper:updated`, pas de PII en clair.

## 10. Mutualisation
- PHP : `FormRenderer`, `ComponentRegistry`, `TemplateRegistry`, `LogicRegistry`, `FormSchemaToValidationRules`.
- JS : `logic-engine`, `dnd-core`, conventions `form:*`.
- UI : utilisation stricte `ui/*`, i18n partagée `common.*`, `form.*`, `csrf.*`.
- Fixtures communes : `resources/dev/data/forms/*.json` pour tests/démos.

## 11. Phases & livrables
### Phase 1 : Fondations PHP
- [ ] Contrats `ComponentDefinition`, `TemplatePattern`, `LogicHook`, interface `BuilderAware`.
- [ ] Registries (component/template/logic) + `FormSchemaToValidationRules`.
- [ ] Service `FormRenderer` (fallback simple).
- [ ] Endpoint metadata (components + templates + logic).

### Phase 2 : Infra DnD générique (nouveau lot)
- [ ] Blade utilitaires `dnd-area`, `dnd-item`, `dnd-placeholder`.
- [ ] Module JS `dnd-core.js` (SortableJS abstrait).
- [ ] Intégration de base dans form-builder (palette/canvas).

### Phase 3 : UI Builder
- [ ] Composant Blade `ui/advanced/form-builder`.
- [ ] Modules JS `form-builder/index.js`, `form-builder/dnd.js`, `form-builder/inspector.js`.
- [ ] Layout 3 colonnes, palette + canvas + inspecteur, sélection, undo/redo.
- [ ] Preview “Run” (reuse FormRenderer).
 - [ ] Canvas grid : dropzones cellule, gestion colSpan/rowSpan, stack mobile, validation de collisions.

### Phase 4 : Logique & Données
- [ ] `logic-engine.js`, gestion JSONata, events `form:*`.
- [ ] Éditeur d’expressions, validation syntaxique, dépendances.
- [ ] Sauvegarde/chargement JSON, fixtures.

### Phase 5 : Form Viewer & intégration Laravel
- [ ] Composant Blade `form-viewer`, module JS associé.
- [ ] Tests : validation rules, rendu edit/readonly, selection template (Lot 3 présent/absent), logique appliquée.

### Phase 6 : UX & Polish
- [ ] Dropzones visibles, click-to-add, messages de dégradation, accessibilité (focus/aria), performances DnD.
- [ ] Audit A11y partagé Viewer/Preview.

## 12. Tests (Pest v4)
- Feature : génération de règles, rendu template (wizard/tabs/inline/simple + fallback), modes readonly, valeurs initiales, showActions/readonlyStrategy, metadata endpoint.
- Browser : DnD (sections/éléments), logique (visibleIf/computed), modes edit/readonly, absence d’erreurs console.
- Logic : JSONata succès/erreurs, fallback simple si template absent.

## 13. Compatibilité & évolutivité
- Fonctionne en mode “simple” autonome.
- Active patterns avancés si Lot 3 présent.
- Nouveaux patterns ajoutables via `TemplateRegistry`.
- Schéma versionné (migration documentée si évolution).

