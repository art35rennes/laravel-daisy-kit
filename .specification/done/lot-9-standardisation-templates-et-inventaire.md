# Lot 9 · Plan de spécification – Standardisation des templates & inventaire

## 1. Objectifs produit

- **Généraliser tous les templates** pour qu’ils soient réellement réutilisables dans n’importe quelle application Laravel (aucun texte métier en dur, sections optionnelles, API Blade claire).
- **Simplifier la compréhension du package** en rendant l’arborescence des templates évidente, prévisible et alignée avec les règles déjà définies.
- **Réduire au minimum la maintenance** en supprimant la couche d’inventaire JSON (`templates.json`) et la commande `InventoryTemplates`, au profit d’un modèle purement déclaratif côté Blade.

## 2. Périmètre fonctionnel

| Axe | Description synthétique | Valeur ajoutée |
|-----|-------------------------|----------------|
| Normalisation de l’arborescence | Tous les templates sont rangés sous `resources/views/templates/{category}/{name}.blade.php` (plus de templates à la racine). | Structure prévisible, compréhension immédiate du catalogue. |
| API Blade générique | Chaque template expose une API claire basée sur **props** + **slots nommés**, avec sections désactivables. | Meilleure réutilisabilité et flexibilité sans fork des vues. |
| Partials & Atomic Design | Les templates peuvent être décomposés en partials dans un sous-répertoire dédié, et/ou en composants `ui/*` si un motif est partagé. | Réduction de la duplication, cohérence visuelle et logique. |
| Suppression de l’inventaire JSON | Décommissionner `InventoryTemplates` et `resources/dev/data/templates.json` au profit d’une navigation/doc purement déclarative. | Moins de MCO, moins de risques de désynchronisation entre code et doc. |

## 3. Exigences transverses

1. **Aucune donnée métier en dur dans les templates**  
   - Tous les titres, textes, labels et messages doivent provenir :
     - soit de **props** (valeur par défaut traduite avec `__()`),
     - soit de **traductions** (`resources/lang/*`),
     - soit de **slots** (permettant au projet hôte d’injecter son propre contenu).
   - Les seules exceptions tolérées sont les placeholders “neutres” de démo dans `resources/dev/views` (pas dans les templates du package).

2. **Sections désactivables**  
   - Toute section visuelle non strictement indispensable (header secondaire, sidebar, bandeau, résumé, meta, actions secondaires, etc.) doit pouvoir être :
     - désactivée via une prop booléenne (`showSidebar`, `showSecondaryActions`, `showMeta`, …), **ou**
     - remplacée via un slot optionnel (présence du slot = activation).

3. **Utilisation complète de Blade**  
   - Combiner **props**, **slots nommés**, **slots par défaut** et, si nécessaire, `@aware` pour faire remonter des infos depuis des composants enfants.
   - Préférer les patterns suivants :
     - Composant “shell” très générique (`layout`, `section`, `panel`, `header`, `toolbar`) + slots riches.
     - Partials pour les sous-blocs récurrents.
     - Composants `ui/*` pour les motifs vraiment transverses (card layout, header de page, bandeau d’actions).

4. **Respect strict de l’architecture existante**  
   - Templates : `resources/views/templates/{category}/{name}.blade.php`.
   - Partials d’un template : `resources/views/templates/{category}/{name}/partials/{section}.blade.php`.
   - Composants UI : `resources/views/components/ui/{family}/{name}.blade.php`.
   - Aucun CSS custom, uniquement Tailwind v4 + daisyUI v5, avec priorité aux classes daisyUI.

5. **Portée globale et absence de rétro‑compatibilité**  
   - Ce lot s’applique à **tous les templates existants et futurs** du package.
   - Aucune rétro‑compatibilité n’est requise : les templates peuvent être **déplacés, renommés ou simplifiés** tant que les nouvelles conventions sont respectées.

## 4. Spécifications détaillées

### 4.1. Normalisation de l’arborescence des templates

#### 4.1.1 Cible

- Tous les templates doivent être rangés sous la forme :

  - `resources/views/templates/{category}/{name}.blade.php`

- Catégories cibles alignées sur les règles existantes :
  - `auth/`
  - `profile/`
  - `form/`
  - `layout/`
  - `communication/`
  - `changelog/`
  - `errors/`

#### 4.1.2 Migration des fichiers existants

- Déplacer les fichiers actuellement à la racine de `resources/views/templates` :
  - `form-inline.blade.php` → `form/form-inline.blade.php`
  - `form-with-tabs.blade.php` → `form/form-with-tabs.blade.php`
  - `form-wizard.blade.php` → `form/form-wizard.blade.php`
  - `chat.blade.php` → `communication/chat.blade.php`
  - `notification-center.blade.php` → `communication/notification-center.blade.php`
  - `changelog.blade.php` → `changelog/changelog.blade.php`
  - `error.blade.php` → `errors/error.blade.php`
  - `maintenance.blade.php` → `errors/maintenance.blade.php`
  - `empty-state.blade.php` → `errors/empty-state.blade.php`
  - `loading-state.blade.php` → `errors/loading-state.blade.php`

