<?php

use App\Console\Commands\InventoryComponents;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('generates component inventory successfully', function () {
    $devDataPath = resource_path('dev/data');
    $csvPath = base_path('docs/inventory');

    // Nettoyer les fichiers existants pour un test propre
    if (File::exists($devDataPath.'/components.json')) {
        File::delete($devDataPath.'/components.json');
    }
    if (File::exists($csvPath.'/components.csv')) {
        File::delete($csvPath.'/components.csv');
    }

    $exitCode = Artisan::call('inventory:components');

    expect($exitCode)->toBe(0)
        ->and(File::exists($devDataPath.'/components.json'))->toBeTrue()
        ->and(File::exists($csvPath.'/components.csv'))->toBeTrue();

    $manifest = json_decode(File::get($devDataPath.'/components.json'), true);

    expect($manifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('components')
        ->and($manifest['components'])->toBeArray()
        ->and(count($manifest['components']))->toBeGreaterThan(0);
});

it('generates valid component manifest structure', function () {
    Artisan::call('inventory:components');

    $manifest = json_decode(File::get(resource_path('dev/data/components.json')), true);

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

