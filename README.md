## Laravel Daisy Kit

Composants Blade (DaisyUI/Tailwind) prêts à l’emploi pour Laravel, fournis en tant que package.

### Installation

1. Installer via Composer:

```bash
composer require art35rennes/laravel-daisy-kit
```

2. Aucune étape manuelle: le Service Provider est auto‑découvert.

### Utilisation

- Bouton (namespace `daisy`):

```blade
<x-daisy::ui.button color="primary">Primary</x-daisy::ui.button>
```

- Input:

```blade
<x-daisy::ui.input placeholder="Votre nom" />
```

- Layout:

```blade
<x-daisy::layout.app title="Titre">
    Contenu…
</x-daisy::layout.app>
```

Liste complète des composants disponibles: voir `resources/views/components/ui/` et la page de démo `resources/dev/views/demo/index.blade.php`.

### Démo locale (dans ce repo)

- Lancer l’app puis ouvrir `/demo` pour visualiser tous les composants (pages de démo non publiées dans le package):
  - Route: `routes/web.php` → `Route::get('/demo', fn () => view('daisy-dev::demo.index'));`
  - Vue: `resources/dev/views/demo/index.blade.php`

### Icônes

Ce package utilise [Blade Icons](https://blade-ui-kit.com/blade-icons#search) avec Bootstrap Icons pour la gestion des icônes SVG. Bootstrap Icons offre plus de 1800 icônes de haute qualité.

Exemple d'utilisation d'une icône Bootstrap Icons :

```blade
<x-bi-heart class="h-6 w-6 text-red-600" />
<x-bi-house class="h-5 w-5" />
<x-bi-search class="w-4 h-4" />
```

Vous pouvez parcourir toutes les icônes disponibles sur [Bootstrap Icons](https://icons.getbootstrap.com/).

### Publication (optionnelle)

Vous pouvez publier les vues et les traductions pour les surcharger:

```bash
php artisan vendor:publish --tag=daisy-views
php artisan vendor:publish --tag=daisy-lang
```

### Licence

MIT
