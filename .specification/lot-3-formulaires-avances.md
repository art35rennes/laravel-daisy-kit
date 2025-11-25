# Lot 3 · Plan de spécification – Templates de formulaires avancés

## 1. Objectifs produit
- **Fournir des expériences formulaires complètes** (navigation guidée, onglets contextuels, filtres riches) démontrant la valeur des composants existants.
- **Garantir l’exploitabilité immédiate** dans une application Laravel : validation, persistance, protection CSRF, accessibilité.
- **Livrer des artefacts testés** (Blade + Browser) avec documentation vivante et exemples connectés aux démos.

## 2. Périmètre fonctionnel
| Livrable | Description synthétique | Valeur ajoutée attendue |
|----------|------------------------|-------------------------|
| `form-wizard.blade.php` | Assistant multi-étapes linéaire ou libre avec persistance session et résumé final. | Simplifier les onboarding longs, gérer les validations étape par étape et l’état utilisateur. |
| `form-with-tabs.blade.php` | Formulaire segmenté en onglets avec badges d’erreurs et restauration automatique de l’onglet actif. | Structurer de gros formulaires en sections compréhensibles et fiables. |
| `form-inline.blade.php` | Barre de filtres réactive avec tokens actifs, actions condensées et panel avancé optionnel. | Offrir un moteur de recherche/filtre prêt à l’emploi pour les listes et tableaux. |
| **Composant CSRF Keeper** | Couple Blade + contrôleur dédié qui régénère le token CSRF à intervalles réguliers ou à la demande. | Éviter les échecs de soumission après mise en veille/longue inactivité côté navigateur. |

## 3. Exigences transverses
1. **Utilisation exclusive** des composants `ui/*` existants (Atomic Design respecté).
2. **Props documentées** via PHPDoc + README demo, slots nommés explicites.
3. **Interop Laravel** : `@csrf`, `@method`, `old()`, `$errors`, `session()`, gestion GET/POST.
4. **Accessibilité** : focus visible, aria-label cohérent, annonces d’erreurs.
5. **Instrumentation JS** : chaque template exporte `data-module` + options dérivées des props.
6. **Tests** : Blade + Browser, exécutés via `composer test` (`pest --parallel`).

## 4. Spécifications détaillées

### 4.1 Template · Wizard multi-étapes
- **Chemin** : `resources/views/templates/form-wizard.blade.php`
- **Props principales**
    ```php
    @props([
        'title' => __('form.wizard.title'),
        'action' => '#',
        'method' => 'POST',
        'steps' => [], // [['key' => 'profile', 'label' => 'Profil', 'icon' => 'user']]
        'currentStep' => 1,
        'linear' => true,
        'allowClickNav' => false,
        'showSummary' => true,
        'prevText' => __('form.previous'),
        'nextText' => __('form.next'),
        'finishText' => __('form.finish'),
        'resumeSlot' => 'summary',
    ])
    ```
- **Slots requis** : `step_{key}` (contenu par étape), `summary`, `actions`, `aside` (optionnel).
- **Comportements attendus**
  - Persistance des saisies dans `session('wizard.data')` via helper `WizardPersistence`.
  - Gestion des erreurs par étape (`$errors->getBag("wizard.step_{$key}")`).
  - Résumé final affiché uniquement à la dernière étape.
  - JS `form-wizard` : synchro stepper ↔ boutons, blocage en mode `linear`, écoute `filter-clear`.
- **Tests**
  - Feature : rendu par défaut, step bloquée, résumé, persistance session.
  - Browser : navigation clavier, boutons `Précédent/Suivant`, rechargement du CSRF via CSRF Keeper.

### 4.2 Template · Formulaire à onglets
- **Chemin** : `resources/views/templates/form-with-tabs.blade.php`
- **Props principales**
    ```php
    @props([
        'title' => __('form.tabs.title'),
        'action' => '#',
        'method' => 'POST',
        'tabs' => [], // [['id' => 'general', 'label' => 'Général']]
        'activeTab' => null,
        'tabsStyle' => 'box', // box|border|lift
        'tabsPlacement' => 'top', // top|bottom
        'highlightErrors' => true,
        'showErrorBadges' => true,
        'persistActiveTabField' => '_active_tab',
    ])
    ```
