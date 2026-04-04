<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

it('renders published manifest assets when vite helper integration is disabled', function () {
    $buildPath = public_path('vendor/art35rennes/laravel-daisy-kit');
    $assetsPath = $buildPath.'/assets';

    File::ensureDirectoryExists($assetsPath);

    File::put($buildPath.'/manifest.json', json_encode([
        'resources/css/app.css' => [
            'file' => 'assets/app.css',
        ],
        'resources/js/app.js' => [
            'file' => 'assets/app.js',
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    config([
        'daisy-kit.use_vite' => false,
        'daisy-kit.auto_assets' => true,
        'daisy-kit.vite_build_directory' => 'vendor/art35rennes/laravel-daisy-kit',
    ]);

    $html = Blade::render(<<<'BLADE'
        @include('daisy::components.partials.assets')
        @stack('styles')
        @stack('scripts')
    BLADE);

    expect($html)
        ->toContain('vendor/art35rennes/laravel-daisy-kit/assets/app.css')
        ->toContain('vendor/art35rennes/laravel-daisy-kit/assets/app.js');
});