### 4.2. API Blade générique des templates

#### 4.2.1 Principes

- Chaque template doit exposer :
  - Un bloc `@props([...])` clair, avec :
    - props de **contenu** (titres, sous-titres, messages, boutons),
    - props de **structure** (affichage/masquage de sections),
    - props de **configuration** (classes supplémentaires, alignements, etc.).
  - Un ensemble de **slots nommés** documentés :
    - `header`, `subheader`, `sidebar`, `toolbar`, `primary`, `secondary`, `footer`, `empty`, etc.

- Règle générale :
  - Si un contenu **peut** raisonnablement être spécifique au projet (texte marketing, CTA, liens, badges, stats…), il **doit** être dans un slot, pas en dur.
  - Si un contenu a une valeur par défaut “générique” (ex. “Primary action”, “Cancel”), il peut être une prop avec valeur par défaut traduite.

#### 4.2.2 Exemple type de signature (pattern)

Sans imposer exactement ce jeu de props à tous les templates, on vise ce niveau de généricité :

```blade
@props([
    'layout' => 'default', // variante de layout si le template en propose
    'title' => null,
    'subtitle' => null,
    'icon' => null,

    'showHeader' => true,
    'showBreadcrumb' => false,
    'showSidebar' => false,
    'showFooter' => true,

    'breadcrumb' => [], // [['label' => ..., 'url' => ...]]
    'actions' => [],    // actions “inline” si pas de slot

    'meta' => [],       // tags, stats, badges génériques
])
```

Et des slots typiques :

- `header` (remplace tout l’en-tête),
- `toolbar` (actions secondaires, filtres),
- `sidebar` (contenu latéral),
- `primary` (contenu principal : formulaire, tableau, conversation, etc.),
- `footer` (actions globales, disclaimers),
- `empty` (état vide spécifique).

### 4.3. Partials & Atomic Design

#### 4.3.1 Partials par template

- Pour chaque template complexe, les sous-blocs récurrents doivent être extraits dans :

  - `resources/views/templates/{category}/{name}/partials/{section}.blade.php`

- Exemples de sections candidates :
  - `header.blade.php` (titre + breadcrumb + actions),
  - `sidebar.blade.php`,
  - `stats.blade.php`,
  - `filters.blade.php`,
  - `meta.blade.php`,
  - `empty.blade.php`.

- Les partials **ne doivent contenir aucune donnée métier** : uniquement de la structure, des composants `ui/*`, des slots et des props passées.

#### 4.3.2 Composants UI (Atomic)

- Si un motif commence à être dupliqué **entre plusieurs templates**, il doit être promu en composant `ui/*`. Exemples :
  - `ui/layout/page-header.blade.php` : titre + sous-titre + actions + meta.
  - `ui/layout/page-shell.blade.php` : structure générique (header, contenu principal, sidebar, footer).
  - `ui/layout/section.blade.php` : section avec titre, description, actions.

- Les templates consomment ces composants via `<x-daisy::ui.layout.*>` au lieu de re-déclarer la structure.

### 4.4. Refactor de la commande `InventoryTemplates`

#### 4.4.1 Objectif

- Remplacer la logique actuelle, très spécifique, par une approche **basée sur conventions**, avec un minimum de méta facultatives dans les templates.

#### 4.4.2 Nouvelles règles de détection

- **Catégorie** : déduite uniquement du chemin (`{category}` = premier dossier sous `templates/`).
- **Nom** (`name`) : nom du fichier sans extension.
- **View path** : `daisy::templates.{category}.{name}` (aucune exception).
- **Type** :
  - `reusable` si `{category}` ∈ `{auth, errors}` ou si une annotation l’indique explicitement.
  - `example` pour les autres par défaut.
  - Possibilité de surcharger via une annotation :
    - `{{-- @template-type reusable --}}` ou `{{-- @template-type example --}}`.
- **Route** :
  - Champ **optionnel** ; peut être `null` si aucune route de démo n’est définie.
  - Si une annotation explicite existe, elle est prioritaire :
    - `{{-- @template-route templates.forms.wizard --}}`.
  - Sinon, la commande peut tenter un pattern simple et unique :
    - `templates.{category}.{name}`.
  - Si `Route::has()` (dans le contexte de l’app de démo) renvoie `false`, le champ `route` est laissé à `null` (aucun cas particulier supplémentaire).

#### 4.4.3 Métadonnées optionnelles dans les templates

- Au début de chaque template, possibilité (non obligatoire mais recommandée) d’ajouter des commentaires Blade structurés :

