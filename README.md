## Laravel Daisy Kit

Composants Blade (DaisyUI/Tailwind) prêts à l’emploi pour Laravel.

### Installation

1. Installer via Composer:

```bash
composer require art35rennes/laravel-daisy-kit:^0.1@alpha
```

2. Aucune étape manuelle: le Service Provider est auto‑découvert.

### Utilisation

- Bouton:

```blade
<x-ui::button color="primary">Primary</x-ui::button>
```

- Input:

```blade
<x-ui::input placeholder="Votre nom" />
```

- Layout:

```blade
<x-layout::app title="Titre">
    Contenu…
</x-layout::app>
```

Voir `resources/views/demo.blade.php` pour des exemples plus complets.

### Icônes

Ce package utilise [Blade Icons](https://blade-ui-kit.com/blade-icons#search) pour la gestion des icônes SVG. Vous pouvez rechercher et utiliser des icônes parmi 84 sets d'icônes différents.

Exemple d'utilisation d'une icône Heroicons :

```blade
<x-heroicon-s-heart class="h-6 w-6 text-red-600" />
```

### Publication (optionnelle)

Vous pouvez publier les vues pour les surcharger:

```bash
php artisan vendor:publish --tag=daisy-kit-views
```

### Licence

MIT