- **Slots requis** : `tab_{id}`, `tab_{id}_footer` (optionnel), `actions`.
- **Comportements attendus**
  - Script `form-tabs` : mise à jour du champ caché `persistActiveTabField`, restauration via `old()`.
  - Helper `TabErrorBag` pour compter les erreurs par onglet (association champs ↔ tab id).
  - Badge d’erreur (via `x-daisy::ui.data-display.badge`) avec le nombre d’erreurs pour chaque onglet impacté.
- **Tests**
  - Feature : onglet par défaut, restauration via `old`, badge erreur.
  - Browser : clic onglet → champ caché mis à jour + conservation après validation échouée.

### 4.3 Template · Formulaire inline / filtres
- **Chemin** : `resources/views/templates/form-inline.blade.php`
- **Props principales**
    ```php
    @props([
        'action' => '#',
        'method' => 'GET',
        'size' => 'sm', // xs|sm|md
        'collapseBelow' => 'md',
        'showReset' => true,
        'submitText' => __('form.search'),
        'resetText' => __('form.reset'),
        'activeFilters' => [], // [['label' => 'Statut', 'value' => 'Actif', 'param' => 'status']]
        'showAdvanced' => false,
        'advancedTitle' => __('form.advanced_filters'),
    ])
    ```
- **Slots requis** : `filters`, `actions`, `active-filters`, `advanced`.
- **Comportements attendus**
  - Tokens actifs affichant les filtres courants avec bouton `×` renvoyant un event `filter-clear`.
  - Passage automatique en layout vertical sous `collapseBelow`.
  - Drawer `x-daisy::ui.overlay.drawer` ouvert via bouton “Filtres avancés” si `showAdvanced=true`.
  - Gestion GET/POST : injection de `@csrf` et `@method` si nécessaire.
- **Tests**
  - Feature : rendu GET (sans CSRF), rendu POST (avec CSRF + méthode), tokens actifs.
  ̀- Browser : suppression d’un filtre → redirection sans le paramètre, ouverture/fermeture du drawer.

### 4.4 Composant CSRF Keeper (NOUVEAU)
- **But** : éviter les échecs de soumission lorsque le navigateur met la page en veille et que le token CSRF expire.
- **Livrables**
  1. **Blade component** `resources/views/components/ui/utilities/csrf-keeper.blade.php`
     - Props : `refreshInterval` (ms, optionnel, par défaut calculé depuis `config('session.lifetime')`), `refreshRatio` (ratio de sécurité, par défaut 0.8), `endpoint` (route dédiée), `module` override.
     - Calcul automatique : `refreshInterval = config('session.lifetime') * 60 * 1000 * refreshRatio` (convertit minutes en ms et applique le ratio de sécurité).
     - Exemple : si `session.lifetime = 120` minutes et `refreshRatio = 0.8`, alors `refreshInterval = 120 * 60 * 1000 * 0.8 = 5760000` ms (96 minutes).
     - Rend un `<div data-module="csrf-keeper" data-refresh-interval="{calculated}" data-endpoint="...">`.
     - Si `refreshInterval` est fourni explicitement, il prend priorité sur le calcul automatique.
  2. **Contrôleur** `Daisy\Kit\Http\Controllers\CsrfTokenController`
     - Méthode `__invoke()` retournant la nouvelle valeur de `csrf_token()` en JSON + header `X-CSRF-TOKEN`.
     - Route incluse dans le package (groupe `daisy-kit.csrf-token`), publication documentée.
  3. **JS module** `resources/js/modules/csrf-keeper.js`
     - Rafraîchit le token à intervalle fixe (basé sur `data-refresh-interval`) ou lorsqu'un event `csrf-keeper:refresh` est dispatché.
     - Met à jour le `<meta name="csrf-token">`, le cookie `_token` si défini, et notifie les listeners via event `csrf-keeper:updated`.
  4. **Intégrations** : les trois templates incluent `x-daisy::ui.utilities.csrf-keeper` (avec possibilité de le désactiver via prop `autoRefreshCsrf=false`).
