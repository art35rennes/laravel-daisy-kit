# Lot 10 · Plan de spécification – Standardisation de la documentation (templates & composants)

## 1. Objectifs produit

- **Clarifier et stabiliser la documentation** pour les templates et les composants UI du package Laravel Daisy Kit.
- **Réduire la complexité des pages “fourre‑tout”** en ayant une vue Blade dédiée pour chaque page de docs.
- **Faciliter la navigation et la découverte** via une structure prévisible, alimentée si besoin par des données calculées (inventaires).

## 2. Périmètre fonctionnel

| Axe | Description synthétique | Cible |
|-----|-------------------------|-------|
| Pages de docs des templates | Documentation de chaque template (lot 1–9) avec exemples d’usage, API Blade, variantes. | `resources/dev/views/docs/templates/*` |
| Pages de docs des composants | Documentation de tous les composants UI (inputs, layout, overlay, feedback, etc.). | `resources/dev/views/docs/components/*` |
| Navigation & index | Pages d’index et de navigation transverses (sommaires, catégories, recherche simple). | `resources/dev/views/docs/index.blade.php` & sous‑sections |

## 3. Exigences transverses

1. **Une vue Blade par page de doc**  
   - Chaque page de documentation (template ou composant) doit avoir sa **vue dédiée** :
     - Templates : `resources/dev/views/docs/templates/{category}/{name}.blade.php` ou `.../templates/{name}.blade.php` si pas de sous‑catégorie.
     - Composants : `resources/dev/views/docs/components/{family}/{name}.blade.php` (famille = inputs, layout, overlay, etc.).
   - Les pages d’index/synthèse restent possibles mais distinctes, par exemple :
     - `resources/dev/views/docs/templates/index.blade.php`
     - `resources/dev/views/docs/components/index.blade.php`

2. **Aucune logique métier dans la doc**  
   - Les vues de docs démontrent les comportements des templates/composants, **sans logique applicative** (pas de modèle métier, pas de règles de domaine).
   - Les textes doivent rester **génériques**, pédagogiques et centrés sur :
     - l’API Blade (props, slots, partials),
     - les variantes visuelles/comportementales,
     - les bonnes pratiques d’intégration.

3. **Synchronisation avec le package**  
   - La doc doit refléter fidèlement :
     - les signatures Blade réelles (props/slots),
     - les conventions de nommage (namespaces `daisy::`, catégories, etc.),
     - les conventions daisyUI/Tailwind (aucun CSS custom).

4. **Utilisation optionnelle des données calculées**  
   - Lorsqu’un inventaire est disponible (ex. `resources/dev/data/templates.json` pour les templates, inventaire des composants dans un lot ultérieur), il peut servir à :
     - générer des listes de templates/composants dans les pages d’index,
     - construire la navigation (menu latéral, table des matières),
     - afficher des métadonnées (type `reusable/example`, tags, catégories).
   - Les pages de doc restent par contre **écrites individuellement**, pas générées dynamiquement.

## 4. Spécifications détaillées

### 4.1. Documentation des templates

#### 4.1.1 Structure des fichiers

- Pour chaque template du package (`resources/views/templates/{category}/{name}.blade.php`), créer une page de doc dédiée :
  - `resources/dev/views/docs/templates/{category}/{name}.blade.php`  
    Exemple :  
    - Template : `resources/views/templates/form/form-with-tabs.blade.php`  
    - Doc : `resources/dev/views/docs/templates/form/form-with-tabs.blade.php`

- Pour les templates sans sous‑dossier (si jamais il en reste après le lot 9), adopter une convention cohérente :
  - soit les ranger eux aussi par catégorie pour la doc,
  - soit les documenter à la racine de `docs/templates` avec un nom explicite.

#### 4.1.2 Contenu minimal par page

Chaque vue de doc de template doit au minimum :

- Rappeler le **chemin Blade** du template :
  - `view('daisy::templates.form.form-with-tabs')`
  - `<x-daisy::templates.form.form-with-tabs />` si le template est exposé comme composant.
