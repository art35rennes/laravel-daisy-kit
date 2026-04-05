<?php

use Tests\TestCase;

pest()->extend(TestCase::class)
    ->in('Feature', 'Unit');

dataset('package publish groups', [
    'views' => [
        'daisy-views',
        fn () => [
            packagePath('src/../resources/views/components') => resource_path('views/vendor/daisy/components'),
        ],
    ],
    'templates' => [
        'daisy-templates',
        fn () => [
            packagePath('src/../resources/views/templates') => resource_path('views/vendor/daisy/templates'),
        ],
    ],
    'lang' => [
        'daisy-lang',
        fn () => [
            packagePath('src/../resources/lang') => resource_path('lang/vendor/daisy'),
        ],
    ],
    'config' => [
        'daisy-config',
        fn () => [
            packagePath('src/../config/daisy-kit.php') => config_path('daisy-kit.php'),
        ],
    ],
    'assets-source' => [
        'daisy-assets-source',
        fn () => [
            packagePath('src/../resources/js') => resource_path('vendor/daisy-kit/js'),
            packagePath('src/../resources/css') => resource_path('vendor/daisy-kit/css'),
        ],
    ],
    'assets-source-alias' => [
        'daisy-src',
        fn () => [
            packagePath('src/../resources/js') => resource_path('vendor/daisy-kit/js'),
            packagePath('src/../resources/css') => resource_path('vendor/daisy-kit/css'),
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