- **Scénarios gérés**
  - Mise en veille prolongée : rafraîchissement silencieux avant soumission, indexé sur le lifetime de session configuré.
  - Expiration détectée (réponse 419) : relance immédiate d'un refresh + affichage d'une alerte via slot `csrfExpired`.
  - Compatibilité avec installations existantes (route suffixée `.json`, middleware `web` requis).
- **Tests**
  - Feature : route du contrôleur retourne bien un JSON avec token, calcul automatique du refresh interval depuis config.
  - Browser : simulation d'un token expiré → module rafraîchit puis relance la requête réussie, vérification que le refresh se déclenche au bon intervalle.

## 5. Livrables techniques complémentaires
- **Helpers PHP**
  - `WizardPersistence` : lecture/écriture/forget des données wizard en session.
  - `TabErrorBag` : comptage des erreurs par onglet.
- **Modules JS**
  - `form-wizard.js`, `form-tabs.js`, `form-inline.js`, `csrf-keeper.js`.
  - Tous enregistrés dans `resources/js/kit/index.js` avec options typées.
- **Localisation**
  - Fichiers `resources/lang/en/form.php` et `resources/lang/fr/form.php`.
  - Clés supplémentaires : `csrf.refreshing`, `csrf.expired`, `advanced_filters`, `clear_filter`.
- **Documentation**
  - Page `resources/dev/views/docs/templates/forms.blade.php`.
  - Ajout au changelog (`lot 3`).

## 6. Plan de tests
| Suite | Cible | Fichiers | Points vérifiés |
|-------|-------|----------|-----------------|
| Feature | Wizard | `tests/Feature/FormWizardRenderingTest.php` | Props par défaut, persistance, résumé, autoRefresh CSRF activé. |
| Feature | Tabs | `tests/Feature/FormTabsRenderingTest.php` | Onglet actif, badges erreurs. |
| Feature | Inline | `tests/Feature/FormInlineRenderingTest.php` | GET/POST, tokens actifs, drawer. |
| Feature | CSRF Keeper | `tests/Feature/CsrfKeeperControllerTest.php` | Refresh token JSON + headers, calcul automatique du refresh interval depuis `config('session.lifetime')`. |
| Browser | Wizard | `tests/Browser/FormWizardTest.php` | Navigation, refresh CSRF après veille simulée. |
| Browser | Tabs | `tests/Browser/FormTabsTest.php` | Maintien onglet après validation. |
| Browser | Inline | `tests/Browser/FormInlineFilterTest.php` | Suppression filtre, panel avancé, refresh token. |

Tous les tests utilisent `composer test` (alias Pest parallèle). Les Browser tests reposent sur des routes demo (`routes/web.php`, groupe `demo.forms`).

## 7. Roadmap d’implémentation
1. **CSRF Keeper** (base nécessaire aux trois templates).
2. **Form Tabs** (structure de slots + helper TabErrorBag).
3. **Form Inline** (tokens + drawer + intégration CSRF Keeper).
4. **Form Wizard** (persistance session + résumé + usage combiné des briques précédentes).
5. **Docs + Changelog + Traductions**.

## 8. Points de vigilance
- Aucun composant inline SVG : uniquement Blade Icons existants.
- Pas de CSS custom, respect strict Tailwind v4 + daisyUI v5.
- Publication des routes/controllers derrière un flag de config si nécessaire.
- Exposer une prop `csrfKeeper` sur chaque template pour permettre l’opt-out dans les projets qui gèrent déjà ce flux.

Ce plan garantit un lot 3 centré sur des expériences formulaires abouties et robustes, tout en adressant le besoin supplémentaire de rafraîchissement automatique du CSRF pour éviter les échecs utilisateur après longue inactivité.