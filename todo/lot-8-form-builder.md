# Lot 8 : Form Builder Avancé (No-Code / Low-Code)

Ce lot vise à créer un constructeur de formulaires visuel (WYSIWYG) inspiré de SurveyJS/GrapeJS, permettant de générer des formulaires dynamiques stockés en JSON, avec une validation côté serveur (Laravel) et une logique conditionnelle (JSONata).

## Objectifs
- **Interface Drag & Drop** : Palette de composants (inputs, layouts, etc.) vers une zone de canvas.
- **Non-duplication** : Le builder doit "connaître" les composants DaisyKit existants sans dupliquer leur code HTML/CSS.
- **Interopérabilité** : Export JSON de la configuration + Génération de règles de validation Laravel (`FormRequest` ou `Validator`).
- **Logique dynamique** : Support de JSONata pour les événements (`onInit`, `onChange`, `onSubmit`) et la visibilité conditionnelle.

## Architecture Technique

### 1. Structure de Données (JSON Schema)
Le formulaire sera décrit par un objet JSON standardisé.
```json
{
  "settings": { "theme": "light", "layout": "grid" },
  "pages": [
    {
      "id": "page_1",
      "elements": [
        {
          "type": "daisy-input",
          "name": "user_email",
          "props": { "label": "Email", "type": "email", "required": true },
          "logic": { "visibleIf": "user_age > 18" }
        }
      ]
    }
  ]
}
```

### 2. Backend : Registry & Validation
Pour éviter la duplication, nous créerons un `ComponentRegistry` capable d'inspecter les composants Blade (via Attributs PHP ou fichiers de définition) pour exposer leurs props au Builder.

- **Interface `BuilderAware`** : Les composants Blade implémenteront cette interface pour définir leurs props éditables.
- **ValidationGenerator** : Service convertissant le JSON Schema du form en tableau de règles Laravel (`['user_email' => 'required|email']`).

### 3. Frontend : Module JS `form-builder`
- **Librairie DnD** : Utilisation de `SortableJS` pour gérer le drag-and-drop fluide entre la palette et le canvas.
- **Moteur de Rendu** :
    - *Canvas* : Rendu des composants via des appels AJAX (pour un rendu fidèle au pixel près) ou mapping JS (plus rapide).
    - *Preview* : Mode lecture seule exécutant le formulaire final.
- **Moteur Logique** : Intégration de la librairie `jsonata` (via npm) pour évaluer les expressions en temps réel.

## Liste des Tâches

### Phase 1 : Fondations & Registry
- [ ] Créer l'interface PHP `BuilderAware` et le Trait associé.
- [ ] Implémenter le `ComponentRegistry` qui liste tous les composants éligibles (Inputs, Layouts).
- [ ] Créer un endpoint API pour récupérer la configuration des composants (props, types, valeurs par défaut).

### Phase 2 : UI du Builder (Blade + JS)
- [ ] Créer le layout 3 colonnes :
    - **Gauche** : Palette (Catégories : Inputs, Layout, Buttons, Hidden).
    - **Centre** : Canvas (Zone de drop avec highlighting).
    - **Droite** : Inspecteur de propriétés (S'active au clic sur un élément).
- [ ] Initialiser le module JS `form-builder.js`.
- [ ] Implémenter le Drag & Drop (Palette -> Canvas et Tri dans Canvas).
- [ ] Gérer la sélection d'élément et l'affichage de ses props dans l'inspecteur.

### Phase 3 : Logique & Données
- [ ] Implémenter la sauvegarde/chargement du JSON.
- [ ] Intégrer `jsonata` pour évaluer les champs calculés ou la visibilité (`visibleIf`).
- [ ] Ajouter l'édition des expressions JSONata dans l'inspecteur (avec validation syntaxique basique).

### Phase 4 : Intégration Laravel
- [ ] Créer le service `FormSchemaToValidationRules`.
- [ ] Créer un composant Blade `<x-daisy-form-renderer :schema="$json" />` capable de rendre le formulaire final.
- [ ] Tests : Vérifier que le JSON généré produit les bonnes règles de validation Laravel.

### Phase 5 : UX & Polish
- [ ] Highlight des zones de drop (Dropzones).
- [ ] "Click to add" (Ajout rapide en fin de formulaire).
- [ ] Support du Undo/Redo.
- [ ] Prévisualisation (Mode "Run").

## Stack JS Requise
- `sortablejs` (Drag & Drop)
- `jsonata` (Logic engine)