- Documenter l’**API Blade** :
  - liste des **props** (nom, type, valeur par défaut, description),
  - liste des **slots nommés** (nom, rôle, contenu attendu),
  - mention des **partials** éventuels (structure, usage).
- Fournir **au moins un exemple complet** d’utilisation réaliste (mais générique) :
  - exemple simple avec values par défaut,
  - exemple plus avancé si pertinent (multi‑sections, états vides, etc.).
- Mentionner les **interactions JS** s’il y en a (modules `data-module` utilisés, options disponibles).

Optionnellement, une page peut aussi :

- Décrire les cas d’usage recommandés / à éviter.
- Lier vers d’autres templates/composants complémentaires.

### 4.2. Documentation des composants

#### 4.2.1 Structure des fichiers

- Les composants sont organisés par famille dans la doc, en miroir de `resources/views/components/ui` :
  - `resources/dev/views/docs/components/inputs/button.blade.php`
  - `resources/dev/views/docs/components/layout/card.blade.php`
  - `resources/dev/views/docs/components/overlay/modal.blade.php`
  - etc.

- Chaque vue de doc correspond à **un composant Blade** (ou un sous‑ensemble cohérent d’un composant complexe s’il est déjà fragmenté dans la doc actuelle).

#### 4.2.2 Contenu minimal par page

Chaque page de doc composant doit :

- Afficher le **nom du composant** et son **namespace Blade** :
  - `<x-daisy::ui.inputs.button … />`
- Lister l’**API Blade** :
  - props supportées (variantes de couleur, taille, état, options de layout, etc.),
  - slots éventuels (contenu, icônes, labels, etc.),
  - conventions Atomic (ce que le composant assume / n’assume pas).
- Montrer plusieurs **exemples d’usage** :
  - configuration minimale,
  - variantes visuelles principales (primary, secondary, ghost…),
  - cas avancés si besoin (composition avec d’autres composants).

### 4.3. Index & navigation

#### 4.3.1 Pages d’index

- Conserver ou créer des pages d’index explicites :
  - `resources/dev/views/docs/index.blade.php` (landing doc globale),
  - `resources/dev/views/docs/templates/index.blade.php` (sommaire templates),
  - `resources/dev/views/docs/components/index.blade.php` (sommaire composants).

- Ces pages peuvent utiliser des données calculées :
  - pour les templates : `resources/dev/data/templates.json`,
  - pour les composants : un inventaire futur (ex. CSV, JSON ou classe helper).

#### 4.3.2 Navigation latérale / table des matières

- La navigation dans les docs (sidebar, table des matières) peut être :
  - soit codée en dur dans des partials de doc si c’est plus simple,
  - soit construite à partir des inventaires (templates/composants).

- Dans tous les cas, elle doit :
  - refléter la structure réelle des fichiers (catégories, familles),
  - rester stable (pas de génération aléatoire ou d’ordre surprenant).

## 5. Livrables techniques complémentaires

- Création / mise à jour des vues dans `resources/dev/views/docs/templates/` pour aligner la doc sur les templates refactorés par le lot 9.
- Création / mise à jour des vues dans `resources/dev/views/docs/components/` pour couvrir l’ensemble des composants UI.
- Adaptation éventuelle des partials de navigation / layout des pages de docs pour refléter la nouvelle structure.

## 6. Plan de tests

- Vérifier manuellement (et via tests Browser existants si nécessaire) que :
  - chaque lien de la doc (index, templates, composants) pointe vers une vue existante,
  - aucune page n’agrège trop de responsabilités (plus de “gros fourre‑tout” pour plusieurs familles différentes),
  - les exemples de code Blade proposés compilent dans l’app de démo.

Ce lot 10 isole la **refonte documentaire** (templates + composants) pour qu’elle soit gérable indépendamment, tout en restant cohérente avec les conventions introduites par le lot 9 sur les templates et l’inventaire.


