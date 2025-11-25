# Lot 8 : Form Builder Avancé (No-Code / Low-Code)

Ce lot vise à créer un constructeur de formulaires visuel (WYSIWYG) inspiré de SurveyJS/GrapeJS, permettant de générer des formulaires dynamiques stockés en JSON, avec une validation côté serveur (Laravel) et une logique conditionnelle (JSONata).

## Objectifs
- **Interface Drag & Drop** : Palette de composants (inputs, layouts, etc.) vers une zone de canvas.
- **Non-duplication** : Le builder doit "connaître" les composants DaisyKit existants sans dupliquer leur code HTML/CSS.
- **Interopérabilité** : Export JSON de la configuration + Génération de règles de validation Laravel (`FormRequest` ou `Validator`).
- **Logique dynamique** : Support de JSONata pour les événements (`onInit`, `onChange`, `onSubmit`) et la visibilité conditionnelle.
- **Support des patterns de formulaires** (futur-proof) : prise en charge des modèles de haut niveau (wizard, tabs, inline, simple) via un schéma JSON extensible et un renderer qui réutilise les templates du Lot 3 s’ils sont présents (dépendance souple).

## Architecture Technique

### 0. Principes de compatibilité (Future-proof)
- **Schéma versionné** : tout JSON exporté inclut `schemaVersion` et `template` (p.ex. `wizard|tabs|inline|simple`) pour permettre des évolutions sans casser l’existant.
- **Dépendance souple avec le Lot 3** : si les templates avancés du Lot 3 sont installés, le renderer les utilise; sinon, il bascule en mode “simple” (fallback) ou rend une alerte de dégradation gracieuse.
- **Registries extensibles** :
  - `ComponentRegistry` pour les composants UI atomiques/moléculaires (inputs, layouts).
  - `TemplateRegistry` pour les patterns de haut niveau (wizard, tabs, inline) décrits par métadonnées (props supportées, structure attendue, mapping vers des templates Blade).
- **Rendu déterministe** : le mapping JSON → Blade est centralisé dans un `FormRenderer` qui orchestre le rendu (pattern + éléments) sans dupliquer les implementations UI.

### 1. Structure de Données (JSON Schema)
Le formulaire est décrit par un objet JSON versionné, avec prise en charge des patterns (wizard, tabs, inline, simple).

Exemple (wizard) :
```json
{
  "schemaVersion": "1.0",
  "template": "wizard",
  "settings": { "title": "Inscription", "action": "/register", "method": "POST" },
  "pattern": {
    "type": "wizard",
    "props": { "linear": true, "allowClickNav": false, "showSummary": true },
    "steps": [
      {
        "key": "profile",
        "label": "Profil",
        "icon": "user",
        "elements": [
          {
            "type": "daisy-input",
            "name": "user_email",
            "props": { "label": "Email", "type": "email", "required": true }
          }
        ]
      },
      { "key": "preferences", "label": "Préférences", "elements": [] }
    ],
    "summary": { "elements": [] }
  },
  "validation": { "user_email": "required|email" },
  "logic": {
    "onInit": "true",
    "onChange": [],
    "onSubmit": "true"
  }
}
```

Exemple (tabs) :
```json
{
  "schemaVersion": "1.0",
  "template": "tabs",
  "settings": { "title": "Profil", "action": "/profile", "method": "POST" },
  "pattern": {
    "type": "tabs",
    "props": { "tabsStyle": "box", "tabsPlacement": "top", "highlightErrors": true },
    "tabs": [
      { "id": "general", "label": "Général", "elements": [] },
      { "id": "security", "label": "Sécurité", "elements": [] }
    ]
  }
}
```

### 2. Backend : Registry & Validation
Pour éviter la duplication, nous créerons un `ComponentRegistry` capable d'inspecter les composants Blade (via Attributs PHP ou fichiers de définition) pour exposer leurs props au Builder. Nous ajoutons un `TemplateRegistry` pour les patterns de haut niveau.

