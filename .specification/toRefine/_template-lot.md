# Template de spécification (Lot)

> But : servir de base homogène pour rédiger les specs des lots dans `/.specification/toRefine/`.
> À copier/coller puis adapter.

## 1) Contexte
- Pourquoi ce lot existe.
- Liens/dépendances vers d’autres lots.

## 2) Objectifs
- Objectifs fonctionnels (UX).
- Objectifs techniques (architecture, perf, accessibilité, packaging).

## 3) Non-objectifs
- Ce que ce lot ne traite pas (hors périmètre).

## 4) Périmètre exact
- **Composants Blade** ciblés (chemins).
- **Templates** ciblés (chemins).
- **Modules JS** ciblés (chemins).
- **Routes/pages** concernées (démo/docs).

## 5) Comportement attendu (UX + accessibilité)
- États (ouvert/fermé, chargé, erreur, disabled, readonly).
- Raccourcis clavier (Escape, Tab, Enter, Space…).
- Gestion du focus et des attributs `aria-*`.

## 6) API Blade
- Props/slots impactés.
- Valeurs par défaut.
- Stratégie de compat (en phase dev : pas de backward compatibility requise, mais cohérence interne indispensable).

## 7) Architecture (Alpine vs Modules JS)
### Alpine (préféré)
- `x-data`, `x-init`, `x-on`, `x-show`, `x-model`, stores.
- Helpers (persist, debounce, etc.).

### Modules JS (si nécessaire)
- Justification : lib tierce, perf, complexité.
- Hooks `data-*` conservés temporairement pour les tests.

## 8) Plan de migration (étapes)
- Étape 1…
- Étape 2…

## 9) Plan de tests
### Feature tests (rendu)
- Fichiers à créer/adapter.
- Assertions stables (éviter les assertions trop fragiles).

### Browser tests (interactions)
- Scénarios.
- Zéro erreur console.

## 10) Critères d’acceptation
- [ ] Tests verts (Feature + Browser concernés).
- [ ] A11y OK (focus/keyboard/aria).
- [ ] Pas de régression sur les démos/docs.
- [ ] Documentation technique mise à jour si nécessaire.

## 11) Risques & mitigations
- Risque → mitigation.


