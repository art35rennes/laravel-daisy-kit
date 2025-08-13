<?php

return [
    // Quand true, le package poussera automatiquement CSS/JS dans les stacks Blade 'styles' et 'scripts'.
    // Par défaut true pour la démo et l'intégration rapide. Désactivez-le dans l'app hôte si vous gérez les imports manuellement.
    'auto_assets' => true,

    // Utiliser Vite (manifest) ou un bundle statique dans public/vendor
    'use_vite' => true,

    // BuildDirectory dédié si vous bâtissez le package séparément :
    // l'app hôte doit ajouter une instance du plugin Vite avec ce buildDirectory et les inputs du package.
    'vite_build_directory' => 'vendor/art35rennes/laravel-daisy-kit',

    // Chemins fallback vers un bundle statique publié (si use_vite = false)
    'bundle' => [
        'css' => 'vendor/daisy-kit/daisy-kit.css',
        'js'  => 'vendor/daisy-kit/daisy-kit.js',
    ],
];