- **Interface `BuilderAware`** : Les composants Blade implémenteront cette interface pour définir leurs props éditables.
- **ValidationGenerator** : Service convertissant le JSON Schema du form en tableau de règles Laravel (`['user_email' => 'required|email']`).
- **TemplateRegistry** : Catalogue des patterns (`wizard`, `tabs`, `inline`, `simple`) décrivant:
  - Props supportées et valeurs par défaut (ex: `linear`, `allowClickNav`, `tabsStyle`…).
  - Structure attendue (ex: `steps[].elements[]`, `tabs[].elements[]`).
  - Mapping vers les templates Blade du Lot 3 lorsque disponibles (ex: `resources/views/templates/form-wizard.blade.php`), sinon fallback “simple”.
- **FormRenderer** : Service qui lit `template` et `pattern` puis:
  - Instancie le template Blade correspondant (Lot 3 si présent).
  - Injecte les éléments générés dans les slots attendus (`step_{key}`, `tab_{id}`, etc.).
  - Rend les éléments via les composants `ui/*` existants (Atomic Design respecté).

### 3. Frontend : Module JS `form-builder`
- **Librairie DnD** : Utilisation de `SortableJS` pour gérer le drag-and-drop fluide entre la palette et le canvas.
- **Moteur de Rendu** :
    - *Canvas* : Rendu des composants via des appels AJAX (pour un rendu fidèle au pixel près) ou mapping JS (plus rapide).
    - *Preview* : Mode lecture seule exécutant le formulaire final.
- **Moteur Logique** : Intégration de la librairie `jsonata` (via npm) pour évaluer les expressions en temps réel.
- **Palette étendue** :
  - Catégorie “Form Templates” : `wizard`, `tabs`, `inline`, `simple`.
  - Catégorie “Form Elements” : Inputs, Layouts, Buttons, Hidden.
- **Édition hiérarchique** :
  - Mode “Pattern” (choix et props globales).
  - Mode “Section” (étapes/onglets : add/remove/reorder).
  - Mode “Élément” (édition des props des inputs/layouts par section).
 - **Initialisation** :
   - Enregistrement dans `resources/js/kit/index.js` (router `[data-module]`).
   - Le composant Blade génère `data-module="form-builder"` et ses options via `data-*` (single source of truth).

## Liste des Tâches

### Phase 1 : Fondations & Registries
- [ ] Créer l'interface PHP `BuilderAware` et le Trait associé.
- [ ] Implémenter le `ComponentRegistry` (Inputs, Layouts).
- [ ] Créer l’`TemplateRegistry` (wizard, tabs, inline, simple) avec métadonnées (props/structure/mapping Blade).
- [ ] Créer un endpoint API pour récupérer la configuration des composants et des templates (props, types, valeurs par défaut).
 - [ ] Définir les contrats `Contracts\TemplatePattern` et `Contracts\ComponentDefinition` (lisibles par le Builder et le Renderer).

### Phase 2 : UI du Builder (Blade + JS)
- [ ] Créer le layout 3 colonnes :
    - **Gauche** : Palette (Catégories : Form Templates, Inputs, Layout, Buttons, Hidden).
    - **Centre** : Canvas (Zone de drop avec highlighting, structure hiérarchique pattern → sections → éléments).
    - **Droite** : Inspecteur de propriétés (Pattern/Section/Élément).
- [ ] Initialiser le module JS `form-builder.js`.
- [ ] Implémenter le Drag & Drop (Palette → Canvas et tri dans Canvas).
- [ ] Gérer la sélection (pattern/section/élément) et l’affichage des props associées.
 - [ ] Enregistrer `form-builder` dans `resources/js/kit/index.js` (scan `[data-module]`).

### Phase 3 : Logique & Données
- [ ] Étendre le schéma JSON avec `schemaVersion`, `template`, `pattern`.
- [ ] Implémenter la sauvegarde/chargement du JSON.
- [ ] Intégrer `jsonata` pour évaluer les champs calculés ou la visibilité (`visibleIf`).
- [ ] Ajouter l'édition des expressions JSONata dans l'inspecteur (avec validation syntaxique basique).
- [ ] (Optionnel) Valider le schéma via `ajv` côté UI (cohérence structurelle).
 - [ ] Créer un module mutualisé `resources/js/kit/logic-engine.js` (wrapper JSONata, API commune pour Viewer/Preview).
 - [ ] Définir un bus d’événements et conventions (`form:*`) : `form:ready`, `form:change`, `form:submit`, `form:error`.
 - [ ] Ajouter des fixtures JSON partagées `resources/dev/data/forms/*.json` (wizard/tabs/inline/simple) pour tests/preview.

