<?php

use Art35rennes\DaisyKit\Support\PackagePaths;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->in('Feature', 'Unit');

dataset('package publish groups', [
    'views' => [
        'daisy-views',
        fn () => [
            PackagePaths::viewsComponents() => resource_path('views/vendor/daisy/components'),
        ],
    ],
    'templates' => [
        'daisy-templates',
        fn () => [
            PackagePaths::viewsTemplates() => resource_path('views/vendor/daisy/templates'),
        ],
    ],
    'lang' => [
        'daisy-lang',
        fn () => [
            PackagePaths::lang() => resource_path('lang/vendor/daisy'),
        ],
    ],
    'config' => [
        'daisy-config',
        fn () => [
            PackagePaths::config() => config_path('daisy-kit.php'),
        ],
    ],
    'assets' => [
        'daisy-assets',
        fn () => [
            PackagePaths::distributableAssets() => public_path('vendor/art35rennes/laravel-daisy-kit'),
        ],
    ],
    'assets-source' => [
        'daisy-assets-source',
        fn () => [
            PackagePaths::js() => resource_path('vendor/daisy-kit/js'),
            PackagePaths::css() => resource_path('vendor/daisy-kit/css'),
        ],
    ],
    'assets-source-alias' => [
        'daisy-src',
        fn () => [
            PackagePaths::js() => resource_path('vendor/daisy-kit/js'),
            PackagePaths::css() => resource_path('vendor/daisy-kit/css'),
        ],
    ],
]);

dataset('asset source entries', [
    'css' => ['type' => 'css', 'expected' => 'resources/vendor/daisy-kit/css/app.css'],
    'js' => ['type' => 'js', 'expected' => 'resources/vendor/daisy-kit/js/app.js'],
]);

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
