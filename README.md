## Laravel Daisy Kit

Composants Blade (DaisyUI v5 / Tailwind CSS v4) prêts à l'emploi pour Laravel, fournis en tant que package.

## Mise à jour de la documentation (`/docs`)

Pour mettre à jour la documentation après avoir ajouté, modifié ou supprimé des composants ou templates :

### Commande unique (recommandée)

Exécutez simplement :
```bash
php artisan inventory:update
```

Cette commande exécute automatiquement toutes les étapes nécessaires dans le bon ordre :
1. Nettoyage des caches Laravel (`optimize:clear`)
2. Génération de l'inventaire des composants
3. Génération de l'inventaire des templates
4. Génération des pages de documentation
5. Compilation des assets (`npm run build`)

Pour forcer la régénération de toutes les pages de documentation (écrase les pages existantes) :
```bash
php artisan inventory:update --force
```

### Commandes individuelles

Si vous préférez exécuter les commandes individuellement :

1. **Générer l'inventaire des composants** :
   ```bash
   php artisan inventory:components
   ```
   Cette commande scanne tous les composants dans `resources/views/components/ui/` et génère :
   - `resources/dev/data/components.json` : Manifeste JSON complet
   - `docs/inventory/components.csv` : Inventaire CSV des composants
   - `docs/inventory/data-attributes.csv` : Inventaire des data-attributes
   - `docs/inventory/js-deps.json` : Dépendances JavaScript

2. **Générer l'inventaire des templates** :
   ```bash
   php artisan inventory:templates
   ```
   Cette commande scanne tous les templates dans `resources/views/templates/` et génère :
   - `resources/dev/data/templates.json` : Manifeste JSON des templates avec catégories et routes

3. **Générer les pages de documentation** :
   ```bash
   php artisan docs:generate-pages
   ```
   Cette commande génère les pages de documentation dans `resources/dev/views/docs/components/{category}/{component}.blade.php` à partir du manifeste.

   Pour forcer la régénération de toutes les pages (écrase les pages existantes) :
   ```bash
   php artisan docs:generate-pages --force
   ```

**Note** : Exécutez toujours `inventory:components` et `inventory:templates` avant `docs:generate-pages` pour garantir que les manifestes sont à jour.

## Installation

1. Installer via Composer :

```bash
composer require art35rennes/laravel-daisy-kit
```

2. Aucune étape manuelle : le Service Provider est auto-découvert par Laravel.

## Architecture du projet

### Arborescence

Le package suit la structure standard d'un package Laravel avec une organisation claire des composants, templates, assets et code source :