### Phase 4 : Intégration Laravel
- [ ] Créer le service `FormSchemaToValidationRules`.
- [ ] Créer un composant Blade `<x-daisy-form-renderer :schema="$json" />` capable de :
    - Détecter `template` et lire `pattern`.
    - Rendre via les templates du Lot 3 si présents (wizard/tabs/inline), sinon fallback “simple”.
    - Injecter les éléments dans les slots (`step_{key}`, `tab_{id}`, etc.) et utiliser `ui/*`.
- [ ] Tests : Vérifier que le JSON généré produit les bonnes règles de validation Laravel.
 - [ ] Mutualiser l’i18n : clés communes `common.submit`, `form.previous`, `form.next`, `form.finish`, `csrf.refreshing`, `csrf.expired`, `advanced_filters`, `clear_filter`.

### Phase 5 : UX & Polish
- [ ] Highlight des zones de drop (Dropzones).
- [ ] "Click to add" (Ajout rapide en fin de formulaire).
- [ ] Support du Undo/Redo.
- [ ] Prévisualisation (Mode "Run").
- [ ] Messages de dégradation gracieuce si un pattern avancé est sélectionné mais le Lot 3 n’est pas installé.
 - [ ] Audit accessibilité partagé (focus, aria, `fieldset[disabled]`) entre Viewer et Preview.

## Stack JS Requise
- `sortablejs` (Drag & Drop)
- `jsonata` (Logic engine)
- (Optionnel) `ajv` (Validation JSON Schema côté UI)

## Composant Form Viewer (Edit / Readonly)

### But
Afficher un formulaire généré depuis le JSON Schema du builder en deux modes:
- `edit`: formulaire interactif (saisie utilisateur).
- `readonly`: affichage non modifiable (désactivé ou rendu en texte statique).

Sans dupliquer la logique UI, le Viewer délègue le rendu au `FormRenderer`, qui lui-même utilise les composants `ui/*` et, si disponibles, les templates du Lot 3 (wizard, tabs, inline).

### Emplacement / Livrables
1. **Blade component**: `resources/views/components/ui/advanced/form-viewer.blade.php`
   - Namespace Blade: `x-daisy::ui.advanced.form-viewer`
2. **JS module**: `resources/js/modules/form-viewer.js`
   - `data-module="form-viewer"`
3. **Intégration**: Utilise le `FormRenderer` pour le rendu, sans duplication.

### Props (proposées)
```php
@props([
  'schema' => null,                // array|JsonSerializable (requis)
  'data' => [],                    // array de valeurs initiales (facultatif)
  'mode' => 'edit',                // 'edit' | 'readonly'
  'readonlyStrategy' => 'disabled',// 'disabled' | 'static' (disabled = champs désactivés, static = texte)
  'applyLogic' => true,            // exécution des règles JSONata (visibleIf, computed, etc.)
  'action' => '#',                 // POST/PUT/… si on souhaite un submit natif
  'method' => 'POST',
  'submitText' => __('common.submit'),
  'showActions' => true,           // rend les boutons (submit/reset) si applicable
  'templateOverride' => null,      // forcer un template ('wizard'|'tabs'|'inline'|'simple'), sinon détecté via schema
  'module' => null,                // override data-module si nécessaire
])
```

### Slots
- `actions`: personnalisation des actions (ex: bouton submit secondaire, bouton annuler).
- (Optionnel) `header`, `footer`: en-tête/pied personnalisés autour du rendu.

### Comportements attendus
- `edit`:
  - Champs interactifs, événements émis à chaque modification (`form-viewer:change`).
  - Soumission possible (si `action` défini) et/ou callback JS (`form-viewer:submit`).
  - Respect de `applyLogic` (visibleIf, computed).
- `readonly`:
  - Si `readonlyStrategy=disabled`: tous les champs sont rendus et désactivés (`disabled`, `aria-disabled="true"`).
  - Si `readonlyStrategy=static`: rendu en texte statique (par type de champ), adapté aux patterns (wizard/tabs).
  - Aucune interaction/modification possible.
- Rendu pattern:
  - Le Viewer délègue au `FormRenderer` qui détecte `schema.template` (wizard|tabs|inline|simple).
  - Si templates du Lot 3 présents: réutilisation des templates et de leurs slots.
  - Sinon: fallback “simple” cohérent (liste verticale de champs).
