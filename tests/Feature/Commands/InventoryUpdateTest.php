<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

it('updates complete inventory successfully', function () {
    // Mock npm run build pour éviter d'exécuter réellement la commande dans les tests
    Process::fake([
        'npm run build' => Process::result(exitCode: 0),
    ]);

    $devDataPath = resource_path('dev/data');
    $csvPath = base_path('docs/inventory');

    // Nettoyer les fichiers existants pour un test propre
    if (File::exists($devDataPath.'/components.json')) {
        File::delete($devDataPath.'/components.json');
    }
    if (File::exists($devDataPath.'/templates.json')) {
        File::delete($devDataPath.'/templates.json');
    }
    if (File::exists($csvPath.'/components.csv')) {
        File::delete($csvPath.'/components.csv');
    }

    $exitCode = Artisan::call('inventory:update', [
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0)
        ->and(File::exists($devDataPath.'/components.json'))->toBeTrue()
        ->and(File::exists($devDataPath.'/templates.json'))->toBeTrue()
        ->and(File::exists($csvPath.'/components.csv'))->toBeTrue();

    $componentsManifest = json_decode(File::get($devDataPath.'/components.json'), true);
    $templatesManifest = json_decode(File::get($devDataPath.'/templates.json'), true);

    expect($componentsManifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('components')
        ->and($componentsManifest['components'])->toBeArray()
        ->and(count($componentsManifest['components']))->toBeGreaterThan(0);

    expect($templatesManifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('templates')
        ->and($templatesManifest['templates'])->toBeArray();

    // Vérifier que npm run build a été appelé
    Process::assertRan('npm run build');
});

it('updates inventory with force option', function () {
    // Mock npm run build pour éviter d'exécuter réellement la commande dans les tests
    Process::fake([
        'npm run build' => Process::result(exitCode: 0),
    ]);

    $exitCode = Artisan::call('inventory:update', [
        '--force' => true,
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0);

    // Vérifier que npm run build a été appelé
    Process::assertRan('npm run build');
});