```
laravel-daisy-kit/
├── src/                          # Code source PHP du package
│   ├── DaisyKitServiceProvider.php  # Service Provider principal
│   ├── Helpers/                  # Classes utilitaires
│   │   ├── TabErrorBag.php       # Gestion des erreurs par onglet
│   │   ├── ThemeHelper.php       # Gestion des thèmes daisyUI
│   │   └── WizardPersistence.php # Persistance des données de formulaire wizard
│   └── Http/
│       └── Controllers/          # Contrôleurs HTTP
│           └── CsrfTokenController.php
│
├── resources/
│   ├── views/
│   │   ├── components/           # Composants Blade organisés par catégorie
│   │   │   ├── layout/          # Layouts (app, docs, navbar, sidebar)
│   │   │   ├── partials/        # Partiels réutilisables (assets, theme-selector)
│   │   │   ├── templates/       # Templates réutilisables (auth, error, etc.)
│   │   │   │   └── auth/        # Pages d'authentification
│   │   │   └── ui/              # Composants UI par catégorie fonctionnelle
│   │   │       ├── inputs/      # Boutons, inputs, selects, checkboxes, etc.
│   │   │       ├── navigation/  # Breadcrumbs, menu, pagination, navbar, tabs, etc.
│   │   │       ├── layout/      # Card, hero, footer, divider, list, stack, etc.
│   │   │       ├── data-display/# Badge, avatar, table, stat, progress, timeline, etc.
│   │   │       ├── overlay/     # Modal, drawer, dropdown, popover, tooltip, etc.
│   │   │       ├── media/       # Carousel, lightbox, media-gallery, embed, leaflet
│   │   │       ├── feedback/    # Alert, toast, loading, skeleton, callout
│   │   │       ├── communication/# Chat, notifications, conversation-view
│   │   │       ├── utilities/   # Mockups, indicator, dock, csrf-keeper
│   │   │       ├── advanced/    # Calendar, chart, code-editor, filter, etc.
│   │   │       ├── changelog/   # Composants de changelog
│   │   │       └── partials/    # Fragments UI réutilisables
│   │   │
│   │   └── templates/           # Templates d'exemple (layouts, communication, etc.)
│   │       ├── auth/            # Pages d'authentification (référence)
│   │       ├── layout/         # Structures de page (navbar, footer, grid)
│   │       ├── communication/  # Interfaces de communication
│   │       ├── profile/        # Pages de profil utilisateur
│   │       ├── form/           # Templates de formulaires (inline, tabs, wizard)
│   │       └── *.blade.php     # Templates autonomes (changelog, error, etc.)
│   │
│   ├── js/                      # Code JavaScript du package
│   │   ├── kit/                 # Core JavaScript (initialisation, utils)
│   │   │   ├── index.js         # Point d'entrée principal
│   │   │   └── utils/           # Utilitaires (aria, dom, events)
│   │   ├── modules/             # Modules JavaScript par composant
│   │   │   ├── forms/           # Modules pour formulaires (inline, tabs, wizard)
│   │   │   ├── chat-*.js        # Modules de chat
│   │   │   ├── select.js        # Module select amélioré
│   │   │   ├── sidebar.js       # Module sidebar
│   │   │   └── *.js             # Autres modules
│   │   ├── calendar-full/       # Module calendrier complet
│   │   ├── chart/               # Module graphiques
│   │   ├── leaflet/             # Module cartes Leaflet
│   │   └── *.js                 # Modules autonomes (lightbox, popover, etc.)
│   │
│   ├── css/
│   │   └── app.css              # Styles CSS (Tailwind v4 + daisyUI v5)
│   │
│   ├── lang/                    # Fichiers de traduction
│   │   ├── en/                  # Traductions anglaises
│   │   └── fr/                  # Traductions françaises
│   │
│   └── dev/                     # Ressources de développement/documentation
│       ├── data/                # Données générées (components.json, templates.json)
│       ├── img/                 # Images de démonstration
│       └── views/               # Pages de documentation/démo
│           ├── demo/            # Pages de démonstration
│           └── docs/            # Pages de documentation générées
│
├── app/                         # Application Laravel (pour tests/développement)
│   ├── Console/Commands/       # Commandes Artisan personnalisées
│   ├── Http/Controllers/       # Contrôleurs pour la documentation
│   └── Helpers/                 # Helpers de développement
│
├── tests/                       # Tests Pest
│   ├── Browser/                # Tests de navigateur (Pest v4)
│   ├── Feature/                # Tests de fonctionnalités
│   └── Unit/                   # Tests unitaires
│
├── config/
│   └── daisy-kit.php           # Configuration du package
│
├── docs/                        # Documentation générée
│   └── inventory/              # Inventaires (components.csv, data-attributes.csv)
│
└── routes/
    └── web.php                 # Routes de documentation (si activées)
```

### Organisation des composants

Les composants UI sont organisés selon le principe **Atomic Design** et regroupés par **catégorie fonctionnelle** :

- **Atoms** : Éléments de base sans dépendances (inputs, badge, avatar, etc.)
- **Molecules** : Combinaisons simples d'atomes (card, alert, etc.)
- **Organisms** : Combinaisons complexes (navbar, sidebar, table, etc.)
- **Templates** : Structures de page complètes (auth, layout, communication)

### Flux de données

1. **Composants Blade** (`resources/views/components/ui/`) → Exposés via le namespace `daisy::`
2. **Templates** (`resources/views/templates/`) → Utilisables comme vues ou composants
3. **JavaScript** (`resources/js/`) → Initialisé automatiquement via `data-module` attributes
4. **CSS** (`resources/css/`) → Tailwind v4 + daisyUI v5 injectés automatiquement

### Commandes Artisan

