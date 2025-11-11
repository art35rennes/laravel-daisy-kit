## Laravel Daisy Kit

Composants Blade (DaisyUI v5 / Tailwind CSS v4) prêts à l'emploi pour Laravel, fournis en tant que package.

## Installation

1. Installer via Composer :

```bash
composer require art35rennes/laravel-daisy-kit
```

2. Aucune étape manuelle : le Service Provider est auto-découvert par Laravel.

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