```blade
{{-- @template-label Form wizard with steps --}}
{{-- @template-description Form layout with a multi-step flow and progress indicator. --}}
{{-- @template-tags form, wizard, multi-step --}}
{{-- @template-type example --}}
{{-- @template-route templates.forms.wizard --}}
```

- `InventoryTemplates` :
  - lit ces annotations par regex simple,
  - sinon applique des valeurs par défaut :
    - `label` = `labelize($name)`,
    - `description` = `'Template '.$this->labelize($name).'.'`,
    - `tags` = `[category]`.

- Suppression des tableaux de mapping codés en dur (`$categoryMap`, `defaults` par catégorie) au profit de cette mécanique systématique.

#### 4.4.4 Schéma de sortie standardisé

- Chaque entrée `template` dans `templates.json` doit respecter la même forme :

```json
{
  "name": "form-wizard",
  "category": "form",
  "label": "Form Wizard",
  "description": "Form layout with a multi-step flow and progress indicator.",
  "view": "daisy::templates.form.form-wizard",
  "route": "templates.forms.wizard",
  "type": "example",
  "tags": ["form", "wizard", "multi-step"]
}
```

- Les catégories dans le manifeste contiennent uniquement des métadonnées génériques (id, label générique, éventuellement une icône abstraite), mais plus de texte métier détaillé.

### 4.5. Données & documentation de démonstration

- Adapter `resources/dev/data/templates.json` pour refléter le nouveau schéma standard.

#### 4.5.1 Vues de documentation dédiées par page

- Chaque **page de documentation** doit avoir sa **vue Blade dédiée**, pour plus de simplicité et de stabilité :
  - Par exemple : `resources/dev/views/docs/templates/{category}/{name}.blade.php` pour documenter un template précis,
  - ou `resources/dev/views/docs/templates/{section}.blade.php` pour des pages de synthèse (ex. “overview”, “guidelines”).
- Les éléments suivants peuvent être **calculés automatiquement** lorsque pertinent :
  - navigation (liste des templates, catégories) à partir de `templates.json`,
  - breadcrumbs basés sur la catégorie et le nom du template,
  - badges de type (`reusable` / `example`) et tags.
- Les vues de docs doivent :
  - s’appuyer sur le manifeste unifié,
  - montrer l’API Blade générique (props + slots, partials),
  - éviter les exemples trop métiers (remplacés par des scénarios génériques).

## 5. Livrables techniques complémentaires

- Refactor complet de `app/Console/Commands/InventoryTemplates.php` :
  - suppression des cas particuliers par catégorie,
  - nouvelles fonctions d’extraction des annotations,
  - simplification de la détection de type/catégorie.
- Éventuels nouveaux composants `ui/layout/*` si des motifs se révèlent transverses lors de la refonte des templates.
- Mise à jour des tests existants + nouveaux tests pour la commande d’inventaire.

## 6. Plan de tests

| Suite | Cible | Fichiers (indicatif) | Points vérifiés |
|-------|-------|----------------------|-----------------|
| Feature | Inventaire | `tests/Feature/InventoryTemplatesTest.php` | Détection de tous les templates, lecture des annotations, valeurs par défaut correctes. |
| Feature | Templates génériques | `tests/Feature/Templates/*.php` | Rendu avec props/slots par défaut, sections désactivables, absence de texte métier en dur. |
| Browser | Démos templates | `tests/Browser/*TemplatesTest.php` | Navigation dans les pages de démonstration, absence d’erreurs JS, sections optionnelles. |

## 7. Roadmap d’implémentation

1. **Normalisation de l’arborescence** (déplacement des fichiers vers `templates/{category}/{name}.blade.php`).
2. **Refactor des templates existants** vers API générique (props + slots + sections désactivables).
3. **Extraction de partials** (`partials/`) et, si pertinent, de nouveaux composants `ui/*`.
4. **Refactor de `InventoryTemplates`** pour s’aligner sur les conventions (catégorie/nom, annotations).
5. **Mise à jour des données et docs de démo** pour consommer le nouveau manifeste.
6. **Ajout/mise à jour des tests** (Feature + Browser) et exécution complète de la suite.

## 8. Points de vigilance

- Ne jamais introduire de textes métiers figés dans les templates du package ; tout contenu doit être générique, configurable, ou issu des traductions.
- Éviter de recréer une nouvelle couche de complexité dans `InventoryTemplates` : la priorité est de **remplacer les cas particuliers par des conventions simples**.
- Assumer les breaking changes nécessaires tant que les nouvelles conventions sont mieux documentées, plus simples et plus cohérentes.
- Continuer à respecter strictement daisyUI v5 + Tailwind v4, sans CSS custom.

Ce lot 9 a pour but de livrer un package dont les templates sont à la fois **clairs à comprendre**, **faciles à réutiliser** et **simples à inventorier**, en s’appuyant au maximum sur la puissance de Blade (props, slots, partials) et sur des conventions homogènes plutôt que sur des cas particuliers.


