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
        'js' => 'vendor/daisy-kit/daisy-kit.js',
    ],

    // Configuration des icônes - préfixe par défaut
    'icon_prefix' => 'bi',

    // Documentation publique (activable comme Swagger)
    'docs' => [
        'enabled' => true, // Active les routes de documentation du package
        'prefix' => 'docs', // Préfixe des routes publiées (/docs, /docs/{category}/{component}, /docs/templates)
    ],

    // Configuration des thèmes daisyUI
    'themes' => [
        // Thèmes intégrés daisyUI activés par défaut
        'builtin' => [
            'light' => ['default' => true],
            'dark' => ['prefersdark' => true],
            'cupcake',
            'bumblebee',
            'emerald',
            'corporate',
            'synthwave',
            'retro',
            'cyberpunk',
            'valentine',
            'halloween',
            'garden',
            'forest',
            'aqua',
            'lofi',
            'pastel',
            'fantasy',
            'wireframe',
            'black',
            'luxury',
            'dracula',
            'cmyk',
            'autumn',
            'business',
            'acid',
            'lemonade',
            'night',
            'coffee',
            'winter',
        ],

        // Thèmes personnalisés
        // Format: 'nom-du-theme' => [propriétés du thème]
        // Vous pouvez copier-coller un thème depuis le générateur de thème daisyUI : https://daisyui.com/theme-generator/
        'custom' => [
            // Exemple de thème personnalisé (décommentez et modifiez selon vos besoins)
            /*
            'corporate' => [
                'name' => 'corporate',
                'default' => false,
                'prefersdark' => false,
                'color-scheme' => 'light',
                'colors' => [
                    'base-100' => 'oklch(100% 0 0)',
                    'base-200' => 'oklch(93% 0 0)',
                    'base-300' => 'oklch(86% 0 0)',
                    'base-content' => 'oklch(20.08% 0.095 265.18)',
                    'primary' => 'oklch(20.08% 0.095 265.18)',
                    'primary-content' => 'oklch(100% 0 0)',
                    'secondary' => 'oklch(76.4% 0.12 251.34)',
                    'secondary-content' => 'oklch(20.08% 0.095 265.18)',
                    'accent' => 'oklch(46.27% 0.055 272.34)',
                    'accent-content' => 'oklch(100% 0 0)',
                    'neutral' => 'oklch(62.62% 0.041 278.52)',
                    'neutral-content' => 'oklch(20.08% 0.095 265.18)',
                    'info' => 'oklch(83.44% 0.083 251.52)',
                    'info-content' => 'oklch(20.08% 0.095 265.18)',
                    'success' => 'oklch(82.91% 0.204 124.69)',
                    'success-content' => 'oklch(20.08% 0.095 265.18)',
                    'warning' => 'oklch(84.69% 0.165 84.12)',
                    'warning-content' => 'oklch(20.08% 0.095 265.18)',
                    'error' => 'oklch(70.53% 0.175 44.03)',
                    'error-content' => 'oklch(20.08% 0.095 265.18)',
                ],
                'radius' => [
                    'selector' => '0.25rem',
                    'field' => '0.25rem',
                    'box' => '0.25rem',
                ],
                'size' => [
                    'selector' => '0.25rem',
                    'field' => '0.25rem',
                ],
                'border' => '1px',
                'depth' => 0,
                'noise' => 0,
            ],
            */
        ],
    ],
];
