<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('generates component inventory successfully', function () {
    $csvPath = base_path('docs/inventory');
    $cachePath = base_path('bootstrap/cache/daisy-components.php');

    // Nettoyer les fichiers existants pour un test propre
    if (File::exists($cachePath)) {
        File::delete($cachePath);
    }
    if (File::exists($csvPath.'/components.csv')) {
        File::delete($csvPath.'/components.csv');
    }

    $exitCode = Artisan::call('inventory:components');

    expect($exitCode)->toBe(0)
        ->and(File::exists($cachePath))->toBeTrue()
        ->and(File::exists($csvPath.'/components.csv'))->toBeTrue();

    $manifest = require $cachePath;

    expect($manifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('files_hash')
        ->toHaveKey('components')
        ->and($manifest['components'])->toBeArray()
        ->and(count($manifest['components']))->toBeGreaterThan(0);
});

it('generates valid component manifest structure', function () {
    Artisan::call('inventory:components');

    $manifest = require base_path('bootstrap/cache/daisy-components.php');

    foreach ($manifest['components'] as $component) {
        expect($component)
            ->toHaveKey('name')
            ->toHaveKey('view')
            ->toHaveKey('category')
            ->toHaveKey('tags')
            ->toHaveKey('status')
            ->and($component['name'])->toBeString()
            ->and($component['view'])->toBeString()
            ->and($component['category'])->toBeString();
    }
});
