<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('updates complete inventory successfully', function () {
    $csvPath = base_path('docs/inventory');
    $componentsCachePath = base_path('bootstrap/cache/daisy-components.php');
    $templatesCachePath = base_path('bootstrap/cache/daisy-templates.php');

    // Nettoyer les fichiers existants pour un test propre
    if (File::exists($componentsCachePath)) {
        File::delete($componentsCachePath);
    }
    if (File::exists($templatesCachePath)) {
        File::delete($templatesCachePath);
    }
    if (File::exists($csvPath.'/components.csv')) {
        File::delete($csvPath.'/components.csv');
    }

    $exitCode = Artisan::call('inventory:update', [
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0)
        ->and(File::exists($componentsCachePath))->toBeTrue()
        ->and(File::exists($templatesCachePath))->toBeTrue()
        ->and(File::exists($csvPath.'/components.csv'))->toBeTrue();

    $componentsManifest = require $componentsCachePath;
    $templatesManifest = require $templatesCachePath;

    expect($componentsManifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('files_hash')
        ->toHaveKey('components')
        ->and($componentsManifest['components'])->toBeArray()
        ->and(count($componentsManifest['components']))->toBeGreaterThan(0);

    expect($templatesManifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('files_hash')
        ->toHaveKey('templates')
        ->and($templatesManifest['templates'])->toBeArray();
});