- Accessibilité:
  - `fieldset[disabled]` pour désactiver en bloc si stratégie “disabled”.
  - `aria-disabled` et focus management.
- Sécurité/CSRF:
  - Compatible avec le CSRF Keeper (écoute `csrf-keeper:updated`).
  - Ne pas afficher de données sensibles en clair; ne jamais logguer de PII.

### JS (form-viewer.js)
- Dataset:
  - `data-mode`, `data-readonly-strategy`, `data-apply-logic`.
  - Synchronise le `mode` (lecture/écriture) avec l’état des champs.
  - Émet:
    - `form-viewer:change` (detail: `{ name, value, path }`).
    - `form-viewer:submit` (detail: `{ values }`) si interception JS.
    - `form-viewer:error` (detail: `{ message, field? }`).
- Interop JSONata:
  - Si `applyLogic=true`, évalue `visibleIf`/computed au change.
 - Initialisation:
   - Enregistrement dans `resources/js/kit/index.js` (router `[data-module]`).
   - Le Blade génère `data-module="form-viewer"` et options via dataset.

## Mutualisation

### Objectifs
- Éviter toute duplication d’UI/markup : toujours réutiliser `resources/views/components/ui/*` et, pour les patterns, les templates du Lot 3.
- Centraliser la logique partagée (PHP + JS) utilisable par le Builder, le Viewer/Renderer et les démos/tests.

### PHP (mutualisé)
- `FormRenderer` (service unique) : rendu déterministe à partir du JSON (détecte `template`, lit `pattern`, injecte les slots) — utilisé par Viewer, démos et tests.
- `ComponentRegistry` : source unique de vérité des composants (props, types, défauts) — utilisé par Builder (palette/inspecteur), Renderer (vérification) et ValidationGenerator.
- `TemplateRegistry` : métadonnées des patterns (props supportées, structure, mapping Blade Lot 3, fallback).
- `FormSchemaToValidationRules` : génération des règles depuis le JSON — utilisée en tests et en runtime côté host app.

### JS (mutualisé)
- `resources/js/kit/logic-engine.js` : wrapper JSONata/évaluations (visibleIf, computed) — utilisé par Preview du Builder et Form Viewer.
- Conventions d’événements `form:*` (préfixées) : `form:ready`, `form:change`, `form:submit`, `form:error` — évitent la fragmentation.
- Initialisation standard via `resources/js/kit/index.js` (router `[data-module]`) pour `form-builder` et `form-viewer`.
- Compatibilité CSRF Keeper : écoute `csrf-keeper:updated` pour re-synchroniser les requêtes si nécessaire.

### UI/Accessibilité (mutualisé)
- Utilisation exclusive des composants `ui/*` : `advanced.label`, `advanced.validator`, inputs/selects, etc. (PRIORITÉ daisyUI).
- Stratégies `readonly` harmonisées : `fieldset[disabled]` ou rendu statique textuel cohérent par type.
- i18n partagée : réutilisation des clés `common.*`, `form.*`, `csrf.*` (EN/FR).

### Tests/Démos (mutualisé)
- Fixtures JSON communes (`resources/dev/data/forms/*.json`) utilisées par:
  - Tests Feature (rendu, validation).
  - Tests Browser (interactions, erreurs console).
  - Démos (prévisualisation builder/viewer).

### Tests
- Feature:
  - Rendu `edit` vs `readonly` (disabled/static).
  - Détection et rendu du template via `FormRenderer` (wizard/tabs/inline/simple).
  - Respect des valeurs initiales (`data`) et options `showActions`, `readonlyStrategy`.
- Browser:
  - Mode `readonly` empêche la saisie (disabled) ou affiche statique (aucun focus interactif).
  - Mode `edit` permet la saisie et déclenche `form-viewer:change`.
  - Soumission (si `action`), vérification absence d’erreurs console.

## Compatibilité & Évolutivité
- Le Form Builder fonctionne de manière autonome (mode “simple”).
- S’il détecte les templates du Lot 3, il active les patterns avancés (wizard, tabs, inline) et le rendu via ces templates.
- Nouveaux patterns ajoutables via `TemplateRegistry` sans modifier le cœur du builder.
- Schéma versionné (p.ex. `schemaVersion: "1.x"`), avec stratégie de migration documentée si nécessaire.