Le package expose plusieurs commandes pour la gestion de la documentation :

- `inventory:components` : Génère l'inventaire des composants
- `inventory:templates` : Génère l'inventaire des templates
- `inventory:update` : Met à jour toute la documentation
- `docs:generate-pages` : Génère les pages de documentation

## Ce que le package expose

### Composants Blade

Le package expose plus de 100 composants Blade organisés par catégories, accessibles via le namespace `daisy::` :

- **Inputs** : `button`, `input`, `textarea`, `select`, `checkbox`, `radio`, `range`, `toggle`, `file-input`, `color-picker`
- **Navigation** : `breadcrumbs`, `menu`, `pagination`, `navbar`, `sidebar`, `tabs`, `steps`, `stepper`
- **Layout** : `card`, `hero`, `footer`, `divider`, `list`, `list-row`, `stack`
- **Data Display** : `badge`, `avatar`, `kbd`, `table`, `stat`, `progress`, `radial-progress`, `status`, `timeline`
- **Overlay** : `modal`, `drawer`, `dropdown`, `popover`, `popconfirm`, `tooltip`
- **Media** : `carousel`, `lightbox`, `media-gallery`, `embed`, `leaflet`
- **Feedback** : `alert`, `toast`, `loading`, `skeleton`, `callout`
- **Utilities** : `mockup-browser`, `mockup-code`, `mockup-phone`, `mockup-window`, `indicator`, `dock`
- **Advanced** : `accordion`, `calendar`, `chart`, `code-editor`, `collapse`, `filter`, `onboarding`, `rating`, `scrollspy`, `transfer`, `tree-view`, `validator`, `wysiwyg`, etc.

### Configuration

Le package expose une configuration via `config('daisy-kit.*')` pour personnaliser :
- Gestion automatique des assets CSS/JS
- Intégration Vite
- Thèmes daisyUI (built-in et personnalisés)
- Routes de documentation
- Préfixe des icônes

### Traductions

Les traductions sont accessibles via `__('daisy::*')` pour les composants nécessitant des libellés (calendrier, etc.).

### Routes de documentation (optionnel)

Si activées dans la configuration, le package expose des routes de documentation interactives :
- `/docs` : Accueil de la documentation
- `/docs/templates` : Pages de templates prêtes à l'emploi
- `/docs/{category}/{component}` : Documentation détaillée de chaque composant

## Utilisation

### Composants de base

```blade
{{-- Bouton --}}
<x-daisy::ui.inputs.button color="primary">Primary</x-daisy::ui.inputs.button>

{{-- Input --}}
<x-daisy::ui.inputs.input placeholder="Votre nom" />

{{-- Card --}}
<x-daisy::ui.layout.card>
    <x-slot:title>Titre</x-slot:title>
    Contenu de la carte
</x-daisy::ui.layout.card>
```

### Layout complet

Le package fournit un layout prêt à l'emploi qui gère automatiquement les assets :

```blade
<x-daisy::layout.app title="Mon Application">
    <x-daisy::ui.inputs.button color="primary">Cliquez-moi</x-daisy::ui.inputs.button>
</x-daisy::layout.app>
```

### Templates

Le package fournit deux types de templates :

#### Templates réutilisables (auth, errors)

Les templates réutilisables sont des pages complètes, génériques et autonomes, utilisables directement comme composants Blade ou comme vues :

**Composants Blade** :
```blade
<x-daisy::templates.auth.login-simple 
    title="Connexion"
    :action="route('login')"
/>
```

**Vues** :
```blade
@include('daisy::templates.auth.login-simple', [
    'title' => 'Connexion',
    'action' => route('login'),
])
```

**Templates réutilisables disponibles** :
- `auth/*` : Pages d'authentification (login, register, forgot-password, etc.)
- `error` : Page d'erreur HTTP générique (404, 500, etc.)
- `empty-state` : Page d'état vide
- `loading-state` : Page d'état de chargement
- `maintenance` : Page de maintenance

#### Templates d'exemple (layouts, communication)

Les templates d'exemple sont des structures de pages à copier et adapter dans votre application. Ils ne sont pas utilisables comme composants Blade, mais uniquement comme vues :

