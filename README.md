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

Ce package utilise [Blade Icons](https://blade-ui-kit.com/blade-icons#search) pour la gestion des icônes SVG. Vous pouvez rechercher et utiliser des icônes parmi 84 sets d'icônes différents.

Exemple d'utilisation d'une icône Heroicons :

```blade
<x-heroicon-s-heart class="h-6 w-6 text-red-600" />
```

### Publication (optionnelle)

Vous pouvez publier les vues et les traductions pour les surcharger:

```bash
php artisan vendor:publish --tag=daisy-views
php artisan vendor:publish --tag=daisy-lang
```

### Licence

MIT