**Usage** :
```blade
{{-- Dans votre contrôleur --}}
return view('daisy::templates.layout.grid', [
    'title' => 'Ma page',
    'gap' => 4,
]);
```

**Recommandation** : Copiez le template dans votre application (`resources/views/`) et adaptez-le selon vos besoins.

**Templates d'exemple disponibles** :
- `layout/*` : Structures de page (navbar, footer, grid, etc.)
- `communication/*` : Interfaces de communication (chat, notifications)
- `profile/*` : Pages de profil utilisateur
- `changelog` : Page de changelog

### Assets CSS/JS

Par défaut, les assets sont injectés automatiquement via le layout `app` ou le partial `assets`. Si vous gérez vos assets manuellement :

1. Publiez les sources :
```bash
php artisan vendor:publish --tag=daisy-src
```

2. Intégrez-les dans votre build Vite (`vite.config.js`) :
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/vendor/daisy-kit/css/app.css', // Assets du package
                'resources/vendor/daisy-kit/js/app.js',
            ],
        }),
    ],
});
```

3. Désactivez l'injection automatique dans `config/daisy-kit.php` :
```php
'auto_assets' => false,
```

### Icônes

Ce package utilise [Blade Icons](https://blade-ui-kit.com/blade-icons#search) avec Bootstrap Icons pour la gestion des icônes SVG. Bootstrap Icons offre plus de 1800 icônes de haute qualité.

```blade
<x-bi-heart class="h-6 w-6 text-red-600" />
<x-bi-house class="h-5 w-5" />
<x-bi-search class="w-4 h-4" />
```

Parcourez toutes les icônes disponibles sur [Bootstrap Icons](https://icons.getbootstrap.com/).

## Publication des ressources

Le package expose plusieurs tags de publication pour personnaliser ou intégrer les ressources dans votre application :

### Vues des composants (`daisy-views`)

Pour surcharger les composants dans votre application :

```bash
php artisan vendor:publish --tag=daisy-views
```

**Destination** : `resources/views/vendor/daisy/components/`

Les composants seront copiés depuis `vendor/art35rennes/laravel-daisy-kit/resources/views/components/` vers votre application, vous permettant de les modifier selon vos besoins.

### Pages de documentation (`daisy-dev-views`)

Pour publier les pages de documentation/démo dans votre application :

```bash
php artisan vendor:publish --tag=daisy-dev-views
```

**Destination** : `resources/views/vendor/daisy-dev/`

Les vues de documentation seront copiées depuis `vendor/art35rennes/laravel-daisy-kit/resources/dev/views/` vers votre application.

### Traductions (`daisy-lang`)

Pour personnaliser les traductions :

```bash
php artisan vendor:publish --tag=daisy-lang
```

**Destination** : `lang/vendor/daisy/`

Les fichiers de traduction seront copiés depuis `vendor/art35rennes/laravel-daisy-kit/resources/lang/` vers votre application.

### Configuration (`daisy-config`)

Pour personnaliser la configuration :

```bash
php artisan vendor:publish --tag=daisy-config
```

**Destination** : `config/daisy-kit.php`

Le fichier de configuration sera copié depuis `vendor/art35rennes/laravel-daisy-kit/config/daisy-kit.php` vers votre application.

### Sources CSS/JS (`daisy-src`)

Pour intégrer les sources dans votre build Vite :

```bash
php artisan vendor:publish --tag=daisy-src
```

**Destinations** :
- `resources/vendor/daisy-kit/js/` (fichiers JavaScript)
- `resources/vendor/daisy-kit/css/` (fichiers CSS)

Les sources seront copiées depuis `vendor/art35rennes/laravel-daisy-kit/resources/js/` et `vendor/art35rennes/laravel-daisy-kit/resources/css/` vers votre application, vous permettant de les intégrer dans votre processus de build Vite.

## Documentation interactive

Si la documentation est activée dans la configuration (`daisy-kit.docs.enabled = true`), accédez à :

- `/docs` : Vue d'ensemble de tous les composants
- `/docs/templates` : Templates prêts à l'emploi
- `/docs/{category}/{component}` : Documentation détaillée de chaque composant

## Licence

MIT
